@extends('front.layout.app')
@section('title', 'Estimate Requests · BidFreq')
@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/estimate_request.css') }}">
@endpush
@section('content')
<main class="app-content estimate-list-page">
    <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('dashboard') }}">Home</a><span class="breadcrumbs__sep">/</span><span>Estimate Requests</span></nav>
    <header class="page-header">
        <div class="page-header__text"><h1 class="page-header__title">Estimate Requests</h1>
            <p class="page-header__subtitle">Track your repair requests and review released estimates.</p></div>
        <div class="page-header__actions"><a class="btn btn--primary" href="{{ route('new-estimate-request') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>New Estimate Request</a></div>
    </header>
    <section class="status-strip" aria-label="Filter by status">
        @foreach (['' => 'All requests', 'under_review' => 'Under Review', 'needs_info' => 'More Information Needed', 'declined' => 'Declined', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'approved' => 'Estimate Ready', 'revision_requested' => 'Revision Requested', 'customer_approved' => 'Approved', 'customer_rejected' => 'Rejected'] as $status => $label)
            @php($count = match ($status) { '' => $counts->sum(), 'under_review' => $counts->only(['draft','ai_pending','ai_retrying','ai_failed','under_review'])->sum(), default => $counts->get($status, 0) })
            <a href="{{ route('estimate-request', array_replace($filters, ['status' => $status ?: null])) }}" class="status-tile @if (($filters['status'] ?? '') === $status) is-active @endif">
                <span class="status-tile__dot {{ match ($status) { 'ai_pending','ai_retrying' => 'dot--ai', 'approved' => 'dot--awaiting', 'revision_requested','needs_info' => 'dot--revision', 'customer_approved','completed' => 'dot--approved', 'declined','ai_failed','customer_rejected' => 'dot--declined', default => 'dot--review' } }}"></span>
                <div class="status-tile__body"><div class="status-tile__label">{{ $label }}</div><div class="status-tile__value">{{ $count }}</div></div>
            </a>
        @endforeach
    </section>
    <form class="filter-bar" method="GET" action="{{ route('estimate-request') }}" id="estimateFilters">
    <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
    <div class="filter-bar__search"><div class="input-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input class="input" type="search" id="tableSearch" name="search" maxlength="100" aria-label="Search estimates" placeholder="Search by ID, address, ZIP, or description…" value="{{ $filters['search'] ?? '' }}">
    </div></div>
    <div class="filter-bar__group"><label class="filter-bar__label" for="tradeFilter">Trade</label>
        <select id="tradeFilter" name="trade"><option value="">All trades</option>
            @foreach (\App\Models\RepairRequest::TRADES as $trade)
                <option value="{{ $trade }}" @selected(($filters['trade'] ?? '') === $trade)>{{ $trade }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-bar__group"><label class="filter-bar__label" for="dateFilter">Date</label>
        <select id="dateFilter" name="date"><option value="">Any time</option>
            @foreach (['today' => 'Today', '7' => 'Last 7 days', '30' => 'Last 30 days', '90' => 'Last 90 days'] as $key => $label)
                <option value="{{ $key }}" @selected(($filters['date'] ?? '') === (string) $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-bar__group"><label class="filter-bar__label" for="sortFilter">Sort</label>
        <select id="sortFilter" name="sort">
            @foreach (['newest' => 'Newest first', 'oldest' => 'Oldest first', 'amount-high' => 'Amount (high → low)', 'amount-low' => 'Amount (low → high)'] as $key => $label)
                <option value="{{ $key }}" @selected(($filters['sort'] ?? 'newest') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn--primary btn--sm" type="submit" data-loading-text="Filtering…">Apply filters</button>
    <a class="btn btn--ghost btn--sm" id="clearFilters" href="{{ route('estimate-request') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Clear
    </a>
</form>
<div class="filter-chips" id="filterChips">
    @foreach (array_filter($filters) as $field => $value)
        @php($chip = in_array($field, ['status', 'sort'], true) ? ($field === 'status' ? (['under_review' => 'Under Review', 'needs_info' => 'More Information Needed', 'declined' => 'Declined', 'approved' => 'Estimate Ready', 'revision_requested' => 'Revision Requested', 'customer_rejected' => 'Rejected', 'customer_approved' => 'Approved', 'in_progress' => 'In Progress', 'completed' => 'Completed'][$value] ?? 'Under Review') : Str::headline($value)) : $value)
        <span class="filter-chip">{{ $chip }} <a href="{{ route('estimate-request', \Illuminate\Support\Arr::except($filters, [$field])) }}" aria-label="Remove {{ $field }} filter">×</a></span>
    @endforeach
</div>
    <div class="table-wrap"><div class="table-scroll">
        <table class="table" id="requestsTable">
            <thead><tr><th scope="col">Estimate ID</th><th scope="col">Property</th><th scope="col">Trade</th><th scope="col">Status</th><th scope="col">Priority</th><th scope="col" class="num">Amount</th><th scope="col">Updated</th><th scope="col" class="actions">Actions</th></tr></thead>
            <tbody id="requestsBody">
            @forelse ($requests as $repair)
                <tr data-estimate-row>
                    <td><div class="est-id-cell">
                        <span class="est-id-cell__icon est-id-cell__icon--blue" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </span>
                        <div class="est-id-cell__text">
                            <div class="est-id-cell__id"><a data-estimate-link title="{{ $repair->number() }}" href="{{ route('repair-requests.show', $repair) }}">{{ $repair->number() }}</a></div>
                            <div class="est-id-cell__time">{{ $repair->files_count }} photos</div>
                        </div>
                    </div></td>
                    <td><div class="property-cell">
                        <div class="property-cell__addr">{{ $repair->property_name ?: $repair->street_address }}</div>
                        <div class="property-cell__meta"><span>{{ $repair->city }}, {{ $repair->state }}</span><span class="dot"></span><span class="text-mono">{{ $repair->zip }}</span></div>
                    </div></td>

                    <td>{{ implode(', ', array_column($repair->descriptions, 'trade')) }}</td>
                    <td><span class="chip {{ $repair->statusClass(false) }}"><span class="chip__dot"></span>{{ $repair->customerStatus() }}</span></td>
                    <td>{{ ucfirst($repair->priority) }}</td>
                    <td class="num">@if ($repair->currentRelease)${{ $repair->currentRelease->snapshot['total'] }}@else — @endif
                        </td>
                    <td>{{ $repair->updated_at->format('M j, Y') }}</td>
                    <td class="actions"><a class="btn btn--secondary btn--sm" href="{{ route('repair-requests.show', $repair) }}">View</a></td>
                </tr>
            @empty
                <tr><td colspan="8">No estimate requests match these filters.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
    @if ($requests->hasPages() || $requests->total())
    <div class="pagination" id="pagination">
        <span class="pagination__info" id="paginationInfo">Showing {{ $requests->firstItem() ?? 0 }}–{{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }} requests</span>
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
    <script src="{{ asset('front_assets/pages_js/dashboard.js') }}"></script>
    <script src="{{ asset('front_assets/pages_js/estimate-list-navigation.js') }}" defer></script>
@endpush
