(function () {
  'use strict';

  // ================================================================
  // MOCK DATA
  // ================================================================
  var DATA = [
    { id:'EST-2026-0042', address:'421 Oak Ridge Dr', city:'Austin, TX', zip:'78745', customer:'Oak Ridge Property Mgmt', trade:'General Repair', status:'ai-processing', statusLabel:'AI Draft Ready', priority:'high', amount:2840.00, estimator:'unassigned', sla:'1h 42m', slaMin:102, icon:'violet' },
    { id:'EST-2026-0041', address:'88 Cypress Ln', city:'Dallas, TX', zip:'75204', customer:'Lone Star Realty', trade:'Roofing', status:'in-review', statusLabel:'In Review', priority:'high', amount:8420.00, estimator:'B. Hayes', sla:'3h 08m', slaMin:188, icon:'warning' },
    { id:'EST-2026-0043', address:'12 Cedar Ct', city:'Fort Worth, TX', zip:'76102', customer:'Bluebonnet Homes', trade:'Electrical', status:'ai-processing', statusLabel:'AI Draft Ready', priority:'med', amount:1720.00, estimator:'unassigned', sla:'6h 20m', slaMin:380, icon:'violet' },
    { id:'EST-2026-0044', address:'900 Riverbend Way', city:'Austin, TX', zip:'78730', customer:'Oak Ridge Property Mgmt', trade:'Drywall / Paint', status:'ai-processing', statusLabel:'AI Draft Ready', priority:'med', amount:1875.00, estimator:'unassigned', sla:'8h 45m', slaMin:525, icon:'violet' },
    { id:'EST-2026-0045', address:'245 Sundial Blvd', city:'San Antonio, TX', zip:'78205', customer:'Vista Realty Co.', trade:'Plumbing', status:'ai-processing', statusLabel:'AI Draft Ready', priority:'low', amount:980.00, estimator:'unassigned', sla:'14h 10m', slaMin:850, icon:'violet' },
    { id:'EST-2026-0046', address:'601 Congress Ave', city:'Austin, TX', zip:'78701', customer:'Capitol Holdings', trade:'HVAC', status:'in-review', statusLabel:'In Review', priority:'high', amount:5420.00, estimator:'M. Alvarez', sla:'0h 48m', slaMin:48, icon:'warning' },
    { id:'EST-2026-0047', address:'77 Maple St', city:'Plano, TX', zip:'75024', customer:'Northgate Partners', trade:'General Repair', status:'awaiting-customer', statusLabel:'Awaiting Customer', priority:'med', amount:2140.00, estimator:'B. Hayes', sla:'1d 06h', slaMin:1800, icon:'blue' },
    { id:'EST-2026-0048', address:'1409 Manchaca Rd', city:'Austin, TX', zip:'78704', customer:'Oak Ridge Property Mgmt', trade:'Flooring', status:'revision', statusLabel:'Revision Requested', priority:'med', amount:3680.00, estimator:'R. Kapoor', sla:'2d 03h', slaMin:3060, icon:'warning' },
    { id:'EST-2026-0049', address:'310 W 6th St', city:'Austin, TX', zip:'78701', customer:'Downtown Lofts LLC', trade:'Drywall / Paint', status:'in-review', statusLabel:'In Review', priority:'low', amount:1560.00, estimator:'T. Nakamura', sla:'1d 18h', slaMin:2520, icon:'warning' },
    { id:'EST-2026-0050', address:'8821 Bee Caves Rd', city:'Austin, TX', zip:'78746', customer:'Hill Country Homes', trade:'Roofing', status:'awaiting-customer', statusLabel:'Awaiting Customer', priority:'low', amount:11200.00, estimator:'B. Hayes', sla:'3d 02h', slaMin:4440, icon:'blue' },
    { id:'EST-2026-0051', address:'455 Post Oak Blvd', city:'Houston, TX', zip:'77056', customer:'Gulf Coast Realty', trade:'Plumbing', status:'ai-processing', statusLabel:'AI Draft Ready', priority:'high', amount:null, estimator:'unassigned', sla:'0h 22m', slaMin:22, icon:'violet' },
    { id:'EST-2026-0052', address:'2100 Barton Springs', city:'Austin, TX', zip:'78704', customer:'Southside Property', trade:'Electrical', status:'in-review', statusLabel:'In Review', priority:'med', amount:2890.00, estimator:'M. Alvarez', sla:'5h 30m', slaMin:330, icon:'warning' },
    { id:'EST-2026-0053', address:'1502 Palm Ave', city:'Houston, TX', zip:'77002', customer:'Bayou Properties', trade:'HVAC', status:'awaiting-customer', statusLabel:'Awaiting Customer', priority:'med', amount:4950.00, estimator:'R. Kapoor', sla:'2d 12h', slaMin:3600, icon:'blue' },
    { id:'EST-2026-0054', address:'68 Elm Creek Dr', city:'Round Rock, TX', zip:'78664', customer:'Northgate Partners', trade:'Flooring', status:'revision', statusLabel:'Revision Requested', priority:'low', amount:1340.00, estimator:'T. Nakamura', sla:'1d 22h', slaMin:2760, icon:'warning' }
  ];

  var state = {
    status: 'queue',
    search: '',
    trade: '',
    priority: '',
    estimator: '',
    sort: 'sla',
    selected: new Set(),
    page: 1,
    perPage: 10
  };

  // ================================================================
  // HELPERS
  // ================================================================
  function statusChip(s) {
    var map = {
      'ai-processing': 'status--ai-processing',
      'in-review': 'status--in-review',
      'awaiting-customer': 'status--awaiting-customer',
      'revision': 'status--revision',
      'approved': 'status--approved',
      'declined': 'status--declined'
    };
    var labels = {
      'ai-processing': 'AI Draft Ready',
      'in-review': 'In Review',
      'awaiting-customer': 'Awaiting Customer',
      'revision': 'Revision Requested',
      'approved': 'Approved',
      'declined': 'Declined'
    };
    return '<span class="chip ' + (map[s] || 'status--draft') + '"><span class="chip__dot"></span>' + (labels[s] || s) + '</span>';
  }

  function iconForRow(icon) {
    var cls = icon ? ' est-id-cell__icon--' + icon : '';
    return '<div class="est-id-cell__icon' + cls + '">' +
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>' +
        '<polyline points="14 2 14 8 20 8"/>' +
      '</svg>' +
    '</div>';
  }

  function formatAmount(n) {
    if (n == null) return '<span class="amount-cell amount-cell--muted">Analyzing…</span>';
    return '<span class="amount-cell">$' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</span>';
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c];
    });
  }

  function priorityBadge(p) {
    var labels = { high: 'High', med: 'Med', low: 'Low' };
    return '<span class="pri pri--' + p + '"><span class="pri__dot"></span>' + labels[p] + '</span>';
  }

  function estimatorCell(name) {
    if (!name || name === 'unassigned') {
      return '<span class="estimator-chip estimator-chip--unassigned">' +
        '<span class="estimator-chip__avatar">?</span>' +
        'Unassigned' +
      '</span>';
    }
    var initials = name.split(/\s+/).map(function (p) { return p[0]; }).join('').slice(0, 2).toUpperCase();
    return '<span class="estimator-chip">' +
      '<span class="estimator-chip__avatar">' + initials + '</span>' +
      escapeHtml(name) +
    '</span>';
  }

  // ================================================================
  // FILTER + SORT
  // ================================================================
  function getFiltered() {
    var rows = DATA.slice();

    if (state.status === 'queue') {
      rows = rows.filter(function (r) { return r.status === 'ai-processing' || r.status === 'in-review'; });
    } else if (state.status !== 'all') {
      rows = rows.filter(function (r) { return r.status === state.status; });
    }
    if (state.trade) rows = rows.filter(function (r) { return r.trade === state.trade; });
    if (state.priority) rows = rows.filter(function (r) { return r.priority === state.priority; });
    if (state.estimator === 'unassigned') rows = rows.filter(function (r) { return r.estimator === 'unassigned'; });
    else if (state.estimator) rows = rows.filter(function (r) { return r.estimator === state.estimator; });
    if (state.search) {
      var q = state.search.toLowerCase();
      rows = rows.filter(function (r) {
        return (r.id + ' ' + r.address + ' ' + r.city + ' ' + r.zip + ' ' + r.customer + ' ' + r.trade).toLowerCase().indexOf(q) !== -1;
      });
    }

    if (state.sort === 'sla') rows.sort(function (a,b) { return a.slaMin - b.slaMin; });
    else if (state.sort === 'newest') rows.sort(function (a,b) { return b.id.localeCompare(a.id); });
    else if (state.sort === 'oldest') rows.sort(function (a,b) { return a.id.localeCompare(b.id); });
    else if (state.sort === 'amount-high') rows.sort(function (a,b) { return (b.amount||0) - (a.amount||0); });

    return rows;
  }

  // ================================================================
  // RENDER
  // ================================================================
  function render() {
    var rows = getFiltered();
    var body = document.getElementById('requestsBody');
    var empty = document.getElementById('emptyState');
    var wrap = document.querySelector('.table-scroll');
    var pg = document.getElementById('pagination');

    var start = (state.page - 1) * state.perPage;
    var paged = rows.slice(start, start + state.perPage);

    if (rows.length === 0) {
      body.innerHTML = '';
      wrap.classList.add('hidden');
      pg.classList.add('hidden');
      empty.classList.remove('hidden');
    } else {
      wrap.classList.remove('hidden');
      pg.classList.remove('hidden');
      empty.classList.add('hidden');

      body.innerHTML = paged.map(function (r) {
        var checked = state.selected.has(r.id) ? 'checked' : '';
        var slaClass = r.slaMin < 180 ? 'queue-item__sla--warn' : '';
        return '<tr data-id="' + r.id + '">' +
          '<td class="table__checkbox">' +
            '<label class="check" style="margin:0">' +
              '<input type="checkbox" class="rowCheck" data-id="' + r.id + '" ' + checked + '>' +
              '<span class="check__box">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' +
              '</span>' +
            '</label>' +
          '</td>' +
          '<td>' +
            '<div class="est-id-cell">' +
              iconForRow(r.icon) +
              '<div class="est-id-cell__text">' +
                '<div class="est-id-cell__id">' + r.id + '</div>' +
                '<div class="est-id-cell__time">' + priorityBadge(r.priority) + '</div>' +
              '</div>' +
            '</div>' +
          '</td>' +
          '<td>' +
            '<div class="property-cell">' +
              '<div class="property-cell__addr">' + escapeHtml(r.address) + '</div>' +
              '<div class="property-cell__meta">' +
                '<span>' + escapeHtml(r.city) + '</span>' +
                '<span class="dot"></span>' +
                '<span class="text-mono">' + r.zip + '</span>' +
              '</div>' +
            '</div>' +
          '</td>' +
          '<td class="text-sm">' + escapeHtml(r.customer) + '</td>' +
          '<td class="text-sm">' + r.trade + '</td>' +
          '<td>' + statusChip(r.status) + '</td>' +
          '<td>' + estimatorCell(r.estimator) + '</td>' +
          '<td class="num">' + formatAmount(r.amount) + '</td>' +
          '<td class="text-xs ' + slaClass + '" style="font-variant-numeric:tabular-nums">' + r.sla + '</td>' +
          '<td class="actions">' +
            '<div class="row-menu">' +
              '<button class="row-menu__trigger" aria-label="Row actions">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>' +
              '</button>' +
              '<div class="row-menu__panel">' +
                '<button class="row-menu__item" data-action="open">' +
                  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>' +
                  'Open review' +
                '</button>' +
                '<button class="row-menu__item" data-action="assign">' +
                  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>' +
                  'Assign to me' +
                '</button>' +
                '<button class="row-menu__item" data-action="download">' +
                  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>' +
                  'Download PDF' +
                '</button>' +
                '<hr class="row-menu__divider">' +
                '<button class="row-menu__item row-menu__item--danger" data-action="return">' +
                  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>' +
                  'Return to AI' +
                '</button>' +
              '</div>' +
            '</div>' +
          '</td>' +
        '</tr>';
      }).join('');

      document.getElementById('paginationInfo').textContent =
        'Showing ' + (start + 1) + '–' + (start + paged.length) + ' of ' + rows.length + ' estimates';
    }

    document.getElementById('selectAll').checked = rows.length > 0 && rows.every(function (r) { return state.selected.has(r.id); });
    updateBulkBar();
  }

  function updateBulkBar() {
    var bar = document.getElementById('bulkBar');
    var count = document.getElementById('bulkCount');
    count.textContent = state.selected.size;
    bar.classList.toggle('is-visible', state.selected.size > 0);
  }

  // ================================================================
  // EVENTS
  // ================================================================
  document.getElementById('selectAll').addEventListener('change', function () {
    var rows = getFiltered();
    if (this.checked) rows.forEach(function (r) { state.selected.add(r.id); });
    else rows.forEach(function (r) { state.selected.delete(r.id); });
    render();
  });

  document.getElementById('clearSelection').addEventListener('click', function () {
    state.selected.clear();
    render();
  });

  document.getElementById('requestsBody').addEventListener('click', function (e) {
    var row = e.target.closest('tr[data-id]');
    if (!row) return;
    var id = row.dataset.id;

    var check = e.target.closest('.rowCheck');
    if (check) {
      if (check.checked) state.selected.add(id);
      else state.selected.delete(id);
      updateBulkBar();
      return;
    }

    var trigger = e.target.closest('.row-menu__trigger');
    if (trigger) {
      e.stopPropagation();
      document.querySelectorAll('.row-menu').forEach(function (m) {
        if (m !== trigger.closest('.row-menu')) m.classList.remove('is-open');
      });
      trigger.closest('.row-menu').classList.toggle('is-open');
      return;
    }

    var item = e.target.closest('.row-menu__item');
    if (item) {
      e.stopPropagation();
      var action = item.dataset.action;
      if (action === 'open' || action === 'assign') {
        window.location.href = window.estimateReviewUrl.replace('__ID__', encodeURIComponent(id));
      } else if (action === 'download') {
        window.location.href = window.estimateReviewUrl.replace('__ID__', encodeURIComponent(id));
      }
      return;
    }

    if (!e.target.closest('.check, .row-menu')) {
      window.location.href = window.estimateReviewUrl.replace('__ID__', encodeURIComponent(id));
    }
  });

  document.addEventListener('click', function () {
    document.querySelectorAll('.row-menu').forEach(function (m) { m.classList.remove('is-open'); });
  });

  // Status tiles
  document.querySelectorAll('.status-tile').forEach(function (tile) {
    tile.addEventListener('click', function () {
      document.querySelectorAll('.status-tile').forEach(function (t) { t.classList.remove('is-active'); });
      tile.classList.add('is-active');
      state.status = tile.dataset.status;
      state.page = 1;
      render();
    });
  });

  // Filters
  document.getElementById('tableSearch').addEventListener('input', function () {
    state.search = this.value.trim(); state.page = 1; render();
  });
  document.getElementById('tradeFilter').addEventListener('change', function () {
    state.trade = this.value; state.page = 1; render();
  });
  document.getElementById('priorityFilter').addEventListener('change', function () {
    state.priority = this.value; state.page = 1; render();
  });
  document.getElementById('estimatorFilter').addEventListener('change', function () {
    state.estimator = this.value; state.page = 1; render();
  });
  document.getElementById('sortFilter').addEventListener('change', function () {
    state.sort = this.value; render();
  });

  document.getElementById('clearFilters').addEventListener('click', clearAll);
  document.getElementById('clearEmptyFilters').addEventListener('click', clearAll);

  function clearAll() {
    state.search = ''; state.trade = ''; state.priority = ''; state.estimator = '';
    state.sort = 'sla'; state.status = 'queue'; state.page = 1;
    document.getElementById('tableSearch').value = '';
    document.getElementById('tradeFilter').value = '';
    document.getElementById('priorityFilter').value = '';
    document.getElementById('estimatorFilter').value = '';
    document.getElementById('sortFilter').value = 'sla';
    document.querySelectorAll('.status-tile').forEach(function (t) {
      t.classList.toggle('is-active', t.dataset.status === 'queue');
    });
    render();
  }

  document.getElementById('assignNextBtn').addEventListener('click', function () {
    window.location.href = window.estimateReviewUrl.replace('__ID__', encodeURIComponent(id));
  });

  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) {
      app.classList.toggle('is-nav-open');
    } else {
      app.classList.toggle('is-collapsed');
    }
  });

  render();
})();