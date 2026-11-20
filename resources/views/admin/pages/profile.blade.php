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
        <span class="breadcrumbs__current">Profile</span>
      </nav>

      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-header__title">My Profile</h1>
          <p class="page-header__subtitle">Manage your personal info, security, e-signature, verification preferences, and active sessions.</p>
        </div>
      </header>

      <div class="profile-grid">
        <nav class="settings-nav">
          <div class="settings-nav__title">Profile Settings</div>
          <button class="settings-nav__item is-active" data-tab="general">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            General
          </button>
          <button class="settings-nav__item" data-tab="security">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Security
          </button>
          <button class="settings-nav__item" data-tab="signature">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/></svg>
            E-Signature
          </button>
          <button class="settings-nav__item" data-tab="prefs">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4"/></svg>
            Verification Prefs
          </button>
          <button class="settings-nav__item" data-tab="sessions">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            Sessions
          </button>
        </nav>

        <div class="panel-stack">

          <!-- GENERAL -->
          <div class="panel-tab is-active" data-panel="general">
            <section class="profile-header">
              <div class="profile-header__cover"></div>
              <div class="profile-header__inner">
                <div class="profile-header__row">
                  <div class="profile-header__avatar">BH</div>
                  <div style="flex:1;min-width:0;padding-bottom:4px">
                    <h2 class="profile-header__name">B. Hayes</h2>
                    <div class="profile-header__meta">
                      <span>b.hayes@bidfreq.com</span>
                      <span class="dot"></span>
                      <span>Senior Estimator</span>
                      <span class="dot"></span>
                      <span class="profile-header__role">Estimator Team</span>
                    </div>
                  </div>
                  <div style="display:flex;gap:var(--s-3);flex-wrap:wrap">
                    <button class="btn btn--secondary" id="cancelGeneral">Cancel</button>
                    <button class="btn btn--primary" id="saveGeneral">Save changes</button>
                  </div>
                </div>
              </div>
            </section>

            <section class="sec">
              <div class="sec__head">
                <div class="sec__head-left">
                  <div class="sec__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                  <div><div class="sec__title">Personal Information</div><div class="sec__sub">Your team profile</div></div>
                </div>
              </div>
              <div class="sec__body">
                <div class="form-row mb-5">
                  <div class="form-group" style="margin-bottom:0"><label class="label">First name</label><input class="input" value="Brandon"></div>
                  <div class="form-group" style="margin-bottom:0"><label class="label">Last name</label><input class="input" value="Hayes"></div>
                </div>
                <div class="form-row mb-5">
                  <div class="form-group" style="margin-bottom:0"><label class="label">Work email</label><input class="input" type="email" value="b.hayes@bidfreq.com"></div>
                  <div class="form-group" style="margin-bottom:0"><label class="label">Phone</label><input class="input" type="tel" value="+1 (512) 555-0188"></div>
                </div>
                <div class="form-row">
                  <div class="form-group" style="margin-bottom:0"><label class="label">Role</label><input class="input" value="Senior Estimator" disabled></div>
                  <div class="form-group" style="margin-bottom:0"><label class="label">Specialties</label><input class="input" value="General Repair, Drywall / Paint"></div>
                </div>
              </div>
            </section>
          </div>

          <!-- SECURITY -->
          <div class="panel-tab" data-panel="security">
            <section class="sec">
              <div class="sec__head">
                <div class="sec__head-left">
                  <div class="sec__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
                  <div><div class="sec__title">Password</div><div class="sec__sub">Last changed 12 days ago</div></div>
                </div>
              </div>
              <div class="sec__body">
                <div class="form-group"><label class="label">Current password</label><input class="input" type="password" placeholder="Enter current password"></div>
                <div class="form-row mb-3">
                  <div class="form-group" style="margin-bottom:0"><label class="label">New password</label><input class="input" type="password" placeholder="At least 12 characters"></div>
                  <div class="form-group" style="margin-bottom:0"><label class="label">Confirm</label><input class="input" type="password" placeholder="Re-enter"></div>
                </div>
                <button class="btn btn--primary" onclick="toast('success','Password updated','')">Update password</button>
              </div>
            </section>

            <section class="sec">
              <div class="sec__head">
                <div class="sec__head-left">
                  <div class="sec__icon sec__icon--emerald"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                  <div><div class="sec__title">Two-Factor Authentication</div><div class="sec__sub">Required for estimator console access</div></div>
                </div>
              </div>
              <div class="sec__body">
                <div class="flag flag--info">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                  <div><div class="flag__title">Authenticator app enabled</div><div class="flag__body">Configured on iPhone · Added Aug 4, 2026</div></div>
                </div>
              </div>
            </section>
          </div>

          <!-- SIGNATURE -->
          <div class="panel-tab" data-panel="signature">
            <section class="sec">
              <div class="sec__head">
                <div class="sec__head-left">
                  <div class="sec__icon sec__icon--violet"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/></svg></div>
                  <div><div class="sec__title">Verification Signature</div><div class="sec__sub">Applied to every estimate you verify</div></div>
                </div>
              </div>
              <div class="sec__body">
                <div style="background:var(--bg-surface);border:2px dashed var(--border-strong);border-radius:var(--r-md);padding:var(--s-5) var(--s-6);min-height:100px;display:flex;flex-direction:column;justify-content:flex-end;margin-bottom:var(--s-5)">
                  <div style="border-bottom:1px solid var(--n-400);padding-bottom:6px;margin-bottom:8px"><div style="font-family:'Brush Script MT','Segoe Script',cursive;font-size:36px;color:var(--navy-800);line-height:1">Brandon Hayes</div></div>
                  <div style="font-size:var(--fs-xs);color:var(--text-muted);font-weight:500">Saved signature · used for estimate verification</div>
                </div>
                <div class="form-group"><label class="label">Signature text</label><input class="input text-mono" value="Brandon Hayes"></div>
                <div class="form-group"><label class="label">Estimator ID <span class="label__hint">displayed on PDFs</span></label><input class="input text-mono" value="B. Hayes · Senior Estimator"></div>
                <div class="flex gap-3 flex-wrap mt-4">
                  <button class="btn btn--secondary" onclick="toast('info','Clear signature','')">Clear</button>
                  <button class="btn btn--primary" onclick="toast('success','Signature saved','')">Save signature</button>
                </div>
              </div>
            </section>
          </div>

          <!-- PREFS -->
          <div class="panel-tab" data-panel="prefs">
            <section class="sec">
              <div class="sec__head">
                <div class="sec__head-left">
                  <div class="sec__icon sec__icon--warning"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                  <div><div class="sec__title">SLA &amp; Queue Preferences</div><div class="sec__sub">Control how the queue assigns work to you</div></div>
                </div>
              </div>
              <div class="sec__body">
                <div class="form-row mb-5">
                  <div class="form-group" style="margin-bottom:0"><label class="label">Max concurrent assignments</label><input class="input" type="number" value="8"></div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="label">Trade specialties</label>
                    <select class="select" multiple style="height:auto;min-height:90px">
                      <option selected>General Repair</option>
                      <option selected>Drywall / Paint</option>
                      <option>Electrical</option>
                      <option>Plumbing</option>
                      <option>HVAC</option>
                      <option>Roofing</option>
                    </select>
                  </div>
                </div>
                <div class="flag flag--warning mb-4">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                  <div><div class="flag__title">SLA alert threshold</div><div class="flag__body">You will be notified when an assigned estimate has less than 2 hours until SLA breach.</div></div>
                </div>
                <label class="check mb-4">
                  <input type="checkbox" checked>
                  <span class="check__box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                  <span class="check__label"><strong>Auto-assign high priority estimates to me</strong><span class="check__desc">When I have fewer than 5 open assignments.</span></span>
                </label>
                <label class="check mb-4">
                  <input type="checkbox" checked>
                  <span class="check__box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                  <span class="check__label"><strong>Email me a daily digest</strong><span class="check__desc">A summary of assignments, verifications, and SLA metrics.</span></span>
                </label>
                <label class="check">
                  <input type="checkbox">
                  <span class="check__box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                  <span class="check__label"><strong>SMS alerts for SLA breaches</strong><span class="check__desc">Requires a verified phone number.</span></span>
                </label>
                <div class="mt-5"><button class="btn btn--primary" onclick="toast('success','Preferences saved','')">Save preferences</button></div>
              </div>
            </section>
          </div>

          <!-- SESSIONS -->
          <div class="panel-tab" data-panel="sessions">
            <section class="sec">
              <div class="sec__head">
                <div class="sec__head-left">
                  <div class="sec__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div>
                  <div><div class="sec__title">Active Sessions</div><div class="sec__sub">Devices signed into your account</div></div>
                </div>
                <button class="btn btn--danger-outline btn--sm" onclick="toast('warning','Signed out other devices','')">Sign out other devices</button>
              </div>
              <div class="sec__body">
                <div class="status-row">
                  <span class="status-row__label">Chrome · macOS (this device)</span>
                  <span class="status-row__value"><span class="chip chip--success" style="padding:2px 8px"><span class="chip__dot"></span>Active now</span></span>
                </div>
                <div class="status-row"><span class="status-row__label">BidFreq iOS App · iPhone 15 Pro</span><span class="status-row__value text-muted">2 hours ago</span></div>
                <div class="status-row"><span class="status-row__label">Safari · iPad Pro</span><span class="status-row__value text-muted">3 days ago</span></div>
              </div>
            </section>
          </div>

        </div>
      </div>
    </main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/profile.js') }}"></script>
@endpush
