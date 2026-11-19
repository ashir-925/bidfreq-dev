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
    var original = this.innerHTML;
    this.disabled = true;
    this.innerHTML = '<span class="spinner spinner--sm spinner--white"></span> Saving…';
    var self = this;
    setTimeout(function () {
      self.disabled = false;
      self.innerHTML = original;
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
    var icons = {
      success: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
      info: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
      warning: '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
      danger: '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>'
    };
    var el = document.createElement('div');
    el.className = 'toast toast--' + kind;
    el.innerHTML =
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        (icons[kind] || icons.info) + '</svg>' +
      '<div><div class="toast__title">' + title + '</div><div class="toast__msg">' + msg + '</div></div>';
    document.getElementById('toastRegion').appendChild(el);
    setTimeout(function () {
      el.style.transition = 'opacity .3s, transform .3s';
      el.style.opacity = '0';
      el.style.transform = 'translateX(20px)';
      setTimeout(function () { el.remove(); }, 320);
    }, 3200);
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