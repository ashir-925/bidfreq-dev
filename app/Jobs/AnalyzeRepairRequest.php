<?php

namespace App\Jobs;

use App\Models\LibraryTask;
use App\Models\RepairRequest;
use App\Services\Estimates\DraftPricing;
use App\Services\Estimates\EstimateAiProvider;
use App\Services\Estimates\GeminiEstimateProvider;
use App\Support\ReportFailure;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;
use Throwable;
use UnexpectedValueException;

class AnalyzeRepairRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $tries = 0;

    public $timeout = 90;

    public function __construct(public int $requestId, public int $run) {}

    public function middleware(): array
    {
        return [(new WithoutOverlapping('repair-ai:'.$this->requestId.':'.$this->run))->releaseAfter(30)->expireAfter(120)];
    }

    public function handle(EstimateAiProvider $provider, DraftPricing $pricing): void
    {
        $repair = RepairRequest::findOrFail($this->requestId);
        if ($repair->ai_run !== $this->run || ! in_array($repair->status, ['ai_pending', 'ai_retrying'], true) || $repair->draft()->exists()) {
            return;
        }

        $previous = $repair->attempts()->where('run', $this->run)->latest('attempt')->first();
        if ($previous?->outcome === 'running') {
            $previous->update(['outcome' => 'failed', 'error' => 'The previous worker did not finish.']);
        }
        $maximum = max(1, (int) config('estimate_requests.max_attempts'));
        if (($previous?->attempt ?? 0) >= $maximum) {
            $this->finishFailure($repair);
            return;
        }

        $input = $previous?->input ?? $this->input($repair);
        $attempt = $repair->attempts()->create([
            'run' => $this->run, 'attempt' => ($previous?->attempt ?? 0) + 1,
            'model' => config('estimate_requests.gemini_model'), 'input' => $input, 'outcome' => 'running',
        ]);

        $response = ['raw' => null];
        try {
            $response = $provider->analyze($input);
            if (isset($response['model'])) {
                $attempt->update(['model' => $response['model']]);
            }
            if ($response['error'] !== null) {
                throw new UnexpectedValueException($response['error']);
            }
            [$analysis, $items] = $pricing->decode($response['text']);
            $citations = json_decode($response['text'], true, 32, JSON_THROW_ON_ERROR)['evidence_links'] ?? [];
            $sentFiles = collect($input['files'])->filter(fn ($file) => $file['sent'])
                ->keyBy(fn ($file) => $file['number'] ?? $file['id']);
            foreach (['complaint', 'cause', 'correction', 'complications'] as $field) {
                $numbers = is_array($citations[$field] ?? null) ? $citations[$field] : [];
                $analysis['evidence_links'][$field] = collect($numbers)->filter(fn ($number) => is_int($number))
                    ->map(fn ($number) => $sentFiles->get($number)['id'] ?? null)
                    ->filter()->unique()->take(20)->values()->all();
            }
            DB::transaction(function () use ($repair, $attempt, $pricing, $analysis, $items, $response, $input) {
                $repair = RepairRequest::lockForUpdate()->findOrFail($repair->id);
                if ($repair->ai_run !== $this->run || $repair->draft()->exists()) {
                    $attempt->update(['outcome' => 'cancelled', 'response' => $response['raw']]);
                    return;
                }

                $priced = $pricing->calculate($repair, $analysis, $items, collect($input['evidence']['library'])->pluck('task_id')->all());
                $draft = $repair->draft()->create($priced['draft'] + ['repair_ai_attempt_id' => $attempt->id]);
                $draft->lines()->createMany($priced['lines']);
                $attempt->update(['outcome' => 'succeeded', 'response' => $response['raw']]);
                $repair->update(['status' => 'draft']);
            });
        } catch (Throwable $e) {
            $error = $e instanceof UnexpectedValueException ? $e->getMessage() : 'The analysis could not be completed.';
            if (! $e instanceof UnexpectedValueException) {
                ReportFailure::log($e, 'repair.ai', $repair->user_id);
            }
            $attempt->update(['outcome' => 'failed', 'error' => $error, 'response' => $response['raw']]);
            if ($attempt->attempt >= $maximum) {
                $this->finishFailure($repair);
                return;
            }

            $repair->update(['status' => 'ai_retrying']);
            $delays = [60, 120, 300, 600];
            $this->release($delays[min($attempt->attempt - 1, count($delays) - 1)]);
        }
    }

    private function input(RepairRequest $repair): array
    {
        $files = $repair->files->values()->map(fn ($file, $index) => [
            'number' => $index + 1,
            'id' => $file->id, 'kind' => $file->kind, 'trade' => $file->trade,
            'mime' => $file->mime, 'path' => $file->path,
            'sent' => GeminiEstimateProvider::canAttach($file->mime) && ($file->mime !== 'text/plain' || $file->bytes <= 131072),
        ])->values()->all();
        return [
            'instruction' => GeminiEstimateProvider::INSTRUCTIONS,
            'files' => $files,
            'evidence' => [
                'follow_up' => $repair->events()->where('action', 'information_received')->get()->pluck('details.message')->filter()->values()->all(),
                'descriptions' => $repair->descriptions, 'home_sqft' => $repair->home_sqft,
                'stories' => $repair->stories, 'damaged_areas' => $repair->dimensions,
                'attachments' => array_map(fn ($file) => array_diff_key($file, ['path' => true, 'id' => true]), $files),
                'library' => LibraryTask::orderBy('task_id')->get(['task_id', 'task_name', 'unit'])->all(),
            ],
        ];
    }

    private function finishFailure(RepairRequest $repair): void
    {
        DB::transaction(function () use ($repair) {
            $current = RepairRequest::lockForUpdate()->findOrFail($repair->id);
            if ($current->ai_run === $this->run && in_array($current->status, ['ai_pending', 'ai_retrying'], true)) {
                $current->update(['status' => 'ai_failed']);
                SendRepairRequestNotice::notifyStaff($current, 'failed');
            }
        });
    }
}
