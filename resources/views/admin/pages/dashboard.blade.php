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
        <span class="breadcrumbs__current">Dashboard</span>
      </nav>

      <!-- HERO -->
      <section class="admin-hero">
        <div class="admin-hero__text">
          <div class="admin-hero__eyebrow">
            <span class="live-dot"></span>
            Live · Tuesday 11 Aug 2026 · 2:14 PM
          </div>
          <h1 class="admin-hero__title">Good morning, Admin</h1>
          <p class="admin-hero__sub">You have <strong style="color:#fff">8 estimates awaiting verification</strong>,
            <strong style="color:#fff">3 flagged as high priority</strong>, and
            <strong style="color:#fff">1 SLA breach</strong> in the next 4 hours.
          </p>
        </div>
        <div class="admin-hero__actions">
          <a href="{{ route('admin.estimates') }}" class="btn btn--secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            Open Queue
          </a>
          <a href="{{ route('admin.estimates', ['filter' => 'high']) }}" class="btn btn--primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Review High Priority
          </a>
        </div>
      </section>

      <!-- KPI CARDS -->
      <section class="grid grid--4 mb-6" aria-label="Key metrics">
        <div class="stat-card">
          <div class="stat-card__top">
            <span class="stat-card__label">Awaiting Verification</span>
            <div class="stat-card__icon stat-card__icon--violet">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
          </div>
          <div class="stat-card__value">{{ $estimateQueueCount }}</div>
          <div class="stat-card__delta stat-card__delta--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
            Needs staff action
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card__top">
            <span class="stat-card__label">Verified Today</span>
            <div class="stat-card__icon stat-card__icon--emerald">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
          </div>
          <div class="stat-card__value">{{ $verifiedToday }}</div>
          <div class="stat-card__delta stat-card__delta--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
            Versions released
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card__top">
            <span class="stat-card__label">Awaiting Customer</span>
            <div class="stat-card__icon stat-card__icon--warning">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
          </div>
          <div class="stat-card__value">{{ $estimateCounts->get('approved', 0) }}</div>
          <div class="stat-card__delta stat-card__delta--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
            Customer decision pending
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card__top">
            <span class="stat-card__label">AI Analysis Failed</span>
            <div class="stat-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
            </div>
          </div>
          <div class="stat-card__value">{{ $estimateCounts->get('ai_failed', 0) }}</div>
          <div class="stat-card__delta stat-card__delta--down">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            Retry or review manually
          </div>
        </div>
      </section>

      <!-- QUICK STATS STRIP -->
      <section class="quick-stats" aria-label="At a glance">
        <div class="quick-stat">
          <span class="quick-stat__label">High Priority</span>
          <span class="quick-stat__value quick-stat__value--danger">{{ $urgentEstimateCount }}</span>
        </div>
        <div class="quick-stat">
          <span class="quick-stat__label">Awaiting Information</span>
          <span class="quick-stat__value quick-stat__value--warning">{{ $estimateCounts->get('needs_info', 0) }}</span>
        </div>
        <div class="quick-stat">
          <span class="quick-stat__label">AI Drafting</span>
          <span class="quick-stat__value quick-stat__value--violet">{{ $estimateCounts->only(['ai_pending', 'ai_retrying'])->sum() }}</span>
        </div>
        <div class="quick-stat">
          <span class="quick-stat__label">Revision Requested</span>
          <span class="quick-stat__value">{{ $estimateCounts->get('revision_requested', 0) }}</span>
        </div>
        <div class="quick-stat">
          <span class="quick-stat__label">Customer Approved</span>
          <span class="quick-stat__value quick-stat__value--emerald">{{ $estimateCounts->get('customer_approved', 0) }}</span>
        </div>
        <div class="quick-stat">
          <span class="quick-stat__label">All Requests</span>
          <span class="quick-stat__value">{{ $estimateCounts->sum() }}</span>
        </div>
      </section>

      <!-- MAIN GRID -->
      <div class="admin-grid">

        <!-- ============ LEFT COLUMN ============ -->
        <div class="admin-col">

          <!-- REVIEW QUEUE -->
          <section class="card" aria-labelledby="queue-heading">
            <div class="card__header">
              <div>
                <div class="card__title" id="queue-heading">Review queue</div>
                <div class="card__subtitle">Requests that need staff action, sorted by priority</div>
              </div>
              <a href="{{ route('admin.estimates') }}" class="btn btn--ghost btn--sm">
                View all
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
              </a>
            </div>
            <div class="card__body" style="padding:0">
              @forelse ($estimateQueue as $estimate)
                <div class="queue-item">
                  <div class="queue-item__priority queue-item__priority--{{ $estimate->priority }}"></div>
                  <div class="queue-item__body">
                    <div class="queue-item__top"><a class="queue-item__id" href="{{ route('admin.estimate-review', $estimate) }}">{{ $estimate->number() }}</a>
                      <span class="chip {{ $estimate->statusClass(true) }}"><span class="chip__dot"></span>{{ Str::headline($estimate->status) }}</span>
                    </div>
                    <div class="queue-item__meta">{{ $estimate->street_address }} · {{ $estimate->customer->company_name ?: $estimate->customer->name }}</div>
                  </div>
                  <div class="queue-item__right"><div class="queue-item__amount">{{ \App\Services\Estimates\EstimateDisplay::currency($estimate->draft?->total) }}</div>
                    <div class="queue-item__sla">{{ ucfirst($estimate->priority) }} priority</div>
                  </div>
                </div>
              @empty
                <p class="sec__body text-muted">No estimates currently need staff action.</p>
              @endforelse

            </div>
          </section>

          <!-- RECENT ACTIVITY -->
          <section class="card" aria-labelledby="activity-heading">
            <div class="card__header">
              <div>
                <div class="card__title" id="activity-heading">Recent team activity</div>
              </div>
              <a href="{{ route('admin.notifications') }}" class="btn btn--ghost btn--sm">All</a>
            </div>
            <div class="card__body" style="padding:0">
              <p class="sec__body"><a href="{{ route('admin.estimates', ['view' => 'all']) }}">Open All Estimates to review individual audit histories.</a></p>
            </div>
          </section>

        </div>

        <!-- ============ RIGHT COLUMN ============ -->
        <div class="admin-col">

          <!-- QUALITY SNAPSHOT -->
          <section class="card" aria-labelledby="quality-heading">
<div class="card__header"><div class="card__title" id="quality-heading">Project status</div><a class="btn btn--ghost btn--sm" href="{{ route('admin.projects') }}">View projects</a></div>
<div class="card__body">@foreach (\App\Models\Project::STATUSES as $status)<div class="status-row"><span>{{ Str::headline($status) }}</span><strong>{{ $projectCounts->get($status, 0) }}</strong></div>@endforeach</div>
</section>

          <!-- ESTIMATOR WORKLOAD -->
          <section class="card" aria-labelledby="workload-heading">
<div class="card__header"><div class="card__title" id="workload-heading">Estimator workload</div><a class="btn btn--ghost btn--sm" href="{{ route('admin.estimators') }}">View estimators</a></div>
<div class="card__body">@forelse ($estimatorWorkload as $contact)<div class="status-row"><a href="{{ route('admin.estimators.show', $contact) }}">{{ $contact->name }}</a><span>{{ $contact->projects_count }} open</span></div>@empty<p>No estimators added.</p>@endforelse</div>
</section>

          <!-- SYSTEM HEALTH -->
          <section class="card" aria-labelledby="health-heading">
<div class="card__header"><div class="card__title" id="health-heading">Daily reports</div><a class="btn btn--ghost btn--sm" href="{{ route('admin.reports') }}">View reports</a></div><div class="card__body">{{ $dailyReportCount }} reports recorded</div>
</section>

        </div>
      </div>

    </main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
@endpush
