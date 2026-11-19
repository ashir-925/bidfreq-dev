<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In | BidFreq</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* All styles are scoped to .user-login */
    .user-login, .user-login * { box-sizing: border-box; }
    .user-login {
      --ul-blue: #2864ed;
      --ul-ink: #111b2e;
      --ul-muted: #687b9b;
      --ul-border: #d1dced;
      min-height: 100vh;
      display: grid;
      grid-template-columns: minmax(0, 1.04fr) minmax(0, 1fr);
      background: #fff;
      color: var(--ul-ink);
      font-family: "Inter", Arial, sans-serif;
      overflow: hidden;
    }
    .user-login button, .user-login input { font: inherit; }
    .user-login__intro {
      position: relative;
      isolation: isolate;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      padding: 35px clamp(26px, 4.1vw, 66px) 28px;
      color: #fff;
      background:
        radial-gradient(ellipse at 70% 56%, rgba(53, 132, 207, .75), transparent 48%),
        radial-gradient(ellipse at 22% 28%, rgba(18, 91, 157, .72), transparent 48%),
        linear-gradient(145deg, #0c203d 0%, #174b7f 53%, #06182f 100%);
    }
.user-login__intro::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    /* background-color: #001c41e8; */
    background: radial-gradient(circle at 15% 20%, rgb(0 32 119), #11243dc7 45%), radial-gradient(circle at 85% 75%, rgb(59 130 246), #2d77c6a3 50%);
}

    .user-login__intro video{
        position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    pointer-events: none;
    z-index: -4;
    /* opacity: 0; */
    transition: opacity 1.1s var(--ease);
    filter: saturate(.95) contrast(1.03) brightness(.95);
    }
    .user-login__brand {
      display: inline-flex;
      align-items: center;
      align-self: flex-start;
      gap: 9px;
      padding: 7px 10px 6px 7px;
      border-radius: 4px;
      color: #10243e;
      background: #e5eaf0;
      text-decoration: none;
    }
    .user-login__brand-icon {
      display: grid;
      place-items: center;
      width: 35px;
      height: 37px;
      border: 2px solid #142a44;
      border-radius: 4px;
      color: #142a44;
    }
    .user-login__brand-name { font-size: 27px; font-weight: 800; letter-spacing: -1.2px; line-height: 1; }
    .user-login__brand-sub { display: block; margin-top: 4px; font-size: 8px; font-weight: 700; letter-spacing: 2px; text-align: center; }
    .user-login__intro-content { width: 100%; max-width: 540px; margin: auto 0; padding: 48px 0 54px; }
    .user-login__headline { margin: 0 0 17px; font-size: clamp(29px, 2.25vw, 38px); line-height: 1.15; font-weight: 800; letter-spacing: -1.5px; }
    .user-login__headline-accent { color: #9b87ef; }
    .user-login__description { max-width: 500px; margin: 0 0 27px; color: #d5e3f3; font-size: 15px; line-height: 1.55; }
    .user-login__steps { display: grid; gap: 12px; }
    .user-login__step {
      display: flex;
      align-items: center;
      gap: 13px;
      min-height: 57px;
      padding: 11px 14px;
      border: 1px solid rgba(185, 216, 246, .14);
      border-radius: 8px;
      background: rgba(93, 151, 207, .22);
      box-shadow: inset 0 1px 0 rgba(255,255,255,.025);
    }
    .user-login__step-number {
      flex: 0 0 26px; width: 26px; height: 26px; display: grid; place-items: center;
      border-radius: 50%; background: rgba(112, 171, 231, .28); color: #d9eaff; font-size: 11px; font-weight: 800;
    }
    .user-login__step:nth-child(2) .user-login__step-number { background: rgba(131, 105, 238, .38); }
    .user-login__step:nth-child(3) .user-login__step-number { background: rgba(24, 190, 153, .26); color: #5de5c1; }
    .user-login__step:nth-child(4) .user-login__step-number { background: rgba(236, 177, 53, .22); color: #ffd45c; }
    .user-login__step strong { display: block; margin-bottom: 3px; font-size: 11.5px; font-weight: 800; }
    .user-login__step span:last-child { display: block; color: #e0eaf6; font-size: 11.5px; line-height: 1.4; }
    .user-login__trust {
      display: flex; flex-wrap: wrap; gap: 22px; padding-top: 21px;
      border-top: 1px solid rgba(219, 236, 255, .14); color: #bdcde0; font-size: 10.5px;
    }
    .user-login__trust-item { display: inline-flex; align-items: center; gap: 7px; }
    .user-login__trust-item svg { color: #20d7a6; }
    .user-login__panel { display: flex; align-items: center; justify-content: center; min-width: 0; padding: 44px clamp(25px, 5vw, 78px); background: #fff; }
    .user-login__form-wrap { width: 100%; max-width: 430px; }
    .user-login__title { margin: 0 0 7px; color: #111a2c; font-size: 27px; font-weight: 800; letter-spacing: -.9px; }
    .user-login__subtitle { margin: 0 0 28px; color: var(--ul-muted); font-size: 13px; }
    .user-login__notice { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 22px; padding: 12px 13px; border: 1px solid #d8e6ff; border-radius: 8px; background: #f0f6ff; color: #2a62dc; font-size: 10.5px; line-height: 1.55; }
    .user-login__notice svg { flex: 0 0 14px; margin-top: 1px; }
    .user-login__field { margin-bottom: 19px; }
    .user-login__label { display: block; margin-bottom: 9px; color: #1b2639; font-size: 11px; font-weight: 700; }
    .user-login__required { color: #e54b5b; }
    .user-login__input-wrap { position: relative; }
    .user-login__input-icon { position: absolute; top: 50%; left: 12px; width: 15px; height: 15px; transform: translateY(-50%); color: #8da0bb; pointer-events: none; }
    .user-login__input {
      width: 100%; height: 40px; padding: 0 42px 0 37px; border: 1px solid var(--ul-border);
      border-radius: 7px; outline: none; background: #fff; color: #17233a; font-size: 12px;
      transition: border-color .18s ease, box-shadow .18s ease;
    }
    .user-login__input::placeholder { color: #8fa1bd; opacity: 1; }
    .user-login__input:focus { border-color: #6794fa; box-shadow: 0 0 0 3px rgba(40,100,237,.10); }
    .user-login__password-toggle { position: absolute; top: 50%; right: 10px; display: grid; place-items: center; width: 24px; height: 26px; padding: 0; transform: translateY(-50%); border: 0; background: transparent; color: #8da0bb; cursor: pointer; }
    .user-login__options { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin: -2px 0 22px; }
    .user-login__remember { display: inline-flex; align-items: center; gap: 9px; color: #40516b; font-size: 11px; cursor: pointer; }
    .user-login__remember input { width: 15px; height: 15px; margin: 0; accent-color: var(--ul-blue); }
    .user-login__link { color: #2864ed; font-size: 11px; font-weight: 600; text-decoration: none; }
    .user-login__link:hover { text-decoration: underline; }
    .user-login__submit { display: flex; align-items: center; justify-content: center; gap: 9px; width: 100%; height: 42px; border: 0; border-radius: 7px; background: var(--ul-blue); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 2px 3px rgba(31,91,219,.16); transition: background .2s ease, transform .2s ease; }
    .user-login__submit:hover { background: #1e55d6; }
    .user-login__submit:active { transform: translateY(1px); }
    .user-login__divider { height: 1px; margin: 27px 0 22px; border: 0; background: #e2e8f1; }
    .user-login__signup { margin: 0; text-align: center; color: #6c7f9d; font-size: 11px; }
    .user-login__legal { margin: 28px 0 0; color: #8b9bb4; text-align: center; font-size: 10px; line-height: 1.6; }
    .user-login__message { display: none; margin: 13px 0 0; color: #2463d9; font-size: 12px; line-height: 1.5; }
    .user-login__message.is-visible { display: block; }
    @media (max-width: 900px) {
      .user-login { grid-template-columns: minmax(0, .92fr) minmax(0, 1fr); }
      .user-login__intro { padding-inline: 28px; }
      .user-login__description { font-size: 13px; }
      .user-login__step { padding: 10px; gap: 10px; }
      .user-login__step strong, .user-login__step span:last-child { font-size: 10.5px; }
      .user-login__panel { padding-inline: 28px; }
    }
    @media (max-width: 680px) {
      .user-login { display: block; overflow: visible; }
      .user-login__intro { min-height: auto; padding: 23px 22px 24px; }
      .user-login__brand-name { font-size: 23px; }
      .user-login__intro-content { margin: 34px 0 27px; padding: 0; }
      .user-login__headline { font-size: 30px; }
      .user-login__description { margin-bottom: 20px; }
      .user-login__steps { grid-template-columns: 1fr 1fr; gap: 9px; }
      .user-login__step { align-items: flex-start; min-height: 94px; padding: 11px; }
      .user-login__step strong, .user-login__step span:last-child { font-size: 10px; }
      .user-login__trust { gap: 12px 17px; padding-top: 16px; }
      .user-login__panel { padding: 38px 22px 42px; }
      .user-login__form-wrap { max-width: 460px; }
      .user-login__title { font-size: 26px; }
    }
    @media (max-width: 380px) {
      .user-login__steps { grid-template-columns: 1fr; }
      .user-login__step { min-height: 62px; }
    }
  </style>
</head>
<body style="margin:0">
  <main class="user-login">
    <section class="user-login__intro" aria-label="BidFreq introduction">
        <video src="img/138962-770800093.mp4" autoplay loop muted></video>
      <a class="user-login__brand" href="#" aria-label="BidFreq home">
        <span class="user-login__brand-icon" aria-hidden="true">
          <svg viewBox="0 0 28 28" width="27" height="27" fill="none">
            <path d="M5 5h15M5 10h10M5 15h7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M3 21h5l3-5 4 7 4-9 3 7h3" stroke="#2876c8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </span>
        <span><span class="user-login__brand-name">BidFreq</span><span class="user-login__brand-sub">POWERED BY AI</span></span>
      </a>

      <div class="user-login__intro-content">
        <h1 class="user-login__headline">Your Projects. <br><span class="user-login__headline-accent">Your Estimates. </span> All in One Place.</h1>
        <p class="user-login__description">Sign in to access your BidFreq account, manage property repair requests, review estimates, and track project progress from one convenient dashboard.</p>
        <div class="user-login__steps">
          <div class="user-login__step"><span class="user-login__step-number">1</span><div><strong>Manage Your Projects </strong><span>Access your property repair requests and project details.</span></div></div>
          <div class="user-login__step"><span class="user-login__step-number">2</span><div><strong>Review Estimates</strong><span>View AI-generated estimates verified by professional estimators.</span></div></div>
          <div class="user-login__step"><span class="user-login__step-number">3</span><div><strong>Track Progress </strong><span>Stay updated on estimate status and customer approvals.</span></div></div>
          <div class="user-login__step"><span class="user-login__step-number">4</span><div><strong>Secure Access</strong><span>Keep your account and project information protected.</span></div></div>
        </div>
      </div>

      <div class="user-login__trust">
        <span class="user-login__trust-item"><svg viewBox="0 0 16 16" width="13" height="13" fill="none"><path d="M8 1.5 13 3v4.1c0 3.2-2 5.7-5 7.4-3-1.7-5-4.2-5-7.4V3l5-1.5Z" stroke="currentColor" stroke-width="1.5"/></svg>SOC 2 aligned</span>
        <span class="user-login__trust-item"><svg viewBox="0 0 16 16" width="13" height="13" fill="none"><rect x="3" y="7" width="10" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M5 7V4.8a3 3 0 0 1 6 0V7" stroke="currentColor" stroke-width="1.5"/></svg>Encrypted at rest</span>
        <span class="user-login__trust-item"><svg viewBox="0 0 16 16" width="13" height="13" fill="none"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5"/><path d="m5 8 2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>Human verified</span>
      </div>
    </section>

    <section class="user-login__panel" aria-label="Sign in">
      <div class="user-login__form-wrap">
        <h2 class="user-login__title">Welcome back</h2>
        <p class="user-login__subtitle">Sign in to your BidFreq account to continue.</p>
        <!--<div class="user-login__notice">-->
        <!--  <svg viewBox="0 0 16 16" width="14" height="14" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5"/><path d="M8 7v3M8 4.7h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>-->
        <!--  <span>Select a role to view the matching dashboard. This is a UI preview.</span>-->
        <!--</div>-->

        <form class="user-login__form" id="userLoginForm">
          <div class="user-login__field">
            <label class="user-login__label" for="userLoginEmail">Email <span class="user-login__required">*</span></label>
            <div class="user-login__input-wrap">
              <svg class="user-login__input-icon" viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="2.5" y="4" width="15" height="12" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="m3.5 5 6.5 5 6.5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <input class="user-login__input" type="email" id="userLoginEmail" name="email" placeholder="you@company.com" autocomplete="username" required>
            </div>
          </div>

          <div class="user-login__field">
            <label class="user-login__label" for="userLoginPassword">Password <span class="user-login__required">*</span></label>
            <div class="user-login__input-wrap">
              <svg class="user-login__input-icon" viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="3.5" y="8.5" width="13" height="9" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M6.5 8.5V6a3.5 3.5 0 0 1 7 0v2.5" stroke="currentColor" stroke-width="1.5"/></svg>
              <input class="user-login__input" type="password" id="userLoginPassword" name="password" placeholder="Enter your password" autocomplete="current-password" required>
              <button class="user-login__password-toggle" type="button" id="userPasswordToggle" aria-label="Show password">
                <svg viewBox="0 0 20 20" width="15" height="15" fill="none" aria-hidden="true"><path d="M2 10s2.8-5 8-5 8 5 8 5-2.8 5-8 5-8-5-8-5Z" stroke="currentColor" stroke-width="1.4"/><circle cx="10" cy="10" r="2.2" stroke="currentColor" stroke-width="1.4"/></svg>
              </button>
            </div>
          </div>
          
          <div class="user-login__options">
            <label class="user-login__remember"><input type="checkbox" name="remember" checked> <span>Keep me signed in</span></label>
            <a class="user-login__link" href="{{ route('admin.forgot-password') }}" id="userForgotPassword">Forgot password?</a>
          </div>
          <a class="user-login__submit" href="dashboard.html">Sign in <svg viewBox="0 0 20 20" width="15" height="15" fill="none" aria-hidden="true"><path d="M4 10h11M10 5l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
          <!-- <button class="user-login__submit" type="submit">Sign in <svg viewBox="0 0 20 20" width="15" height="15" fill="none" aria-hidden="true"><path d="M4 10h11M10 5l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></button> -->
          <p class="user-login__message" id="userLoginMessage" role="status"></p>
        </form>

        <hr class="user-login__divider">
        <!--<p class="user-login__signup">Don't have an account? <a class="user-login__link" href="#" id="userCreateAccount">Create one</a></p>-->
        <!--<p class="user-login__legal">By signing in you agree to the <a class="user-login__link" href="#">Terms of Service</a> and <a class="user-login__link" href="#">Privacy Policy</a>.</p>-->
      </div>
    </section>
  </main>

  <script>
    (() => {
      const root = document.querySelector('.user-login');
      if (!root) return;

      const password = root.querySelector('#userLoginPassword');
      const toggle = root.querySelector('#userPasswordToggle');
      const form = root.querySelector('#userLoginForm');
      const message = root.querySelector('#userLoginMessage');

      toggle.addEventListener('click', () => {
        const show = password.type === 'password';
        password.type = show ? 'text' : 'password';
        toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
      });

      form.addEventListener('submit', (event) => {
        event.preventDefault();
        message.textContent = 'UI preview only — connect this form to your authentication endpoint to sign in.';
        message.classList.add('is-visible');
      });

      root.querySelector('#userForgotPassword').addEventListener('click', (event) => {
        event.preventDefault();
        message.textContent = 'Connect this link to your password recovery page.';
        message.classList.add('is-visible');
      });

      root.querySelector('#userCreateAccount').addEventListener('click', (event) => {
        event.preventDefault();
        message.textContent = 'Connect this link to your account registration page.';
        message.classList.add('is-visible');
      });
    })();
  </script>
</body>
</html>
