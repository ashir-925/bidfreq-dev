@extends('admin.layout.app')
@section('title', 'Request inbox · BidFreq')
@section('content')
<main class="app-content">

<header class="page-header"><div class="page-header__text"><h1 class="page-header__title">Request inbox</h1></div></header>
<form action="{{ route('admin.repair-requests.index') }}" method="GET" class="filter-bar">
    @foreach (['priority' => \App\Models\RepairRequest::PRIORITIES, 'status' => ['ai_pending', 'ai_retrying', 'ai_failed', 'draft']] as $field => $options)
        <div class="filter-bar__group">
            <label for="filter-{{ $field }}" class="filter-bar__label">{{ ucfirst($field) }}</label>
            <select id="filter-{{ $field }}" name="{{ $field }}"><option value="">All</option>
                @foreach ($options as $value)<option value="{{ $value }}" @selected(($filters[$field] ?? null) === $value)>{{ Str::headline($value) }}</option>@endforeach
            </select>
        </div>
    @endforeach
    <div class="filter-bar__group"><label class="filter-bar__label" for="filter-property">Property</label>
        <input class="input" type="search" id="filter-property" name="property" value="{{ $filters['property'] ?? '' }}"></div>
    <div class="filter-bar__group"><label class="filter-bar__label" for="filter-date">Submitted date</label>
        <input class="input" type="date" id="filter-date" name="date" value="{{ $filters['date'] ?? '' }}"></div>
    <button class="btn btn--primary btn--sm" type="submit" data-loading-text="Filtering…">Filter</button>
    <a class="btn btn--ghost btn--sm" href="{{ route('admin.repair-requests.index') }}">Clear</a>
</form>
<div class="table-wrap"><div class="table-scroll"><table class="table">
    <thead><tr><th scope="col">Request</th><th scope="col">Property</th><th scope="col">Priority</th><th scope="col">Status</th><th scope="col">Flags</th></tr></thead>
    <tbody>
    @forelse ($requests as $repair)
        <tr><td><a href="{{ route('admin.repair-requests.show', $repair) }}">{{ $repair->number() }}</a></td>
            <td>{{ $repair->property_name ?: $repair->street_address }}</td><td>{{ ucfirst($repair->priority) }}</td>
            <td>{{ Str::headline($repair->status) }}</td><td>
                @if ($repair->draft?->over_nte)<span class="badge badge--danger">Over NTE</span>@endif
                @if ($repair->draft?->lines->contains(fn ($line) => $line->flags !== []))<span class="badge badge--blue">Review flags</span>@endif
                @if ($repair->status === 'ai_failed')<span class="badge badge--danger">AI failed</span>@endif
            </td></tr>
    @empty<tr><td colspan="5">No requests match these filters.</td></tr>@endforelse</tbody>
</table></div></div>
@include('admin.library.pagination', ['pages' => $requests])
</main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
    
@endpush
