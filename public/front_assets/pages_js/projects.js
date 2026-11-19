(function () {
  'use strict';

  // ================================================================
  // MOCK PROJECT DATA
  // (Approved estimates that transitioned to Approved Project status)
  // ================================================================
  var PROJECTS = [
    { id:'EST-2026-0038', projId:'PRJ-2026-0021', address:'305 Meadow Rd', city:'Austin, TX', zip:'78704', trade:'HVAC', amount:4950.00, approvedDays:3, approvedLabel:'Aug 8, 2026', completed:false },
    { id:'EST-2026-0034', projId:'PRJ-2026-0020', address:'78 Lakeline Dr', city:'Austin, TX', zip:'78717', trade:'Plumbing', amount:980.00, approvedDays:4, approvedLabel:'Aug 7, 2026', completed:true },
    { id:'EST-2026-0033', projId:'PRJ-2026-0019', address:'1100 Congress Ave', city:'Austin, TX', zip:'78701', trade:'HVAC', amount:6320.00, approvedDays:6, approvedLabel:'Aug 5, 2026', completed:false },
    { id:'EST-2026-0029', projId:'PRJ-2026-0018', address:'815 Rio Grande St', city:'Austin, TX', zip:'78701', trade:'Electrical', amount:2140.00, approvedDays:9, approvedLabel:'Aug 2, 2026', completed:true },
    { id:'EST-2026-0027', projId:'PRJ-2026-0017', address:'204 W 6th St', city:'Austin, TX', zip:'78701', trade:'Drywall / Paint', amount:3680.00, approvedDays:12, approvedLabel:'Jul 30, 2026', completed:false },
    { id:'EST-2026-0024', projId:'PRJ-2026-0016', address:'68 Elm Creek Dr', city:'Round Rock, TX', zip:'78664', trade:'Roofing', amount:9450.00, approvedDays:15, approvedLabel:'Jul 27, 2026', completed:true },
    { id:'EST-2026-0021', projId:'PRJ-2026-0015', address:'1409 Manchaca Rd', city:'Austin, TX', zip:'78704', trade:'General Repair', amount:1785.00, approvedDays:18, approvedLabel:'Jul 24, 2026', completed:true },
    { id:'EST-2026-0018', projId:'PRJ-2026-0014', address:'5511 Airport Blvd', city:'Austin, TX', zip:'78751', trade:'Flooring', amount:4820.00, approvedDays:22, approvedLabel:'Jul 20, 2026', completed:false }
  ];

  var view = 'list';

  // ================================================================
  // RENDER
  // ================================================================
  function render() {
    var rows = filterAndSort(PROJECTS);
    var list = document.getElementById('projectList');
    var grid = document.getElementById('projectGrid');
    var empty = document.getElementById('emptyProjects');
    var pg = document.getElementById('projPagination');

    if (rows.length === 0) {
      list.classList.add('hidden');
      grid.classList.add('hidden');
      empty.classList.remove('hidden');
      pg.classList.add('hidden');
      return;
    }

    list.classList.remove('hidden');
    grid.classList.remove('hidden');
    empty.classList.add('hidden');
    pg.classList.remove('hidden');

    // LIST VIEW
    list.innerHTML = rows.map(function (p) {
      return '<div class="project-row" data-id="' + p.projId + '">' +
        '<div class="project-row__icon">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>' +
        '</div>' +
        '<div class="project-row__main">' +
          '<div class="project-row__top">' +
            '<span class="project-row__id">' + p.projId + '</span>' +
            '<span class="chip status--project"><span class="chip__dot"></span>Approved Project</span>' +
            (p.completed
              ? '<span class="chip chip--success"><span class="chip__dot"></span>Completed</span>'
              : '<span class="chip chip--warning"><span class="chip__dot"></span>In Progress</span>') +
          '</div>' +
          '<div class="project-row__addr">' + p.address + '</div>' +
          '<div class="project-row__meta">' +
            '<span>' + p.city + '</span>' +
            '<span class="dot"></span>' +
            '<span class="text-mono">' + p.zip + '</span>' +
            '<span class="dot"></span>' +
            '<span>' + p.trade + '</span>' +
            '<span class="dot"></span>' +
            '<span>From ' + p.id + '</span>' +
          '</div>' +
        '</div>' +
        '<div class="project-row__stats">' +
          '<div class="project-row__stat">' +
            '<div class="project-row__stat-label">Approved</div>' +
            '<div class="project-row__stat-value project-row__stat-value--muted">' + p.approvedLabel + '</div>' +
          '</div>' +
          '<div class="project-row__stat">' +
            '<div class="project-row__stat-label">Amount</div>' +
            '<div class="project-row__stat-value project-row__stat-value--amount">$' + p.amount.toLocaleString('en-US', {minimumFractionDigits:2}) + '</div>' +
          '</div>' +
        '</div>' +
        '<div class="project-row__actions">' +
          '<button class="icon-btn tooltip" data-tip="Download PDF" aria-label="Download PDF">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>' +
          '</button>' +
          '<button class="icon-btn tooltip" data-tip="Open project" aria-label="Open project">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>' +
          '</button>' +
        '</div>' +
      '</div>';
    }).join('');

    // GRID VIEW
    grid.innerHTML = rows.map(function (p) {
      return '<div class="project-card" data-id="' + p.projId + '">' +
        '<div class="project-card__head">' +
          '<div class="project-card__icon">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>' +
          '</div>' +
          '<span class="project-card__id">' + p.projId + '</span>' +
        '</div>' +
        '<div>' +
          '<div class="project-card__addr">' + p.address + '</div>' +
          '<div class="project-card__meta">' +
            '<span>' + p.city + '</span>' +
            '<span class="dot"></span>' +
            '<span class="text-mono">' + p.zip + '</span>' +
          '</div>' +
        '</div>' +
        '<div class="project-card__amount">$' + p.amount.toLocaleString('en-US', {minimumFractionDigits:2}) + '</div>' +
        '<div class="project-card__footer">' +
          '<span class="project-card__trade">' + p.trade + '</span>' +
          (p.completed
            ? '<span class="chip chip--success"><span class="chip__dot"></span>Completed</span>'
            : '<span class="chip chip--warning"><span class="chip__dot"></span>In Progress</span>') +
        '</div>' +
      '</div>';
    }).join('');

    // Update view visibility
    list.classList.toggle('hidden', view !== 'list');
    grid.classList.toggle('hidden', view !== 'grid');

    // Pagination info
    document.getElementById('projPaginationInfo').textContent =
      'Showing 1–' + rows.length + ' of 38 projects';
  }

  // ================================================================
  // FILTER + SORT
  // ================================================================
  function filterAndSort(rows) {
    var q = document.getElementById('projSearch').value.toLowerCase().trim();
    var trade = document.getElementById('projTrade').value;
    var date = document.getElementById('projDate').value;
    var sort = document.getElementById('projSort').value;

    var out = rows.slice();

    if (q) {
      out = out.filter(function (r) {
        return (r.id + ' ' + r.projId + ' ' + r.address + ' ' + r.city + ' ' + r.zip + ' ' + r.trade).toLowerCase().indexOf(q) !== -1;
      });
    }
    if (trade) out = out.filter(function (r) { return r.trade === trade; });
    if (date === 'Last 7 days') out = out.filter(function (r) { return r.approvedDays <= 7; });
    else if (date === 'Last 30 days') out = out.filter(function (r) { return r.approvedDays <= 30; });
    else if (date === 'Last 90 days') out = out.filter(function (r) { return r.approvedDays <= 90; });

    if (sort === 'newest') out.sort(function (a,b) { return a.approvedDays - b.approvedDays; });
    else if (sort === 'oldest') out.sort(function (a,b) { return b.approvedDays - a.approvedDays; });
    else if (sort === 'amount-high') out.sort(function (a,b) { return b.amount - a.amount; });
    else if (sort === 'amount-low') out.sort(function (a,b) { return a.amount - b.amount; });

    return out;
  }

  // ================================================================
  // EVENTS
  // ================================================================
  ['projSearch', 'projTrade', 'projDate', 'projSort'].forEach(function (id) {
    var el = document.getElementById(id);
    el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', render);
  });

  document.getElementById('clearProjects').addEventListener('click', function () {
    document.getElementById('projSearch').value = '';
    document.getElementById('projTrade').value = '';
    document.getElementById('projDate').value = '';
    document.getElementById('projSort').value = 'newest';
    render();
  });

  // View toggle
  document.querySelectorAll('.view-toggle button').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.view-toggle button').forEach(function (b) { b.classList.remove('is-active'); });
      btn.classList.add('is-active');
      view = btn.dataset.view;
      render();
    });
  });

  // Row / card click → project detail
  ['projectList', 'projectGrid'].forEach(function (container) {
    document.getElementById(container).addEventListener('click', function (e) {
      if (e.target.closest('.icon-btn')) return;
      var row = e.target.closest('[data-id]');
      if (row) window.location.href = window.projectDetailUrl.replace('__ID__', row.dataset.id);
    });
  });

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