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
    apConfirm.disabled = !(apConsent.checked && apSign.value.trim().length >= 3);
  }
  apConsent.addEventListener('change', checkApprove);
  apSign.addEventListener('input', checkApprove);

  apConfirm.addEventListener('click', function () {
    apConfirm.disabled = true;
    apConfirm.innerHTML = '<span class="spinner spinner--sm spinner--white"></span> Processing…';
    setTimeout(function () {
      close('approveModal');
      toast('success', 'Estimate approved', 'EST-2026-0040 moved to Approved Projects.');
      setTimeout(function () { window.location.href = 'projects.html'; }, 1400);
    }, 900);
  });

  // ================================================================
  // REQUEST REVISION — Enable confirm once reason + notes
  // ================================================================
  var rvReason = document.getElementById('revReason');
  var rvNotes = document.getElementById('revNotes');
  var rvConfirm = document.getElementById('revisionConfirm');

  function checkRevision() {
    rvConfirm.disabled = !(rvReason.value && rvNotes.value.trim().length >= 5);
  }
  rvReason.addEventListener('change', checkRevision);
  rvNotes.addEventListener('input', checkRevision);

  rvConfirm.addEventListener('click', function () {
    rvConfirm.disabled = true;
    rvConfirm.innerHTML = '<span class="spinner spinner--sm spinner--white"></span> Sending…';
    setTimeout(function () {
      close('revisionModal');
      toast('warning', 'Revision requested', 'Returned to estimator B. Hayes for review.');
    }, 900);
  });

  // ================================================================
  // DECLINE
  // ================================================================
  document.getElementById('declineConfirm').addEventListener('click', function () {
    this.disabled = true;
    this.innerHTML = '<span class="spinner spinner--sm spinner--white"></span> Declining…';
    var self = this;
    setTimeout(function () {
      close('declineModal');
      toast('danger', 'Estimate declined', 'EST-2026-0040 has been marked as declined.');
      self.disabled = false;
      self.innerHTML = 'Decline Estimate';
    }, 900);
  });

  // ================================================================
  // TOASTS
  // ================================================================
  function toast(kind, title, msg) {
    var icons = {
      success: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
      warning: '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
      danger: '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>'
    };
    var el = document.createElement('div');
    el.className = 'toast toast--' + kind;
    el.innerHTML =
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        (icons[kind] || icons.success) + '</svg>' +
      '<div><div class="toast__title">' + title + '</div><div class="toast__msg">' + msg + '</div></div>';
    document.getElementById('toastRegion').appendChild(el);
    setTimeout(function () {
      el.style.transition = 'opacity .3s, transform .3s';
      el.style.opacity = '0';
      el.style.transform = 'translateX(20px)';
      setTimeout(function () { el.remove(); }, 320);
    }, 3600);
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