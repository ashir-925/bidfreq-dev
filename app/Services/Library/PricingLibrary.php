<?php

namespace App\Services\Library;

use App\Models\LibraryImportPreview;
use App\Models\LibraryTask;
use App\Models\LibraryTaskChange;
use App\Models\LibraryVersion;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PricingLibrary
{
    public function __construct(public readonly LibraryFormat $format) {}

    public function preview(string $path, string $filename, User $user): LibraryImportPreview
    {
        try {
            [$rows, $errors] = $this->format->read($path, strtolower(pathinfo($filename, PATHINFO_EXTENSION)));
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['file' => $e->getMessage()]);
        }

        LibraryImportPreview::where('expires_at', '<=', now())->delete();

        return LibraryImportPreview::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'filename' => Str::limit(basename(str_replace('\\', '/', $filename)), 255, ''),
            'rows' => $rows,
            'errors' => $errors,
            'fingerprint' => $this->fingerprint($this->tasks($rows)),
            'expires_at' => now()->addHour(),
        ]);
    }

    public function changes(array $rows, ?Collection $tasks = null): array
    {
        $tasks ??= $this->tasks($rows);
        $counts = ['new' => 0, 'rate_changed' => 0, 'updated' => 0, 'unchanged' => 0];
        $changes = [];
        foreach ($rows as $id => $data) {
            $before = $tasks->has($id) ? $this->format->row($tasks->get($id)) : null;
            $after = $this->format->normalizedRow($data);
            $kind = match (true) {
                $before === null => 'new',
                $before === $after => 'unchanged',
                $before['bidfreq_standard_rate'] !== $after['bidfreq_standard_rate'] => 'rate_changed',
                default => 'updated',
            };
            $counts[$kind]++;
            if ($kind !== 'unchanged') {
                $changes[$id] = compact('kind', 'before', 'after');
            }
        }

        return [$counts, $changes];
    }

    public function apply(string $id, User $user): LibraryVersion
    {
        return DB::transaction(function () use ($id, $user) {
            $preview = LibraryImportPreview::whereKey($id)->where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            if ($preview->expires_at->isPast() || $preview->errors !== []) {
                throw ValidationException::withMessages(['file' => 'This preview has errors or has expired. Upload a corrected file.']);
            }

            $rows = $preview->rows;
            $tasks = $this->tasks($rows, lock: true);
            if (! hash_equals($preview->fingerprint, $this->fingerprint($tasks))) {
                throw ValidationException::withMessages(['file' => 'The Library changed after this preview. Upload the file again and review the current changes.']);
            }

            [$summary, $changes] = $this->changes($rows, $tasks);
            $version = LibraryVersion::create([
                'user_id' => $user->id, 'filename' => $preview->filename,
                'action' => 'import', 'summary' => $summary,
            ]);

            foreach ($changes as $taskId => $change) {
                $task = $tasks->get($taskId) ?? new LibraryTask;
                $task->fill($rows[$taskId]);
                $this->saveChange($task, $version, $change['before'], $change['after']);
            }

            $preview->delete();

            return $version;
        }, 3);
    }

    public function edit(LibraryTask $task, array $fields, User $user, string $expectedVersion): bool
    {
        return DB::transaction(function () use ($task, $fields, $user, $expectedVersion) {
            $current = LibraryTask::whereKey($task->getKey())->lockForUpdate()->firstOrFail();
            if ((string) $current->library_version_id !== $expectedVersion) {
                throw ValidationException::withMessages(['library_version_id' => 'This task was updated while you were editing it. Reload the task before saving.']);
            }

            $before = $this->format->row($current);
            $data = $this->format->normalize(['task_id' => $current->task_id] + $fields);
            $after = $this->format->normalizedRow($data);
            if ($before === $after) {
                return false;
            }

            $version = LibraryVersion::create([
                'user_id' => $user->id, 'action' => 'edit', 'summary' => ['updated' => 1],
            ]);
            $this->saveChange($current->fill($data), $version, $before, $after);

            return true;
        }, 3);
    }

    public function isStale(LibraryImportPreview $preview): bool
    {
        return ! hash_equals($preview->fingerprint, $this->fingerprint($this->tasks($preview->rows)));
    }

    private function tasks(array $rows, bool $lock = false): Collection
    {
        // Chunk IN bindings for databases with lower parameter limits.
        $tasks = collect();
        $ids = array_keys($rows);
        sort($ids, SORT_STRING);
        foreach (array_chunk($ids, 400) as $chunk) {
            $result = LibraryTask::whereIn('task_id', $chunk)->orderBy('task_id')->when($lock, fn ($query) => $query->lockForUpdate())->get();
            foreach ($result as $task) {
                $tasks->put($task->task_id, $task);
            }
        }

        return $tasks;
    }

    private function fingerprint(Collection $tasks): string
    {
        return hash('sha256', json_encode($tasks->map(fn (LibraryTask $task) => $task->library_version_id), JSON_THROW_ON_ERROR));
    }

    private function saveChange(LibraryTask $task, LibraryVersion $version, ?array $before, array $after): void
    {
        $task->library_version_id = $version->id;
        $task->save();
        LibraryTaskChange::create([
            'task_id' => $task->task_id, 'library_version_id' => $version->id,
            'before' => $before, 'after' => $after,
        ]);
    }
}
