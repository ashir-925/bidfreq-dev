(function () {
  'use strict';

  var NOTIFS = [
    { id:'n1', day:'Today', time:'2 min ago', kind:'danger', unread:true, category:'sla',
      title:'SLA critical: EST-2026-0051 due in 22 minutes',
      text:'High-priority estimate from <strong>Gulf Coast Realty</strong> will breach SLA. Verify now or reassign.',
      ref:'EST-2026-0051', refUrl:'estimate-review.html',
      actions:[{ label:'Review now', href:'estimate-review.html' }] },
    { id:'n2', day:'Today', time:'8 min ago', kind:'emerald', unread:true, category:'queue',
      title:'M. Alvarez verified EST-2026-0039',
      text:'Final verified total: <strong>$2,310.00</strong>. Sent to customer for approval.',
      ref:'EST-2026-0039', refUrl:'estimate-review.html' },
    { id:'n3', day:'Today', time:'23 min ago', kind:'violet', unread:true, category:'queue',
      title:'AI draft ready for EST-2026-0045',
      text:'Confidence 88% · Plumbing · 245 Sundial Blvd, San Antonio. Awaiting assignment.',
      ref:'EST-2026-0045', refUrl:'estimates.html',
      actions:[{ label:'Assign to me', href:'estimate-review.html' }] },
    { id:'n4', day:'Today', time:'1 hour ago', kind:'warning', unread:true, category:'sla',
      title:'SLA warning: EST-2026-0042 due in 1h 42m',
      text:'High-priority general repair from Oak Ridge Property Mgmt. Unassigned.',
      ref:'EST-2026-0042', refUrl:'estimate-review.html',
      actions:[{ label:'Assign now', href:'estimate-review.html' }] },
    { id:'n5', day:'Today', time:'2 hours ago', kind:'blue', unread:true, category:'customer',
      title:'New request from Oak Ridge Property Mgmt',
      text:'<strong>EST-2026-0044</strong> submitted with 6 photos. Roofing · Austin, TX.',
      ref:'EST-2026-0044', refUrl:'estimates.html' },
    { id:'n6', day:'Today', time:'3 hours ago', kind:'violet', unread:false, category:'queue',
      title:'Estimator note added to EST-2026-0038',
      text:'B. Hayes added a note about client communication preference.',
      ref:'EST-2026-0038', refUrl:'estimate-review.html' },
    { id:'n7', day:'Today', time:'5 hours ago', kind:'emerald', unread:false, category:'queue',
      title:'14 estimates verified today',
      text:'Team is on track. Avg. turnaround: <strong>1.7 days</strong> (target 2.0).', ref:'', refUrl:'' },
    { id:'n8', day:'Yesterday', time:'Yesterday · 6:12 PM', kind:'warning', unread:false, category:'customer',
      title:'Customer requested revision',
      text:'Downtown Lofts LLC requested revision on <strong>EST-2026-0048</strong>. Reason: pricing above budget.',
      ref:'EST-2026-0048', refUrl:'estimate-review.html' },
    { id:'n9', day:'Yesterday', time:'Yesterday · 4:00 PM', kind:'danger', unread:false, category:'sla',
      title:'SLA breach: EST-2026-0034',
      text:'Estimate went 45 minutes past SLA. Auto-escalated to team lead.',
      ref:'EST-2026-0034', refUrl:'estimates.html' },
    { id:'n10', day:'Yesterday', time:'Yesterday · 11:30 AM', kind:'blue', unread:false, category:'customer',
      title:'Bluebonnet Homes invited a teammate',
      text:'Alicia Reyes added <strong>dana@bluebonnet.com</strong> as a Submitter.',
      ref:'ORG-003', refUrl:'customers.html' },
    { id:'n11', day:'Aug 9, 2026', time:'2 days ago', kind:'emerald', unread:false, category:'queue',
      title:'Project completed: PRJ-2026-0020',
      text:'78 Lakeline Dr plumbing project closed. Invoice auto-sent.', ref:'PRJ-2026-0020', refUrl:'projects.html' },
    { id:'n12', day:'Aug 9, 2026', time:'2 days ago', kind:'neutral', unread:false, category:'system',
      title:'Pricing feed updated',
      text:'Localized pricing for ZIP 78745 refreshed. New rates apply to future drafts only.', ref:'', refUrl:'' },
    { id:'n13', day:'Aug 9, 2026', time:'2 days ago', kind:'violet', unread:false, category:'queue',
      title:'AI re-training completed',
      text:'Model v3.2 deployed. Estimated 4% improvement in confidence calibration.', ref:'', refUrl:'' },
    { id:'n14', day:'Aug 8, 2026', time:'3 days ago', kind:'emerald', unread:false, category:'queue',
      title:'T. Nakamura verified first estimate',
      text:'Onboarding milestone: <strong>EST-2026-0029</strong> verified in 2.4 days.', ref:'EST-2026-0029', refUrl:'estimate-review.html' },
    { id:'n15', day:'Aug 8, 2026', time:'3 days ago', kind:'warning', unread:false, category:'customer',
      title:'Customer flag raised',
      text:'Southside Property has not submitted in 32 days. Health score dropped to 42.', ref:'ORG-009', refUrl:'customers.html' },
    { id:'n16', day:'Aug 8, 2026', time:'3 days ago', kind:'neutral', unread:false, category:'system',
      title:'Security: new team member signed in',
      text:'L. Petrov signed in from Chrome on Windows · Round Rock, TX.', ref:'u6', refUrl:'estimators.html' }
  ];

  var state = { filter:'all', selected:new Set(), data: NOTIFS.map(function (n) { return Object.assign({}, n); }) };

  function iconFor(kind) {
    var icons = {
      violet:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>',
      emerald:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
      blue:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/></svg>',
      warning:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
      danger:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
      neutral:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
    };
    return icons[kind] || icons.neutral;
  }

  function render() {
    var list = document.getElementById('notifList');
    var empty = document.getElementById('emptyState');
    var rows = state.data.slice();
    if (state.filter === 'unread') rows = rows.filter(function (n) { return n.unread; });
    else if (state.filter !== 'all') rows = rows.filter(function (n) { return n.category === state.filter; });

    if (!rows.length) { list.innerHTML=''; empty.classList.remove('hidden'); updateBulk(); return; }
    empty.classList.add('hidden');

    var groups = [];
    rows.forEach(function (n) {
      var last = groups[groups.length - 1];
      if (!last || last.day !== n.day) groups.push({ day:n.day, items:[n] });
      else last.items.push(n);
    });

    list.innerHTML = groups.map(function (g) {
      return '<div class="notif-day">' + g.day + '</div>' + g.items.map(renderItem).join('');
    }).join('');
    updateBulk();
  }

  function renderItem(n) {
    var checked = state.selected.has(n.id) ? 'checked' : '';
    var unreadClass = n.unread ? ' is-unread' : '';
    var actionsHtml = '';
    if (n.actions && n.actions.length) {
      actionsHtml = '<div class="notif__actions">' + n.actions.map(function (a) {
        return '<button class="notif__action-btn" data-href="' + a.href + '">' + a.label + '</button>';
      }).join('') + '</div>';
    }
    var refChip = n.ref ? '<span class="notif__id">' + n.ref + '</span>' : '';
    return '<article class="notif' + unreadClass + '" data-id="' + n.id + '">' +
      '<div class="notif__check"><label class="check" style="margin:0"><input type="checkbox" class="notifCheck" data-id="' + n.id + '" ' + checked + '><span class="check__box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span></label></div>' +
      '<div class="notif__icon notif__icon--' + n.kind + '">' + iconFor(n.kind) + '</div>' +
      '<div class="notif__body">' +
        '<div class="notif__row"><div class="notif__title">' + n.title + '</div><div class="notif__time">' + n.time + '</div></div>' +
        '<div class="notif__text">' + n.text + '</div>' +
        '<div class="notif__meta">' + refChip + '</div>' +
        actionsHtml +
      '</div>' +
      '<div class="notif__unread-dot"></div>' +
    '</article>';
  }

  function getVisible() {
    var rows = state.data.slice();
    if (state.filter === 'unread') rows = rows.filter(function (n) { return n.unread; });
    else if (state.filter !== 'all') rows = rows.filter(function (n) { return n.category === state.filter; });
    return rows;
  }

  function updateBulk() {
    var count = state.selected.size;
    document.getElementById('markSelectedRead').disabled = count === 0;
    document.getElementById('markSelectedUnread').disabled = count === 0;
    document.getElementById('selectionInfo').classList.toggle('hidden', count === 0);
    document.getElementById('selectedCount').textContent = count;
  }

  document.querySelectorAll('.notif-tabs .tab').forEach(function (tab) {
    tab.addEventListener('click', function () {
      document.querySelectorAll('.notif-tabs .tab').forEach(function (t) { t.classList.remove('is-active'); });
      tab.classList.add('is-active');
      state.filter = tab.dataset.filter;
      render();
    });
  });

  document.getElementById('selectAll').addEventListener('change', function () {
    var rows = getVisible();
    if (this.checked) rows.forEach(function (n) { state.selected.add(n.id); });
    else rows.forEach(function (n) { state.selected.delete(n.id); });
    render();
  });

  document.getElementById('notifList').addEventListener('click', function (e) {
    var row = e.target.closest('.notif');
    if (!row) return;
    var id = row.dataset.id;
    var item = state.data.find(function (n) { return n.id === id; });
    if (!item) return;

    var check = e.target.closest('.notifCheck');
    if (check) {
      if (check.checked) state.selected.add(id); else state.selected.delete(id);
      updateBulk(); return;
    }

    var actionBtn = e.target.closest('.notif__action-btn');
    if (actionBtn) { e.stopPropagation(); item.unread = false; window.location.href = actionBtn.dataset.href; return; }

    item.unread = false;
    if (item.refUrl) window.location.href = item.refUrl; else render();
  });

  document.getElementById('markSelectedRead').addEventListener('click', function () {
    state.selected.forEach(function (id) { var n = state.data.find(function (x) { return x.id === id; }); if (n) n.unread = false; });
    state.selected.clear(); render(); toast('success','Marked as read','');
  });
  document.getElementById('markSelectedUnread').addEventListener('click', function () {
    state.selected.forEach(function (id) { var n = state.data.find(function (x) { return x.id === id; }); if (n) n.unread = true; });
    state.selected.clear(); render(); toast('info','Marked as unread','');
  });
  document.getElementById('markAllRead').addEventListener('click', function () {
    state.data.forEach(function (n) { n.unread = false; }); render(); toast('success','All caught up','');
  });

  function toast(kind, title, msg) {
    var icons = { success:'<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>', info:'<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>' };
    var el = document.createElement('div');
    el.className = 'toast toast--' + kind;
    el.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' + (icons[kind]||icons.info) + '</svg><div><div class="toast__title">' + title + '</div><div class="toast__msg">' + msg + '</div></div>';
    document.getElementById('toastRegion').appendChild(el);
    setTimeout(function () { el.style.transition='opacity .3s, transform .3s'; el.style.opacity='0'; el.style.transform='translateX(20px)'; setTimeout(function(){ el.remove(); }, 320); }, 3000);
  }

  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) app.classList.toggle('is-nav-open');
    else app.classList.toggle('is-collapsed');
  });

  render();
})();