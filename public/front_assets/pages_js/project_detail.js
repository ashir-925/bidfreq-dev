(function () {
  'use strict';

  // ================================================================
  // DOWNLOAD PDF
  // ================================================================
  ['downloadPdf', 'downloadPdfSide'].forEach(function (id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('click', function () {
      toast('success', 'Downloading project PDF', 'PRJ-2026-0021.pdf is preparing…');
    });
  });

  // ================================================================
  // MESSAGE / SUPPORT
  // ================================================================
  document.getElementById('messageBtn').addEventListener('click', function () {
    toast('info', 'Opening conversation', 'Connecting you to the project team…');
  });
  document.getElementById('supportBtn').addEventListener('click', function () {
    toast('info', 'Contacting support', 'A BidFreq agent will respond shortly.');
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
    }, 3400);
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