@extends('front.layout.app')
@section('title', 'Dashboard · BidFreq™')


@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/profile.css') }}">
@endpush

@section('content')
    <main class="app-content">

      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="customer-dashboard.html">Home</a>
        <span class="breadcrumbs__sep">/</span>
        <span class="breadcrumbs__current">Profile</span>
      </nav>

      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-header__title">My Profile</h1>
          <p class="page-header__subtitle">
            Manage your personal information, security, notification preferences,
            and the electronic signature used for estimate approvals.
          </p>
        </div>
      </header>

      <!-- ============================================================
           GRID
           ============================================================ -->
      <div class="profile-grid">

        <!-- ============ LEFT: SETTINGS NAV ============ -->
        <nav class="settings-nav" aria-label="Profile sections">
          <div class="settings-nav__title">Profile Settings</div>
          <button class="settings-nav__item is-active" data-tab="general">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            General
          </button>
          <button class="settings-nav__item" data-tab="security">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Security
          </button>
         
          <button class="settings-nav__item" data-tab="signature">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg>
            E-Signature
          </button>
         
        </nav>

        <!-- ============ RIGHT: PANELS ============ -->
        <div class="panel-stack">

          <!-- ==================================================
               TAB: GENERAL
               ================================================== -->
          <div class="panel-tab is-active" data-panel="general">

            <!-- Header card -->
            <section class="profile-header">
              <div class="profile-header__cover"></div>
              <div class="profile-header__inner">
                <div class="profile-header__row">
                  <div class="profile-header__avatar">
                    JD
                    <button class="profile-header__avatar-btn" aria-label="Change photo" onclick="toast('info','Photo upload','A file picker would open here.')">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    </button>
                  </div>
                  <div class="profile-header__text">
                    <h2 class="profile-header__name">Jane Doe</h2>
                    <div class="profile-header__meta">
                      <span>jane@oakridge.com</span>
                      <span class="dot"></span>
                      <span>Oak Ridge Property Mgmt</span>
                      <span class="dot"></span>
                      <span class="profile-header__role">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                        Org Admin
                      </span>
                    </div>
                    <div class="role-badges">
                      <span class="chip chip--blue"><span class="chip__dot"></span>Can submit estimates</span>
                      <span class="chip chip--emerald"><span class="chip__dot"></span>Can approve estimates</span>
                      <span class="chip chip--violet"><span class="chip__dot"></span>Can manage org</span>
                    </div>
                  </div>
                  <div class="profile-header__actions">
                    <button class="btn btn--secondary" id="cancelGeneral">Cancel</button>
                    <button class="btn btn--primary" id="saveGeneral">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                      Save changes
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <!-- Personal information -->
            <section class="sec">
              <div class="sec__head">
                <div class="sec__head-left">
                  <div class="sec__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  </div>
                  <div>
                    <div class="sec__title">Personal Information</div>
                    <div class="sec__sub">How you appear across the platform</div>
                  </div>
                </div>
              </div>
              <div class="sec__body">
                <div class="form-row mb-5">
                  <div class="form-group" style="margin-bottom:0">
                    <label class="label" for="p-first">First name <span class="req">*</span></label>
                    <input class="input" id="p-first" type="text" value="Jane">
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="label" for="p-last">Last name <span class="req">*</span></label>
                    <input class="input" id="p-last" type="text" value="Doe">
                  </div>
                </div>

                <div class="form-row mb-5">
                  <div class="form-group" style="margin-bottom:0">
                    <label class="label" for="p-email">Work email <span class="req">*</span></label>
                    <div class="input-icon">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="22 6 12 13 2 6"/></svg>
                      <input class="input" id="p-email" type="email" value="jane@oakridge.com">
                    </div>
                    <div class="form-hint">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="vertical-align:-1px;margin-right:2px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                      Verified on Aug 4, 2026
                    </div>
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="label" for="p-phone">Phone</label>
                    <div class="input-icon">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                      <input class="input" id="p-phone" type="tel" value="+1 (512) 555-0142">
                    </div>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group" style="margin-bottom:0">
                    <label class="label" for="p-title">Job title</label>
                    <input class="input" id="p-title" type="text" value="Director of Operations">
                  </div>
                 
                </div>
              </div>
            </section>

            <!-- Preferences -->
          

            <!-- Danger zone (personal) -->
         

          </div>

          <!-- ==================================================
               TAB: SECURITY
               ================================================== -->
          <div class="panel-tab" data-panel="security">

            <!-- Password -->
            <section class="sec">
              <div class="sec__head">
                <div class="sec__head-left">
                  <div class="sec__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                  </div>
                  <div>
                    <div class="sec__title">Password</div>
                    <div class="sec__sub">Last changed 47 days ago</div>
                  </div>
                </div>
              </div>
              <div class="sec__body">
                <div class="form-group">
                  <label class="label" for="p-cur-pw">Current password <span class="req">*</span></label>
                  <input class="input" id="p-cur-pw" type="password" placeholder="Enter current password">
                </div>

                <div class="form-row mb-3">
                  <div class="form-group" style="margin-bottom:0">
                    <label class="label" for="p-new-pw">New password <span class="req">*</span></label>
                    <input class="input" id="p-new-pw" type="password" placeholder="At least 12 characters">
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="label" for="p-conf-pw">Confirm new password <span class="req">*</span></label>
                    <input class="input" id="p-conf-pw" type="password" placeholder="Re-enter new password">
                  </div>
                </div>

                <div class="panel-ai mb-5">
                  <div class="flex items-start gap-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--violet-600);flex-shrink:0;margin-top:2px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <div class="text-sm" style="color:var(--violet-700)">
                      <strong>Password requirements</strong>
                      <ul style="margin:6px 0 0;padding-left:18px;line-height:1.6">
                        <li>At least 12 characters</li>
                        <li>One uppercase and one lowercase letter</li>
                        <li>One number and one symbol</li>
                        <li>Not found in known breach databases</li>
                      </ul>
                    </div>
                  </div>
                </div>

                <button class="btn btn--primary" onclick="toast('success','Password updated','Use your new password next time you sign in.')">
                  Update password
                </button>
              </div>
            </section>

            <!-- Two-factor auth -->
          

          </div>

          <!-- ==================================================
               TAB: NOTIFICATIONS
               ================================================== -->
          <div class="panel-tab" data-panel="notifications">
            <section class="sec">
              <div class="sec__head">
                <div class="sec__head-left">
                  <div class="sec__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                  </div>
                  <div>
                    <div class="sec__title">Notification Preferences</div>
                    <div class="sec__sub">Choose how and when you're notified</div>
                  </div>
                </div>
              </div>
              <div class="sec__body" style="padding:0">

                <table class="table table--compact" style="border-radius:0">
                  <thead>
                    <tr>
                      <th style="width:50%">Event</th>
                      <th class="text-center" style="width:16.6%">In-app</th>
                      <th class="text-center" style="width:16.6%">Email</th>
                      <th class="text-center" style="width:16.6%">SMS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <div class="text-sm strong">Estimate submitted</div>
                        <div class="text-xs text-muted">Confirmation when you send a request</div>
                      </td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox"><span class="switch__track"></span></label></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="text-sm strong">AI analysis complete</div>
                        <div class="text-xs text-muted">Initial AI draft is ready for estimator review</div>
                      </td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox"><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox"><span class="switch__track"></span></label></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="text-sm strong">Human-verified estimate ready</div>
                        <div class="text-xs text-muted">Final PDF awaiting your approval</div>
                      </td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="text-sm strong">Revision issued</div>
                        <div class="text-xs text-muted">Estimator responded to your revision request</div>
                      </td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox"><span class="switch__track"></span></label></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="text-sm strong">Project status changes</div>
                        <div class="text-xs text-muted">Scheduled, in progress, completed</div>
                      </td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox"><span class="switch__track"></span></label></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="text-sm strong">Security alerts</div>
                        <div class="text-xs text-muted">New sign-ins, password changes</div>
                      </td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked disabled><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox" checked><span class="switch__track"></span></label></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="text-sm strong">Product updates &amp; tips</div>
                        <div class="text-xs text-muted">Occasional announcements</div>
                      </td>
                      <td class="text-center"><label class="switch"><input type="checkbox"><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox"><span class="switch__track"></span></label></td>
                      <td class="text-center"><label class="switch"><input type="checkbox"><span class="switch__track"></span></label></td>
                    </tr>
                  </tbody>
                </table>

              </div>
              <div class="card__footer">
                <button class="btn btn--ghost">Reset to defaults</button>
                <button class="btn btn--primary" onclick="toast('success','Preferences saved','Your notification settings were updated.')">Save preferences</button>
              </div>
            </section>

            <!-- Quiet hours -->
            <section class="sec">
              <div class="sec__head">
                <div class="sec__head-left">
                  <div class="sec__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                  </div>
                  <div>
                    <div class="sec__title">Quiet Hours</div>
                    <div class="sec__sub">Non-urgent notifications are held during this window</div>
                  </div>
                </div>
                <label class="switch">
                  <input type="checkbox" checked>
                  <span class="switch__track"></span>
                </label>
              </div>
              <div class="sec__body">
                <div class="form-row">
                  <div class="form-group" style="margin-bottom:0">
                    <label class="label" for="q-start">Start time</label>
                    <input class="input" id="q-start" type="time" value="20:00">
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="label" for="q-end">End time</label>
                    <input class="input" id="q-end" type="time" value="07:00">
                  </div>
                </div>
              </div>
            </section>
          </div>

          <!-- ==================================================
               TAB: E-SIGNATURE
               ================================================== -->
          <div class="panel-tab" data-panel="signature">

            <section class="esign-card">
              <div class="esign-card__head">
                <div class="esign-card__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/></svg>
                </div>
                <div>
                  <div class="esign-card__title">Electronic Signature</div>
                  <div class="esign-card__sub">Applied when you approve estimates (SOW §3.7)</div>
                </div>
              </div>
              <div class="esign-card__body">

                <div class="signature-preview mb-5">
                  <div class="signature-preview__line">
                    <div class="signature-preview__value">Jane Doe</div>
                  </div>
                  <div class="signature-preview__label">
                    Saved signature · used for e-signing estimate approvals
                  </div>
                </div>

                <div class="form-group">
                  <label class="label" for="sig-name">Signature text <span class="req">*</span></label>
                  <input class="input text-mono" id="sig-name" type="text" value="Jane Doe">
                  <div class="form-hint">Your typed legal name — this is what appears on the signed acceptance PDF.</div>
                </div>

                <div class="form-group mb-5">
                  <label class="label" for="sig-style">Signature style</label>
                  <select class="select" id="sig-style">
                    <option value="handwritten">Handwritten-style (cursive)</option>
                    <option value="typed">Standard type (monospace)</option>
                    <option value="serif">Formal (serif)</option>
                  </select>
                </div>

                <div class="form-group mb-5">
                  <label class="label" for="sig-initials">Initials <span class="label__hint">optional</span></label>
                  <input class="input" id="sig-initials" type="text" value="JD" maxlength="4" style="max-width:120px">
                  <div class="form-hint">Used on multi-page documents in the bottom corner.</div>
                </div>

                <hr class="divider">

                <label class="check mb-4">
                  <input type="checkbox" checked>
                  <span class="check__box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </span>
                  <span class="check__label">
                    <strong>Save this signature for future approvals</strong>
                    <span class="check__desc">You can still confirm each approval individually with a fresh typed name.</span>
                  </span>
                </label>

             

                <div class="flex gap-3 flex-wrap">
                  <button class="btn btn--secondary" onclick="toast('info','Clear signature','Signature was cleared.')">Clear</button>
                  <button class="btn btn--primary" onclick="toast('success','Signature saved','Your e-signature is now used for approvals.')">Save signature</button>
                </div>

              </div>
            </section>

           
          </div>

          <!-- ==================================================
               TAB: SESSIONS
               ================================================== -->
          <div class="panel-tab" data-panel="sessions">
            <section class="sec">
              <div class="sec__head">
                <div class="sec__head-left">
                  <div class="sec__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                  </div>
                  <div>
                    <div class="sec__title">Active Sessions</div>
                    <div class="sec__sub">Devices currently signed into your account</div>
                  </div>
                </div>
                <button class="btn btn--danger-outline btn--sm" onclick="toast('warning','Signed out all other devices','All other sessions have been terminated.')">
                  Sign out all other devices
                </button>
              </div>
              <div class="sec__body">

                <div class="session-row">
                  <div class="session-row__icon session-row__icon--current">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                  </div>
                  <div class="session-row__body">
                    <div class="session-row__title">
                      Chrome on macOS
                      <span class="chip chip--success" style="padding:2px 8px"><span class="chip__dot"></span>This device</span>
                    </div>
                    <div class="session-row__meta">Austin, TX · IP 72.14.···.92 · Active now</div>
                  </div>
                </div>

                <div class="session-row">
                  <div class="session-row__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                  </div>
                  <div class="session-row__body">
                    <div class="session-row__title">BidFreq iOS App</div>
                    <div class="session-row__meta">iPhone 15 Pro · Austin, TX · Last active 2 hours ago</div>
                  </div>
                  <button class="btn btn--ghost btn--sm" onclick="toast('info','Session ended','You signed out of that device.')">Sign out</button>
                </div>

                <div class="session-row">
                  <div class="session-row__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                  </div>
                  <div class="session-row__body">
                    <div class="session-row__title">Safari on iPad</div>
                    <div class="session-row__meta">iPad Pro · Dallas, TX · Last active 3 days ago</div>
                  </div>
                  <button class="btn btn--ghost btn--sm" onclick="toast('info','Session ended','You signed out of that device.')">Sign out</button>
                </div>

              </div>
            </section>
          </div>

        </div>
      </div>

    </main>
@endsection
@push('scripts')
   
    <script src="{{ asset('front_assets/pages_js/profile.js') }}"></script>
@endpush
