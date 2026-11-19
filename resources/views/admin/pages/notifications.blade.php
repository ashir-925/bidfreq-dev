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
        <span class="breadcrumbs__current">Notifications</span>
      </nav>

      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-header__title">Notifications</h1>
          <p class="page-header__subtitle">
            SLA warnings, queue alerts, customer events, and system notices for the estimator team.
          </p>
        </div>
        <div class="page-header__actions">
          <a href="profile.html" class="btn btn--secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Preferences
          </a>
          <button class="btn btn--primary" id="markAllRead">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            Mark all read
          </button>
        </div>
      </header>

      <div class="notif-grid">
        <div class="notif-main">
          <div class="notif-tabs-wrap">
            <nav class="notif-tabs" role="tablist">
              <button class="tab is-active" data-filter="all">All <span class="badge">22</span></button>
              <button class="tab" data-filter="unread">Unread <span class="badge badge--blue">5</span></button>
              <button class="tab" data-filter="sla">SLA Alerts <span class="badge">4</span></button>
              <button class="tab" data-filter="queue">Queue <span class="badge">9</span></button>
              <button class="tab" data-filter="customer">Customer <span class="badge">6</span></button>
              <button class="tab" data-filter="system">System <span class="badge">3</span></button>
            </nav>
          </div>

          <div class="card" style="overflow:hidden">
            <div class="notif-toolbar">
              <div class="notif-toolbar__left">
                <label class="check" style="margin:0">
                  <input type="checkbox" id="selectAll">
                  <span class="check__box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                  <span class="check__label">Select all</span>
                </label>
                <span id="selectionInfo" class="hidden">· <strong id="selectedCount">0</strong> selected</span>
              </div>
              <div class="notif-toolbar__actions">
                <button class="btn btn--ghost btn--sm" id="markSelectedRead" disabled>Mark as read</button>
                <button class="btn btn--ghost btn--sm" id="markSelectedUnread" disabled>Mark as unread</button>
              </div>
            </div>

            <div class="notif-list" id="notifList"></div>

            <div class="empty notif-empty hidden" id="emptyState">
              <div class="empty__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div>
              <div class="empty__title">You're all caught up</div>
              <p class="empty__text">No notifications match this filter.</p>
            </div>
          </div>
        </div>
      </div>
    </main>
@endsection
@push('scripts')
<div class="toast-region" id="toastRegion"></div>
    <script src="{{ asset('admin_assets/pages_js/notifications.js') }}"></script>
@endpush
