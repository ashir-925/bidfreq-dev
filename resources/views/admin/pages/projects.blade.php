@extends('admin.layout.app')
@section('title', 'Estimator Console · BidFreq™')


@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('admin_assets/pages_css/dashboard.css') }}"> --}}
@endpush

@section('content')
     <main class="app-content">

      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Console</a>
        <span class="breadcrumbs__sep">/</span>
        <span class="breadcrumbs__current">Projects</span>
      </nav>

      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-header__title">Projects</h1>
          <p class="page-header__subtitle">
            All approved projects across every customer organization.
          </p>
        </div>
        <div class="page-header__actions">
          <button class="btn btn--secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export CSV
          </button>
        </div>
      </header>

      <section class="quick-stats" aria-label="Project metrics">
        <div class="quick-stat"><span class="quick-stat__label">Total</span><span class="quick-stat__value">218</span></div>
        <div class="quick-stat"><span class="quick-stat__label">Active</span><span class="quick-stat__value quick-stat__value--warning">42</span></div>
        <div class="quick-stat"><span class="quick-stat__label">Completed</span><span class="quick-stat__value quick-stat__value--emerald">171</span></div>
        <div class="quick-stat"><span class="quick-stat__label">Total Value</span><span class="quick-stat__value">$1.4M</span></div>
        <div class="quick-stat"><span class="quick-stat__label">Avg. Value</span><span class="quick-stat__value">$6.4K</span></div>
        <div class="quick-stat"><span class="quick-stat__label">On-time</span><span class="quick-stat__value quick-stat__value--emerald">94%</span></div>
      </section>

      <div class="filter-bar">
        <div class="filter-bar__search">
          <div class="input-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input class="input" type="search" id="projSearch" placeholder="Search by project ID, address, customer…">
          </div>
        </div>
        <div class="filter-bar__group">
          <span class="filter-bar__label">Status</span>
          <select id="projStatus">
            <option value="">All</option>
            <option>Active</option>
            <option>Completed</option>
          </select>
        </div>
        <div class="filter-bar__group">
          <span class="filter-bar__label">Trade</span>
          <select id="projTrade">
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
          <span class="filter-bar__label">Sort</span>
          <select id="projSort">
            <option value="newest">Newest first</option>
            <option value="amount-high">Value (high → low)</option>
            <option value="progress">Progress</option>
          </select>
        </div>
      </div>

      <div class="table-wrap">
        <div class="table-scroll">
          <table class="table">
            <thead>
              <tr>
                <th>Project</th>
                <th>Customer</th>
                <th>Property</th>
                <th>Trade</th>
                <th>Status</th>
                <th style="width:160px">Progress</th>
                <th class="num">Value</th>
                <th>Started</th>
              </tr>
            </thead>
            <tbody id="projBody"></tbody>
          </table>
        </div>
        <div class="empty hidden" id="emptyState">
          <div class="empty__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
          <div class="empty__title">No projects match</div>
          <p class="empty__text">Try adjusting filters.</p>
        </div>
        <div class="pagination" id="pagination">
          <span class="pagination__info" id="paginationInfo">Showing 1–10 of 218 projects</span>
          <div class="pagination__controls">
            <button class="pagination__btn" disabled><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
            <button class="pagination__btn is-active">1</button>
            <button class="pagination__btn">2</button>
            <button class="pagination__btn">3</button>
            <button class="pagination__btn"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
          </div>
        </div>
      </div>
    </main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/projects.js') }}"></script>
@endpush
