(function () {
  'use strict';
  document.querySelectorAll('.settings-nav__item').forEach(function (item) {
    item.addEventListener('click', function () {
      document.querySelectorAll('.settings-nav__item').forEach(function (i) { i.classList.remove('is-active'); });
      item.classList.add('is-active');
      document.querySelectorAll('.panel-tab').forEach(function (p) { p.classList.toggle('is-active', p.dataset.panel === item.dataset.tab); });
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  });
  document.getElementById('saveGeneral').addEventListener('click', function () {
    toast('success', 'Profile updated', 'Your information was saved.');
  });
  document.getElementById('cancelGeneral').addEventListener('click', function () {
    toast('info', 'Changes discarded', '');
  });
  function toast(kind, title, msg) {
    var icons = { success:'<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>', info:'<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>', warning:'<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>' };
    var el = document.createElement('div');
    el.className = 'toast toast--' + kind;
    el.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' + (icons[kind]||icons.info) + '</svg><div><div class="toast__title">' + title + '</div><div class="toast__msg">' + msg + '</div></div>';
    document.getElementById('toastRegion').appendChild(el);
    setTimeout(function () { el.style.transition='opacity .3s, transform .3s'; el.style.opacity='0'; el.style.transform='translateX(20px)'; setTimeout(function(){ el.remove(); }, 320); }, 3000);
  }
  // Exposed globally because the buttons above call toast() from inline onclick handlers
  window.toast = toast;

  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) app.classList.toggle('is-nav-open');
    else app.classList.toggle('is-collapsed');
  });
})();