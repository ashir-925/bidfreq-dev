@extends('admin.layout.app')
@section('title', 'Estimate Queue · BidFreq™')


@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('admin_assets/pages_css/dashboard.css') }}"> --}}
@endpush

@section('content')
     <main class="app-content">

      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Console</a>
        <span class="breadcrumbs__sep">/</span>
        <span class="breadcrumbs__current">Estimate Queue</span>
      </nav>

      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-header__title">Estimate Queue</h1>
          <p class="page-header__subtitle">
            Every estimate in the BidFreq pipeline. Filter by status, trade, priority, or estimator
            to keep verification moving and SLAs on track.
          </p>
        </div>
        <div class="page-header__actions">
          <button class="btn btn--secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export CSV
          </button>
          <button class="btn btn--primary" id="assignNextBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Assign next to me
          </button>
        </div>
      </header>

      <!-- STATUS STRIP -->
      <section class="status-strip" aria-label="Filter by status">
        <button class="status-tile is-active" data-status="queue">
          <span class="status-tile__dot" style="background:var(--navy-800)"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">Awaiting verification</div>
            <div class="status-tile__value">8</div>
          </div>
        </button>
        <button class="status-tile" data-status="ai-processing">
          <span class="status-tile__dot dot--ai"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">AI drafting</div>
            <div class="status-tile__value">4</div>
          </div>
        </button>
        <button class="status-tile" data-status="in-review">
          <span class="status-tile__dot dot--review"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">In review</div>
            <div class="status-tile__value">6</div>
          </div>
        </button>
        <button class="status-tile" data-status="awaiting-customer">
          <span class="status-tile__dot dot--awaiting"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">Awaiting customer</div>
            <div class="status-tile__value">5</div>
          </div>
        </button>
        <button class="status-tile" data-status="revision">
          <span class="status-tile__dot dot--revision"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">Revision</div>
            <div class="status-tile__value">2</div>
          </div>
        </button>
        <button class="status-tile" data-status="approved">
          <span class="status-tile__dot dot--approved"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">Approved</div>
            <div class="status-tile__value">94</div>
          </div>
        </button>
        <button class="status-tile" data-status="all">
          <span class="status-tile__dot" style="background:var(--n-400)"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">All estimates</div>
            <div class="status-tile__value">186</div>
          </div>
        </button>
      </section>

      <!-- FILTER BAR -->
      <div class="filter-bar">
        <div class="filter-bar__search">
          <div class="input-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input class="input" type="search" id="tableSearch" placeholder="Search by ID, address, customer, ZIP…">
          </div>
        </div>

        <div class="filter-bar__group">
          <span class="filter-bar__label">Trade</span>
          <select id="tradeFilter">
            <option value="">All trades</option>
            <option>General Repair</option>
            <option>Electrical</option>
            <option>Plumbing</option>
            <option>HVAC</option>
            <option>Roofing</option>
            <option>Flooring</option>
            <option>Drywall / Paint</option>
          </select>
        </div>

        <div class="filter-bar__group">
          <span class="filter-bar__label">Priority</span>
          <select id="priorityFilter">
            <option value="">Any priority</option>
            <option value="high">High</option>
            <option value="med">Medium</option>
            <option value="low">Low</option>
          </select>
        </div>

        <div class="filter-bar__group">
          <span class="filter-bar__label">Estimator</span>
          <select id="estimatorFilter">
            <option value="">Anyone</option>
            <option>B. Hayes</option>
            <option>M. Alvarez</option>
            <option>R. Kapoor</option>
            <option>T. Nakamura</option>
            <option value="unassigned">Unassigned</option>
          </select>
        </div>

        <div class="filter-bar__group">
          <span class="filter-bar__label">Sort</span>
          <select id="sortFilter">
            <option value="sla">SLA (urgent first)</option>
            <option value="newest">Newest first</option>
            <option value="oldest">Oldest first</option>
            <option value="amount-high">Amount (high → low)</option>
          </select>
        </div>

        <button class="btn btn--ghost btn--sm" id="clearFilters">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          Clear
        </button>
      </div>

      <!-- BULK BAR -->
      <div class="bulk-bar" id="bulkBar">
        <div class="bulk-bar__left">
          <span class="bulk-bar__count" id="bulkCount">0</span>
          <span>selected</span>
        </div>
        <div class="bulk-bar__actions">
          <button class="btn btn--secondary btn--sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Assign to…
          </button>
          <button class="btn btn--secondary btn--sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export
          </button>
          <button class="btn btn--ghost btn--sm" id="clearSelection">Cancel</button>
        </div>
      </div>

      <!-- TABLE -->
      <div class="table-wrap">
        <div class="table-scroll">
          <table class="table" id="requestsTable">
            <thead>
              <tr>
                <th class="table__checkbox">
                  <label class="check" style="margin:0">
                    <input type="checkbox" id="selectAll">
                    <span class="check__box">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                  </label>
                </th>
                <th>Estimate</th>
                <th>Property</th>
                <th>Customer</th>
                <th>Trade</th>
                <th>Status</th>
                <th>Estimator</th>
                <th class="num">Amount</th>
                <th>SLA</th>
                <th class="actions">Actions</th>
              </tr>
            </thead>
            <tbody id="requestsBody"></tbody>
          </table>
        </div>

        <div class="empty hidden" id="emptyState">
          <div class="empty__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </div>
          <div class="empty__title">No estimates match</div>
          <p class="empty__text">Try adjusting your filters or clear them to see the full queue.</p>
          <button class="btn btn--secondary" id="clearEmptyFilters">Clear filters</button>
        </div>

        <div class="pagination" id="pagination">
          <span class="pagination__info" id="paginationInfo">Showing 1–10 of 25 estimates</span>
          <div class="pagination__controls">
            <button class="pagination__btn" aria-label="Previous page" disabled>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <button class="pagination__btn is-active">1</button>
            <button class="pagination__btn">2</button>
            <button class="pagination__btn">3</button>
            <button class="pagination__btn" aria-label="Next page">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
        </div>
      </div>

    </main>
@endsection
@push('scripts')
<script>
  
        window.estimateReviewUrl = @json(
            route('admin.estimate-review', ['id' => '__ID__'])
        );
    
</script>
    <script src="{{ asset('admin_assets/pages_js/estimates.js') }}"></script>
@endpush
