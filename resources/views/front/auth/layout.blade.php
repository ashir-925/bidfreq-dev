<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('front_assets/css/styles.css') }}">

<style>
/* ------------------------------------------------------------------
   AUTH LAYOUT (shared with sign-in)
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
  max-width: 400px;
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

.auth-form__link {
  font-size: var(--fs-sm);
  font-weight: var(--fw-medium);
  color: var(--blue-600);
}
.auth-form__link:hover { color: var(--blue-700); text-decoration: underline; }

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
  margin-top: var(--s-8);
  font-size: var(--fs-xs);
  color: var(--text-faint);
  text-align: center;
  line-height: var(--lh-base);
}
.auth-form__legal a { color: var(--text-muted); }

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

/* Back link */
.auth-back {
  display: inline-flex; align-items: center; gap: 6px;
  margin-top: var(--s-6);
  font-size: var(--fs-sm);
  font-weight: var(--fw-medium);
  color: var(--text-muted);
}
.auth-back svg { width: 14px; height: 14px; }
.auth-back:hover { color: var(--blue-600); }

/* Success state */
.auth-success { display: none; text-align: center; }
.auth-success.is-visible { display: block; }
.auth-success__icon {
  width: 58px; height: 58px; margin: 0 auto var(--s-5);
  border-radius: 50%;
  display: grid; place-items: center;
  background: rgba(16,185,129,.12);
  color: var(--emerald-500, #10B981);
  border: 1px solid rgba(16,185,129,.28);
}
.auth-success__icon svg { width: 26px; height: 26px; }
.auth-success__title {
  font-size: var(--fs-2xl);
  margin-bottom: var(--s-3);
  letter-spacing: -0.02em;
}
.auth-success__text {
  font-size: var(--fs-sm);
  color: var(--text-muted);
  line-height: var(--lh-base);
  margin-bottom: var(--s-6);
}
.auth-success__text strong { color: var(--text-strong); font-weight: var(--fw-semibold); }
.auth-success__actions {
  display: flex; flex-direction: column; gap: var(--s-3);
  align-items: center;
}

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
  <section class="auth-brand" aria-label="Password reset overview">

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
        Locked out?<br>
        <em>We'll get you back in.</em>
      </h1>
      <p class="auth-brand__lede">
        Password resets take about a minute. We'll email you a secure,
        single-use link so you can set a new password and get straight
        back to your estimates.
      </p>

      <div class="auth-brand__flow">
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">1</div>
          <div class="auth-brand__flow-text">
            <strong>Enter your work email</strong>
            Use the address tied to your BidFreq account.
          </div>
        </div>
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">2</div>
          <div class="auth-brand__flow-text">
            <strong>Check your inbox</strong>
            Your secure reset link arrives within a few minutes.
          </div>
        </div>
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">3</div>
          <div class="auth-brand__flow-text">
            <strong>Set a new password</strong>
            Choose something strong and unique to BidFreq.
          </div>
        </div>
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">4</div>
          <div class="auth-brand__flow-text">
            <strong>Back to bidding</strong>
            Sign in and pick up exactly where you left off.
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
  <section class="auth-panel" aria-label="Forgot password">

    @yield('content')
  </section>
</main>



@include('shared.feedback')
</body>
</html>
