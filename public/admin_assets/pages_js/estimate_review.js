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
    if (!window.bidFreqFeedback.start(btn, 'Verifying estimate…')) return;
    setTimeout(function () {
      window.bidFreqFeedback.stop(btn);
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
    window.bidFreqFeedback.toast(kind, title, msg);
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