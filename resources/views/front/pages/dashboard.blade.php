@extends('front.layout.app')
@section('title', 'Dashboard · BidFreq™')


@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/dashboard.css') }}">
@endpush

@section('content')
    <main class="app-content">

        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a>
            <span class="breadcrumbs__sep">/</span>
            <span class="breadcrumbs__current">Dashboard</span>
        </nav>

        <!-- ============================================================
               HERO
               ============================================================ -->
        <section class="dash-hero">
            <div class="dash-hero__text">
                <div class="dash-hero__eyebrow">Tuesday · 11 August 2026</div>
                <h1 class="dash-hero__title">Good afternoon, Jane</h1>
                <p class="dash-hero__sub">
                    You have <strong style="color:#fff">2 estimates awaiting your approval</strong> and
                    <strong style="color:#fff">12 open requests</strong> in progress.
                </p>
            </div>
            <div class="dash-hero__actions">
                <a href="{{ route('estimate-request') }}" class="btn btn--secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                    View all requests
                </a>
                <a href="{{ route('new-estimate-request') }}" class="btn btn--primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    New Estimate Request
                </a>
            </div>
        </section>

        <!-- ============================================================
               KPI CARDS
               ============================================================ -->
        <section class="grid grid--4 mb-6" aria-label="Key metrics">
            <div class="stat-card">
                <div class="stat-card__top">
                    <span class="stat-card__label">Open Requests</span>
                    <div class="stat-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                    </div>
                </div>
                <div class="stat-card__value">12</div>
                <div class="stat-card__delta stat-card__delta--up">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="18 15 12 9 6 15" />
                    </svg>
                    4 added this week
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card__top">
                    <span class="stat-card__label">Awaiting Your Approval</span>
                    <div class="stat-card__icon stat-card__icon--violet">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                    </div>
                </div>
                <div class="stat-card__value">2</div>
                <div class="stat-card__delta text-muted">Action required</div>
            </div>

            <div class="stat-card">
                <div class="stat-card__top">
                    <span class="stat-card__label">Approved Projects</span>
                    <div class="stat-card__icon stat-card__icon--emerald">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                    </div>
                </div>
                <div class="stat-card__value">38</div>
                <div class="stat-card__delta stat-card__delta--up">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="18 15 12 9 6 15" />
                    </svg>
                    +12% month-over-month
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card__top">
                    <span class="stat-card__label">Revisions Requested</span>
                    <div class="stat-card__icon stat-card__icon--warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                            <path d="M3 3v5h5" />
                        </svg>
                    </div>
                </div>
                <div class="stat-card__value">3</div>
                <div class="stat-card__delta stat-card__delta--down">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                    2 resolved this week
                </div>
            </div>
        </section>

        <!-- ============================================================
               QUICK STATS STRIP
               ============================================================ -->
        <section class="quick-stats" aria-label="At a glance">
            <div class="quick-stat">
                <span class="quick-stat__label">AI Drafting</span>
                <span class="quick-stat__value quick-stat__value--violet">1</span>
            </div>
            <div class="quick-stat">
                <span class="quick-stat__label">In Estimator Review</span>
                <span class="quick-stat__value">4</span>
            </div>
            <div class="quick-stat">
                <span class="quick-stat__label">Awaiting Customer</span>
                <span class="quick-stat__value quick-stat__value--warning">2</span>
            </div>
            <div class="quick-stat">
                <span class="quick-stat__label">Approved this month</span>
                <span class="quick-stat__value quick-stat__value--emerald">9</span>
            </div>
            <div class="quick-stat">
                <span class="quick-stat__label">Avg. turnaround</span>
                <span class="quick-stat__value">1.8<span style="font-size:12px;color:var(--text-muted);font-weight:500">
                        days</span></span>
            </div>
        </section>

        <!-- ============================================================
               MAIN GRID
               ============================================================ -->
        <div class="dash-grid">

            <!-- ============ LEFT COLUMN ============ -->
            <div class="dash-col">

                <!-- Awaiting your action -->
                <section class="card" aria-labelledby="action-heading">
                    <div class="card__header">
                        <div>
                            <div class="card__title" id="action-heading">Awaiting your action</div>
                            <div class="card__subtitle">Estimates ready for review or response</div>
                        </div>
                        <span class="badge badge--violet">2</span>
                    </div>
                    <div class="card__body" style="padding:0">

                        <div class="action-item">
                            <div class="action-item__icon action-item__icon--violet">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                </svg>
                            </div>
                            <div class="action-item__body">
                                <div class="action-item__title">Final estimate ready for approval — $1,482.50</div>
                                <div class="action-item__meta">
                                    <span class="action-item__id">EST-2026-0040</span>
                                    <span class="dot"></span>
                                    <span>1502 Palm Ave, Houston, TX</span>
                                    <span class="dot"></span>
                                    <span>Sent 2 hours ago</span>
                                </div>
                            </div>
                            <a href="{{ route('estimate_request_detail') }}" class="btn btn--primary btn--sm">
                                Review
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </a>
                        </div>

                        <div class="action-item">
                            <div class="action-item__icon action-item__icon--blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                </svg>
                            </div>
                            <div class="action-item__body">
                                <div class="action-item__title">Updated estimate issued after your revision — $3,140.00
                                </div>
                                <div class="action-item__meta">
                                    <span class="action-item__id">EST-2026-0037</span>
                                    <span class="dot"></span>
                                    <span>12 Cedar Ct, Fort Worth, TX</span>
                                    <span class="dot"></span>
                                    <span>Yesterday</span>
                                </div>
                            </div>
                            <a href="{{ route('estimate_request_detail') }}" class="btn btn--secondary btn--sm">
                                Review
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </a>
                        </div>

                    </div>
                </section>

                <!-- Recent estimates -->
                <section class="card" aria-labelledby="recent-heading">
                    <div class="card__header">
                        <div>
                            <div class="card__title" id="recent-heading">Recent estimates</div>
                            <div class="card__subtitle">Latest activity across your portfolio</div>
                        </div>
                        <a href="{{ route('estimate-request') }}" class="btn btn--ghost btn--sm">
                            View all
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </a>
                    </div>
                    <div class="card__body" style="padding:0">

                        <div class="est-row">
                            <div class="est-row__main">
                                <div class="est-row__top">
                                    <span class="est-row__id">EST-2026-0042</span>
                                    <span class="chip status--ai-processing"><span class="chip__dot"></span>AI
                                        Processing</span>
                                </div>
                                <div class="est-row__property">421 Oak Ridge Dr, Austin, TX 78745</div>
                                <div class="est-row__meta">
                                    <span>General Repair</span>
                                    <span class="dot"></span>
                                    <span>6 photos</span>
                                    <span class="dot"></span>
                                    <span>Submitted 2 min ago</span>
                                </div>
                            </div>
                            <div class="est-row__right">
                                <div class="est-row__amount est-row__amount--muted">Analyzing…</div>
                                <div class="est-row__time">Confidence pending</div>
                            </div>
                        </div>

                        <div class="est-row">
                            <div class="est-row__main">
                                <div class="est-row__top">
                                    <span class="est-row__id">EST-2026-0041</span>
                                    <span class="chip status--in-review"><span class="chip__dot"></span>In Estimator
                                        Review</span>
                                </div>
                                <div class="est-row__property">88 Cypress Ln, Dallas, TX 75204</div>
                                <div class="est-row__meta">
                                    <span>Roofing</span>
                                    <span class="dot"></span>
                                    <span>8 photos · 1 video</span>
                                    <span class="dot"></span>
                                    <span>Updated 1 hr ago</span>
                                </div>
                            </div>
                            <div class="est-row__right">
                                <div class="est-row__amount">$8,420.00</div>
                                <div class="est-row__time">Draft</div>
                            </div>
                        </div>

                        <div class="est-row">
                            <div class="est-row__main">
                                <div class="est-row__top">
                                    <span class="est-row__id">EST-2026-0040</span>
                                    <span class="chip status--awaiting-customer"><span class="chip__dot"></span>Awaiting
                                        Customer</span>
                                </div>
                                <div class="est-row__property">1502 Palm Ave, Houston, TX 77002</div>
                                <div class="est-row__meta">
                                    <span>Plumbing</span>
                                    <span class="dot"></span>
                                    <span>5 photos</span>
                                    <span class="dot"></span>
                                    <span>Sent 2 hr ago</span>
                                </div>
                            </div>
                            <div class="est-row__right">
                                <div class="est-row__amount">$1,482.50</div>
                                <div class="est-row__time">Human verified</div>
                            </div>
                        </div>

                        <div class="est-row">
                            <div class="est-row__main">
                                <div class="est-row__top">
                                    <span class="est-row__id">EST-2026-0039</span>
                                    <span class="chip status--revision"><span class="chip__dot"></span>Revision
                                        Requested</span>
                                </div>
                                <div class="est-row__property">77 Birch St, Plano, TX 75024</div>
                                <div class="est-row__meta">
                                    <span>Electrical</span>
                                    <span class="dot"></span>
                                    <span>4 photos</span>
                                    <span class="dot"></span>
                                    <span>Returned to estimator</span>
                                </div>
                            </div>
                            <div class="est-row__right">
                                <div class="est-row__amount">$2,310.00</div>
                                <div class="est-row__time">2 days ago</div>
                            </div>
                        </div>

                        <div class="est-row">
                            <div class="est-row__main">
                                <div class="est-row__top">
                                    <span class="est-row__id">EST-2026-0038</span>
                                    <span class="chip status--approved"><span class="chip__dot"></span>Approved</span>
                                </div>
                                <div class="est-row__property">305 Meadow Rd, Austin, TX 78704</div>
                                <div class="est-row__meta">
                                    <span>HVAC</span>
                                    <span class="dot"></span>
                                    <span>7 photos · 1 report</span>
                                    <span class="dot"></span>
                                    <span>Approved 3 days ago</span>
                                </div>
                            </div>
                            <div class="est-row__right">
                                <div class="est-row__amount">$4,950.00</div>
                                <div class="est-row__time">→ Approved Project</div>
                            </div>
                        </div>

                    </div>
                </section>

            </div>

            <!-- ============ RIGHT COLUMN ============ -->
            <div class="dash-col">

                <!-- Portfolio health -->
                <section class="card" aria-labelledby="health-heading">
                    <div class="card__header">
                        <div>
                            <div class="card__title" id="health-heading">Portfolio snapshot</div>
                            <div class="card__subtitle">This month</div>
                        </div>
                    </div>
                    <div class="card__body">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="ring" style="--p: 74;">
                                <span class="ring__value">74%</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm" style="font-weight:600;color:var(--text-strong)">Approval rate</div>
                                <div class="text-xs text-muted">9 of 12 estimates approved without revision</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mb-4">
                            <div class="ring" style="--p: 62;">
                                <span class="ring__value">62%</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm" style="font-weight:600;color:var(--text-strong)">AI confidence avg.
                                </div>
                                <div class="text-xs text-muted">Based on 12 estimates this month</div>
                            </div>
                        </div>

                        <hr class="divider">

                        <dl class="kv">
                            <dt>Avg. turnaround</dt>
                            <dd><strong>1.8 days</strong> <span class="text-xs text-muted">(vs 2.4 last month)</span></dd>
                            <dt>Active properties</dt>
                            <dd><strong>24</strong></dd>
                            <dt>Total estimate value</dt>
                            <dd><strong>$48,210.00</strong> <span class="text-xs text-muted">this month</span></dd>
                        </dl>
                    </div>
                </section>

                <!-- Recent activity -->
                <section class="card" aria-labelledby="activity-heading">
                    <div class="card__header">
                        <div>
                            <div class="card__title" id="activity-heading">Recent activity</div>
                        </div>
                        <a href="{{ route('notifications') }}" class="btn btn--ghost btn--sm">All</a>
                    </div>
                    <div class="card__body" style="padding:0">

                        <div class="activity">
                            <div class="activity__item">
                                <div class="activity__icon activity__icon--violet">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z" />
                                    </svg>
                                </div>
                                <div class="activity__body">
                                    <div class="activity__text"><strong>AI analysis started</strong> for EST-2026-0042
                                    </div>
                                    <div class="activity__time">2 minutes ago</div>
                                </div>
                            </div>

                            <div class="activity__item">
                                <div class="activity__icon activity__icon--emerald">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </div>
                                <div class="activity__body">
                                    <div class="activity__text"><strong>B. Hayes</strong> verified estimate for 1502 Palm
                                        Ave</div>
                                    <div class="activity__time">2 hours ago</div>
                                </div>
                            </div>

                            <div class="activity__item">
                                <div class="activity__icon activity__icon--blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg>
                                </div>
                                <div class="activity__body">
                                    <div class="activity__text">Final PDF emailed for <strong>EST-2026-0040</strong></div>
                                    <div class="activity__time">2 hours ago</div>
                                </div>
                            </div>

                            <div class="activity__item">
                                <div class="activity__icon activity__icon--warning">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                        <path d="M3 3v5h5" />
                                    </svg>
                                </div>
                                <div class="activity__body">
                                    <div class="activity__text">Revision requested on <strong>EST-2026-0039</strong></div>
                                    <div class="activity__time">2 days ago</div>
                                </div>
                            </div>

                            <div class="activity__item">
                                <div class="activity__icon activity__icon--emerald">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                        <polyline points="22 4 12 14.01 9 11.01" />
                                    </svg>
                                </div>
                                <div class="activity__body">
                                    <div class="activity__text"><strong>EST-2026-0038</strong> moved to Approved Project
                                    </div>
                                    <div class="activity__time">3 days ago</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- Help / prompt card -->

            </div>
        </div>

    </main>
@endsection
@push('scripts')
    <script src="{{ asset('front_assets/pages_js/dashboard.js') }}"></script>
@endpush
