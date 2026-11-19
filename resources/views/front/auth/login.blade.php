<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In · BidFreq™</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('front_assets/css/styles.css') }}">

<style>
/* ------------------------------------------------------------------
   LOGIN-SPECIFIC LAYOUT (extends design system)
   ------------------------------------------------------------------ */
.auth {
  display: grid;
  grid-template-columns: 1.05fr 1fr;
  min-height: 100vh;
}

/* --- Left: brand panel --- */
.auth-brand {
  position: relative;
  isolation: isolate;
  background: linear-gradient(155deg, var(--navy-900) 0%, var(--navy-800) 55%, var(--navy-700) 100%);
  color: #fff;
  padding: var(--s-10) var(--s-12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* ------------------------------------------------------------------
   BACKGROUND VIDEO LAYER
   ------------------------------------------------------------------ */
.auth-brand__media {
  position: absolute;
  inset: 0;
  z-index: 0;
  overflow: hidden;
  background-color: var(--navy-900);

  /* Poster fallback — shown before the video buffers, and whenever
     the video is suppressed (reduced motion, data saver, small screens) */
  background-image:
    radial-gradient(circle at 15% 20%, rgba(124,58,237,.28), transparent 45%),
    radial-gradient(circle at 85% 75%, rgba(59,130,246,.30), transparent 50%),
    url("{{ asset('front_assets/video/estimating-workflow-poster.jpg') }}");
  background-size: cover, cover, cover;
  background-position: center;
}

.auth-brand__video {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  pointer-events: none;
  opacity: 0;
  transition: opacity 1.1s var(--ease);
  /* Slight grade so the footage sits inside the navy palette */
  filter: saturate(.95) contrast(1.03) brightness(.95);
}
.auth-brand__video.is-ready { opacity: 1; }

/* Scrim — the single most important layer for text legibility.
   Two stacked gradients: vertical (top/bottom anchoring) + diagonal
   (heavier on the left where the headline sits). */
.auth-brand__media::after {
  content: "";
  position: absolute;
  inset: 0;
  pointer-events: none;
 background:
  linear-gradient(
    180deg,
    rgba(3, 20, 48, 0.92) 0%,
    rgba(4, 75, 147, 0.70) 35%,
    rgba(3, 40, 85, 0.82) 72%,
    rgba(2, 15, 35, 0.97) 100%
  ),
  linear-gradient(
    100deg,
    rgba(4, 75, 147, 0.85) 0%,
    rgba(0, 120, 255, 0.32) 52%,
    rgba(0, 180, 255, 0.50) 100%
  );
}

/* Optional: faint grid, now sitting *over* the footage.
   Delete this element entirely if you prefer a clean video. */
.auth-brand__grid {
  position: absolute;
  inset: 0;
  z-index: 1;
  pointer-events: none;
  background-image:
    linear-gradient(rgba(255,255,255,.045) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,.045) 1px, transparent 1px);
  background-size: 44px 44px;
  -webkit-mask-image: radial-gradient(circle at 50% 40%, #000 18%, transparent 70%);
  mask-image: radial-gradient(circle at 50% 40%, #000 18%, transparent 70%);
}

/* Content sits above the media layers */
.auth-brand__top,
.auth-brand__main,
.auth-brand__foot {
  position: relative;
  z-index: 2;
}

/* ------------------------------------------------------------------
   EXISTING CONTENT RULES (unchanged)
   ------------------------------------------------------------------ */
.auth-brand__top {
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
  text-shadow: 0 2px 24px rgba(0,0,0,.45);
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
  color: #C6D2E2;
  margin-bottom: var(--s-8);
  text-shadow: 0 1px 14px rgba(0,0,0,.5);
}

.auth-brand__flow {
  display: flex; flex-direction: column; gap: var(--s-4);
}
.auth-brand__flow-item {
  display: flex; align-items: center; gap: var(--s-4);
  padding: var(--s-3) var(--s-4);
  border-radius: var(--r-md);
  background: rgba(255,255,255,.055);
  border: 1px solid rgba(255,255,255,.09);
  backdrop-filter: blur(10px) saturate(120%);
  -webkit-backdrop-filter: blur(10px) saturate(120%);
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
.auth-brand__flow-text { font-size: var(--fs-sm); color: #DCE5F0; }
.auth-brand__flow-text strong { color: #fff; font-weight: var(--fw-semibold); display: block; }

.auth-brand__foot {
  margin-top: auto;
  display: flex; align-items: center; gap: var(--s-6);
  flex-wrap: wrap;
  padding-top: var(--s-6);
  border-top: 1px solid rgba(255,255,255,.10);
}
.auth-brand__trust {
  display: flex; align-items: center; gap: var(--s-2);
  font-size: var(--fs-xs);
  color: #A9B8CC;
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

/* SSO buttons */
.sso-row { display: grid; grid-template-columns: 1fr 1fr; gap: var(--s-3); }

.auth-form__row {
  display: flex; align-items: center; justify-content: space-between;
  gap: var(--s-4);
  margin-bottom: var(--s-6);
  flex-wrap: wrap;
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

/* Demo note */
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

/* Password visibility toggle */
.pw-toggle {
  position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; padding: 6px;
  color: var(--text-faint); border-radius: var(--r-sm);
  display: grid; place-items: center;
}
.pw-toggle:hover { background: var(--n-100); color: var(--text-default); }
.pw-toggle svg { width: 16px; height: 16px; }

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

  /* Mobile: drop the video stream, keep the poster frame */
  .auth-brand__video { display: none; }
}
@media (max-width: 480px) {
  .auth-brand__headline { font-size: 24px; }
  .sso-row { grid-template-columns: 1fr; }
}

/* ---- Accessibility: honour reduced-motion ---- */
@media (prefers-reduced-motion: reduce) {
  .auth-brand__video { display: none; }
  .auth-brand__video.is-ready { opacity: 0; }
  .auth-brand__flow-item { backdrop-filter: none; -webkit-backdrop-filter: none; }
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

    <!-- Background video: AI drafts → human verifies -->
    <div class="auth-brand__media" aria-hidden="true">
      <video
        class="auth-brand__video"
        id="brandVideo"
        autoplay
        muted
        loop
        playsinline
        preload="metadata"
        disablepictureinpicture
        tabindex="-1"
        poster="img/138962-770800093.mp4">
        <source src="img/138962-770800093.mp4" type="video/webm">
        <source src="img/138962-770800093.mp4"  type="video/mp4">
      </video>
    </div>

    <!-- Optional subtle grid over the footage — remove if unwanted -->
    <div class="auth-brand__grid" aria-hidden="true"></div>

    <div class="auth-brand__top">
      <img src="{{ asset('front_assets/img/logo.webp') }}" class="img-fluid logo-img" >
    </div>

    <div class="auth-brand__main">
      <h1 class="auth-brand__headline">
        Photos in.<br>
        <em>Verified estimates</em> out.
      </h1>
      <p class="auth-brand__lede">
        Submit property repair information and required photos.
        BidFreq's AI drafts the Scope of Work, a human estimator verifies it,
        and a professional proposal reaches your customer — every time.
      </p>

      <div class="auth-brand__flow">
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">1</div>
          <div class="auth-brand__flow-text">
            <strong>Upload photos &amp; description</strong>
            Minimum two repair-area photos required before estimation.
          </div>
        </div>
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">2</div>
          <div class="auth-brand__flow-text">
            <strong>AI drafts the estimate</strong>
            Scope of Work, trades, materials, quantities, and localized pricing.
          </div>
        </div>
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">3</div>
          <div class="auth-brand__flow-text">
            <strong>Human verified</strong>
            A BidFreq estimator reviews and approves every estimate.
          </div>
        </div>
        <div class="auth-brand__flow-item">
          <div class="auth-brand__flow-num">4</div>
          <div class="auth-brand__flow-text">
            <strong>Customer approves</strong>
            View, download, approve, decline, or request revisions.
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
  <section class="auth-panel" aria-label="Sign in">

    <form class="auth-form" id="loginForm" novalidate>

      <header class="auth-form__head">
        <h2 class="auth-form__title">Welcome back</h2>
        <p class="auth-form__sub">Sign in to your BidFreq account.</p>
      </header>

      <!-- Demo note -->
      <!--<div class="auth-note">-->
      <!--  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>-->
      <!--</div>-->

      <!-- Email -->
      <div class="form-group">
        <label class="label" for="email">Work email <span class="req">*</span></label>
        <div class="input-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="22 6 12 13 2 6"/></svg>
          <input class="input" id="email" name="email" type="email" autocomplete="email"
                 placeholder="you@company.com" required>
        </div>
      </div>

      <!-- Password -->
      <div class="form-group">
        <label class="label" for="password">Password <span class="req">*</span></label>
        <div class="input-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input class="input" id="password" name="password" type="password"
                 autocomplete="current-password" placeholder="Enter your password"
                 required style="padding-right:44px;">
          <button type="button" class="pw-toggle" id="pwToggle" aria-label="Show password">
            <svg id="pwIconShow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg id="pwIconHide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
      </div>

      <!-- Remember + Forgot -->
      <div class="auth-form__row">
        <label class="check">
          <input type="checkbox" name="remember" checked>
          <span class="check__box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </span>
          <span class="check__label">Keep me signed in</span>
        </label>
        <a href="forgot-password.html" class="auth-form__link">Forgot password?</a>
      </div>

      <!-- Submit -->
      <button class="btn btn--primary btn--lg btn--block" type="submit" id="submitBtn">
        Sign in
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>

      <!-- Register CTA -->
      <div class="auth-form__foot">
        Don't have an account?
        <a href="register.html">Create one</a>
      </div>

      <p class="auth-form__legal">
        By signing in you agree to the
        <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
      </p>

    </form>
  </section>
</main>

<script>
  // ------------------------------------------------------------------
  // Background video: reveal when ready, pause off-screen, respect
  // data saver and reduced motion.
  // ------------------------------------------------------------------
  (function () {
    var video = document.getElementById('brandVideo');
    if (!video) return;

    var reveal = function () { video.classList.add('is-ready'); };

    // Already buffered enough (e.g. from cache)?
    if (video.readyState >= 2) reveal();
    video.addEventListener('loadeddata', reveal);
    video.addEventListener('canplay', reveal);

    // If a source fails to load, keep the poster + gradient.
    video.addEventListener('error', function () {
      video.style.display = 'none';
    }, true);

    // Don't burn CPU on a background tab.
    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        video.pause();
      } else if (video.style.display !== 'none') {
        var p = video.play();
        if (p && p.catch) p.catch(function () {});
      }
    });

    // Respect Data Saver / very slow connections.
    var conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    if (conn && (conn.saveData || /(^|\b)(slow-)?2g$/.test(conn.effectiveType || ''))) {
      video.removeAttribute('autoplay');
      video.pause();
      video.style.display = 'none';
    }

    // Respect prefers-reduced-motion at runtime.
    var mq = window.matchMedia('(prefers-reduced-motion: reduce)');
    var applyMotionPref = function () {
      if (mq.matches) {
        video.pause();
        video.style.display = 'none';
      }
    };
    applyMotionPref();
    if (mq.addEventListener) mq.addEventListener('change', applyMotionPref);
  })();

  // ------------------------------------------------------------------
  // Password visibility toggle
  // ------------------------------------------------------------------
  (function () {
    var input    = document.getElementById('password');
    var toggle   = document.getElementById('pwToggle');
    var iconShow = document.getElementById('pwIconShow');
    var iconHide = document.getElementById('pwIconHide');

    toggle.addEventListener('click', function () {
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      iconShow.style.display = show ? 'none' : 'block';
      iconHide.style.display = show ? 'block' : 'none';
      toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  })();

  // ------------------------------------------------------------------
  // Role-aware submit
  // ------------------------------------------------------------------
  (function () {
    var form = document.getElementById('loginForm');
    var btn  = document.getElementById('submitBtn');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var role  = (form.querySelector('input[name="role"]:checked') || {}).value || 'customer';
      var email = document.getElementById('email').value.trim();
      var pass  = document.getElementById('password').value;

      if (!email || !pass) {
        if (!email) document.getElementById('email').classList.add('input--error');
        if (!pass)  document.getElementById('password').classList.add('input--error');
        return;
      }

      var original = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner spinner--sm spinner--white"></span> Signing in…';

      var routes = {
        customer:  'customer-dashboard.html',
        estimator: 'estimator-dashboard.html',
        admin:     'admin-dashboard.html'
      };

      setTimeout(function () {
        window.location.href = routes[role] || routes.customer;
      }, 700);
    });

    ['email', 'password'].forEach(function (id) {
      document.getElementById(id).addEventListener('input', function () {
        this.classList.remove('input--error');
      });
    });
  })();
</script>

</body>
</html>