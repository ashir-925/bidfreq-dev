(function () {
  'use strict';

  // ================================================================
  // TAB SWITCHING
  // ================================================================
  document.querySelectorAll('.settings-nav__item').forEach(function (item) {
    item.addEventListener('click', function () {
      document.querySelectorAll('.settings-nav__item').forEach(function (i) { i.classList.remove('is-active'); });
      item.classList.add('is-active');
      var target = item.dataset.tab;
      document.querySelectorAll('.panel-tab').forEach(function (p) {
        p.classList.toggle('is-active', p.dataset.panel === target);
      });
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  });

  // ================================================================
  // SEGMENTED CONTROL
  // ================================================================
  document.querySelectorAll('.segmented').forEach(function (seg) {
    seg.querySelectorAll('button').forEach(function (btn) {
      btn.addEventListener('click', function () {
        seg.querySelectorAll('button').forEach(function (b) { b.classList.remove('is-active'); });
        btn.classList.add('is-active');
      });
    });
  });

  // ================================================================
  // SAVE GENERAL
  // ================================================================
  document.getElementById('saveGeneral').addEventListener('click', function () {
    if (!window.bidFreqFeedback.start(this, 'Saving changes…')) return;
    var self = this;
    setTimeout(function () {
      window.bidFreqFeedback.stop(self);
      toast('success', 'Profile updated', 'Your personal information was saved.');
    }, 700);
  });

  document.getElementById('cancelGeneral').addEventListener('click', function () {
    document.getElementById('p-first').value = 'Jane';
    document.getElementById('p-last').value = 'Doe';
    document.getElementById('p-email').value = 'jane@oakridge.com';
    document.getElementById('p-phone').value = '+1 (512) 555-0142';
    document.getElementById('p-title').value = 'Director of Operations';
    toast('info', 'Changes discarded', 'Your form has been reset.');
  });

  // ================================================================
  // TOASTS
  // ================================================================
  function toast(kind, title, msg) {
    window.bidFreqFeedback.toast(kind, title, msg);
  }

  // ================================================================
  // NAV TOGGLE
  // ================================================================
  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) {
      app.classList.toggle('is-nav-open');
    } else {
      app.classList.toggle('is-collapsed');
    }
  });
})();