<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account · BidFreq™</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('front_assets/css/styles.css') }}">

<style>
/* ------------------------------------------------------------------
   AUTH LAYOUT (shared with sign-in / forgot-password)
   ------------------------------------------------------------------ */
.auth {
  display: grid;
  grid-template-columns: 1.05fr 1fr;
  min-height: 100vh;
}

/* --- Left: brand panel --- */
.auth-brand {
  position: relative;
  background:
    radial-gradient(circle at 15% 20%, rgba(124,58,237,.28), transparent 45%),
    radial-gradient(circle at 85% 75%, rgba(59,130,246,.30), transparent 50%),
    linear-gradient(155deg, var(--navy-900) 0%, var(--navy-800) 55%, var(--navy-700) 100%);
  color: #fff;
  padding: var(--s-10) var(--s-12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.auth-brand::before {
  content: "";
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
  background-size: 44px 44px;
  mask-image: radial-gradient(circle at 50% 40%, #000 30%, transparent 78%);
  pointer-events: none;
}

.auth-brand__top {
  position: relative;
  display: flex; align-items: center; gap: var(--s-3);
  margin-bottom: auto;
}
.auth-brand__logo {
  width: 42px; height: 42px; border-radius: var(--r-md);
  background: linear-gradient(135deg, var(--blue-500), var(--violet-600));
  display: grid; place-items: center;
  font-family: var(--font-display); font-weight: var(--fw-bold);
  font-size: var(--fs-xl); color: #fff;
  box-shadow: 0 6px 18px rgba(124,58,237,.4);
}
.auth-brand__name {
  font-family: var(--font-display);
  font-weight: var(--fw-bold);
  font-size: var(--fs-lg);
  letter-spacing: -0.01em;
}
.auth-brand__name span { color: var(--violet-500); }
.auth-brand__tag {
  font-size: 11px; letter-spacing: .12em; text-transform: uppercase;
  color: var(--text-sidebar-muted);
}

.auth-brand__main {
  position: relative;
  max-width: 520px;
  margin: var(--s-16) 0 var(--s-10);
}
.auth-brand__headline {
  font-family: var(--font-display);
  font-size: 40px;
  line-height: 1.15;
  font-weight: var(--fw-bold);
  letter-spacing: -0.02em;
  color: #fff;
  margin-bottom: var(--s-5);
}
.auth-brand__headline em {
  font-style: normal;
  background: linear-gradient(90deg, #A78BFA, #60A5FA);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.auth-brand__lede {
  font-size: var(--fs-lg);
  line-height: var(--lh-base);
  color: #B9C7DA;
  margin-bottom: var(--s-8);
}

.auth-brand__flow {
  display: flex; flex-direction: column; gap: var(--s-4);
}
.auth-brand__flow-item {
  display: flex; align-items: center; gap: var(--s-4);
  padding: var(--s-3) var(--s-4);
  border-radius: var(--r-md);
  background: rgba(255,255,255,.05);
  border: 1px solid rgba(255,255,255,.08);
  backdrop-filter: blur(6px);
}
.auth-brand__flow-num {
  width: 30px; height: 30px; border-radius: 50%;
  display: grid; place-items: center;
  font-size: var(--fs-xs); font-weight: var(--fw-bold);
  flex-shrink: 0;
  background: rgba(255,255,255,.09);
  color: #CBD5E1;
}
.auth-brand__flow-item:nth-child(1) .auth-brand__flow-num { background: rgba(59,130,246,.22);  color: #93C5FD; }
.auth-brand__flow-item:nth-child(2) .auth-brand__flow-num { background: rgba(124,58,237,.24);  color: #C4B5FD; }
.auth-brand__flow-item:nth-child(3) .auth-brand__flow-num { background: rgba(16,185,129,.22);  color: #6EE7B7; }
.auth-brand__flow-item:nth-child(4) .auth-brand__flow-num { background: rgba(245,158,11,.20);  color: #FCD34D; }
.auth-brand__flow-text { font-size: var(--fs-sm); color: #D7E0EC; }
.auth-brand__flow-text strong { color: #fff; font-weight: var(--fw-semibold); display: block; }

.auth-brand__foot {
  position: relative;
  margin-top: auto;
  display: flex; align-items: center; gap: var(--s-6);
  flex-wrap: wrap;
  padding-top: var(--s-6);
  border-top: 1px solid rgba(255,255,255,.08);
}
.auth-brand__trust {
  display: flex; align-items: center; gap: var(--s-2);
  font-size: var(--fs-xs);
  color: var(--text-sidebar-muted);
}
.auth-brand__trust svg { width: 14px; height: 14px; color: var(--emerald-500); }

/* --- Right: form panel --- */
.auth-panel {
  display: flex; align-items: center; justify-content: center;
  padding: var(--s-10) var(--s-8);
  background: var(--bg-surface);
}
.auth-form {
  width: 100%;
  max-width: 420px;
}
.auth-form__head { margin-bottom: var(--s-8); }
.auth-form__title {
  font-size: var(--fs-3xl);
  margin-bottom: var(--s-2);
  letter-spacing: -0.02em;
}
.auth-form__sub {
  color: var(--text-muted);
  font-size: var(--fs-md);
}

/* Role selector */
.role-select {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: var(--s-2);
  margin-bottom: var(--s-6);
  padding: 4px;
  background: var(--n-100);
  border-radius: var(--r-lg);
}
.role-select__opt {
  position: relative;
  cursor: pointer;
  text-align: center;
  padding: var(--s-3) var(--s-2);
  border-radius: var(--r-md);
  transition: all var(--dur) var(--ease);
  border: 1px solid transparent;
}
.role-select__opt input { position: absolute; opacity: 0; inset: 0; cursor: pointer; }
.role-select__icon {
  width: 30px; height: 30px; margin: 0 auto var(--s-2);
  border-radius: var(--r-sm);
  display: grid; place-items: center;
  background: var(--n-200); color: var(--text-muted);
  transition: all var(--dur) var(--ease);
}
.role-select__icon svg { width: 15px; height: 15px; }
.role-select__label {
  font-size: var(--fs-xs);
  font-weight: var(--fw-semibold);
  color: var(--text-muted);
  transition: color var(--dur) var(--ease);
}
.role-select__opt:has(input:checked) {
  background: var(--bg-surface);
  box-shadow: var(--sh-sm);
  border-color: var(--blue-100);
}
.role-select__opt:has(input:checked) .role-select__icon {
  background: var(--blue-600); color: #fff;
  box-shadow: 0 3px 8px rgba(37,99,235,.35);
}
.role-select__opt:has(input:checked) .role-select__label { color: var(--text-strong); }
.role-select__opt:has(input:focus-visible) { box-shadow: var(--sh-focus); }

/* Two-column row for name fields */
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--s-4);
}

/* Optional field hint */
.label__opt {
  font-size: 10px;
  font-weight: var(--fw-medium);
  letter-spacing: .06em;
  text-transform: uppercase;
  color: var(--text-faint);
  margin-left: 4px;
}

/* Password visibility toggle */
.pw-toggle {
  position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; padding: 6px;
  color: var(--text-faint); border-radius: var(--r-sm);
  display: grid; place-items: center;
}
.pw-toggle:hover { background: var(--n-100); color: var(--text-default); }
.pw-toggle svg { width: 16px; height: 16px; }

/* Password strength meter */
.pw-meter { margin-top: var(--s-3); }
.pw-meter__bars {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 5px;
  margin-bottom: var(--s-2);
}
.pw-meter__bar {
  height: 4px;
  border-radius: 99px;
  background: var(--n-200);
  transition: background var(--dur) var(--ease);
}
.pw-meter__label {
  font-size: var(--fs-xs);
  color: var(--text-faint);
  display: flex; align-items: center; justify-content: space-between;
  gap: var(--s-3);
}
.pw-meter__label strong { font-weight: var(--fw-semibold); }

/* strength colors */
.pw-meter[data-score="1"] .pw-meter__bar:nth-child(-n+1) { background: var(--red-500, #EF4444); }
.pw-meter[data-score="2"] .pw-meter__bar:nth-child(-n+2) { background: var(--amber-500, #F59E0B); }
.pw-meter[data-score="3"] .pw-meter__bar:nth-child(-n+3) { background: var(--blue-500, #3B82F6); }
.pw-meter[data-score="4"] .pw-meter__bar { background: var(--emerald-500, #10B981); }

.pw-meter[data-score="1"] .pw-meter__label strong { color: var(--red-500, #EF4444); }
.pw-meter[data-score="2"] .pw-meter__label strong { color: var(--amber-500, #F59E0B); }
.pw-meter[data-score="3"] .pw-meter__label strong { color: var(--blue-600, #2563EB); }
.pw-meter[data-score="4"] .pw-meter__label strong { color: var(--emerald-500, #10B981); }

/* Inline error text */
.field-error {
  display: none;
  margin-top: 6px;
  font-size: var(--fs-xs);
  color: var(--red-600, #DC2626);
}
.field-error.is-visible { display: block; }

/* Terms checkbox block */
.terms {
  margin-bottom: var(--s-6);
}
.terms .check { align-items: flex-start; }
.terms .check__label { font-size: var(--fs-sm); line-height: var(--lh-base); }
.terms a { color: var(--blue-600); font-weight: var(--fw-medium); }
.terms a:hover { text-decoration: underline; }

/* Divider */
.auth-divider {
  display: flex; align-items: center; gap: var(--s-3);
  margin: var(--s-6) 0;
  font-size: var(--fs-xs);
  color: var(--text-faint);
  text-transform: uppercase;
  letter-spacing: .08em;
}
.auth-divider::before, .auth-divider::after {
  content: ""; flex: 1; height: 1px; background: var(--border);
}

/* SSO row */
.sso-row { display: grid; grid-template-columns: 1fr 1fr; gap: var(--s-3); }

.auth-form__foot {
  margin-top: var(--s-8);
  padding-top: var(--s-6);
  border-top: 1px solid var(--border);
  text-align: center;
  font-size: var(--fs-sm);
  color: var(--text-muted);
}
.auth-form__foot a { font-weight: var(--fw-semibold); }

.auth-form__legal {
  margin-top: var(--s-6);
  font-size: var(--fs-xs);
  color: var(--text-faint);
  text-align: center;
  line-height: var(--lh-base);
}

/* Note callout */
.auth-note {
  display: flex; gap: var(--s-3);
  padding: var(--s-3) var(--s-4);
  border-radius: var(--r-md);
  background: var(--blue-50);
  border: 1px solid var(--blue-100);
  font-size: var(--fs-xs);
  color: var(--blue-700);
  margin-bottom: var(--s-6);
}
.auth-note svg { width: 14px; height: 14px; flex-shrink: 0; margin-top: 2px; }

/* ---- Responsive ---- */
@media (max-width: 1024px) {
  .auth { grid-template-columns: 1fr 1fr; }
  .auth-brand { padding: var(--s-8); }
  .auth-brand__headline { font-size: 32px; }
}
@media (max-width: 860px) {
  .auth { grid-template-columns: 1fr; }
  .auth-brand {
    padding: var(--s-8) var(--s-6);
    min-height: auto;
  }
  .auth-brand__main { margin: var(--s-8) 0; }
  .auth-brand__headline { font-size: 28px; }
  .auth-brand__lede { font-size: var(--fs-base); margin-bottom: var(--s-6); }
  .auth-brand__flow { display: none; }
  .auth-brand__foot { padding-top: var(--s-5); }
  .auth-panel { padding: var(--s-8) var(--s-5) var(--s-10); }
  .auth-form__title { font-size: var(--fs-2xl); }
}
@media (max-width: 480px) {
  .auth-brand__headline { font-size: 24px; }
  .form-row { grid-template-columns: 1fr; gap: 0; }
  .sso-row { grid-template-columns: 1fr; }
}

img.img-fluid.logo-img {
    width: 200px;
    background-color: #ccc;
    border-radius: 5px;
}
</style>
</head>
<body>

<main class="auth">

  <!-- ============================================================
       LEFT — BRAND PANEL
       ============================================================ -->
  <section class="auth-brand" aria-label="BidFreq overview">

    <!-- <div class="auth-brand__top">
      <div class="auth-brand__logo">B</div>
      <div>
        <div class="auth-brand__name">BidFreq<span>™</span></div>
        <div class="auth-brand__tag">AI-Assisted Estimating</div>
      </div>
    </div> -->

     <div class="auth-brand__top">
      <img src="{{ asset('front_assets/img/logo.webp') }}" class="img-fluid logo-img" >
    </div>

    <div class="auth-brand__main">
      <h1 class="auth-brand__headline">
        Start estimating<br>
        <em>in minutes.</em>
      </h1>
      <p class="auth-brand__lede">
        Create your BidFreq account and turn property photos into
        verified, professional repair estimates — drafted by AI,
        approved by a human estimator.
      </p>

      <div class="auth-brand__flow">
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">1</div>
          <div class="auth-brand__flow-text">
            <strong>Create your account</strong>
            Takes under a minute — no credit card required.
          </div>
        </div>
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">2</div>
          <div class="auth-brand__flow-text">
            <strong>Upload photos & description</strong>
            Minimum two repair-area photos required before estimation.
          </div>
        </div>
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">3</div>
          <div class="auth-brand__flow-text">
            <strong>AI drafts the estimate</strong>
            Scope of Work, trades, materials, quantities, and localized pricing.
          </div>
        </div>
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">4</div>
          <div class="auth-brand__flow-text">
            <strong>Human verified & delivered</strong>
            A BidFreq estimator approves every estimate before it ships.
          </div>
        </div>
      </div>
    </div>

    <div class="auth-brand__foot">
      <div class="auth-brand__trust">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        SOC 2 aligned
      </div>
      <div class="auth-brand__trust">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Encrypted at rest
      </div>
      <div class="auth-brand__trust">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Human verified
      </div>
    </div>
  </section>

  <!-- ============================================================
       RIGHT — FORM PANEL
       ============================================================ -->
  <section class="auth-panel" aria-label="Create account">

    <form class="auth-form" id="registerForm" method="POST" action="{{ route('register') }}" novalidate>
      @csrf

      <header class="auth-form__head">
        <h2 class="auth-form__title">Create your account</h2>
        <p class="auth-form__sub">Join BidFreq and start submitting repairs.</p>
      </header>
      @include('shared.flash')

      <!-- Role selector -->
      <!-- <div class="role-select" role="radiogroup" aria-label="Account type">
        <label class="role-select__opt">
          <input type="radio" name="role" value="customer" checked>
          <div class="role-select__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <div class="role-select__label">Customer</div>
        </label>

        <label class="role-select__opt">
          <input type="radio" name="role" value="estimator">
          <div class="role-select__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
          </div>
          <div class="role-select__label">Estimator</div>
        </label>
      </div>


      <div class="auth-note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        <div>This is a UI preview. Any valid details will create a demo account.</div>
      </div> -->

      <!-- Name -->
      <div class="form-row">
        <div class="form-group">
          <label class="label" for="firstName">First name <span class="req">*</span></label>
          <div class="input-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input class="input" id="firstName" name="first_name" value="{{ is_string(old('first_name')) ? old('first_name') : '' }}" type="text"
                   autocomplete="given-name" placeholder="Jane" required>
          </div>
          @include('shared.auth-field-error', ['name' => 'first_name'])
        </div>

        <div class="form-group">
          <label class="label" for="lastName">Last name <span class="req">*</span></label>
          <div class="input-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input class="input" id="lastName" name="last_name" value="{{ is_string(old('last_name')) ? old('last_name') : '' }}" type="text"
                   autocomplete="family-name" placeholder="Doe" required>
          </div>
          @include('shared.auth-field-error', ['name' => 'last_name'])
        </div>
      </div>

      <!-- Email -->
      <div class="form-group">
        <label class="label" for="email">Work email <span class="req">*</span></label>
        <div class="input-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="22 6 12 13 2 6"/></svg>
          <input class="input" id="email" name="email" value="{{ is_string(old('email')) ? old('email') : '' }}" type="email" autocomplete="email"
                 placeholder="you@company.com" required>
        </div>
        <div class="field-error" id="emailError">Enter a valid email address.</div>
      </div>

      @include('shared.auth-field-error', ['name' => 'email'])

      <div class="form-group">
        <label class="label" for="phone">Phone <span class="req">*</span></label>
        <input class="input" id="phone" name="phone" type="tel" autocomplete="tel" value="{{ is_string(old('')) ? old('') : '' }}" required>
        @include('shared.auth-field-error', ['name' => 'phone'])
      </div>

      <!-- Company (optional) -->
      <div class="form-group">
        <label class="label" for="company">Company <span class="label__opt">Optional</span></label>
        <div class="input-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
          <input class="input" id="company" name="company_name" value="{{ is_string(old('company_name')) ? old('company_name') : '' }}" type="text"
                 autocomplete="organization" placeholder="Acme Property Group">
        </div>
      </div>

      @include('shared.auth-field-error', ['name' => 'company_name'])
      <!-- Password -->
      <div class="form-group">
        <label class="label" for="password">Password <span class="req">*</span></label>
        <div class="input-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input class="input" id="password" name="password" type="password"
                 autocomplete="new-password" placeholder="Create a password"
                 required style="padding-right:44px;">
          <button type="button" class="pw-toggle" id="pwToggle" data-target="password" aria-label="Show password">
            <svg class="pw-icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="pw-icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>

        <!-- Strength meter -->
        <div class="pw-meter" id="pwMeter" data-score="0">
          <div class="pw-meter__bars">
            <div class="pw-meter__bar"></div>
            <div class="pw-meter__bar"></div>
            <div class="pw-meter__bar"></div>
            <div class="pw-meter__bar"></div>
          </div>
          <div class="pw-meter__label">
            <span>Use 8+ characters with a mix of letters, numbers &amp; symbols.</span>
            <strong id="pwMeterText">—</strong>
          </div>
        </div>
        <div class="field-error" id="passwordError">Password must be at least 8 characters.</div>
      </div>

      @include('shared.auth-field-error', ['name' => 'password'])
      <!-- Confirm password -->
      <div class="form-group">
        <label class="label" for="confirmPassword">Confirm password <span class="req">*</span></label>
        <div class="input-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input class="input" id="confirmPassword" name="password_confirmation" type="password"
                 autocomplete="new-password" placeholder="Re-enter your password"
                 required style="padding-right:44px;">
          <button type="button" class="pw-toggle" data-target="confirmPassword" aria-label="Show password">
            <svg class="pw-icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="pw-icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
        <div class="field-error" id="confirmError">Passwords do not match.</div>
      </div>

      @include('shared.auth-field-error', ['name' => 'password_confirmation'])
      <!-- Terms -->
      <div class="terms">
        <label class="check">
          <input type="checkbox" name="terms" id="terms" value="1" @checked(old('terms'))>
          <span class="check__box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </span>
          <span class="check__label">
            I agree to the <a href="#">Terms of Service</a> and
            <a href="#">Privacy Policy</a>, and consent to BidFreq
            processing my account data.
          </span>
        </label>
        <div class="field-error" id="termsError">You must accept the terms to continue.</div>
      </div>

      @include('shared.auth-field-error', ['name' => 'terms'])
      <!-- Submit -->
      <button class="btn btn--primary btn--lg btn--block" type="submit" id="submitBtn" data-loading-text="Creating account…">
        Create account
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>

      <!-- Divider -->
      <!-- <div class="auth-divider">or sign up with</div> -->

      <!-- SSO -->
      <!-- <div class="sso-row">
        <button type="button" class="btn btn--secondary">
          <svg viewBox="0 0 24 24" width="16" height="16"><path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.2-2.27H12v4.51h6.47a5.57 5.57 0 0 1-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96H1.29v3.09A11.99 11.99 0 0 0 12 24z"/><path fill="#FBBC05" d="M5.27 14.29A7.2 7.2 0 0 1 4.89 12c0-.8.14-1.57.38-2.29V6.62H1.29A11.99 11.99 0 0 0 0 12c0 1.94.46 3.77 1.29 5.38l3.98-3.09z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.7 0 3.99 2.47 1.29 6.62l3.98 3.09C6.22 6.86 8.87 4.75 12 4.75z"/></svg>
          Google
        </button>
        <button type="button" class="btn btn--secondary">
          <svg viewBox="0 0 24 24" width="16" height="16"><path fill="#F25022" d="M1 1h10v10H1z"/><path fill="#7FBA00" d="M13 1h10v10H13z"/><path fill="#00A4EF" d="M1 13h10v10H1z"/><path fill="#FFB900" d="M13 13h10v10H13z"/></svg>
          Microsoft
        </button>
      </div> -->

      <!-- Sign in CTA -->
      <div class="auth-form__foot">
        Already have an account?
        <a href="{{ route('login') }}">Sign in</a>
      </div>

      <p class="auth-form__legal">
        By creating an account you agree to the
        <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
      </p>

    </form>
  </section>
</main>

<script>
  // ------------------------------------------------------------------
  // Password visibility toggles (works for both password fields)
  // ------------------------------------------------------------------
  (function () {
    document.querySelectorAll('.pw-toggle').forEach(function (toggle) {
      var targetId = toggle.getAttribute('data-target');
      var input    = document.getElementById(targetId);
      var show     = toggle.querySelector('.pw-icon-show');
      var hide     = toggle.querySelector('.pw-icon-hide');

      toggle.addEventListener('click', function () {
        var reveal = input.type === 'password';
        input.type = reveal ? 'text' : 'password';
        show.style.display = reveal ? 'none' : 'block';
        hide.style.display = reveal ? 'block' : 'none';
        toggle.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
      });
    });
  })();

  // ------------------------------------------------------------------
  // Password strength meter
  // ------------------------------------------------------------------
  (function () {
    var input  = document.getElementById('password');
    var meter  = document.getElementById('pwMeter');
    var text   = document.getElementById('pwMeterText');

    var LABELS = ['—', 'Weak', 'Fair', 'Good', 'Strong'];

    function scorePassword(v) {
      if (!v) return 0;
      var score = 0;
      if (v.length >= 8)  score++;
      if (v.length >= 12) score++;
      if (/[a-z]/.test(v) && /[A-Z]/.test(v)) score++;
      if (/\d/.test(v) && /[^A-Za-z0-9]/.test(v)) score++;
      // penalise very short passwords
      if (v.length < 8) score = Math.min(score, 1);
      return Math.min(score, 4);
    }

    input.addEventListener('input', function () {
      var s = scorePassword(this.value);
      meter.setAttribute('data-score', s);
      text.textContent = LABELS[s];
    });
  })();

  // ------------------------------------------------------------------
  // Registration validation
  // ------------------------------------------------------------------
  (function () {
    var form    = document.getElementById('registerForm');
    var btn     = document.getElementById('submitBtn');

    var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    var fields = {
      firstName:       document.getElementById('firstName'),
      lastName:        document.getElementById('lastName'),
      email:           document.getElementById('email'),
      password:        document.getElementById('password'),
      confirmPassword: document.getElementById('confirmPassword'),
      terms:           document.getElementById('terms')
    };

    var errors = {
      email:    document.getElementById('emailError'),
      password: document.getElementById('passwordError'),
      confirm:  document.getElementById('confirmError'),
      terms:    document.getElementById('termsError')
    };

    function setError(input, errorEl, show) {
      input.classList.toggle('input--error', show);
      if (errorEl) errorEl.classList.toggle('is-visible', show);
    }

    function validate() {
      var ok = true;

      // names
      if (!fields.firstName.value.trim()) { fields.firstName.classList.add('input--error'); ok = false; }
      if (!fields.lastName.value.trim())  { fields.lastName.classList.add('input--error');  ok = false; }

      // email
      var emailOk = EMAIL_RE.test(fields.email.value.trim());
      setError(fields.email, errors.email, !emailOk);
      if (!emailOk) ok = false;

      // password length
      var pwOk = fields.password.value.length >= 8;
      setError(fields.password, errors.password, !pwOk);
      if (!pwOk) ok = false;

      // confirm match
      var matchOk = fields.confirmPassword.value === fields.password.value
                    && fields.confirmPassword.value !== '';
      setError(fields.confirmPassword, errors.confirm, !matchOk);
      if (!matchOk) ok = false;

      // terms
      var termsOk = fields.terms.checked;
      errors.terms.classList.toggle('is-visible', !termsOk);
      if (!termsOk) ok = false;

      return ok;
    }

    form.addEventListener('submit', function (e) {
      if (!validate()) {
        e.preventDefault();
        var firstBad = form.querySelector('.input--error') || fields.terms;
        firstBad.focus();
      }
    });

    // Clear errors as the user corrects them
    ['firstName', 'lastName'].forEach(function (id) {
      fields[id].addEventListener('input', function () {
        this.classList.remove('input--error');
      });
    });

    fields.email.addEventListener('input', function () {
      this.classList.remove('input--error');
      errors.email.classList.remove('is-visible');
    });

    fields.password.addEventListener('input', function () {
      this.classList.remove('input--error');
      errors.password.classList.remove('is-visible');
      // re-check confirm live if it has a value
      if (fields.confirmPassword.value) {
        var match = fields.confirmPassword.value === this.value;
        setError(fields.confirmPassword, errors.confirm, !match);
      }
    });

    fields.confirmPassword.addEventListener('input', function () {
      var match = this.value === fields.password.value && this.value !== '';
      setError(this, errors.confirm, !match);
    });

    fields.terms.addEventListener('change', function () {
      if (this.checked) errors.terms.classList.remove('is-visible');
    });
  })();
</script>

@include('shared.feedback')
</body>
</html>
