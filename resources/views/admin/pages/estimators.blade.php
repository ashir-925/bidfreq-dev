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
        <span class="breadcrumbs__current">Estimators</span>
      </nav>

      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-header__title">Estimator Team</h1>
          <p class="page-header__subtitle">
            Manage your estimating team, monitor workload balance, and review verification quality.
          </p>
        </div>
        <div class="page-header__actions">
          <button class="btn btn--secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export
          </button>
          <button class="btn btn--primary" id="inviteEstimatorBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Invite estimator
          </button>
        </div>
      </header>

      <!-- KPIs -->
      <section class="quick-stats" aria-label="Team metrics">
        <div class="quick-stat"><span class="quick-stat__label">Team Size</span><span class="quick-stat__value">6</span></div>
        <div class="quick-stat"><span class="quick-stat__label">Online Now</span><span class="quick-stat__value quick-stat__value--emerald">4</span></div>
        <div class="quick-stat"><span class="quick-stat__label">Open Assignments</span><span class="quick-stat__value quick-stat__value--violet">17</span></div>
        <div class="quick-stat"><span class="quick-stat__label">Avg. Turnaround</span><span class="quick-stat__value">1.8<span style="font-size:12px;color:var(--text-muted);font-weight:500"> days</span></span></div>
        <div class="quick-stat"><span class="quick-stat__label">Verified Today</span><span class="quick-stat__value quick-stat__value--emerald">14</span></div>
        <div class="quick-stat"><span class="quick-stat__label">Team Quality</span><span class="quick-stat__value">94%</span></div>
      </section>

      <!-- Estimator cards -->
      <section class="est-grid" id="estimatorGrid"></section>

    </main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/estimators.js') }}"></script>
@endpush
