@extends('admin.layout.app')
@section('title', 'Daily Reports · BidFreq')
@section('content')
<main class="app-content">
    <header class="page-header"><div class="page-header__text"><h1 class="page-header__title">Daily Reports</h1></div></header>
<form class="filter-bar" method="GET">
<label class="label">Search <input class="input" name="search" type="search" value="{{ request('search') }}"></label>

<label class="label">Sort <select class="select" name="sort"><option value="">Default</option><option value="newest" @selected(request('sort') === 'newest')>Newest first</option><option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option></select></label>
<label class="label">Report date <input class="input" type="date" name="date" value="{{ request('date') }}"></label>
<button class="btn btn--primary btn--sm">Apply filters</button>
</form>
<div class="table-wrap"><div class="table-scroll"><table class="table">
<thead><tr><th scope="col">Date</th><th scope="col">Project</th><th scope="col">Reported by</th><th scope="col">Progress</th></tr></thead>
<tbody>@forelse ($reports as $row)<tr><td>{{ $row->report_date->format('M j, Y') }}</td><td><a href="{{ route('admin.projects.show', $row->project) }}">{{ $row->project->address }}</a></td><td>{{ $row->reported_by }}</td><td>{{ Str::limit($row->progress, 120) }}</td></tr>@empty<tr><td colspan="4">No daily reports found.</td></tr>@endforelse</tbody></table></div></div>
{{ $reports->links() }}
</main>
@endsection
