<?php

namespace App\Http\Controllers;

use App\Models\{Project, ProjectInvoice, ProjectPayment};
use App\Support\ReportFailure;
use Brick\Math\BigDecimal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Gate};
use Illuminate\Validation\ValidationException;
use Throwable;

class ProjectBillingController extends Controller
{
    public const MONEY = ['required', 'numeric', 'gt:0', 'regex:/^\d{1,12}(?:\.\d{1,2})?$/D'];

    public function invoice(Request $request, Project $project, ?ProjectInvoice $invoice = null)
    {
        Gate::authorize('manage', Project::class);
        abort_if($invoice?->exists && (int) $invoice->project_id !== $project->id, 404);
        $data = $request->validate([
            'status' => 'required|in:draft,sent,cancelled', 'due_date' => 'required|date_format:Y-m-d',
            'invoice_type' => $invoice?->exists ? 'nullable|in:initial,additional,final' : 'required|in:initial,additional,final',
            'reference' => $invoice?->exists ? 'nullable|uuid' : 'required|uuid',
            'initial_amount' => array_merge(['nullable'], array_slice(self::MONEY, 1)),
            'orders' => 'nullable|array|list', 'orders.*' => 'integer|distinct',
        ]);

        return $this->mutate($request, $project, 'invoice_saved', function (Project $current) use ($invoice, $data) {
            $existing = $invoice?->exists ? $current->invoices()->with('payments')->findOrFail($invoice->id) : null;
            if ($existing) {
                $before = $existing->only(['status', 'due_date']);
                $allowed = match ($existing->status) {
                    'draft' => ['draft', 'sent', 'cancelled'],
                    'sent' => ['sent', 'cancelled'],
                    default => [],
                };
                if (! in_array($data['status'], $allowed, true) || ($data['status'] === 'cancelled' && ($existing->netPaid()->isPositive() || $this->openAttempt($existing)))) {
                    $this->invalid('Resolve payments before cancelling. Issued invoices cannot return to draft.');
                }
                $existing->update(['status' => $data['status'], 'due_date' => $data['due_date']]);
                return ['invoice_id' => $existing->id, 'before' => $before, 'after' => $existing->only(['status', 'due_date'])];
            }

            if ($current->invoices()->where('reference', $data['reference'])->exists()) {
                return null;
            }

            $type = $data['invoice_type'];
            if ($data['status'] === 'cancelled' || ($type === 'final' && $current->status !== 'completed')
                || ($type !== 'final' && $current->status === 'cancelled')) {
                $this->invalid('Final invoices require a completed project. Cancelled projects cannot receive new charges.');
            }
            $issued = $current->invoices()->where('status', '!=', 'cancelled')->get()->flatMap(fn ($row) => $row->lines);
            $snapshot = $current->release->snapshot;
            $remaining = $issued->where('source', 'estimate')->reduce(fn ($amount, $line) => $amount->minus($line['amount']), BigDecimal::of($snapshot['total']));
            $lines = [];
            if ($type === 'initial') {
                $amount = isset($data['initial_amount']) ? BigDecimal::of($data['initial_amount']) : null;
                if ($current->status === 'completed' || ! $amount || $amount->isGreaterThan($remaining)) {
                    $this->invalid('Enter an initial amount within the unbilled approved estimate before project completion.');
                }
                $lines[] = ['source' => 'estimate', 'description' => 'Initial installment of approved estimate', 'amount' => (string) $amount];
            }
            if ($type === 'final' && $remaining->isPositive()) {
                $lines[] = ['source' => 'estimate', 'description' => 'Unbilled approved estimate', 'amount' => (string) $remaining];
            }
            $orders = $current->changeOrders()->where('status', 'approved')->whereNotIn('id', $issued->where('source', 'order')->pluck('order_id'))->get();
            if ($type === 'additional') {
                $ids = $data['orders'] ?? [];
                $orders = $orders->whereIn('id', $ids);
                if (! $ids || $orders->count() !== count($ids)) {
                    $this->invalid('Select approved Change Orders that have not already been invoiced.');
                }
            }
            if ($type !== 'initial') {
                foreach ($orders as $order) {
                    $lines[] = ['source' => 'order', 'order_id' => $order->id, 'description' => $order->title, 'amount' => $order->total];
                }
            }
            $total = collect($lines)->reduce(fn ($amount, $line) => $amount->plus($line['amount']), BigDecimal::zero());
            if (! $total->isPositive()) {
                $this->invalid('This invoice has no unbilled amount.');
            }
            $invoice = $current->invoices()->create([
                'reference' => $data['reference'], 'invoice_type' => $type, 'status' => $data['status'], 'due_date' => $data['due_date'],
                'lines' => $lines, 'total' => (string) $total,
            ]);
            return ['invoice_id' => $invoice->id, 'after' => $invoice->toArray()];
        });
    }

    public function record(Request $request, Project $project, ProjectInvoice $invoice)
    {
        Gate::authorize('manage', Project::class);
        abort_unless((int) $invoice->project_id === $project->id, 404);
        $data = $request->validate(['amount' => self::MONEY, 'reference' => 'required|uuid',
            'received_on' => 'required|date_format:Y-m-d|before_or_equal:today',
            'method' => 'required|string|max:80', 'external_reference' => 'nullable|string|max:150',
            'status' => 'required|in:completed,pending,failed,refunded']);
        return $this->mutate($request, $project, 'payment_recorded', function (Project $current) use ($invoice, $data) {
            $invoice = $current->invoices()->with('payments')->findOrFail($invoice->id);
            $recorded = $invoice->payments->firstWhere('reference', $data['reference']);
            if ($recorded && $recorded->provider === 'manual') {
                return null;
            }
            $amount = BigDecimal::of($data['amount']);
            if ($invoice->status !== 'sent' || $this->openAttempt($invoice)
                || ($data['status'] === 'refunded' && $amount->isGreaterThan($invoice->netPaid()))
                || ($data['status'] === 'completed' && $amount->isGreaterThan($invoice->balance()))) {
                $this->invalid('Use a sent invoice without an active card attempt. Payments cannot exceed the balance and refunds cannot exceed net paid.');
            }
            $payment = $invoice->payments()->create($data + ['provider' => 'manual']);
            return ['invoice_id' => $invoice->id, 'after' => $payment->toArray()];
        });
    }

    public function refundDetails(Request $request, Project $project, ProjectInvoice $invoice, ProjectPayment $payment)
    {
        Gate::authorize('manage', Project::class);
        abort_unless((int) $invoice->project_id === $project->id && (int) $payment->project_invoice_id === $invoice->id
            && $payment->provider === 'manual' && $payment->status === 'refunded', 404);
        $values = $request->validate(['received_on' => 'required|date_format:Y-m-d|before_or_equal:today',
            'method' => 'required|string|max:80', 'external_reference' => 'nullable|string|max:150']);
        return $this->mutate($request, $project, 'refund_details_updated', function () use ($invoice, $payment, $values) {
            $payment->refresh();
            $before = $payment->only(array_keys($values));
            $payment->update($values);
            return ['invoice_id' => $invoice->id, 'payment_id' => $payment->id, 'before' => $before, 'after' => $values];
        });
    }

    private function openAttempt(ProjectInvoice $invoice): bool
    {
        return $invoice->payments->contains(fn ($payment) => $payment->provider !== 'manual' && in_array($payment->status, ['pending', 'failed'], true));
    }

    private function mutate(Request $request, Project $project, string $action, callable $mutation)
    {
        try {
            DB::transaction(function () use ($request, $project, $action, $mutation) {
                $current = Project::lockForUpdate()->findOrFail($project->id);
                $details = $mutation($current);
                if ($details !== null) {
                    $current->events()->create(['user_id' => $request->user()->id, 'action' => $action, 'details' => $details]);
                }
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            ReportFailure::log($exception, $action, $request->user()->id);
            return back()->withInput()->with('error', 'Could not save the billing record.');
        }

        return back()->with('status', 'Billing record saved.');
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['billing' => $message]);
    }
}


