 <aside class="sidebar">
            <div class="sidebar__brand">
               
                <img src="{{ asset('front_assets/img/logo.webp') }}" class="img-fluid logo-img" style="WIDTH: 170px;">
            </div>

            <nav class="sidebar__nav">
                <div class="sidebar__section">
                    <div class="sidebar__section-title">Main</div>
                    <a href="{{ route('dashboard') }}" class="nav-item @if (request()->routeIs('dashboard')) is-active @endif" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="9" />
                            <rect x="14" y="3" width="7" height="5" />
                            <rect x="14" y="12" width="7" height="9" />
                            <rect x="3" y="16" width="7" height="5" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('estimate-request') }}" class="nav-item @if (request()->routeIs('estimate-request', 'estimate_request_detail', 'new-estimate-request')) is-active @endif" @if (request()->routeIs('estimate-request', 'estimate_request_detail', 'new-estimate-request')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="9" y1="13" x2="15" y2="13" />
                            <line x1="9" y1="17" x2="13" y2="17" />
                        </svg>
                        <span>Estimate Requests</span>
                        <span class="nav-item__badge">12</span>
                    </a>
                    <a href="{{ route('projects') }}" class="nav-item @if (request()->routeIs('projects', 'project-detail')) is-active @endif" @if (request()->routeIs('projects', 'project-detail')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <span>Approved Projects</span>
                    </a>
                </div>

                <div class="sidebar__section">
                    <div class="sidebar__section-title">Workspace</div>
                    <a href="{{ route('notifications') }}" class="nav-item @if (request()->routeIs('notifications')) is-active @endif" @if (request()->routeIs('notifications')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                        <span>Notifications</span>
                        <span class="nav-item__badge is-danger">3</span>
                    </a>

                    <a href="{{ route('profile') }}" class="nav-item @if (request()->routeIs('profile')) is-active @endif" @if (request()->routeIs('profile')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <span>Profile</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
  @csrf
  <button type="submit" class="nav-item" data-loading-text="Signing out…" style="width: 100%; background: none; border: 0; text-align: left; cursor: pointer;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                        <span>Logout</span>
                    </button></form>
                </div>
            </nav>

            <div class="sidebar__footer">
                <div class="user-chip">
                    <div class="avatar avatar--sm">JD</div>
                    <div class="user-chip__meta">
                        <span class="user-chip__name">{{ auth()->user()->name }}</span>
                        <span class="user-chip__role">{{ auth()->user()->company_name ?? 'Customer' }}</span>
                    </div>
                </div>
            </div>
        </aside>