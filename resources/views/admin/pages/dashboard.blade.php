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
          <div class="stat-card__value">8</div>
          <div class="stat-card__delta stat-card__delta--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
            3 added in last hour
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card__top">
            <span class="stat-card__label">Verified Today</span>
            <div class="stat-card__icon stat-card__icon--emerald">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
          </div>
          <div class="stat-card__value">14</div>
          <div class="stat-card__delta stat-card__delta--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
            +22% vs. yesterday
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card__top">
            <span class="stat-card__label">Avg. Turnaround</span>
            <div class="stat-card__icon stat-card__icon--warning">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
          </div>
          <div class="stat-card__value">1.8<span style="font-size:14px;color:var(--text-muted);font-weight:500"> days</span></div>
          <div class="stat-card__delta stat-card__delta--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
            Target: 2.0 days
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card__top">
            <span class="stat-card__label">AI Edit Rate</span>
            <div class="stat-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
            </div>
          </div>
          <div class="stat-card__value">38%</div>
          <div class="stat-card__delta stat-card__delta--down">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            −6 pts vs. last week
          </div>
        </div>
      </section>

      <!-- QUICK STATS STRIP -->
      <section class="quick-stats" aria-label="At a glance">
        <div class="quick-stat">
          <span class="quick-stat__label">High Priority</span>
          <span class="quick-stat__value quick-stat__value--danger">3</span>
        </div>
        <div class="quick-stat">
          <span class="quick-stat__label">SLA at Risk</span>
          <span class="quick-stat__value quick-stat__value--warning">1</span>
        </div>
        <div class="quick-stat">
          <span class="quick-stat__label">Assigned to Me</span>
          <span class="quick-stat__value quick-stat__value--violet">5</span>
        </div>
        <div class="quick-stat">
          <span class="quick-stat__label">Returned</span>
          <span class="quick-stat__value">2</span>
        </div>
        <div class="quick-stat">
          <span class="quick-stat__label">Approved this month</span>
          <span class="quick-stat__value quick-stat__value--emerald">94</span>
        </div>
        <div class="quick-stat">
          <span class="quick-stat__label">Team Online</span>
          <span class="quick-stat__value">4<span style="font-size:12px;color:var(--text-muted);font-weight:500"> / 6</span></span>
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
                <div class="card__subtitle">Estimates awaiting human verification — sorted by priority</div>
              </div>
              <a href="{{ route('admin.estimates') }}" class="btn btn--ghost btn--sm">
                View all
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
              </a>
            </div>
            <div class="card__body" style="padding:0">

              <div class="queue-item" onclick="location.href=this.dataset.href" data-href="{{ route('admin.estimate-review') }}">
                <div class="queue-item__priority queue-item__priority--high"></div>
                <div class="queue-item__body">
                  <div class="queue-item__top">
                    <span class="queue-item__id">EST-2026-0042</span>
                    <span class="chip status--ai-processing"><span class="chip__dot"></span>AI Draft Ready</span>
                    <span class="chip chip--danger" style="padding:2px 8px"><span class="chip__dot"></span>High</span>
                  </div>
                  <div class="queue-item__property">421 Oak Ridge Dr, Austin, TX 78745</div>
                  <div class="queue-item__meta">
                    <span>General Repair</span>
                    <span class="dot"></span>
                    <span>6 photos</span>
                    <span class="dot"></span>
                    <span>Customer: Oak Ridge Property Mgmt</span>
                  </div>
                </div>
                <div class="queue-item__right">
                  <div class="queue-item__amount">$2,840.00</div>
                  <div class="queue-item__sla queue-item__sla--warn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    SLA 1h 42m
                  </div>
                </div>
              </div>

              <div class="queue-item" onclick="location.href=this.dataset.href" data-href="{{ route('admin.estimate-review') }}">
                <div class="queue-item__priority queue-item__priority--high"></div>
                <div class="queue-item__body">
                  <div class="queue-item__top">
                    <span class="queue-item__id">EST-2026-0041</span>
                    <span class="chip status--in-review"><span class="chip__dot"></span>In Review</span>
                    <span class="chip chip--danger" style="padding:2px 8px"><span class="chip__dot"></span>High</span>
                  </div>
                  <div class="queue-item__property">88 Cypress Ln, Dallas, TX 75204</div>
                  <div class="queue-item__meta">
                    <span>Roofing</span>
                    <span class="dot"></span>
                    <span>8 photos · 1 video</span>
                    <span class="dot"></span>
                    <span>Assigned to <strong style="color:var(--text-strong)">B. Hayes</strong></span>
                  </div>
                </div>
                <div class="queue-item__right">
                  <div class="queue-item__amount">$8,420.00</div>
                  <div class="queue-item__sla queue-item__sla--warn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    SLA 3h 08m
                  </div>
                </div>
              </div>

              <div class="queue-item" onclick="location.href=this.dataset.href" data-href="{{ route('admin.estimate-review') }}">
                <div class="queue-item__priority queue-item__priority--med"></div>
                <div class="queue-item__body">
                  <div class="queue-item__top">
                    <span class="queue-item__id">EST-2026-0043</span>
                    <span class="chip status--ai-processing"><span class="chip__dot"></span>AI Draft Ready</span>
                    <span class="chip chip--warning" style="padding:2px 8px"><span class="chip__dot"></span>Med</span>
                  </div>
                  <div class="queue-item__property">12 Cedar Ct, Fort Worth, TX 76102</div>
                  <div class="queue-item__meta">
                    <span>Electrical</span>
                    <span class="dot"></span>
                    <span>5 photos</span>
                    <span class="dot"></span>
                    <span>Customer: Bluebonnet Homes</span>
                  </div>
                </div>
                <div class="queue-item__right">
                  <div class="queue-item__amount">$1,720.00</div>
                  <div class="queue-item__sla">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    SLA 6h 20m
                  </div>
                </div>
              </div>

              <div class="queue-item" onclick="location.href=this.dataset.href" data-href="{{ route('admin.estimate-review') }}">
                <div class="queue-item__priority queue-item__priority--med"></div>
                <div class="queue-item__body">
                  <div class="queue-item__top">
                    <span class="queue-item__id">EST-2026-0044</span>
                    <span class="chip status--ai-processing"><span class="chip__dot"></span>AI Draft Ready</span>
                    <span class="chip chip--warning" style="padding:2px 8px"><span class="chip__dot"></span>Med</span>
                  </div>
                  <div class="queue-item__property">900 Riverbend Way, Austin, TX 78730</div>
                  <div class="queue-item__meta">
                    <span>Drywall / Paint</span>
                    <span class="dot"></span>
                    <span>6 photos</span>
                    <span class="dot"></span>
                    <span>Customer: Oak Ridge Property Mgmt</span>
                  </div>
                </div>
                <div class="queue-item__right">
                  <div class="queue-item__amount">$1,875.00</div>
                  <div class="queue-item__sla">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    SLA 8h 45m
                  </div>
                </div>
              </div>

              <div class="queue-item" onclick="location.href=this.dataset.href" data-href="{{ route('admin.estimate-review') }}">
                <div class="queue-item__priority queue-item__priority--low"></div>
                <div class="queue-item__body">
                  <div class="queue-item__top">
                    <span class="queue-item__id">EST-2026-0045</span>
                    <span class="chip status--ai-processing"><span class="chip__dot"></span>AI Draft Ready</span>
                    <span class="chip chip--blue" style="padding:2px 8px"><span class="chip__dot"></span>Low</span>
                  </div>
                  <div class="queue-item__property">245 Sundial Blvd, San Antonio, TX 78205</div>
                  <div class="queue-item__meta">
                    <span>Plumbing</span>
                    <span class="dot"></span>
                    <span>4 photos</span>
                    <span class="dot"></span>
                    <span>Customer: Vista Realty Co.</span>
                  </div>
                </div>
                <div class="queue-item__right">
                  <div class="queue-item__amount">$980.00</div>
                  <div class="queue-item__sla">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    SLA 14h 10m
                  </div>
                </div>
              </div>

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
              <div class="activity">
                <div class="activity__item">
                  <div class="activity__icon activity__icon--emerald">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </div>
                  <div class="activity__body">
                    <div class="activity__text"><strong>M. Alvarez</strong> verified <strong>EST-2026-0039</strong> for $2,310.00</div>
                    <div class="activity__time">8 minutes ago</div>
                  </div>
                </div>

                <div class="activity__item">
                  <div class="activity__icon activity__icon--violet">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
                  </div>
                  <div class="activity__body">
                    <div class="activity__text">AI draft completed for <strong>EST-2026-0045</strong> · 88% confidence</div>
                    <div class="activity__time">23 minutes ago</div>
                  </div>
                </div>

                <div class="activity__item">
                  <div class="activity__icon activity__icon--warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                  </div>
                  <div class="activity__body">
                    <div class="activity__text">You returned <strong>EST-2026-0037</strong> to AI for re-analysis</div>
                    <div class="activity__time">1 hour ago</div>
                  </div>
                </div>

                <div class="activity__item">
                  <div class="activity__icon activity__icon--blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  </div>
                  <div class="activity__body">
                    <div class="activity__text">New estimate request <strong>EST-2026-0044</strong> submitted by Oak Ridge</div>
                    <div class="activity__time">2 hours ago</div>
                  </div>
                </div>

                <div class="activity__item">
                  <div class="activity__icon activity__icon--danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                  </div>
                  <div class="activity__body">
                    <div class="activity__text">SLA warning: <strong>EST-2026-0042</strong> due in under 2 hours</div>
                    <div class="activity__time">2 hours ago</div>
                  </div>
                </div>
              </div>
            </div>
          </section>

        </div>

        <!-- ============ RIGHT COLUMN ============ -->
        <div class="admin-col">

          <!-- QUALITY SNAPSHOT -->
          <section class="card" aria-labelledby="quality-heading">
            <div class="card__header">
              <div>
                <div class="card__title" id="quality-heading">Verification quality</div>
                <div class="card__subtitle">This week</div>
              </div>
            </div>
            <div class="card__body">
              <div class="flex items-center gap-4 mb-4">
                <div class="ring" style="--p: 94;">
                  <span class="ring__value">94%</span>
                </div>
                <div class="flex-1">
                  <div class="text-sm" style="font-weight:600;color:var(--text-strong)">Final confidence avg.</div>
                  <div class="text-xs text-muted">Across 62 verified estimates</div>
                </div>
              </div>

              <div class="flex items-center gap-4 mb-4">
                <div class="ring" style="--p: 38;">
                  <span class="ring__value">38%</span>
                </div>
                <div class="flex-1">
                  <div class="text-sm" style="font-weight:600;color:var(--text-strong)">AI edit rate</div>
                  <div class="text-xs text-muted">Estimates with at least one line-item change</div>
                </div>
              </div>

              <hr class="divider">

              <dl class="kv">
                <dt>Returned to AI</dt>
                <dd><strong>3</strong> <span class="text-xs text-muted">this week</span></dd>
                <dt>Avg. line items edited</dt>
                <dd><strong>2.4</strong></dd>
                <dt>Human-verified on time</dt>
                <dd><strong>98.4%</strong> <span class="text-xs text-muted">(target 95%)</span></dd>
              </dl>
            </div>
          </section>

          <!-- ESTIMATOR WORKLOAD -->
          <section class="card" aria-labelledby="workload-heading">
            <div class="card__header">
              <div>
                <div class="card__title" id="workload-heading">Estimator workload</div>
                <div class="card__subtitle">Open assignments per estimator</div>
              </div>
            </div>
            <div class="card__body">
              <div class="workload">

                <div class="workload-row">
                  <div class="workload-row__avatar">BH</div>
                  <div class="workload-row__body">
                    <div class="workload-row__head">
                      <span class="workload-row__name">B. Hayes (you)</span>
                      <span class="workload-row__count">5 open</span>
                    </div>
                    <div class="workload-row__bar">
                      <div class="workload-row__fill" style="width:62%"></div>
                    </div>
                  </div>
                </div>

                <div class="workload-row">
                  <div class="workload-row__avatar" style="background:linear-gradient(135deg,#10B981,#059669)">MA</div>
                  <div class="workload-row__body">
                    <div class="workload-row__head">
                      <span class="workload-row__name">M. Alvarez</span>
                      <span class="workload-row__count">3 open</span>
                    </div>
                    <div class="workload-row__bar">
                      <div class="workload-row__fill workload-row__fill--emerald" style="width:38%"></div>
                    </div>
                  </div>
                </div>

                <div class="workload-row">
                  <div class="workload-row__avatar" style="background:linear-gradient(135deg,#F59E0B,#D97706)">RK</div>
                  <div class="workload-row__body">
                    <div class="workload-row__head">
                      <span class="workload-row__name">R. Kapoor</span>
                      <span class="workload-row__count">7 open</span>
                    </div>
                    <div class="workload-row__bar">
                      <div class="workload-row__fill workload-row__fill--warning" style="width:88%"></div>
                    </div>
                  </div>
                </div>

                <div class="workload-row">
                  <div class="workload-row__avatar" style="background:linear-gradient(135deg,#3B82F6,#2563EB)">TN</div>
                  <div class="workload-row__body">
                    <div class="workload-row__head">
                      <span class="workload-row__name">T. Nakamura</span>
                      <span class="workload-row__count">2 open</span>
                    </div>
                    <div class="workload-row__bar">
                      <div class="workload-row__fill" style="width:25%"></div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </section>

          <!-- SYSTEM HEALTH -->
          <section class="card" aria-labelledby="health-heading">
            <div class="card__header">
              <div>
                <div class="card__title" id="health-heading">System health</div>
              </div>
            </div>
            <div class="card__body" style="padding:0">
              <div class="status-row" style="padding:var(--s-3) var(--s-5)">
                <span class="status-row__label">AI drafting service</span>
                <span class="status-row__value" style="color:var(--emerald-600)">
                  <span class="chip chip--success" style="padding:2px 8px"><span class="chip__dot"></span>Operational</span>
                </span>
              </div>
              <div class="status-row" style="padding:var(--s-3) var(--s-5)">
                <span class="status-row__label">Pricing feed</span>
                <span class="status-row__value">
                  <span class="chip chip--success" style="padding:2px 8px"><span class="chip__dot"></span>Operational</span>
                </span>
              </div>
              <div class="status-row" style="padding:var(--s-3) var(--s-5)">
                <span class="status-row__label">PDF rendering</span>
                <span class="status-row__value">
                  <span class="chip chip--success" style="padding:2px 8px"><span class="chip__dot"></span>Operational</span>
                </span>
              </div>
              <div class="status-row" style="padding:var(--s-3) var(--s-5)">
                <span class="status-row__label">Avg. AI draft time</span>
                <span class="status-row__value">47 seconds</span>
              </div>
              <div class="status-row" style="padding:var(--s-3) var(--s-5)">
                <span class="status-row__label">Queue processing rate</span>
                <span class="status-row__value">18 / hour</span>
              </div>
            </div>
          </section>

        </div>
      </div>

    </main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
@endpush
