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
    window.bidFreqFeedback.toast(kind, title, msg);
  }
  // Exposed globally because the buttons above call toast() from inline onclick handlers
  window.toast = toast;

  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) app.classList.toggle('is-nav-open');
    else app.classList.toggle('is-collapsed');
  });
})();