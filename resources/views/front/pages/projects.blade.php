@extends('front.layout.app')
@section('title', 'Dashboard · BidFreq™')


@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/projects.css') }}">
@endpush

@section('content')
    <main class="app-content">

        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a>
            <span class="breadcrumbs__sep">/</span>
            <span class="breadcrumbs__current">Approved Projects</span>
        </nav>

        <header class="page-header">
            <div class="page-header__text">
                <h1 class="page-header__title">Approved Projects</h1>
                <p class="page-header__subtitle">
                    Estimates approved by your organization. Each project retains its approved estimate,
                    property information, approval status, and related estimate records (SOW §3.9).
                </p>
            </div>
            <div class="page-header__actions">
                <button class="btn btn--secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="7 10 12 15 17 10" />
                        <line x1="12" y1="15" x2="12" y2="3" />
                    </svg>
                    Export CSV
                </button>
                <a href="{{ route('new-estimate-request') }}" class="btn btn--primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    New Estimate Request
                </a>
            </div>
        </header>

        <!-- ============================================================
               KPI STRIP
               ============================================================ -->
        <section class="proj-kpis" aria-label="Project metrics">
            <div class="proj-kpi">
                <div class="proj-kpi__icon proj-kpi__icon--emerald">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
                <div class="proj-kpi__body">
                    <div class="proj-kpi__label">Total Projects</div>
                    <div class="proj-kpi__value">38</div>
                    <div class="proj-kpi__sub">Since account start</div>
                </div>
            </div>

            <div class="proj-kpi">
                <div class="proj-kpi__icon proj-kpi__icon--blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <div class="proj-kpi__body">
                    <div class="proj-kpi__label">Approved Value</div>
                    <div class="proj-kpi__value">$184K</div>
                    <div class="proj-kpi__sub">Across all projects</div>
                </div>
            </div>

            <div class="proj-kpi">
                <div class="proj-kpi__icon proj-kpi__icon--violet">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                </div>
                <div class="proj-kpi__body">
                    <div class="proj-kpi__label">This Month</div>
                    <div class="proj-kpi__value">9</div>
                    <div class="proj-kpi__sub">+12% vs last month</div>
                </div>
            </div>

            <div class="proj-kpi">
                <div class="proj-kpi__icon proj-kpi__icon--warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
                <div class="proj-kpi__body">
                    <div class="proj-kpi__label">Avg. Value</div>
                    <div class="proj-kpi__value">$4.8K</div>
                    <div class="proj-kpi__sub">Per approved project</div>
                </div>
            </div>
        </section>

        <!-- ============================================================
               FILTER BAR
               ============================================================ -->
        <div class="proj-filter-bar">
            <div class="proj-filter-bar__search">
                <div class="input-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round">
                        <circle cx="11" cy="11" r="7" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input class="input" type="search" id="projSearch"
                        placeholder="Search by ID, address, ZIP, or trade…">
                </div>
            </div>

            <select id="projTrade" aria-label="Filter by trade">
                <option value="">All trades</option>
                <option>General Repair</option>
                <option>Electrical</option>
                <option>Plumbing</option>
                <option>HVAC</option>
                <option>Roofing</option>
                <option>Flooring</option>
                <option>Drywall / Paint</option>
            </select>

            <select id="projDate" aria-label="Filter by date">
                <option value="">Any time</option>
                <option>Last 7 days</option>
                <option>Last 30 days</option>
                <option>Last 90 days</option>
            </select>

            <select id="projSort" aria-label="Sort">
                <option value="newest">Newest first</option>
                <option value="oldest">Oldest first</option>
                <option value="amount-high">Amount (high → low)</option>
                <option value="amount-low">Amount (low → high)</option>
            </select>

            <div class="view-toggle" role="tablist" aria-label="View">
                <button class="is-active" data-view="list" aria-label="List view" title="List view">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="8" y1="6" x2="21" y2="6" />
                        <line x1="8" y1="12" x2="21" y2="12" />
                        <line x1="8" y1="18" x2="21" y2="18" />
                        <line x1="3" y1="6" x2="3.01" y2="6" />
                        <line x1="3" y1="12" x2="3.01" y2="12" />
                        <line x1="3" y1="18" x2="3.01" y2="18" />
                    </svg>
                </button>
                <button data-view="grid" aria-label="Grid view" title="Grid view">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- ============================================================
               LIST VIEW
               ============================================================ -->
        <div class="project-list" id="projectList"></div>

        <!-- GRID VIEW (hidden by default) -->
        <div class="projects-grid hidden" id="projectGrid"></div>

        <!-- EMPTY STATE -->
        <div class="empty empty-projects hidden" id="emptyProjects">
            <div class="empty__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
            </div>
            <div class="empty__title">No approved projects match</div>
            <p class="empty__text">Try adjusting your filters, or approve a pending estimate to create your first project.
            </p>
            <div class="flex items-center gap-3 justify-center flex-wrap">
                <button class="btn btn--secondary" id="clearProjects">Clear filters</button>
                <a href="{{ route('new-estimate-request') }}" class="btn btn--primary">View pending estimates</a>
            </div>
        </div>

        <!-- PAGINATION -->
        <div class="pagination" id="projPagination"
            style="border:1px solid var(--border);border-top:none;border-radius:0 0 var(--r-lg) var(--r-lg);background:var(--bg-surface);">
            <span class="pagination__info" id="projPaginationInfo">Showing 1–8 of 38 projects</span>
            <div class="pagination__controls">
                <button class="pagination__btn" aria-label="Previous page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6" />
                    </svg>
                </button>
                <button class="pagination__btn is-active">1</button>
                <button class="pagination__btn">2</button>
                <button class="pagination__btn">3</button>
                <button class="pagination__btn">4</button>
                <button class="pagination__btn">5</button>
                <button class="pagination__btn" aria-label="Next page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </button>
            </div>
        </div>

    </main>
@endsection
@push('scripts')
    <script>
        window.projectDetailUrl = @json(route('project-detail', ['id' => '__ID__']));
    </script>
    <script src="{{ asset('front_assets/pages_js/projects.js') }}"></script>
@endpush
