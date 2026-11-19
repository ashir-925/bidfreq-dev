(function () {
  'use strict';

  // Tab switching in notes box
  document.querySelectorAll('.note-box__tab').forEach(function (tab) {
    tab.addEventListener('click', function () {
      document.querySelectorAll('.note-box__tab').forEach(function (t) { t.classList.remove('is-active'); });
      tab.classList.add('is-active');
      var ta = document.querySelector('.note-box textarea');
      ta.placeholder = tab.dataset.tab === 'internal'
        ? 'Add internal notes for your team…'
        : 'Add a note visible to the customer…';
    });
  });

  // Buttons
  document.getElementById('verifyBtn').addEventListener('click', function () {
    var btn = this;
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner spinner--sm spinner--white"></span> Verifying…';
    setTimeout(function () {
      btn.disabled = false;
      btn.innerHTML = orig;
      toast('success', 'Estimate verified', 'EST-2026-0042 is now awaiting customer approval.');
    }, 900);
  });

  document.getElementById('returnToAiBtn').addEventListener('click', function () {
    toast('warning', 'Returned to AI', 'The AI will re-analyze with your feedback and produce a new draft.');
  });
  document.getElementById('returnBtn').addEventListener('click', function () {
    toast('warning', 'Returned to AI', 'The AI will re-analyze with your feedback.');
  });
  document.getElementById('revisionBtn').addEventListener('click', function () {
    toast('info', 'Requested info', 'Customer notified: additional information needed.');
  });
  document.getElementById('saveDraftBtn').addEventListener('click', function () {
    toast('success', 'Draft saved', 'Your edits are saved. You can resume later.');
  });
  document.getElementById('previewBtn').addEventListener('click', function () {
    toast('info', 'PDF preview', 'Opening the customer-facing preview…');
  });
  document.getElementById('addLineBtn').addEventListener('click', function () {
    toast('info', 'Line item added', 'A new editable line item was appended.');
  });
  document.getElementById('postNote').addEventListener('click', function () {
    toast('success', 'Note posted', 'Your note was added to the estimate history.');
  });

  // Toast helper
  function toast(kind, title, msg) {
    var icons = {
      success: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
      info: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
      warning: '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'
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

  // Nav toggle
  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) {
      app.classList.toggle('is-nav-open');
    } else {
      app.classList.toggle('is-collapsed');
    }
  });
})();