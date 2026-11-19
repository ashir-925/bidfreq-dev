@extends('front.layout.app')
@section('title', 'Dashboard · BidFreq™')


@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/notifications.css') }}">
@endpush

@section('content')
    <main class="app-content">

        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="customer-dashboard.html">Home</a>
            <span class="breadcrumbs__sep">/</span>
            <span class="breadcrumbs__current">Notifications</span>
        </nav>

        <header class="page-header">
            <div class="page-header__text">
                <h1 class="page-header__title">Notifications</h1>
                <p class="page-header__subtitle">
                    Every event across your estimating workflow — AI drafts, human-verified
                    estimates, revisions, approvals, and project updates.
                </p>
            </div>
            <div class="page-header__actions">
                <a href="profile.html" class="btn btn--secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3" />
                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                    </svg>
                    Preferences
                </a>
                <button class="btn btn--primary" id="markAllRead">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Mark all read
                </button>
            </div>
        </header>

        <!-- ============================================================
                   MAIN GRID
                   ============================================================ -->
        <div class="notif-grid">

            <!-- ============ LEFT: LIST ============ -->
            <div class="notif-main">

                <!-- Tabs -->
                <div class="notif-tabs-wrap">
                    <nav class="notif-tabs" role="tablist" aria-label="Notification filters">
                        <button class="tab is-active" data-filter="all" role="tab">
                            All
                            <span class="badge">18</span>
                        </button>
                        <button class="tab" data-filter="unread" role="tab">
                            Unread
                            <span class="badge badge--blue">3</span>
                        </button>
                        <button class="tab" data-filter="estimates" role="tab">
                            Estimates
                            <span class="badge">10</span>
                        </button>
                        <button class="tab" data-filter="projects" role="tab">
                            Projects
                            <span class="badge">5</span>
                        </button>
                        <button class="tab" data-filter="system" role="tab">
                            System
                            <span class="badge">3</span>
                        </button>
                    </nav>
                </div>

                <!-- Panel -->
                <div class="card" style="overflow:hidden">

                    <!-- Toolbar -->
                    <div class="notif-toolbar">
                        <div class="notif-toolbar__left">
                            <label class="check" style="margin:0">
                                <input type="checkbox" id="selectAll">
                                <span class="check__box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </span>
                                <span class="check__label">Select all</span>
                            </label>
                            <span id="selectionInfo" class="hidden">
                                · <strong id="selectedCount">0</strong> selected
                            </span>
                        </div>
                        <div class="notif-toolbar__actions">
                            <button class="btn btn--ghost btn--sm" id="markSelectedRead" disabled>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                Mark as read
                            </button>
                            <button class="btn btn--ghost btn--sm" id="markSelectedUnread" disabled>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                Mark as unread
                            </button>
                        </div>
                    </div>

                    <!-- List -->
                    <div class="notif-list" id="notifList"></div>

                    <!-- Empty state -->
                    <div class="empty notif-empty hidden" id="emptyState">
                        <div class="empty__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                            </svg>
                        </div>
                        <div class="empty__title">You're all caught up</div>
                        <p class="empty__text">No notifications match this filter.</p>
                    </div>

                </div>
            </div>

            <!-- ============ RIGHT: SIDEBAR ============ -->

        </div>

    </main>
@endsection
@push('scripts')
    
    <script src="{{ asset('front_assets/pages_js/notifications.js') }}"></script>
   

{{-- <script>
    window.notificationRoutes = {
        estimate: @json(route('front.estimate_request.detail', ['id' => '__ID__'])),

        // Apne actual project detail route name se replace karein
        project: @json(route('front.project.detail', ['id' => '__ID__']))
    };
</>

<script src="{{ asset('front_assets/pages_js/notifications.js') }}"></script> --}}

@endpush
