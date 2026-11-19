@extends('admin.layout.app')
@section('title', 'Estimator Console · BidFreq™')


@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('admin_assets/pages_css/dashboard.css') }}"> --}}
@endpush

@section('content')
    <main class="app-content">

        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="dashboard.html">Console</a>
            <span class="breadcrumbs__sep">/</span>
            <span class="breadcrumbs__current">Customers</span>
        </nav>

        <header class="page-header">
            <div class="page-header__text">
                <h1 class="page-header__title">Customers</h1>
                <p class="page-header__subtitle">
                    Every organization submitting estimates to BidFreq. Filter by plan, health,
                    trade mix, or activity to manage accounts and identify expansion opportunities.
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
                    Export
                </button>
                <button class="btn btn--primary" id="inviteCustomerBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Invite customer
                </button>
            </div>
        </header>

        <!-- KPIs -->
        <section class="cust-kpis" aria-label="Customer metrics">
            <div class="cust-kpi">
                <div class="cust-kpi__icon cust-kpi__icon--emerald">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M3 21h18" />
                        <path d="M5 21V7l8-4v18" />
                    </svg>
                </div>
                <div>
                    <div class="cust-kpi__label">Active Customers</div>
                    <div class="cust-kpi__value">142</div>
                    <div class="cust-kpi__sub">+8 this month</div>
                </div>
            </div>
            <div class="cust-kpi">
                <div class="cust-kpi__icon cust-kpi__icon--blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <div>
                    <div class="cust-kpi__label">Monthly GMV</div>
                    <div class="cust-kpi__value">$1.2M</div>
                    <div class="cust-kpi__sub">+14% MoM</div>
                </div>
            </div>
            <div class="cust-kpi">
                <div class="cust-kpi__icon cust-kpi__icon--violet">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                </div>
                <div>
                    <div class="cust-kpi__label">Avg. Approval Rate</div>
                    <div class="cust-kpi__value">89%</div>
                    <div class="cust-kpi__sub">Across all orgs</div>
                </div>
            </div>
            <div class="cust-kpi">
                <div class="cust-kpi__icon cust-kpi__icon--warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                </div>
                <div>
                    <div class="cust-kpi__label">At Risk</div>
                    <div class="cust-kpi__value">6</div>
                    <div class="cust-kpi__sub">No activity &gt; 30 days</div>
                </div>
            </div>
        </section>

        <!-- Filter bar -->
        <div class="filter-bar">
            <div class="filter-bar__search">
                <div class="input-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round">
                        <circle cx="11" cy="11" r="7" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input class="input" type="search" id="custSearch"
                        placeholder="Search by org name, contact, or email…">
                </div>
            </div>
            <div class="filter-bar__group">
                <span class="filter-bar__label">Plan</span>
                <select id="planFilter">
                    <option value="">All plans</option>
                    <option>Enterprise</option>
                    <option>Growth</option>
                    <option>Starter</option>
                    <option>Trial</option>
                </select>
            </div>
            <div class="filter-bar__group">
                <span class="filter-bar__label">Status</span>
                <select id="statusFilter">
                    <option value="">Any status</option>
                    <option>Active</option>
                    <option>Trial</option>
                    <option>Suspended</option>
                </select>
            </div>
            <div class="filter-bar__group">
                <span class="filter-bar__label">Sort</span>
                <select id="sortFilter">
                    <option value="gmv">GMV (high → low)</option>
                    <option value="newest">Newest first</option>
                    <option value="name">Name (A → Z)</option>
                    <option value="activity">Last activity</option>
                </select>
            </div>
            <button class="btn btn--ghost btn--sm" id="clearFilters">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
                Clear
            </button>
        </div>

        <!-- Bulk bar -->
        <div class="bulk-bar" id="bulkBar">
            <div class="bulk-bar__left">
                <span class="bulk-bar__count" id="bulkCount">0</span>
                <span>selected</span>
            </div>
            <div class="bulk-bar__actions">
                <button class="btn btn--secondary btn--sm">Email</button>
                <button class="btn btn--secondary btn--sm">Export</button>
                <button class="btn btn--ghost btn--sm" id="clearSelection">Cancel</button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-wrap">
            <div class="table-scroll">
                <table class="table" id="custTable">
                    <thead>
                        <tr>
                            <th class="table__checkbox">
                                <label class="check" style="margin:0">
                                    <input type="checkbox" id="selectAll">
                                    <span class="check__box">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    </span>
                                </label>
                            </th>
                            <th>Organization</th>
                            <th>Contact</th>
                            <th>Plan</th>
                            <th>Properties</th>
                            <th class="num">Est. / mo</th>
                            <th class="num">GMV</th>
                            <th>Health</th>
                            <th>Last activity</th>
                            <th class="actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="custBody"></tbody>
                </table>
            </div>

            <div class="empty hidden" id="emptyState">
                <div class="empty__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="7" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </div>
                <div class="empty__title">No customers match</div>
                <p class="empty__text">Try adjusting your filters or clear them to see all customers.</p>
                <button class="btn btn--secondary" id="clearEmptyFilters">Clear filters</button>
            </div>

            <div class="pagination" id="pagination">
                <span class="pagination__info" id="paginationInfo">Showing 1–10 of 142 customers</span>
                <div class="pagination__controls">
                    <button class="pagination__btn" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                    </button>
                    <button class="pagination__btn is-active">1</button>
                    <button class="pagination__btn">2</button>
                    <button class="pagination__btn">3</button>
                    <button class="pagination__btn">4</button>
                    <button class="pagination__btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

    </main>
@endsection
@push('scripts')
    <!-- CUSTOMER DETAIL DRAWER -->
    <div class="modal-overlay" id="custDrawer" onclick="if(event.target===this)this.classList.remove('is-open')">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="cdTitle" style="max-width:720px">
            <div class="modal__header">
                <div>
                    <div class="modal__title" id="cdTitle">Oak Ridge Property Mgmt</div>
                    <div class="modal__subtitle" id="cdSubtitle">Enterprise · Active since Mar 2024</div>
                </div>
                <button class="modal__close" aria-label="Close"
                    onclick="document.getElementById('custDrawer').classList.remove('is-open')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal__body">
                <div class="grid grid--3 mb-5" style="gap:var(--s-4)">
                    <div class="cust-kpi">
                        <div class="cust-kpi__icon cust-kpi__icon--blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                        </div>
                        <div>
                            <div class="cust-kpi__label">Estimates</div>
                            <div class="cust-kpi__value">124</div>
                        </div>
                    </div>
                    <div class="cust-kpi">
                        <div class="cust-kpi__icon cust-kpi__icon--emerald">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                        </div>
                        <div>
                            <div class="cust-kpi__label">Projects</div>
                            <div class="cust-kpi__value">38</div>
                        </div>
                    </div>
                    <div class="cust-kpi">
                        <div class="cust-kpi__icon cust-kpi__icon--violet">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23" />
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                            </svg>
                        </div>
                        <div>
                            <div class="cust-kpi__label">GMV</div>
                            <div class="cust-kpi__value">$184K</div>
                        </div>
                    </div>
                </div>
                <div class="sec mb-4">
                    <div class="sec__head">
                        <div class="sec__head-left">
                            <div class="sec__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg></div>
                            <div>
                                <div class="sec__title">Primary Contact</div>
                            </div>
                        </div>
                    </div>
                    <div class="sec__body">
                        <dl class="kv">
                            <dt>Name</dt>
                            <dd><strong>Jane Doe</strong></dd>
                            <dt>Title</dt>
                            <dd>Director of Operations</dd>
                            <dt>Email</dt>
                            <dd>jane@oakridge.com</dd>
                            <dt>Phone</dt>
                            <dd>+1 (512) 555-0142</dd>
                        </dl>
                    </div>
                </div>
                <div class="sec">
                    <div class="sec__head">
                        <div class="sec__head-left">
                            <div class="sec__icon sec__icon--emerald"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                                </svg></div>
                            <div>
                                <div class="sec__title">Account Health</div>
                            </div>
                        </div>
                    </div>
                    <div class="sec__body">
                        <div class="status-row"><span class="status-row__label">Approval rate</span><span
                                class="status-row__value" style="color:var(--emerald-600)">94%</span></div>
                        <div class="status-row"><span class="status-row__label">Avg. turnaround</span><span
                                class="status-row__value">1.6 days</span></div>
                        <div class="status-row"><span class="status-row__label">Revision rate</span><span
                                class="status-row__value">8%</span></div>
                        <div class="status-row"><span class="status-row__label">Last estimate</span><span
                                class="status-row__value">2 hours ago</span></div>
                    </div>
                </div>
            </div>
            <div class="modal__footer">
                <button class="btn btn--ghost"
                    onclick="document.getElementById('custDrawer').classList.remove('is-open')">Close</button>
                <button class="btn btn--secondary">View estimates</button>
                <button class="btn btn--primary">Open workspace</button>
            </div>
        </div>
    </div>

    <div class="toast-region" id="toastRegion"></div>


    <script src="{{ asset('admin_assets/pages_js/customers.js') }}"></script>
@endpush
