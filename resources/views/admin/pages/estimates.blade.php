@extends('admin.layout.app')
@section('title', 'Estimate Queue · BidFreq')

@section('content')
<main class="app-content estimate-list-page">
    <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('admin.dashboard') }}">Console</a><span class="breadcrumbs__sep">/</span><span>Estimate Queue</span></nav>
    <header class="page-header">
        <div class="page-header__text"><h1 class="page-header__title">Estimate Queue</h1>
            <p class="page-header__subtitle">Review and verify submitted estimates in priority order.</p></div>

    </header>
    <section class="status-strip" aria-label="Filter by status">
        @foreach (['' => 'All estimates', 'ai_pending' => 'AI drafting', 'ai_retrying' => 'AI retrying', 'draft' => 'Awaiting verification', 'ai_failed' => 'AI failed', 'needs_info' => 'Needs more information', 'declined' => 'Declined', 'under_review' => 'In review', 'approved' => 'Awaiting customer', 'revision_requested' => 'Revision', 'customer_approved' => 'Approved', 'customer_rejected' => 'Rejected'] as $status => $label)
            @php($count = match ($status) { '' => $counts->sum(), 'under_review' => $counts->only(['draft','ai_pending','ai_retrying','ai_failed'])->sum(), default => $counts->get($status, 0) })
            <a href="{{ route('admin.estimates', array_replace($filters, ['status' => $status ?: null])) }}" class="status-tile @if (($filters['status'] ?? '') === $status) is-active @endif">
                <span class="status-tile__dot {{ match ($status) { 'ai_pending','ai_retrying' => 'dot--ai', 'approved' => 'dot--awaiting', 'revision_requested','needs_info' => 'dot--revision', 'customer_approved','completed' => 'dot--approved', 'declined','ai_failed','customer_rejected' => 'dot--declined', default => 'dot--review' } }}"></span>
                <div class="status-tile__body"><div class="status-tile__label">{{ $label }}</div><div class="status-tile__value">{{ $count }}</div></div>
            </a>
        @endforeach
    </section>
    <form class="filter-bar" method="GET" action="{{ route('admin.estimates') }}" id="estimateFilters">
    <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
    <div class="filter-bar__search"><div class="input-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input class="input" type="search" id="tableSearch" name="property" maxlength="100" aria-label="Search estimates" placeholder="Search by ID, address, customer, ZIP…" value="{{ $filters['property'] ?? '' }}">
    </div></div>
    <div class="filter-bar__group"><label class="filter-bar__label" for="tradeFilter">Trade</label>
        <select id="tradeFilter" name="trade"><option value="">All trades</option>
            @foreach (\App\Models\RepairRequest::TRADES as $trade)
                <option value="{{ $trade }}" @selected(($filters['trade'] ?? '') === $trade)>{{ $trade }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-bar__group"><label class="filter-bar__label" for="priorityFilter">Priority</label>
        <select id="priorityFilter" name="priority"><option value="">Any priority</option>
            @foreach (\App\Models\RepairRequest::PRIORITIES as $priority)
                <option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>{{ ucfirst($priority) }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-bar__group"><label class="filter-bar__label" for="dateFilter">Date</label>
        <input class="input" type="date" id="dateFilter" name="date" value="{{ $filters['date'] ?? '' }}">
    </div>
    <div class="filter-bar__group"><label class="filter-bar__label" for="sortFilter">Sort</label>
        <select id="sortFilter" name="sort">
            @foreach (['sla' => 'Priority first', 'newest' => 'Newest first', 'oldest' => 'Oldest first', 'amount-high' => 'Amount (high → low)'] as $key => $label)
                <option value="{{ $key }}" @selected(($filters['sort'] ?? 'sla') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn--primary btn--sm" type="submit" data-loading-text="Filtering…">Apply filters</button>
    <a class="btn btn--ghost btn--sm" id="clearFilters" href="{{ route('admin.estimates') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Clear
    </a>
</form>
<div class="filter-chips" id="filterChips">
    @foreach (array_filter($filters) as $field => $value)
        @php($chip = in_array($field, ['status', 'sort'], true) ? Str::headline($value) : $value)
        <span class="filter-chip">{{ $chip }} <a href="{{ route('admin.estimates', \Illuminate\Support\Arr::except($filters, [$field])) }}" aria-label="Remove {{ $field }} filter">×</a></span>
    @endforeach
</div>
    <div class="table-wrap"><div class="table-scroll">
        <table class="table" id="requestsTable">
            <thead><tr><th scope="col">Estimate ID</th><th scope="col">Property</th><th scope="col">Customer</th><th scope="col">Trade</th><th scope="col">Status</th><th scope="col">Priority</th><th scope="col" class="num">Estimate amount</th><th scope="col" class="num">NTE</th><th scope="col">Submitted</th><th scope="col" class="actions">Actions</th></tr></thead>
            <tbody id="requestsBody">
            @forelse ($requests as $repair)
                <tr data-estimate-row>
                    <td><div class="est-id-cell">
                        <span class="est-id-cell__icon est-id-cell__icon--blue" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </span>
                        <div class="est-id-cell__text">
                            <div class="est-id-cell__id"><a data-estimate-link title="{{ $repair->number() }}" href="{{ route('admin.estimate-review', $repair) }}">{{ $repair->number() }}</a></div>
                            <div class="est-id-cell__time">{{ ucfirst($repair->priority) }} priority</div>
                        </div>
                    </div></td>
                    <td><div class="property-cell">
                        <div class="property-cell__addr">{{ $repair->property_name ?: $repair->street_address }}</div>
                        <div class="property-cell__meta"><span>{{ $repair->city }}, {{ $repair->state }}</span><span class="dot"></span><span class="text-mono">{{ $repair->zip }}</span></div>
                    </div></td>
                    <td>{{ $repair->customer->company_name ?: $repair->customer->name }}</td>
                    <td>{{ implode(', ', array_column($repair->descriptions, 'trade')) }}</td>
                    <td><span class="chip {{ $repair->statusClass(true) }}"><span class="chip__dot"></span>{{ Str::headline($repair->status) }}</span></td>
                    <td>{{ ucfirst($repair->priority) }}</td>
                    <td class="num">@if ($repair->draft)${{ $repair->draft->total }}@else — @endif
                        @if ($repair->draft?->over_nte)<span class="badge badge--danger">Over NTE</span>@endif
                        @if ($repair->draft?->lines->contains(fn ($line) => $line->flags !== []))<span class="badge badge--blue">Review flags</span>@endif</td>
                    <td class="num">{{ $repair->nte === null ? '—' : '$'.$repair->nte }}</td>
                    <td>{{ $repair->created_at->format('M j, Y H:i') }}</td>
                    <td class="actions">
                        @if ($repair->status === 'needs_info')<span class="badge badge--blue">Needs more information</span>@endif
                        @if ($repair->status === 'ai_failed')<span class="badge badge--danger">AI failed</span>@endif
                        <a class="btn btn--secondary btn--sm" href="{{ route('admin.estimate-review', $repair) }}">Review</a></td>
                </tr>
            @empty
                <tr><td colspan="10">No estimate requests match these filters.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
    @if ($requests->hasPages() || $requests->total())
    <div class="pagination" id="pagination">
        <span class="pagination__info" id="paginationInfo">Showing {{ $requests->firstItem() ?? 0 }}–{{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }} estimates</span>
        <nav class="pagination__controls" aria-label="Pagination">
            @if ($requests->previousPageUrl())
                <a href="{{ $requests->previousPageUrl() }}" class="pagination__btn" aria-label="Previous page">‹</a>
            @else
                <button class="pagination__btn" type="button" aria-label="Previous page" disabled>‹</button>
            @endif
            @foreach ($requests->getUrlRange(max(1,$requests->currentPage()-2), min($requests->lastPage(),$requests->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="pagination__btn @if ($page === $requests->currentPage()) is-active @endif" @if ($page === $requests->currentPage()) aria-current="page" @endif>{{ $page }}</a>
            @endforeach
            @if ($requests->nextPageUrl())
                <a href="{{ $requests->nextPageUrl() }}" class="pagination__btn" aria-label="Next page">›</a>
            @else
                <button class="pagination__btn" type="button" aria-label="Next page" disabled>›</button>
            @endif
        </nav>
    </div>
@endif
</main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
    <script src="{{ asset('front_assets/pages_js/estimate-list-navigation.js') }}" defer></script>
@endpush
