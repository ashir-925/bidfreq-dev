<?php

namespace App\Http\Controllers;

use App\Models\{Project, ProjectInvoice, ProjectPayment};
use App\Services\{ProjectPaymentProvider};
use App\Support\ReportFailure;
use Brick\Math\BigDecimal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Gate};
use Illuminate\Validation\ValidationException;
use Throwable;

class ProjectPaymentController extends Controller
{
    public function start(Request $request, Project $project, ProjectInvoice $invoice, ProjectPaymentProvider $provider)
    {
        Gate::authorize('view', $project);
        abort_unless($request->user()->id === (int) $project->user_id && (int) $invoice->project_id === $project->id, 403);
        abort_unless(config('project_payments.stripe.key') && config('project_payments.stripe.secret'), 503);
        $data = $request->validate(['amount' => ProjectBillingController::MONEY, 'reference' => 'required|uuid']);
        $amount = BigDecimal::of($data['amount']);
        try {
            $payment = DB::transaction(function () use ($project, $invoice, $data, $amount) {
                Project::lockForUpdate()->findOrFail($project->id);
                $invoice->refresh()->load('payments');
                $known = $invoice->payments->firstWhere('reference', $data['reference']);
                $open = $invoice->payments->first(fn ($record) => $record->provider !== 'manual' && in_array($record->status, ['pending', 'failed'], true));
                if ($invoice->status !== 'sent' || $amount->isGreaterThan($invoice->balance()) || $amount->isLessThan('0.50') || $amount->isGreaterThan('999999.99')
                    || ($known && (!$open || !$open->is($known)))
                    || ($open && (! $amount->isEqualTo($open->amount) || $open->provider !== config('project_payments.provider')))) {
                    throw ValidationException::withMessages(['amount' => 'Enter a payable amount within the invoice balance, or cancel the current attempt before changing its amount.']);
                }
                return $open ?? $invoice->payments()->create([
                    'reference' => $data['reference'], 'provider' => config('project_payments.provider'), 'status' => 'pending', 'amount' => (string) $amount,
                ]);
            });
            $session = $provider->session($payment);
            ProjectPayment::whereKey($payment->id)->whereNull('provider_id')->update(['provider_id' => $session['id']]);
            return response()->json(['client_secret' => $session['client_secret']])->header('Cache-Control', 'no-store');
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            ReportFailure::log($exception, 'project.payment.start', $request->user()->id);
            return response()->json(['message' => 'The card form could not be prepared. Try again.'], 503);
        }
    }

    public function cancel(Request $request, Project $project, ProjectInvoice $invoice, ProjectPaymentProvider $provider)
    {
        Gate::authorize('view', $project);
        abort_unless(($request->routeIs('admin.*') || $request->user()->id === (int) $project->user_id) && (int) $invoice->project_id === $project->id, 403);
        try {
            DB::transaction(function () use ($project, $invoice, $provider, $request) {
                $current = Project::lockForUpdate()->findOrFail($project->id);
                $payment = $invoice->payments()->where('provider', config('project_payments.provider'))->whereIn('status', ['pending', 'failed'])->first();
                if ($payment) {
                    $provider->cancel($payment);
                    $payment->update(['status' => 'cancelled']);
                    $current->events()->create(['user_id' => $request->user()->id, 'action' => 'payment_attempt_cancelled', 'details' => ['payment_id' => $payment->id]]);
                }
            });
        } catch (Throwable $exception) {
            ReportFailure::log($exception, 'project.payment.cancel_attempt', $request->user()->id);
            return back()->with('error', 'The card attempt could not be cancelled. It may already be processing.');
        }
        return back()->with('status', 'Card attempt cleared.');
    }

    public function verify(Request $request, Project $project, ProjectInvoice $invoice, ProjectPaymentProvider $provider)
    {
        Gate::authorize('view', $project);
        abort_unless(($request->routeIs('admin.*') || $request->user()->id === (int) $project->user_id)
            && (int) $invoice->project_id === $project->id, 403);
        try {
            $status = DB::transaction(function () use ($request, $project, $invoice, $provider) {
                $current = Project::lockForUpdate()->findOrFail($project->id);
                $payment = $invoice->payments()->where('provider', config('project_payments.provider'))
                    ->whereIn('status', ['pending', 'failed'])->first();
                if (! $payment) {
                    return $invoice->payments()->where('provider', config('project_payments.provider'))->value('status') ?? 'none';
                }
                $verified = $provider->verify($payment);
                $before = $payment->status;
                $payment->update([
                    'status' => $verified['status'], 'provider_id' => $verified['id'], 'method' => 'Card',
                    'received_on' => $verified['status'] === 'completed' ? today() : null,
                ]);
                if ($before !== $payment->status) {
                    $current->events()->create(['user_id' => $request->user()->id, 'action' => 'card_payment_verified',
                        'details' => ['payment_id' => $payment->id, 'before' => $before, 'after' => $payment->status]]);
                }
                return $payment->status;
            });
        } catch (Throwable $exception) {
            ReportFailure::log($exception, 'project.payment.verify', $request->user()->id);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Payment status is unavailable. Check again shortly.'], 503);
            }
            return back()->with('error', 'Payment status is unavailable. Check again shortly.');
        }
        if ($request->expectsJson()) {
            return response()->json(['status' => $status])->header('Cache-Control', 'no-store');
        }
        return back()->with('status', 'Card payment status: '.$status.'.');
    }
}
