@extends('front.layout.app')
@section('title', 'Dashboard · BidFreq™')


@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/estimate_request.css') }}">
@endpush

@section('content')

  <main class="app-content">

      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="customer-dashboard.html">Home</a>
        <span class="breadcrumbs__sep">/</span>
        <span class="breadcrumbs__current">Estimate Requests</span>
      </nav>

      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-header__title">Estimate Requests</h1>
          <p class="page-header__subtitle">
            Every request your organization has submitted, with live status through the
            AI-assisted and human-verified estimating workflow.
          </p>
        </div>
        <div class="page-header__actions">
          <button class="btn btn--secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export CSV
          </button>
          <a href="{{ route('new-estimate-request') }}" class="btn btn--primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Estimate Request
          </a>
        </div>
      </header>

      <!-- ============================================================
           STATUS STRIP
           ============================================================ -->
      <section class="status-strip" aria-label="Filter by status">
        <button class="status-tile is-active" data-status="all">
          <span class="status-tile__dot" style="background:var(--navy-800)"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">All requests</div>
            <div class="status-tile__value">12</div>
          </div>
        </button>
        <button class="status-tile" data-status="ai-processing">
          <span class="status-tile__dot dot--ai"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">AI processing</div>
            <div class="status-tile__value">1</div>
          </div>
        </button>
        <button class="status-tile" data-status="in-review">
          <span class="status-tile__dot dot--review"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">In review</div>
            <div class="status-tile__value">4</div>
          </div>
        </button>
        <!-- <button class="status-tile" data-status="awaiting-customer">
          <span class="status-tile__dot dot--awaiting"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">Awaiting you</div>
            <div class="status-tile__value">2</div>
          </div>
        </button>
        <button class="status-tile" data-status="revision">
          <span class="status-tile__dot dot--revision"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">Revision</div>
            <div class="status-tile__value">1</div>
          </div>
        </button> -->
        <button class="status-tile" data-status="approved">
          <span class="status-tile__dot dot--approved"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">Approved</div>
            <div class="status-tile__value">3</div>
          </div>
        </button>
        <button class="status-tile" data-status="declined">
          <span class="status-tile__dot dot--declined"></span>
          <div class="status-tile__body">
            <div class="status-tile__label">Declined</div>
            <div class="status-tile__value">1</div>
          </div>
        </button>
      </section>

      <!-- ============================================================
           FILTER BAR
           ============================================================ -->
      <div class="filter-bar">
        <div class="filter-bar__search">
          <div class="input-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input class="input" type="search" id="tableSearch" placeholder="Search by ID, address, ZIP, or description…">
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
          <span class="filter-bar__label">Date</span>
          <select id="dateFilter">
            <option value="">Any time</option>
            <option>Today</option>
            <option>Last 7 days</option>
            <option>Last 30 days</option>
            <option>Last 90 days</option>
          </select>
        </div>

        <div class="filter-bar__group">
          <span class="filter-bar__label">Sort</span>
          <select id="sortFilter">
            <option value="newest">Newest first</option>
            <option value="oldest">Oldest first</option>
            <option value="amount-high">Amount (high → low)</option>
            <option value="amount-low">Amount (low → high)</option>
          </select>
        </div>

        <button class="btn btn--ghost btn--sm" id="clearFilters">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          Clear
        </button>
      </div>

      <!-- Active filter chips -->
      <div class="filter-chips" id="filterChips"></div>

      <!-- ============================================================
           BULK ACTION BAR
           ============================================================ -->
      <div class="bulk-bar" id="bulkBar">
        <div class="bulk-bar__left">
          <span class="bulk-bar__count" id="bulkCount">0</span>
          <span>selected</span>
        </div>
        <div class="bulk-bar__actions">
          <button class="btn btn--secondary btn--sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download PDFs
          </button>
          <button class="btn btn--secondary btn--sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/></svg>
            Email
          </button>
          <button class="btn btn--ghost btn--sm" id="clearSelection">Cancel</button>
        </div>
      </div>

      <!-- ============================================================
           TABLE
           ============================================================ -->
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
                <th class="sortable" data-sort="id">Estimate ID <span class="sort-ind">↓</span></th>
                <th>Property</th>
                <th>Trade</th>
                <th>Status</th>
                <th class="num sortable" data-sort="amount">Amount</th>
                <th>Updated</th>
                <th class="actions">Actions</th>
              </tr>
            </thead>
            <tbody id="requestsBody"></tbody>
          </table>
        </div>

        <!-- Empty state (hidden by default) -->
        <div class="empty hidden" id="emptyState">
          <div class="empty__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </div>
          <div class="empty__title">No matching requests</div>
          <p class="empty__text">Try adjusting your search or filters, or start a new estimate request.</p>
          <button class="btn btn--secondary" id="clearEmptyFilters">Clear filters</button>
        </div>

        <!-- Pagination -->
        <div class="pagination" id="pagination">
          <span class="pagination__info" id="paginationInfo">Showing 1–8 of 12 requests</span>
          <div class="pagination__controls">
            <button class="pagination__btn" aria-label="Previous page" disabled>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <button class="pagination__btn is-active">1</button>
            <button class="pagination__btn">2</button>
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
        window.estimateDetailUrl = @json(
            route('estimate_request_detail', ['id' => '__ID__'])
        );
    </script>

    <script src="{{ asset('front_assets/pages_js/estimate_request.js') }}"></script>


@endpush
