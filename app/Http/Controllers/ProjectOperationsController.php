<?php

namespace App\Http\Controllers;

use App\Jobs\SendProjectCompletedNotice;
use App\Models\Project;
use App\Models\RepairRequest;
use App\Support\ReportFailure;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProjectOperationsController extends Controller
{
    public function update(Request $request, Project $project)
    {
        Gate::authorize('manage', Project::class);
        $data = $request->validate([
            'estimator_id' => ['nullable', 'integer', Rule::exists('estimators', 'id')],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'estimated_completion_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'materials' => ['nullable', 'string', 'max:5000'],
            'crew_contacts' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(Project::STATUSES)],
        ]);
        $data['materials'] = $this->lines($data['materials'] ?? null);
        $data['crew_contacts'] = $this->lines($data['crew_contacts'] ?? null);

        return $this->mutate($request, $project, 'project_updated', function (Project $current) use ($data) {
            $this->requireOpen($current);
            $next = $data['status'];
            $estimatorId = $data['estimator_id'] ?? null;
            if ($estimatorId !== null && (int) $estimatorId !== (int) $current->estimator_id
                && ! \App\Models\Estimator::whereKey($estimatorId)->lockForUpdate()->first()?->active) {
                throw ValidationException::withMessages(['estimator_id' => 'Select an active estimator.']);
            }
            if (($next === 'in_progress' && empty($data['start_date'])) || ($current->status === 'in_progress' && $next === 'scheduled')) {
                throw ValidationException::withMessages(['status' => 'Starting work requires a start date. A project already in progress cannot return to Scheduled.']);
            }
            if ($next === 'completed' && ($current->status !== 'in_progress' || $current->walkthrough_status !== 'complete'
                || ! $current->certificate_reference || ! $current->certificate_date || ! $current->files()->where('kind', 'certificate')->exists())) {
                throw ValidationException::withMessages(['status' => 'Start work, complete the walkthrough, and upload the completion certificate before marking the project complete.']);
            }
            if (in_array($next, ['completed', 'cancelled'], true) && $current->changeOrders()->whereIn('status', ['draft', 'pending'])->exists()) {
                throw ValidationException::withMessages(['status' => 'Resolve the open Change Orders before closing this project.']);
            }

            $before = $current->only(array_keys($data));
            $current->update($data);
            $estimateStatus = match ($next) {
                'scheduled' => 'customer_approved',
                default => $next,
            };
            $current->repairRequest()->update(['status' => $estimateStatus]);
            if ($next === 'completed') {
                SendProjectCompletedNotice::dispatch($current->id)->onConnection(config('estimate_requests.queue_connection'))->afterCommit();
            }

            return ['before' => $before, 'after' => $current->only(array_keys($data))];
        });
    }

    public function report(Request $request, Project $project)
    {
        Gate::authorize('manage', Project::class);
        $data = $request->validate([
            'report_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'reported_by' => ['required', 'string', 'max:150'],
            'progress' => ['required', 'string', 'max:8000'],
            'notes' => ['nullable', 'string', 'max:8000'],
            'photos' => ['nullable', 'array', 'max:8'],
            'photos.*' => ['required', 'file', 'max:5120', 'mimetypes:'.implode(',', RepairRequest::PHOTO_MIMES)],
            'captions' => ['nullable', 'array', 'max:8'],
            'captions.*' => ['nullable', 'string', 'max:255'],
        ]);
        foreach ($request->file('photos', []) as $index => $photo) {
            if (blank($data['captions'][$index] ?? null)) {
                throw ValidationException::withMessages(["captions.$index" => 'Add a caption for this photo.']);
            }
        }

        return $this->mutate($request, $project, 'daily_report_added', function (Project $current, array &$paths) use ($request, $data) {
            $this->requireOpen($current);
            if ($current->reports()->whereDate('report_date', $data['report_date'])->exists()) {
                throw ValidationException::withMessages(['report_date' => 'This project already has a report for that date.']);
            }
            $report = $current->reports()->create([
                ...collect($data)->only(['report_date', 'reported_by', 'progress', 'notes'])->all(),
                'user_id' => $request->user()->id,
            ]);
            foreach ($request->file('photos', []) as $index => $photo) {
                $this->storeFile($current, $photo, 'photo', $paths, $report->id, $data['captions'][$index]);
            }

            return ['report_id' => $report->id, 'after' => $report->toArray()];
        });
    }

    public function document(Request $request, Project $project)
    {
        Gate::authorize('manage', Project::class);
        $request->validate([
            'submission_key' => ['nullable', 'uuid'],
            'document' => ['required', 'file', 'max:10240', 'mimetypes:'.implode(',', RepairRequest::DOCUMENT_MIMES)],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        return $this->mutate($request, $project, 'document_added', function (Project $current, array &$paths) use ($request) {
            $this->requireOpen($current);

            $submissionKey = $request->input('submission_key');
            if ($submissionKey && $current->events()->where('action', 'document_added')->where('details->submission_key', $submissionKey)->exists()) {
                return null;
            }

            return [
                'submission_key' => $submissionKey,
                'file_id' => $this->storeFile($current, $request->file('document'), 'document', $paths, null, $request->input('caption')),
            ];
        });
    }

    public function removeFile(Request $request, Project $project, \App\Models\ProjectFile $file)
    {
        Gate::authorize('manage', Project::class);
        abort_unless((int) $file->project_id === $project->id, 404);

        return $this->mutate($request, $project, 'project_file_removed', function (Project $current) use ($file) {
            $this->requireOpen($current);
            $attachment = $current->files()->findOrFail($file->id);
            if ($attachment->kind === 'certificate') {
                throw ValidationException::withMessages(['document' => 'Completion certificates are retained in project history.']);
            }
            $attachment->delete();
            DB::afterCommit(function () use ($attachment) {
                try {
                    if (! Storage::disk('local')->delete($attachment->path)) {
                        throw new \RuntimeException('Could not remove the stored project file.');
                    }
                } catch (Throwable $exception) {
                    ReportFailure::log($exception, 'project.delete_file');
                }
            });

            return ['before' => $attachment->only(['id', 'name', 'kind']), 'after' => null];
        });
    }

    public function walkthrough(Request $request, Project $project)
    {
        Gate::authorize('manage', Project::class);
        $data = $request->validate([
            'walkthrough_date' => ['required', 'date_format:Y-m-d'],
            'walkthrough_status' => ['required', 'in:scheduled,follow_up,complete'],
            'walkthrough_notes' => ['nullable', 'string', 'max:8000'],
            'certificate_reference' => ['nullable', 'required_with:certificate,certificate_date', 'string', 'max:255'],
            'certificate_date' => ['nullable', 'required_with:certificate,certificate_reference', 'date_format:Y-m-d', 'after_or_equal:walkthrough_date', 'before_or_equal:today'],
            'certificate' => ['nullable', 'file', 'max:10240', 'mimetypes:application/pdf,image/jpeg,image/png,image/webp'],
        ]);
        unset($data['certificate']);
        if ($data['walkthrough_status'] === 'complete' && $data['walkthrough_date'] > today()->toDateString()) {
            throw ValidationException::withMessages(['walkthrough_date' => 'A completed walkthrough cannot be dated in the future.']);
        }

        return $this->mutate($request, $project, 'walkthrough_updated', function (Project $current, array &$paths) use ($request, $data) {
            $this->requireOpen($current);
            if ($request->hasFile('certificate')) {
                $this->storeFile($current, $request->file('certificate'), 'certificate', $paths);
            }
            $before = $current->only(array_keys($data));
            $current->update($data);

            return ['before' => $before, 'after' => $current->only(array_keys($data))];
        });
    }

    private function lines(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', $text ?? '')), fn ($value) => $value !== ''));
    }

    private function requireOpen(Project $project): void
    {
        if (in_array($project->status, ['completed', 'cancelled'], true)) {
            throw ValidationException::withMessages(['project' => 'This project is closed.']);
        }
    }

    private function storeFile(Project $project, UploadedFile $file, string $kind, array &$paths, ?int $report = null, ?string $caption = null): int
    {
        $path = $file->store('projects/'.$project->reference, 'local');
        if (! $path) {
            throw new \RuntimeException('Unable to store project attachment.');
        }
        $paths[] = $path;
        return $project->files()->create([
            'project_daily_report_id' => $report, 'kind' => $kind, 'path' => $path,
            'name' => Str::limit(basename(str_replace('\\', '/', $file->getClientOriginalName())), 200, ''),
            'mime' => $file->getMimeType(), 'caption' => $caption,
        ])->id;
    }

    private function mutate(Request $request, Project $project, string $action, Closure $operation)
    {
        $paths = [];
        try {
            DB::transaction(function () use ($request, $project, $action, $operation, &$paths) {
                $current = Project::lockForUpdate()->findOrFail($project->id);
                $details = $operation($current, $paths);
                if ($details !== null) {
                    $current->events()->create(['user_id' => $request->user()->id, 'action' => $action, 'details' => $details]);
                }
            });
        } catch (ValidationException $exception) {
            Storage::disk('local')->delete($paths);
            throw $exception;
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($paths);
            ReportFailure::log($exception, $action, $request->user()->id);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Project changes could not be saved. Please try again.'], 500);
            }

            return back()->withInput()->with('error', 'Project changes could not be saved. Please try again.');
        }

        if ($request->expectsJson()) {
            $request->session()->flash('status', 'Project changes saved.');
            return response()->json(['saved' => true]);
        }

        return back()->with('status', 'Project changes saved.');
    }
}






