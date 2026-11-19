(function () {
  'use strict';

  // ================================================================
  // MOCK NOTIFICATION DATA
  // ================================================================
  var NOTIFS = [
    // Today
    { id:'n1', day:'Today', time:'2 min ago', kind:'ai', unread:true, category:'estimates',
      title:'AI analysis started',
      text:'BidFreq AI is analyzing 6 photos and your description for <strong>EST-2026-0042</strong> (421 Oak Ridge Dr).',
      ref:'EST-2026-0042', refUrl:'estimate-detail.html',
      actions:[{ label:'View request', href:'estimate-detail.html' }] },

    { id:'n2', day:'Today', time:'2 hours ago', kind:'emerald', unread:true, category:'estimates',
      title:'Human-verified estimate ready for approval',
      text:'A BidFreq estimator verified the estimate for <strong>EST-2026-0040</strong> at <strong>$1,482.50</strong>. Awaiting your review.',
      ref:'EST-2026-0040', refUrl:'estimate-detail.html',
      actions:[{ label:'Review & approve', href:'estimate-detail.html', primary:true }] },

    { id:'n3', day:'Today', time:'2 hours ago', kind:'blue', unread:false, category:'estimates',
      title:'Final PDF emailed',
      text:'The final PDF for <strong>EST-2026-0040</strong> was delivered to jane@oakridge.com.',
      ref:'EST-2026-0040', refUrl:'estimate-detail.html' },

    // Yesterday
    { id:'n4', day:'Yesterday', time:'Yesterday · 4:15 PM', kind:'warning', unread:true, category:'estimates',
      title:'Updated estimate issued after your revision',
      text:'The estimator revised <strong>EST-2026-0037</strong> in response to your feedback. New total: <strong>$3,140.00</strong>.',
      ref:'EST-2026-0037', refUrl:'estimate-detail.html',
      actions:[{ label:'Review updated estimate', href:'estimate-detail.html', primary:true }] },

    { id:'n5', day:'Yesterday', time:'Yesterday · 10:02 AM', kind:'emerald', unread:false, category:'projects',
      title:'Project progressed to 60%',
      text:'HVAC installation for <strong>PRJ-2026-0021</strong> at 305 Meadow Rd is now 60% complete.',
      ref:'PRJ-2026-0021', refUrl:'project-detail.html',
      actions:[{ label:'View project', href:'project-detail.html' }] },

    { id:'n6', day:'Yesterday', time:'Yesterday · 9:14 AM', kind:'neutral', unread:false, category:'system',
      title:'Pricing source updated',
      text:'Localized pricing for ZIP <strong>78704</strong> was refreshed. New rates apply to future estimates only.',
      ref:'Pricing', refUrl:'estimate-requests.html' },

    // 2 days ago
    { id:'n7', day:'Aug 9, 2026', time:'2 days ago', kind:'warning', unread:false, category:'estimates',
      title:'Revision requested',
      text:'You requested a revision on <strong>EST-2026-0039</strong> (Electrical · 77 Birch St). It has returned to the estimator.',
      ref:'EST-2026-0039', refUrl:'estimate-detail.html' },

    { id:'n8', day:'Aug 9, 2026', time:'2 days ago', kind:'emerald', unread:false, category:'projects',
      title:'Project approved',
      text:'Your approval of <strong>EST-2026-0038</strong> created project <strong>PRJ-2026-0021</strong> at 305 Meadow Rd.',
      ref:'PRJ-2026-0021', refUrl:'project-detail.html',
      actions:[{ label:'View project', href:'project-detail.html' }] },

    { id:'n9', day:'Aug 9, 2026', time:'2 days ago', kind:'violet', unread:false, category:'estimates',
      title:'Draft estimate ready for estimator review',
      text:'AI drafted a $8,420.00 estimate for <strong>EST-2026-0041</strong> (Roofing · 88 Cypress Ln). A human estimator will verify it shortly.',
      ref:'EST-2026-0041', refUrl:'estimate-detail.html' },

    // 3 days ago
    { id:'n10', day:'Aug 8, 2026', time:'3 days ago', kind:'emerald', unread:false, category:'projects',
      title:'Project completed',
      text:'<strong>PRJ-2026-0020</strong> at 78 Lakeline Dr has been completed and closed.',
      ref:'PRJ-2026-0020', refUrl:'project-detail.html' },

    { id:'n11', day:'Aug 8, 2026', time:'3 days ago', kind:'blue', unread:false, category:'estimates',
      title:'New estimate request submitted',
      text:'You submitted <strong>EST-2026-0042</strong> for 421 Oak Ridge Dr with 6 photos.',
      ref:'EST-2026-0042', refUrl:'estimate-detail.html' },

    // Last week
    { id:'n12', day:'Aug 5, 2026', time:'6 days ago', kind:'neutral', unread:false, category:'system',
      title:'Security: new sign-in',
      text:'New sign-in from Chrome on macOS · Austin, TX. If this wasn\'t you, reset your password.',
      ref:'Security', refUrl:'profile.html',
      actions:[{ label:'Review activity', href:'profile.html' }] },

    { id:'n13', day:'Aug 5, 2026', time:'6 days ago', kind:'danger', unread:false, category:'estimates',
      title:'Estimate declined',
      text:'<strong>EST-2026-0037</strong> was declined by your organization. Reason: pricing above budget.',
      ref:'EST-2026-0037', refUrl:'estimate-detail.html' },

    { id:'n14', day:'Aug 5, 2026', time:'6 days ago', kind:'violet', unread:false, category:'estimates',
      title:'AI draft revised by estimator',
      text:'Estimator B. Hayes adjusted 3 line items on <strong>EST-2026-0038</strong> before approval. Final confidence: 94%.',
      ref:'EST-2026-0038', refUrl:'estimate-detail.html' },

    { id:'n15', day:'Aug 4, 2026', time:'7 days ago', kind:'emerald', unread:false, category:'projects',
      title:'Project scheduled',
      text:'<strong>PRJ-2026-0021</strong> is scheduled to begin Aug 15, 2026. Estimated completion Aug 22.',
      ref:'PRJ-2026-0021', refUrl:'project-detail.html' },

    { id:'n16', day:'Aug 4, 2026', time:'7 days ago', kind:'neutral', unread:false, category:'system',
      title:'Team member invited',
      text:'You invited <strong>marcus@oakridge.com</strong> to your organization as a Submitter.',
      ref:'Team', refUrl:'organization-settings.html' },

    { id:'n17', day:'Aug 4, 2026', time:'7 days ago', kind:'emerald', unread:false, category:'estimates',
      title:'Estimate approved & e-signed',
      text:'You approved <strong>EST-2026-0038</strong> ($4,950.00) with electronic signature. Project created.',
      ref:'EST-2026-0038', refUrl:'estimate-detail.html' },

    { id:'n18', day:'Aug 4, 2026', time:'7 days ago', kind:'blue', unread:false, category:'estimates',
      title:'Welcome to BidFreq',
      text:'Your account is ready. Start a new estimate request by uploading photos of the repair area.',
      ref:'Welcome', refUrl:'new-estimate-request.html',
      actions:[{ label:'Start a request', href:'new-estimate-request.html', primary:true }] }
  ];

  // ================================================================
  // STATE
  // ================================================================
  var state = {
    filter: 'all',
    selected: new Set(),
    data: NOTIFS.map(function (n) { return Object.assign({}, n); })
  };

  // ================================================================
  // ICONS
  // ================================================================
  function iconFor(kind) {
    var icons = {
      ai: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>',
      emerald: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
      violet: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
      blue: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/></svg>',
      warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
      danger: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
      neutral: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
    };
    return icons[kind] || icons.neutral;
  }

  var KIND_CLASS = {
    ai: 'violet',
    emerald: 'emerald',
    violet: 'violet',
    blue: 'blue',
    warning: 'warning',
    danger: 'danger',
    neutral: 'neutral'
  };

  // ================================================================
  // RENDER
  // ================================================================
  function render() {
    var list = document.getElementById('notifList');
    var empty = document.getElementById('emptyState');

    // Apply filter
    var rows = state.data.slice();
    if (state.filter === 'unread') rows = rows.filter(function (n) { return n.unread; });
    else if (state.filter !== 'all') rows = rows.filter(function (n) { return n.category === state.filter; });

    if (rows.length === 0) {
      list.innerHTML = '';
      empty.classList.remove('hidden');
      updateBulkActions();
      return;
    }
    empty.classList.add('hidden');

    // Group by day, preserving original day order
    var groups = [];
    rows.forEach(function (n) {
      var last = groups[groups.length - 1];
      if (!last || last.day !== n.day) {
        groups.push({ day: n.day, items: [n] });
      } else {
        last.items.push(n);
      }
    });

    list.innerHTML = groups.map(function (g) {
      return '<div class="notif-day">' + g.day + '</div>' +
        g.items.map(renderItem).join('');
    }).join('');

    updateBulkActions();
    updateSelectAll();
  }

  function renderItem(n) {
    var checked = state.selected.has(n.id) ? 'checked' : '';
    var unreadClass = n.unread ? ' is-unread' : '';
    var kindClass = KIND_CLASS[n.kind] || 'neutral';

    var actionsHtml = '';
    if (n.actions && n.actions.length) {
      actionsHtml = '<div class="notif__actions">' +
        n.actions.map(function (a) {
          return '<button class="notif__action-btn' + (a.primary ? '' : '') + '" data-href="' + a.href + '">' +
            (a.primary
              ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'
              : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>') +
            a.label +
          '</button>';
        }).join('') +
      '</div>';
    }

    var refChip = n.ref
      ? '<span class="notif__id">' + n.ref + '</span>'
      : '';

    return '<article class="notif' + unreadClass + '" data-id="' + n.id + '">' +
      '<div class="notif__check">' +
        '<label class="check" style="margin:0">' +
          '<input type="checkbox" class="notifCheck" data-id="' + n.id + '" ' + checked + '>' +
          '<span class="check__box">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' +
          '</span>' +
        '</label>' +
      '</div>' +
      '<div class="notif__icon notif__icon--' + kindClass + '">' +
        iconFor(n.kind) +
      '</div>' +
      '<div class="notif__body">' +
        '<div class="notif__row">' +
          '<div class="notif__title' + (n.unread ? '' : ' notif__title--muted') + '">' + n.title + '</div>' +
          '<div class="notif__time">' + n.time + '</div>' +
        '</div>' +
        '<div class="notif__text">' + n.text + '</div>' +
        '<div class="notif__meta">' + refChip + '</div>' +
        actionsHtml +
      '</div>' +
      '<div class="notif__unread-dot" aria-label="' + (n.unread ? 'Unread' : '') + '"></div>' +
    '</article>';
  }

  // ================================================================
  // SELECTION
  // ================================================================
  function getVisible() {
    var rows = state.data.slice();
    if (state.filter === 'unread') rows = rows.filter(function (n) { return n.unread; });
    else if (state.filter !== 'all') rows = rows.filter(function (n) { return n.category === state.filter; });
    return rows;
  }

  function updateBulkActions() {
    var count = state.selected.size;
    document.getElementById('markSelectedRead').disabled = count === 0;
    document.getElementById('markSelectedUnread').disabled = count === 0;
    var info = document.getElementById('selectionInfo');
    info.classList.toggle('hidden', count === 0);
    document.getElementById('selectedCount').textContent = count;
  }

  function updateSelectAll() {
    var visible = getVisible();
    var cb = document.getElementById('selectAll');
    if (visible.length === 0) { cb.checked = false; cb.indeterminate = false; return; }
    var allSelected = visible.every(function (n) { return state.selected.has(n.id); });
    var someSelected = visible.some(function (n) { return state.selected.has(n.id); });
    cb.checked = allSelected;
    cb.indeterminate = !allSelected && someSelected;
  }

  // ================================================================
  // EVENTS
  // ================================================================
  // Tabs
  document.querySelectorAll('.notif-tabs .tab').forEach(function (tab) {
    tab.addEventListener('click', function () {
      document.querySelectorAll('.notif-tabs .tab').forEach(function (t) { t.classList.remove('is-active'); });
      tab.classList.add('is-active');
      state.filter = tab.dataset.filter;
      render();
    });
  });

  // Select all
  document.getElementById('selectAll').addEventListener('change', function () {
    var visible = getVisible();
    if (this.checked) visible.forEach(function (n) { state.selected.add(n.id); });
    else visible.forEach(function (n) { state.selected.delete(n.id); });
    render();
  });

  // Row clicks + checkbox + actions
  document.getElementById('notifList').addEventListener('click', function (e) {
    var row = e.target.closest('.notif');
    if (!row) return;
    var id = row.dataset.id;
    var item = state.data.find(function (n) { return n.id === id; });
    if (!item) return;

    // Checkbox
    var check = e.target.closest('.notifCheck');
    if (check) {
      if (check.checked) state.selected.add(id);
      else state.selected.delete(id);
      updateBulkActions();
      updateSelectAll();
      return;
    }

    // Action button
    var actionBtn = e.target.closest('.notif__action-btn');
    if (actionBtn) {
      e.stopPropagation();
      // Mark read then navigate
      item.unread = false;
      window.location.href = actionBtn.dataset.href;
      return;
    }

    // Anywhere else in the row → mark read + navigate to ref
    item.unread = false;
    if (item.refUrl) window.location.href = item.refUrl;
    else render();
  });

  // Mark selected read / unread
  document.getElementById('markSelectedRead').addEventListener('click', function () {
    state.selected.forEach(function (id) {
      var item = state.data.find(function (n) { return n.id === id; });
      if (item) item.unread = false;
    });
    state.selected.clear();
    render();
    toast('success', 'Marked as read', 'Selected notifications updated.');
  });

  document.getElementById('markSelectedUnread').addEventListener('click', function () {
    state.selected.forEach(function (id) {
      var item = state.data.find(function (n) { return n.id === id; });
      if (item) item.unread = true;
    });
    state.selected.clear();
    render();
    toast('info', 'Marked as unread', 'Selected notifications updated.');
  });

  // Mark all read
  document.getElementById('markAllRead').addEventListener('click', function () {
    state.data.forEach(function (n) { n.unread = false; });
    render();
    toast('success', 'All caught up', 'Every notification was marked as read.');
  });

  // ================================================================
  // TOASTS
  // ================================================================
  function toast(kind, title, msg) {
    var icons = {
      success: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
      info: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>'
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
    }, 3000);
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

  // Initial render
  render();
})();