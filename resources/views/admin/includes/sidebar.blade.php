  <aside class="sidebar">
    <div class="sidebar__brand">
      <img src="{{ asset('admin_assets/img/logo.webp') }}" class="img-fluid logo-img" alt="BidFreq" style="width: 170px;">
      <div class="sidebar__brand-text" style="margin-top:6px">
        <div class="sidebar__tag" style="color:#6EE7B7">Estimator Console</div>
      </div>
    </div>

    <nav class="sidebar__nav">
      <div class="sidebar__section">
        <div class="sidebar__section-title">Operations</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item @if (request()->routeIs('admin.dashboard')) is-active @endif" @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
          <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.estimates') }}" class="nav-item @if (request()->routeIs('admin.estimates', 'admin.estimate-review') && request('view') !== 'all') is-active @endif">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
          <span>Estimate Queue</span>
          <span class="nav-item__badge is-danger">8</span>
        </a>
        <a href="{{ route('admin.estimates', ['view' => 'all']) }}" class="nav-item @if (request()->routeIs('admin.estimates') && request('view') === 'all') is-active @endif">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          <span>All Estimates</span>
        </a>
        <a href="{{ route('admin.projects') }}" class="nav-item @if (request()->routeIs('admin.projects')) is-active @endif" @if (request()->routeIs('admin.projects')) aria-current="page" @endif>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <span>Projects</span>
        </a>
      </div>

      <div class="sidebar__section">
        <div class="sidebar__section-title">Management</div>
        <a href="{{ route('admin.users.index') }}" class="nav-item @if (request()->routeIs('admin.users.*')) is-active @endif">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v2"/></svg>
          <span>Users</span>
        </a>
        <a href="{{ route('admin.customers') }}" class="nav-item @if (request()->routeIs('admin.customers')) is-active @endif" @if (request()->routeIs('admin.customers')) aria-current="page" @endif>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
          <span>Customers</span>
        </a>
        <a href="{{ route('admin.estimators') }}" class="nav-item @if (request()->routeIs('admin.estimators')) is-active @endif" @if (request()->routeIs('admin.estimators')) aria-current="page" @endif>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          <span>Estimators</span>
        </a>
        <a href="{{ route('admin.reports') }}" class="nav-item @if (request()->routeIs('admin.reports')) is-active @endif" @if (request()->routeIs('admin.reports')) aria-current="page" @endif>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          <span>Reports</span>
        </a>
      </div>

      <div class="sidebar__section">
        <div class="sidebar__section-title">Workspace</div>
        <a href="{{ route('admin.notifications') }}" class="nav-item @if (request()->routeIs('admin.notifications')) is-active @endif" @if (request()->routeIs('admin.notifications')) aria-current="page" @endif>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span>Notifications</span>
          <span class="nav-item__badge is-danger">5</span>
        </a>
        <a href="{{ route('admin.profile') }}" class="nav-item @if (request()->routeIs('admin.profile')) is-active @endif" @if (request()->routeIs('admin.profile')) aria-current="page" @endif>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <span>Profile</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
  @csrf
  <button type="submit" class="nav-item" data-loading-text="Signing out…" style="width: 100%; background: none; border: 0; text-align: left; cursor: pointer;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          <span>Logout</span>
        </button></form>
      </div>
    </nav>

    <div class="sidebar__footer">
      <div class="user-chip">
        <div class="avatar avatar--sm">BH</div>
        <div class="user-chip__meta">
          <span class="user-chip__name">{{ auth()->user()->name }}</span>
          <span class="user-chip__role">{{ auth()->user()->role === 'super_admin' ? 'Super Admin' : 'Admin' }}</span>
        </div>
      </div>
    </div>
  </aside>