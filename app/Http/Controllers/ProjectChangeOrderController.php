<?php

namespace App\Http\Controllers;

use App\Models\{LibraryTask, Project, ProjectChangeOrder};
use App\Support\ReportFailure;
use App\Services\Estimates\DraftPricing;
use Brick\Math\BigDecimal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Gate};
use Illuminate\Validation\ValidationException;
use Throwable;

class ProjectChangeOrderController extends Controller
{
    public function save(Request $request, Project $project, ?ProjectChangeOrder $order = null)
    {
        Gate::authorize('manage', Project::class);
        abort_if($order?->exists && (int) $order->project_id !== $project->id, 404);
        $data = $request->validate([
            'title' => 'required|string|max:255', 'report_id' => 'nullable|integer',
            'lines' => 'required|array|list|min:1|max:50', 'lines.*.task_id' => 'nullable|string|max:32',
            'lines.*.description' => 'nullable|string|max:255',
            'lines.*.quantity' => ['nullable','numeric','regex:/^\d{1,8}(?:\.\d{1,6})?$/D','gt:0'],
            'lines.*.rate' => ['nullable','regex:/^\d{1,10}(?:\.\d{1,6})?$/D'],
            'lines.*.reason' => 'nullable|string|max:1000', 'lines.*.confirmed' => 'nullable|boolean',
        ]);

        return $this->mutate($request, $project, function (Project $current) use ($data, $order) {
            $reportId = $data['report_id'] ?? null;
            if ($reportId && ! $current->reports()->whereKey($reportId)->exists()) {
                throw ValidationException::withMessages(['report_id' => 'Select a report from this project.']);
            }
            $existing = $order?->exists ? $current->changeOrders()->findOrFail($order->id) : null;
            if ($existing && $existing->status !== 'draft') {
                throw ValidationException::withMessages(['change_order' => 'Only draft Change Orders can be edited.']);
            }
            $tasks = LibraryTask::whereIn('task_id', array_filter(array_column($data['lines'], 'task_id')))->get()->keyBy('task_id');
            $total = BigDecimal::zero()->toScale(2);
            $lines = [];
            foreach ($data['lines'] as $index => $line) {
                $task = $tasks->get($line['task_id'] ?? '');
                $quantity = $line['quantity'] ?? null;
                $description = $line['description'] ?? null;
                if (! $task && blank($description)) {
                    throw ValidationException::withMessages(["lines.$index.description" => 'Select a Library task or describe unmatched work.']);
                }
                $prior = $existing?->lines[$index] ?? [];
                $sameTask = $task && ($prior['task_id'] ?? null) === $task->task_id;
                $original = $sameTask ? $prior['library_rate'] : $task?->bidfreq_standard_rate;
                $rate = $task ? ($line['rate'] ?? $original) : null;
                $override = $original !== null && ! BigDecimal::of($rate)->isEqualTo($original);
                $flags = array_values(array_filter([
                    ! $task ? 'Unmatched work' : null,
                    ! $quantity ? 'Enter a quantity' : null,
                    $task && $rate === null ? 'Enter an Admin rate for this quote-by-scope task' : null,
                    $override && blank($line['reason'] ?? null) ? 'Enter an override reason' : null,
                    $task && in_array($task->task_id, LibraryTask::AMBIGUOUS, true) && empty($line['confirmed']) ? 'Confirm unit and quantity' : null,
                ]));
                $amount = $flags ? null : DraftPricing::lineTotal($quantity, $rate);
                if ($amount !== null) {
                    $total = $total->plus($amount);
                }
                $lines[] = [
                    'task_id' => $task?->task_id, 'name' => $sameTask ? $prior['name'] : ($task?->task_name ?? $description),
                    'unit' => $sameTask ? $prior['unit'] : $task?->unit,
                    'library_version_id' => $sameTask ? $prior['library_version_id'] : $task?->library_version_id,
                    'quantity' => $quantity, 'rate' => $rate, 'library_rate' => $original,
                    'override_reason' => $override ? ($line['reason'] ?? null) : null, 'flags' => $flags,
                    'line_total' => $amount === null ? null : (string) $amount, 'source' => 'manual', 'priced_on' => now()->toDateString(),
                    'confirmed' => (bool) ($line['confirmed'] ?? false),
                ];
            }
            $values = ['title' => $data['title'], 'project_daily_report_id' => $reportId, 'lines' => $lines, 'total' => (string) $total, 'status' => 'draft'];
            $before = $existing?->toArray();
            if ($existing) {
                $existing->update($values);
            } else {
                $existing = $current->changeOrders()->create($values);
            }
            return ['before' => $before, 'after' => $existing->toArray()];
        });
    }

    public function status(Request $request, Project $project, ProjectChangeOrder $order)
    {
        Gate::authorize('manage', Project::class);
        abort_unless((int) $order->project_id === $project->id, 404);
        $next = $request->validate(['status' => 'required|in:pending,approved,rejected'])['status'];
        return $this->mutate($request, $project, function (Project $current) use ($order, $next) {
            $order = $current->changeOrders()->findOrFail($order->id);
            $previous = $order->status;
            $allowed = match ($previous) {
                'draft' => ['pending', 'rejected'],
                'pending' => ['approved', 'rejected'],
                default => [],
            };
            if (! in_array($next, $allowed, true) || ($next !== 'rejected' && collect($order->lines)->contains(fn ($line) => ! empty($line['flags'])))) {
                throw ValidationException::withMessages(['change_order' => 'Resolve all flagged lines before submitting or approving a Change Order.']);
            }
            $order->update(['status' => $next]);
            return ['order_id' => $order->id, 'before' => $previous, 'after' => $next];
        });
    }

    private function mutate(Request $request, Project $project, callable $mutation)
    {
        try {
            DB::transaction(function () use ($request, $project, $mutation) {
                $current = Project::lockForUpdate()->findOrFail($project->id);
                if (in_array($current->status, ['cancelled', 'completed'], true)) {
                    throw ValidationException::withMessages(['change_order' => 'This project is closed.']);
                }
                $current->events()->create(['user_id' => $request->user()->id, 'action' => 'change_order_updated', 'details' => $mutation($current)]);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            ReportFailure::log($exception, 'project.change_order', $request->user()->id);
            return back()->withInput()->with('error', 'Could not save the Change Order.');
        }
        return back()->with('status', 'Change Order saved.');
    }
}


