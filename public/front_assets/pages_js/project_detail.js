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