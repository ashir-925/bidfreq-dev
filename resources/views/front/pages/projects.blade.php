@extends('front.layout.app')
@section('title', 'Approved Projects · BidFreq')
@section('content')
<main class="app-content">
    <header class="page-header"><div class="page-header__text"><h1 class="page-header__title">Approved Projects</h1></div></header>
@push('styles')<link rel="stylesheet" href="{{ asset('front_assets/pages_css/estimate_request.css') }}">@endpush
<div class="status-strip">@foreach (\App\Models\Project::STATUSES as $status)
<a class="status-tile {{ request('status') === $status ? 'is-active' : '' }}" href="{{ request()->url() }}?{{ http_build_query(request()->except('page', 'status') + ['status' => $status]) }}"><span class="status-tile__body"><span class="status-tile__label">{{ Str::headline($status) }}</span><strong class="status-tile__value">{{ $counts->get($status, 0) }}</strong></span></a>
@endforeach</div>
<form class="filter-bar" method="GET">
<label class="label">Search <input class="input" name="search" type="search" value="{{ request('search') }}"></label>
<label class="label">Status <select class="select" name="status"><option value="">All statuses</option>@foreach (\App\Models\Project::STATUSES as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ Str::headline($status) }}</option>@endforeach</select></label>
<label class="label">Sort <select class="select" name="sort"><option value="">Default</option><option value="newest" @selected(request('sort') === 'newest')>Newest first</option><option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option></select></label>

<button class="btn btn--primary btn--sm">Apply filters</button>
</form>
<div class="table-wrap"><div class="table-scroll"><table class="table">
<thead><tr><th scope="col">Property</th><th scope="col">Status</th><th scope="col">Start</th><th scope="col">Target completion</th><th scope="col"></th></tr></thead>
<tbody>@forelse ($projects as $item)
<tr><td>{{ $item->address }}</td><td><span class="chip">{{ Str::headline($item->status) }}</span></td><td>{{ $item->start_date?->format('M j, Y') ?? 'Not scheduled' }}</td><td>{{ $item->estimated_completion_date?->format('M j, Y') ?? 'Not scheduled' }}</td><td><a class="btn btn--secondary btn--sm" href="{{ route($staff ? 'admin.projects.show' : 'project-detail', $item) }}">View project</a></td></tr>
@empty<tr><td colspan="5">No projects match these filters.</td></tr>@endforelse</tbody></table></div></div>
{{ $projects->links() }}
</main>
@endsection
