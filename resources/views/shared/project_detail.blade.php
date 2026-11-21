@if ($project)
@php($fileRoute = $staff ? 'admin.projects.file' : 'projects.file')
@php($open = !in_array($project->status, ['completed', 'cancelled'], true))
<header class="page-header"><div class="page-header__text"><h1 class="page-header__title">{{ $project->address }}</h1><p class="page-header__subtitle"><span class="chip">{{ Str::headline($project->status) }}</span> · {{ $project->repairRequest->number() }}</p></div></header>
@if ($errors->any())<div class="alert alert--danger" role="alert"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="detail-grid">
<div class="detail-main">
<section class="sec"><div class="sec__head"><h2 class="sec__title">Approved Estimate</h2>
<a class="btn btn--secondary btn--sm" href="{{ $staff ? route('admin.estimate-review', $project->repairRequest) : route('repair-requests.pdf', [$project->repairRequest, $project->release]) }}">{{ $staff ? 'Review estimate' : 'Download estimate PDF' }}</a></div>
<div class="sec__body table-scroll"><table class="line-items"><thead><tr><th>Work item</th><th>Qty / unit</th><th class="num">Rate</th><th class="num">Total</th></tr></thead><tbody>
@foreach ($project->release->snapshot['lines'] as $line)
<tr><td>{{ $line['name'] }}</td><td>{{ \App\Services\Estimates\EstimateDisplay::quantity($line['quantity']) }} {{ $line['unit'] }}</td><td class="num">{{ \App\Services\Estimates\EstimateDisplay::currency($line['rate']) }}</td><td class="num">{{ \App\Services\Estimates\EstimateDisplay::currency($line['line_total']) }}</td></tr>
@endforeach
</tbody></table></div></section>

<section class="sec"><div class="sec__head"><h2 class="sec__title">Daily progress</h2></div><div class="sec__body">
@if ($staff && $open)
<details @if ($errors->has('report_date') || $errors->has('progress') || $errors->has('captions.*')) open @endif><summary class="btn btn--secondary btn--sm">Add daily report</summary>
<form method="POST" enctype="multipart/form-data" data-project-upload action="{{ route('admin.projects.report', $project) }}" class="mt-4">@csrf
<div class="form-row"><label class="label">Report date <input type="date" class="input" name="report_date" value="{{ old('report_date', today()->toDateString()) }}" required></label>
<label class="label">Reported by <input class="input" name="reported_by" maxlength="150" value="{{ old('reported_by') }}" required></label></div>
<label class="label">Work progress <textarea class="textarea" name="progress" required>{{ old('progress') }}</textarea></label>
<label class="label">Internal notes <textarea class="textarea" name="notes">{{ old('notes') }}</textarea></label>
<div data-photo-rows>
<div class="form-row"><label class="label">Work photo <input class="input" type="file" name="photos[0]" accept="image/jpeg,image/png,image/webp"></label><label class="label">Photo caption <input class="input" name="captions[0]" maxlength="255"></label></div>
</div>
<button type="button" class="btn btn--ghost btn--sm" data-add-photo>Add another photo</button>
<button class="btn btn--primary" data-loading-text="Saving report…">Save daily report</button><p role="status" aria-live="polite" data-upload-feedback></p>
</form></details>
@endif
@forelse ($reports as $report)
<article class="mt-4"><h3 class="sec__title">{{ $report->report_date->format('M j, Y') }}</h3>
@if ($staff)<p class="text-muted">Reported by {{ $report->reported_by }}</p>@endif
<p>{{ $report->progress }}</p>@if ($staff && $report->notes)<p>{{ $report->notes }}</p>@endif
@if ($report->files->isNotEmpty())<div class="photo-grid">
@foreach ($report->files as $file)
<div class="project-file-entry"><a class="photo-tile" href="{{ route($fileRoute, [$project, $file]) }}"><img class="photo-tile__img" loading="lazy" alt="{{ $file->caption }}" src="{{ route($fileRoute, [$project, $file, 'preview' => 1]) }}"><span class="photo-tile__label">{{ $file->caption }}</span></a>
@include('shared.project_file_remove')
</div>
@endforeach
</div>@endif
</article>
@empty<p class="text-muted">No daily reports have been recorded.</p>@endforelse
{{ $reports->links() }}
</div></section>

<section class="sec"><div class="sec__head"><h2 class="sec__title">Change Orders</h2></div><div class="sec__body">
@forelse ($orders as $order)
<article class="trade-group"><div class="trade-group__head"><strong>{{ $order->title }}</strong><span class="chip">{{ Str::headline($order->status) }}</span></div>
<div class="table-scroll"><table class="line-items"><thead><tr><th>Work item</th><th>Qty / unit</th><th class="num">Rate</th><th class="num">Total</th></tr></thead><tbody>
@foreach ($order->lines as $line)<tr><td>{{ $line['name'] }}
@if ($staff && $line['flags'])<p class="text-danger">{{ implode(' · ', $line['flags']) }}</p>@endif
@if ($staff && $line['override_reason'])<p class="text-muted">Original Library rate {{ \App\Services\Estimates\EstimateDisplay::currency($line['library_rate']) }} · {{ $line['override_reason'] }}</p>@endif
</td><td>{{ \App\Services\Estimates\EstimateDisplay::quantity($line['quantity']) }} {{ $line['unit'] }}</td><td class="num">{{ \App\Services\Estimates\EstimateDisplay::currency($line['rate']) }}</td><td class="num">{{ \App\Services\Estimates\EstimateDisplay::currency($line['line_total']) }}</td></tr>@endforeach
</tbody></table></div>
@if ($open && $staff && in_array($order->status, ['draft', 'pending'], true))
<div class="sec__body">
@if ($order->status === 'draft')<details><summary class="btn btn--secondary btn--sm">Edit draft</summary>@include('admin.pages.project_order_form', ['order' => $order])</details>@endif
<form method="POST" action="{{ route('admin.projects.orders.status', [$project, $order]) }}" class="mt-4">@csrf @method('PATCH')
<button name="status" value="{{ $order->status === 'draft' ? 'pending' : 'approved' }}" class="btn btn--primary btn--sm">{{ $order->status === 'draft' ? 'Submit for review' : 'Approve Change Order' }}</button>
<button name="status" value="rejected" class="btn btn--danger-outline btn--sm">Reject</button>
</form></div>@endif
</article>
@empty<p class="text-muted">{{ $staff ? 'No Change Orders recorded.' : 'No approved Change Orders.' }}</p>@endforelse
@if ($staff && $open)<details><summary class="btn btn--secondary btn--sm">New Change Order</summary>@include('admin.pages.project_order_form', ['order' => null])</details>@endif
</div></section>

@include('shared.project_billing')
<section class="sec"><div class="sec__head"><h2 class="sec__title">Documents</h2></div><div class="sec__body">
@forelse ($documents as $file)
<div class="project-file-entry"><p><a href="{{ route($fileRoute, [$project, $file]) }}">{{ $file->name }}</a> · {{ $file->mime }}@if ($file->caption) · {{ $file->caption }}@endif</p>
@include('shared.project_file_remove')
</div>
@empty<p class="text-muted">No project documents uploaded.</p>@endforelse
@if ($staff && $open)
<form method="POST" enctype="multipart/form-data" data-project-upload action="{{ route('admin.projects.document', $project) }}" class="mt-4">@csrf
<input type="hidden" name="submission_key" value="{{ old('submission_key', Str::uuid()) }}">
<label class="label">Supporting document (required) <input type="file" class="input" name="document" required></label>
<label class="label">Caption (optional) <input class="input" name="caption" maxlength="255" value="{{ old('caption') }}"></label>
<button class="btn btn--secondary" data-loading-text="Uploading…">Upload document</button><p role="status" aria-live="polite" data-upload-feedback></p>
</form>@endif
</div></section>
</div>
<aside class="detail-side">
@if ($staff)
<section class="sec"><div class="sec__head"><h2 class="sec__title">Customer details</h2></div>
<div class="sec__body"><p><a href="{{ route('admin.customers.show', $project->customer) }}">{{ $project->customer->name }}</a></p>
@if ($project->customer->company_name)<p>{{ $project->customer->company_name }}</p>@endif
<p>{{ $project->customer->email }}</p>
@if ($project->customer->phone)<p>{{ $project->customer->phone }}</p>@endif
</div></section>
@endif
<section class="sec"><div class="sec__head"><h2 class="sec__title">Project overview</h2></div><div class="sec__body">
@foreach (['Original estimate' => $original, 'Approved Change Orders' => $extras, 'Combined total' => $combined] as $label => $amount)
<div class="status-row"><span class="status-row__label">{{ $label }}</span><strong class="status-row__value">{{ \App\Services\Estimates\EstimateDisplay::currency($amount) }}</strong></div>
@endforeach
<p class="mt-4">Start: {{ $project->start_date?->format('M j, Y') ?? 'Not scheduled' }}</p>
<p>Target completion: {{ $project->estimated_completion_date?->format('M j, Y') ?? 'Not scheduled' }}</p>
@if ($project->walkthrough_date)<p>Walkthrough: {{ $project->walkthrough_date->format('M j, Y') }} · {{ Str::headline($project->walkthrough_status) }}</p>@endif
@if ($project->certificate_reference)<p>Certificate: {{ $project->certificate_reference }} · {{ $project->certificate_date?->format('M j, Y') }}</p>@endif
@if ($staff)<p>Estimator: {{ $project->estimator?->name ?? 'Unassigned' }}</p>@endif
</div></section>
@if ($staff) @include('admin.pages.project_management') @endif
</aside></div>
@else<p>No project selected. <a href="{{ route('projects') }}">Open Approved Projects</a></p>@endif




