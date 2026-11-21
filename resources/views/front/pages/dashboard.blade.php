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
                <div class="stat-card__value">{{ $openEstimateCount }}</div>
                <div class="stat-card__delta stat-card__delta--up">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="18 15 12 9 6 15" />
                    </svg>
                    Requests in progress
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
                <div class="stat-card__value">{{ $awaitingApprovalCount }}</div>
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
                <div class="stat-card__value">{{ $projectCounts->sum() }}</div>
                <div class="stat-card__delta stat-card__delta--up">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="18 15 12 9 6 15" />
                    </svg>
                    Accepted by you
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
                <div class="stat-card__value">{{ $revisionCount }}</div>
                <div class="stat-card__delta stat-card__delta--down">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                    Waiting for a revised estimate
                </div>
            </div>
        </section>

        <!-- ============================================================
               QUICK STATS STRIP
               ============================================================ -->
        <section class="quick-stats" aria-label="At a glance">
            <div class="quick-stat">
                <span class="quick-stat__label">Under Review</span>
                <span class="quick-stat__value quick-stat__value--violet">{{ $estimateCounts->only(['ai_pending', 'ai_retrying', 'ai_failed', 'draft', 'under_review'])->sum() }}</span>
            </div>
            <div class="quick-stat">
                <span class="quick-stat__label">More Information Needed</span>
                <span class="quick-stat__value">{{ $estimateCounts->get('needs_info', 0) }}</span>
            </div>
            <div class="quick-stat">
                <span class="quick-stat__label">Awaiting Your Decision</span>
                <span class="quick-stat__value quick-stat__value--warning">{{ $awaitingApprovalCount }}</span>
            </div>
            <div class="quick-stat">
                <span class="quick-stat__label">Revision Requested</span>
                <span class="quick-stat__value quick-stat__value--emerald">{{ $revisionCount }}</span>
            </div>
            <div class="quick-stat">
                <span class="quick-stat__label">All Requests</span>
                <span class="quick-stat__value">{{ $estimateCounts->sum() }}</span>
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
                        <span class="badge badge--violet">{{ $customerActionCount }}</span>
                    </div>
                    <div class="card__body" style="padding:0">
                        @forelse ($customerActions as $estimate)
                            <div class="action-item"><div class="action-item__body">
                                <div class="action-item__title">{{ $estimate->customerStatus() }}</div>
                                <div class="action-item__meta">{{ $estimate->street_address }} · {{ $estimate->updated_at->diffForHumans() }}</div>
                            </div>
                            <a class="btn btn--secondary btn--sm" href="{{ route('repair-requests.show', $estimate) }}">{{ $estimate->status === 'needs_info' ? 'Respond' : 'Review estimate' }}</a></div>
                        @empty
                            <p class="sec__body text-muted">No estimate requests need your action.</p>
                        @endforelse

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
                        @forelse ($recentEstimates as $estimate)
                            <div class="est-row"><div class="est-row__main">
                                <div class="est-row__top"><a class="est-row__id" href="{{ route('repair-requests.show', $estimate) }}">{{ $estimate->number() }}</a>
                                    <span class="chip {{ $estimate->statusClass() }}"><span class="chip__dot"></span>{{ $estimate->customerStatus() }}</span></div>
                                <div class="est-row__property">{{ $estimate->street_address }}, {{ $estimate->city }}, {{ $estimate->state }}</div>
                            </div>
                            @if ($estimate->currentRelease)<div class="est-row__amount">{{ \App\Services\Estimates\EstimateDisplay::currency($estimate->currentRelease->snapshot['total']) }}</div>@endif
                            </div>
                        @empty
                            <p class="sec__body text-muted">No estimates submitted yet.</p>
                        @endforelse

                    </div>
                </section>

            </div>

            <!-- ============ RIGHT COLUMN ============ -->
            <div class="dash-col">

                <!-- Portfolio health -->
                <section class="card" aria-labelledby="health-heading">
<div class="card__header"><div class="card__title" id="health-heading">Project status</div><a class="btn btn--ghost btn--sm" href="{{ route('projects') }}">View projects</a></div>
<div class="card__body">@foreach (\App\Models\Project::STATUSES as $status)<div class="status-row"><span>{{ Str::headline($status) }}</span><strong>{{ $projectCounts->get($status, 0) }}</strong></div>@endforeach</div>
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
                        <p class="sec__body"><a href="{{ route('estimate-request') }}">Open Estimate Requests for status and version history.</a></p>

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
