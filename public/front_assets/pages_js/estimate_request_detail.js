(function () {
  'use strict';

  // ================================================================
  // MODAL OPENERS
  // ================================================================
  function open(id) { document.getElementById(id).classList.add('is-open'); }
  function close(id) { document.getElementById(id).classList.remove('is-open'); }

  document.getElementById('approveBtn').addEventListener('click', function () { open('approveModal'); });
  document.getElementById('revisionBtn').addEventListener('click', function () { open('revisionModal'); });
  document.getElementById('declineBtn').addEventListener('click', function () { open('declineModal'); });

  // Download PDF (both buttons)
  ['downloadPdf', 'downloadPdfSide'].forEach(function (id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('click', function () {
      toast('success', 'Downloading PDF', 'EST-2026-0040.pdf is preparing…');
    });
  });

  // Escape closes modals
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      ['approveModal', 'revisionModal', 'declineModal'].forEach(close);
    }
  });

  // ================================================================
  // APPROVE — Enable confirm once consent + signature
  // ================================================================
  var apConsent = document.getElementById('approveConsent');
  var apSign = document.getElementById('approveSign');
  var apConfirm = document.getElementById('approveConfirm');

  function checkApprove() {
    if (apConfirm.hasAttribute('aria-busy')) return;
    apConfirm.disabled = !(apConsent.checked && apSign.value.trim().length >= 3);
  }
  apConsent.addEventListener('change', checkApprove);
  apSign.addEventListener('input', checkApprove);

  apConfirm.addEventListener('click', function () {
    if (!window.bidFreqFeedback.start(apConfirm, 'Processing approval…')) return;
    setTimeout(function () {
      window.bidFreqFeedback.stop(apConfirm);
      close('approveModal');
      toast('success', 'Estimate approved', 'EST-2026-0040 moved to Approved Projects.');
      setTimeout(function () { window.location.href = window.bidFreqRoutes['projects']; }, 1400);
    }, 900);
  });

  // ================================================================
  // REQUEST REVISION — Enable confirm once reason + notes
  // ================================================================
  var rvReason = document.getElementById('revReason');
  var rvNotes = document.getElementById('revNotes');
  var rvConfirm = document.getElementById('revisionConfirm');

  function checkRevision() {
    if (rvConfirm.hasAttribute('aria-busy')) return;
    rvConfirm.disabled = !(rvReason.value && rvNotes.value.trim().length >= 5);
  }
  rvReason.addEventListener('change', checkRevision);
  rvNotes.addEventListener('input', checkRevision);

  rvConfirm.addEventListener('click', function () {
    if (!window.bidFreqFeedback.start(rvConfirm, 'Sending revision request…')) return;
    setTimeout(function () {
      window.bidFreqFeedback.stop(rvConfirm);
      close('revisionModal');
      toast('warning', 'Revision requested', 'Returned to estimator B. Hayes for review.');
    }, 900);
  });

  // ================================================================
  // DECLINE
  // ================================================================
  document.getElementById('declineConfirm').addEventListener('click', function () {
    if (!window.bidFreqFeedback.start(this, 'Declining estimate…')) return;
    var self = this;
    setTimeout(function () {
      close('declineModal');
      toast('danger', 'Estimate declined', 'EST-2026-0040 has been marked as declined.');
      window.bidFreqFeedback.stop(self);
    }, 900);
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