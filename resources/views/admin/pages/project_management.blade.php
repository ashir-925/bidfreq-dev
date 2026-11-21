@if ($open)
<section class="sec"><div class="sec__head"><h2 class="sec__title">Assignment and dates</h2></div><div class="sec__body">
<form method="POST" action="{{ route('admin.projects.update', $project) }}">@csrf @method('PATCH')
<label class="label">Estimator <select class="select" name="estimator_id"><option value="">Unassigned</option>
@if ($project->estimator && !$project->estimator->active)<option value="{{ $project->estimator_id }}" selected>{{ $project->estimator->name }} (inactive)</option>@endif
@foreach ($estimators as $contact)<option value="{{ $contact->id }}" @selected((string) old('estimator_id', $project->estimator_id) === (string) $contact->id)>{{ $contact->name }}</option>@endforeach
</select></label>
<label class="label">Start date <input class="input" type="date" name="start_date" value="{{ old('start_date', $project->start_date?->toDateString()) }}"></label>
<label class="label">Target completion date <input class="input" type="date" name="estimated_completion_date" value="{{ old('estimated_completion_date', $project->estimated_completion_date?->toDateString()) }}"></label>
<label class="label">Materials (one per line) <textarea class="textarea" name="materials">{{ old('materials', implode("\n", $project->materials ?? [])) }}</textarea></label>
<label class="label">Crews or subcontractors (contact and assigned work, one per line) <textarea class="textarea" name="crew_contacts">{{ old('crew_contacts', implode("\n", $project->crew_contacts ?? [])) }}</textarea></label>
<label class="label">Project status <select class="select" name="status">
@foreach (\App\Models\Project::STATUSES as $status)
@if (($project->status === 'scheduled' && $status !== 'completed') || ($project->status === 'in_progress' && $status !== 'scheduled'))
<option value="{{ $status }}" @selected(old('status', $project->status) === $status)>{{ Str::headline($status) }}</option>
@endif
@endforeach
</select></label>
<button class="btn btn--primary" data-loading-text="Saving project…">Save project</button>
</form></div></section>
<section class="sec"><div class="sec__head"><h2 class="sec__title">Walkthrough and certificate</h2></div><div class="sec__body">
<form method="POST" enctype="multipart/form-data" data-project-upload action="{{ route('admin.projects.walkthrough', $project) }}">@csrf
<label class="label">Walkthrough date <input class="input" type="date" name="walkthrough_date" value="{{ old('walkthrough_date', $project->walkthrough_date?->toDateString()) }}" required></label>
<label class="label">Walkthrough status <select class="select" name="walkthrough_status">@foreach (['scheduled', 'follow_up', 'complete'] as $status)<option value="{{ $status }}" @selected(old('walkthrough_status', $project->walkthrough_status) === $status)>{{ Str::headline($status) }}</option>@endforeach</select></label>
<label class="label">Walkthrough notes <textarea class="textarea" name="walkthrough_notes">{{ old('walkthrough_notes', $project->walkthrough_notes) }}</textarea></label>
<label class="label">Certificate reference <input class="input" name="certificate_reference" value="{{ old('certificate_reference', $project->certificate_reference) }}"></label>
<label class="label">Certificate date <input class="input" type="date" name="certificate_date" value="{{ old('certificate_date', $project->certificate_date?->toDateString()) }}"></label>
<label class="label">Certificate file <input class="input" type="file" name="certificate" accept="application/pdf,image/jpeg,image/png,image/webp"></label>
<button class="btn btn--primary" data-loading-text="Saving walkthrough…">Save walkthrough</button><p role="status" aria-live="polite" data-upload-feedback></p>
</form></div></section>
@endif
<section class="sec"><div class="sec__head"><h2 class="sec__title">Project history</h2></div><div class="sec__body">
@forelse ($events as $event)<p><strong>{{ Str::headline($event->action) }}</strong> · {{ $event->created_at->format('M j, Y g:i A') }}@if (is_string($event->details['after'] ?? null)) · {{ Str::headline($event->details['after']) }}@endif</p>
@empty<p>No history has been recorded.</p>@endforelse
</div></section>

