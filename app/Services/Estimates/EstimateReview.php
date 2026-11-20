<?php

namespace App\Services\Estimates;

use App\Jobs\SendRepairRequestNotice;
use App\Models\LibraryTask;
use App\Models\RepairDraft;
use App\Models\RepairEstimateRelease;
use App\Models\RepairRequest;
use App\Models\User;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EstimateReview
{
    public function save(RepairRequest $repair, User $staff, array $input): void
    {
        DB::transaction(function () use ($repair, $staff, $input) {
            $repair = RepairRequest::lockForUpdate()->findOrFail($repair->id);
            $draft = $this->editableDraft($repair, (int) $input['review_version']);
            $oldLines = $draft->lines->keyBy('id');
            $selected = collect($input['lines'])->reject(fn ($line) => ! empty($line['exclude']))->pluck('task_id')->filter();
            $tasks = LibraryTask::whereIn('task_id', $selected)->get()->keyBy('task_id');
            $before = ['analysis' => $draft->analysis, 'total' => $draft->total, 'lines' => $draft->lines->toArray()];
            $lines = [];
            $used = [];
            $total = BigDecimal::zero()->toScale(2);
            foreach ($input['lines'] as $i => $row) {
                $id = $row['line_id'] ?? null;
                $previous = $id === null ? null : $oldLines->get($id);
                if ($id !== null && (! $previous || isset($used[$id]))) {
                    $this->invalid("lines.{$i}.task_id", 'This draft line is invalid or duplicated. Reload the estimate.');
                }
                if ($id !== null) {
                    $used[$id] = true;
                }
                if (! empty($row['exclude'])) {
                    continue;
                }

                $task = $tasks->get($row['task_id'] ?? '');
                if (! $task || empty($row['quantity']) || ! BigDecimal::of($row['quantity'])->isPositive()) {
                    $this->invalid("lines.{$i}.task_id", 'Choose a Library task and enter a positive quantity, or mark the line excluded.');
                }
                $sameTask = $previous?->task_id === $task->task_id && $previous?->unit === $task->unit;
                if (in_array($task->task_id, LibraryTask::AMBIGUOUS, true) && empty($row['confirmed'])) {
                    $this->invalid("lines.{$i}.confirmed", 'Confirm the unit and quantity for this task.');
                }

                $fixed = $task->pricing_mode === 'fixed';
                $original = $fixed ? ($sameTask ? ($previous->original_rate ?? $previous->rate ?? $task->bidfreq_standard_rate) : $task->bidfreq_standard_rate) : null;
                $rate = $row['rate'] ?? ($fixed ? $original : ($sameTask ? $previous->rate : null));
                if ($rate === null) {
                    $this->invalid("lines.{$i}.rate", 'Enter the Admin rate for this quote-by-scope item.');
                }
                $override = $original !== null && ! BigDecimal::of($rate)->isEqualTo($original);
                $reason = $row['override_reason'] ?? ($sameTask ? $previous->override_reason : null);
                if ($override && blank($reason)) {
                    $this->invalid("lines.{$i}.override_reason", 'Enter a written reason for the rate override.');
                }

                $lineTotal = BigDecimal::of($row['quantity'])->multipliedBy($rate)->toScale(2, RoundingMode::HalfUp);
                $total = $total->plus($lineTotal);
                $lines[] = [
                    'task_id' => $task->task_id, 'library_version_id' => $sameTask && $previous->rate !== null ? $previous->library_version_id : $task->library_version_id,
                    'name' => $sameTask ? $previous->name : $task->task_name, 'unit' => $task->unit,
                    'quantity' => $row['quantity'], 'rate' => (string) $rate, 'line_total' => (string) $lineTotal,
                    'original_rate' => $override ? (string) $original : null, 'override_reason' => $override ? $reason : null,
                    'priced_on' => now()->toDateString(), 'source' => 'manual',
                    'evidence' => $sameTask ? $previous->evidence : [], 'flags' => [],
                ];
            }
            if (count($used) !== $oldLines->count()) {
                $this->invalid('lines', 'Review or explicitly exclude each existing line.');
            }
            if ($lines === []) {
                $this->invalid('lines', 'Keep at least one priced line in the estimate.');
            }

            $analysis = array_replace($draft->analysis, Arr::only($input, ['complaint', 'cause', 'correction', 'complications']), [
                'assumptions' => $this->notes($input['assumptions'] ?? null),
                'exclusions' => $this->notes($input['exclusions'] ?? null),
                'evidence_links' => $input['evidence_links'] ?? [],
            ]);
            $draft->update([
                'analysis' => $analysis, 'total' => (string) $total,
                'over_nte' => $repair->nte !== null && $total->isGreaterThan($repair->nte),
                'review_version' => $draft->review_version + 1,
            ]);
            $draft->lines()->delete();
            $draft->lines()->createMany($lines);
            $repair->events()->create(['user_id' => $staff->id, 'action' => 'edited', 'details' => [
                'note' => $input['review_note'], 'before' => $before,
                'after' => ['analysis' => $analysis, 'total' => (string) $total, 'lines' => $lines],
            ]]);
        });
    }

    public function release(RepairRequest $repair, User $staff, int $expected): RepairEstimateRelease
    {
        return DB::transaction(function () use ($repair, $staff, $expected) {
            $repair = RepairRequest::lockForUpdate()->findOrFail($repair->id);
            $draft = $this->editableDraft($repair, $expected);
            if ($draft->lines->isEmpty() || $draft->lines->contains(fn ($line) => $line->line_total === null || $line->flags !== [])) {
                $this->invalid('approval', 'Resolve or explicitly exclude all flagged and unpriced lines before release.');
            }
            $prior = $repair->releases()->max('version') ?? 0;
            if ($repair->status === 'revision_requested' && $draft->review_version <= ($repair->currentRelease?->snapshot['review_version'] ?? 0)) {
                $this->invalid('approval', 'Save the revised estimate before releasing a new version.');
            }

            $snapshot = [
                'number' => $repair->number(), 'review_version' => $draft->review_version,
                'address' => "{$repair->street_address}, {$repair->city}, {$repair->state} {$repair->zip}",
                'total' => $draft->total, 'analysis' => Arr::only($draft->analysis, ['complaint', 'cause', 'correction', 'complications', 'assumptions', 'exclusions']),
                'lines' => $draft->lines->map(fn ($line) => $line->only(['name', 'unit', 'quantity', 'rate', 'line_total', 'task_id', 'library_version_id', 'priced_on', 'source', 'original_rate', 'override_reason']))->all(),
            ];
            $release = $repair->releases()->create([
                'approved_by' => $staff->id, 'version' => $prior + 1, 'snapshot' => $snapshot,
            ]);
            $repair->update(['status' => 'approved']);
            $repair->events()->create(['user_id' => $staff->id, 'action' => 'released', 'details' => ['version' => $release->version]]);
            SendRepairRequestNotice::dispatch($repair->id, $prior ? 'revised' : 'ready', $repair->user_id, $release->id)
                ->onConnection(config('estimate_requests.queue_connection'));

            return $release;
        });
    }

    public function decide(RepairRequest $repair, User $customer, array $input): void
    {
        DB::transaction(function () use ($repair, $customer, $input) {
            $repair = RepairRequest::lockForUpdate()->findOrFail($repair->id);
            $release = $repair->currentRelease;
            if ($repair->status !== 'approved' || ! $release || $release->id !== $input['release_id'] || $release->decided_at !== null) {
                $this->invalid('decision', 'This estimate version is no longer available for a decision.');
            }
            $status = match ($input['decision']) {
                'approve' => 'customer_approved',
                'reject' => 'customer_rejected',
                'revise' => 'revision_requested',
            };

            $release->update([
                'decision' => $input['decision'], 'decided_by' => $customer->id, 'decided_at' => now(),
                'customer_comment' => $input['comment'] ?? null,
            ]);
            $repair->update(['status' => $status]);
            $repair->events()->create(['user_id' => $customer->id, 'action' => $status, 'details' => ['version' => $release->version, 'signature' => $input['decision'] === 'approve' ? $input['signature'] : null]]);
        });
    }

    private function editableDraft(RepairRequest $repair, int $expected): RepairDraft
    {
        $draft = $repair->draft()->with('lines')->first();
        if (! $draft || ! in_array($repair->status, ['draft', 'under_review', 'revision_requested'], true) || $draft->review_version !== $expected) {
            $this->invalid('approval', 'This draft has changed or is unavailable. Reload the review page.');
        }

        return $draft;
    }

    private function invalid(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    private function notes(?string $text): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", $text ?? '')), fn ($line) => $line !== ''));
    }
}
