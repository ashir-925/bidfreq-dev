(function () {
  'use strict';

  // ================================================================
  // MOCK DATA (would come from API in production)
  // ================================================================
  var DATA = [
    { id:'EST-2026-0042', address:'421 Oak Ridge Dr', city:'Austin, TX', zip:'78745', trade:'General Repair', status:'ai-processing', statusLabel:'AI Processing', amount:null, updated:2, updatedLabel:'2 min ago', photos:6, icon:'violet' },
    { id:'EST-2026-0041', address:'88 Cypress Ln', city:'Dallas, TX', zip:'75204', trade:'Roofing', status:'in-review', statusLabel:'In Estimator Review', amount:8420.00, updated:60, updatedLabel:'1 hr ago', photos:8, icon:'warning' },
    { id:'EST-2026-0040', address:'1502 Palm Ave', city:'Houston, TX', zip:'77002', trade:'Plumbing', status:'awaiting-customer', statusLabel:'Awaiting Customer', amount:1482.50, updated:120, updatedLabel:'2 hr ago', photos:5, icon:'blue' },
    { id:'EST-2026-0039', address:'77 Birch St', city:'Plano, TX', zip:'75024', trade:'Electrical', status:'revision', statusLabel:'Revision Requested', amount:2310.00, updated:2880, updatedLabel:'2 days ago', photos:4, icon:'warning' },
    { id:'EST-2026-0038', address:'305 Meadow Rd', city:'Austin, TX', zip:'78704', trade:'HVAC', status:'approved', statusLabel:'Approved', amount:4950.00, updated:4320, updatedLabel:'3 days ago', photos:7, icon:'emerald' },
    { id:'EST-2026-0037', address:'12 Cedar Ct', city:'Fort Worth, TX', zip:'76102', trade:'Flooring', status:'declined', statusLabel:'Declined', amount:3140.00, updated:7200, updatedLabel:'5 days ago', photos:3, icon:'danger' },
    { id:'EST-2026-0036', address:'901 Riverbend Way', city:'Austin, TX', zip:'78730', trade:'Drywall / Paint', status:'in-review', statusLabel:'In Estimator Review', amount:1875.00, updated:1440, updatedLabel:'Yesterday', photos:6, icon:'warning' },
    { id:'EST-2026-0035', address:'245 Sundial Blvd', city:'San Antonio, TX', zip:'78205', trade:'General Repair', status:'in-review', statusLabel:'In Estimator Review', amount:2240.00, updated:1500, updatedLabel:'Yesterday', photos:5, icon:'warning' },
    { id:'EST-2026-0034', address:'78 Lakeline Dr', city:'Austin, TX', zip:'78717', trade:'Plumbing', status:'approved', statusLabel:'Approved', amount:980.00, updated:5760, updatedLabel:'4 days ago', photos:4, icon:'emerald' },
    { id:'EST-2026-0033', address:'1100 Congress Ave', city:'Austin, TX', zip:'78701', trade:'HVAC', status:'approved', statusLabel:'Approved', amount:6320.00, updated:8640, updatedLabel:'6 days ago', photos:9, icon:'emerald' },
    { id:'EST-2026-0032', address:'2201 Barton Springs', city:'Austin, TX', zip:'78704', trade:'Electrical', status:'in-review', statusLabel:'In Estimator Review', amount:3480.00, updated:10080, updatedLabel:'7 days ago', photos:5, icon:'warning' },
    { id:'EST-2026-0031', address:'455 Post Oak Blvd', city:'Houston, TX', zip:'77056', trade:'Flooring', status:'awaiting-customer', statusLabel:'Awaiting Customer', amount:2660.00, updated:11520, updatedLabel:'8 days ago', photos:6, icon:'blue' }
  ];

  var state = {
    status: 'all',
    search: '',
    trade: '',
    date: '',
    sort: 'newest',
    selected: new Set(),
    page: 1,
    perPage: 8
  };

  // ================================================================
  // STATUS HELPERS
  // ================================================================
  function statusChip(s) {
    var map = {
      'draft':             'status--draft',
      'submitted':         'status--submitted',
      'ai-processing':     'status--ai-processing',
      'in-review':         'status--in-review',
      'awaiting-customer': 'status--awaiting-customer',
      'revision':          'status--revision',
      'approved':          'status--approved',
      'declined':          'status--declined',
      'project':           'status--project'
    };
    return '<span class="chip ' + (map[s] || 'status--draft') + '"><span class="chip__dot"></span>' + labelFor(s) + '</span>';
  }

  function labelFor(s) {
    return {
      'draft': 'Draft',
      'submitted': 'Submitted',
      'ai-processing': 'AI Processing',
      'in-review': 'In Estimator Review',
      'awaiting-customer': 'Awaiting Customer',
      'revision': 'Revision Requested',
      'approved': 'Approved',
      'declined': 'Declined',
      'project': 'Approved Project'
    }[s] || s;
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

  // ================================================================
  // FILTER + SORT
  // ================================================================
  function getFiltered() {
    var rows = DATA.slice();

    if (state.status !== 'all') {
      rows = rows.filter(function (r) { return r.status === state.status; });
    }
    if (state.trade) {
      rows = rows.filter(function (r) { return r.trade === state.trade; });
    }
    if (state.search) {
      var q = state.search.toLowerCase();
      rows = rows.filter(function (r) {
        return (r.id + ' ' + r.address + ' ' + r.city + ' ' + r.zip + ' ' + r.trade).toLowerCase().indexOf(q) !== -1;
      });
    }
    if (state.date === 'Today') rows = rows.filter(function (r) { return r.updated < 1440; });
    else if (state.date === 'Last 7 days') rows = rows.filter(function (r) { return r.updated < 10080; });
    else if (state.date === 'Last 30 days') rows = rows.filter(function (r) { return r.updated < 43200; });
    else if (state.date === 'Last 90 days') rows = rows.filter(function (r) { return r.updated < 129600; });

    // Sort
    if (state.sort === 'oldest') rows.sort(function (a,b) { return b.updated - a.updated; });
    else if (state.sort === 'newest') rows.sort(function (a,b) { return a.updated - b.updated; });
    else if (state.sort === 'amount-high') rows.sort(function (a,b) { return (b.amount||0) - (a.amount||0); });
    else if (state.sort === 'amount-low') rows.sort(function (a,b) { return (a.amount||0) - (b.amount||0); });

    return rows;
  }

  // ================================================================
  // RENDER TABLE
  // ================================================================
  function render() {
    var rows = getFiltered();
    var body = document.getElementById('requestsBody');
    var empty = document.getElementById('emptyState');
    var wrap = document.querySelector('.table-scroll');
    var pg = document.getElementById('pagination');

    // Pagination slice
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
                '<div class="est-id-cell__time">' + r.photos + ' photos</div>' +
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
          '<td class="text-sm">' + r.trade + '</td>' +
          '<td>' + statusChip(r.status) + '</td>' +
          '<td class="num">' + formatAmount(r.amount) + '</td>' +
          '<td class="text-muted text-sm">' + r.updatedLabel + '</td>' +
          '<td class="actions">' +
            '<div class="row-menu">' +
              '<button class="row-menu__trigger" aria-label="Row actions">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>' +
              '</button>' +
              '<div class="row-menu__panel">' +
                '<button class="row-menu__item" data-action="open">' +
                  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>' +
                  'Open estimate' +
                '</button>' +
                '<button class="row-menu__item" data-action="download">' +
                  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>' +
                  'Download PDF' +
                '</button>' +
                '<hr class="row-menu__divider">' +
                '<button class="row-menu__item row-menu__item--danger" data-action="archive">' +
                  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>' +
                  'Archive' +
                '</button>' +
              '</div>' +
            '</div>' +
          '</td>' +
        '</tr>';
      }).join('');

      // Update pagination info
      var info = document.getElementById('paginationInfo');
      info.textContent = 'Showing ' + (start + 1) + '–' + (start + paged.length) + ' of ' + rows.length + ' requests';
    }

    // Update checkbox states
    document.getElementById('selectAll').checked = rows.length > 0 && rows.every(function (r) { return state.selected.has(r.id); });
    updateBulkBar();
    renderFilterChips();
  }

  // ================================================================
  // SELECTION
  // ================================================================
  function updateBulkBar() {
    var bar = document.getElementById('bulkBar');
    var count = document.getElementById('bulkCount');
    count.textContent = state.selected.size;
    bar.classList.toggle('is-visible', state.selected.size > 0);
  }

  document.getElementById('selectAll').addEventListener('change', function () {
    var rows = getFiltered();
    if (this.checked) {
      rows.forEach(function (r) { state.selected.add(r.id); });
    } else {
      rows.forEach(function (r) { state.selected.delete(r.id); });
    }
    render();
  });

  document.getElementById('clearSelection').addEventListener('click', function () {
    state.selected.clear();
    render();
  });

  // ================================================================
  // TABLE EVENT DELEGATION
  // ================================================================
  document.getElementById('requestsBody').addEventListener('click', function (e) {
    var row = e.target.closest('tr[data-id]');
    if (!row) return;
    var id = row.dataset.id;

    // Checkbox
    var check = e.target.closest('.rowCheck');
    if (check) {
      if (check.checked) state.selected.add(id);
      else state.selected.delete(id);
      updateBulkBar();
      document.getElementById('selectAll').checked = getFiltered().every(function (r) { return state.selected.has(r.id); });
      return;
    }

    // Row menu trigger
    var trigger = e.target.closest('.row-menu__trigger');
    if (trigger) {
      e.stopPropagation();
      document.querySelectorAll('.row-menu').forEach(function (m) {
        if (m !== trigger.closest('.row-menu')) m.classList.remove('is-open');
      });
      trigger.closest('.row-menu').classList.toggle('is-open');
      return;
    }

    // Row menu item
    var item = e.target.closest('.row-menu__item');
    if (item) {
      e.stopPropagation();
      var action = item.dataset.action;
      if (action === 'open' || action === 'download') {
         window.location.href = window.estimateDetailUrl.replace('__ID__', encodeURIComponent(id));
      } else if (action === 'archive') {
        row.style.opacity = '0.4';
        setTimeout(function () { row.remove(); }, 300);
      }
      return;
    }

    // Whole-row click → open detail
    if (!e.target.closest('.check, .row-menu')) {
      window.location.href = window.estimateDetailUrl.replace('__ID__', encodeURIComponent(id));
    }
  });

  // Close menus on outside click
  document.addEventListener('click', function () {
    document.querySelectorAll('.row-menu').forEach(function (m) { m.classList.remove('is-open'); });
  });

  // ================================================================
  // STATUS TILES
  // ================================================================
  document.querySelectorAll('.status-tile').forEach(function (tile) {
    tile.addEventListener('click', function () {
      document.querySelectorAll('.status-tile').forEach(function (t) { t.classList.remove('is-active'); });
      tile.classList.add('is-active');
      state.status = tile.dataset.status;
      state.page = 1;
      render();
    });
  });

  // ================================================================
  // FILTER CONTROLS
  // ================================================================
  document.getElementById('tableSearch').addEventListener('input', function () {
    state.search = this.value.trim();
    state.page = 1;
    render();
  });
  document.getElementById('tradeFilter').addEventListener('change', function () {
    state.trade = this.value;
    state.page = 1;
    render();
  });
  document.getElementById('dateFilter').addEventListener('change', function () {
    state.date = this.value;
    state.page = 1;
    render();
  });
  document.getElementById('sortFilter').addEventListener('change', function () {
    state.sort = this.value;
    render();
  });

  document.getElementById('clearFilters').addEventListener('click', clearAll);
  document.getElementById('clearEmptyFilters').addEventListener('click', clearAll);

  function clearAll() {
    state.search = '';
    state.trade = '';
    state.date = '';
    state.sort = 'newest';
    state.status = 'all';
    state.page = 1;
    document.getElementById('tableSearch').value = '';
    document.getElementById('tradeFilter').value = '';
    document.getElementById('dateFilter').value = '';
    document.getElementById('sortFilter').value = 'newest';
    document.querySelectorAll('.status-tile').forEach(function (t) {
      t.classList.toggle('is-active', t.dataset.status === 'all');
    });
    render();
  }

  // ================================================================
  // FILTER CHIPS
  // ================================================================
  function renderFilterChips() {
    var wrap = document.getElementById('filterChips');
    var chips = [];
    if (state.status !== 'all') chips.push({ k: 'status', label: 'Status: ' + labelFor(state.status) });
    if (state.trade) chips.push({ k: 'trade', label: 'Trade: ' + state.trade });
    if (state.date) chips.push({ k: 'date', label: 'Date: ' + state.date });
    if (state.search) chips.push({ k: 'search', label: 'Search: "' + state.search + '"' });

    wrap.innerHTML = chips.map(function (c) {
      return '<span class="filter-chip">' +
        escapeHtml(c.label) +
        '<button data-remove="' + c.k + '" aria-label="Remove filter">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
        '</button>' +
      '</span>';
    }).join('');

    wrap.querySelectorAll('[data-remove]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var k = this.dataset.remove;
        if (k === 'status') {
          state.status = 'all';
          document.querySelectorAll('.status-tile').forEach(function (t) {
            t.classList.toggle('is-active', t.dataset.status === 'all');
          });
        } else if (k === 'trade') { state.trade = ''; document.getElementById('tradeFilter').value = ''; }
        else if (k === 'date') { state.date = ''; document.getElementById('dateFilter').value = ''; }
        else if (k === 'search') { state.search = ''; document.getElementById('tableSearch').value = ''; }
        render();
      });
    });
  }

  // ================================================================
  // INITIAL RENDER
  // ================================================================
  render();

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