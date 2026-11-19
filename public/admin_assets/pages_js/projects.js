(function () {
  'use strict';

  var DATA = [
    { id:'PRJ-2026-0021', address:'305 Meadow Rd, Austin, TX', customer:'Oak Ridge Property Mgmt', trade:'HVAC', status:'active', progress:60, amount:4950, started:'Aug 8, 2026' },
    { id:'PRJ-2026-0020', address:'78 Lakeline Dr, Austin, TX', customer:'Oak Ridge Property Mgmt', trade:'Plumbing', status:'completed', progress:100, amount:980, started:'Aug 7, 2026' },
    { id:'PRJ-2026-0019', address:'1100 Congress Ave, Austin, TX', customer:'Capitol Holdings', trade:'HVAC', status:'active', progress:35, amount:6320, started:'Aug 5, 2026' },
    { id:'PRJ-2026-0018', address:'815 Rio Grande St, Austin, TX', customer:'Downtown Lofts LLC', trade:'Electrical', status:'completed', progress:100, amount:2140, started:'Aug 2, 2026' },
    { id:'PRJ-2026-0017', address:'204 W 6th St, Austin, TX', customer:'Downtown Lofts LLC', trade:'Drywall / Paint', status:'active', progress:72, amount:3680, started:'Jul 30, 2026' },
    { id:'PRJ-2026-0016', address:'68 Elm Creek Dr, Round Rock, TX', customer:'Northgate Partners', trade:'Roofing', status:'completed', progress:100, amount:9450, started:'Jul 27, 2026' },
    { id:'PRJ-2026-0015', address:'1409 Manchaca Rd, Austin, TX', customer:'Oak Ridge Property Mgmt', trade:'General Repair', status:'completed', progress:100, amount:1785, started:'Jul 24, 2026' },
    { id:'PRJ-2026-0014', address:'5511 Airport Blvd, Austin, TX', customer:'Bluebonnet Homes', trade:'Flooring', status:'active', progress:15, amount:4820, started:'Jul 20, 2026' },
    { id:'PRJ-2026-0013', address:'77 Maple St, Plano, TX', customer:'Northgate Partners', trade:'General Repair', status:'active', progress:88, amount:2140, started:'Jul 18, 2026' },
    { id:'PRJ-2026-0012', address:'601 Congress Ave, Austin, TX', customer:'Capitol Holdings', trade:'HVAC', status:'completed', progress:100, amount:5420, started:'Jul 15, 2026' }
  ];

  var state = { search:'', status:'', trade:'', sort:'newest' };

  function escapeHtml(s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]; }); }

  function statusChip(s) {
    return s === 'active'
      ? '<span class="chip chip--warning"><span class="chip__dot"></span>In Progress</span>'
      : '<span class="chip chip--success"><span class="chip__dot"></span>Completed</span>';
  }

  function progressBar(p) {
    var color = p === 100 ? 'var(--emerald-600)' : p >= 50 ? 'var(--blue-600)' : 'var(--warning-600)';
    return '<div style="display:flex;align-items:center;gap:8px"><div style="flex:1;height:5px;background:var(--n-200);border-radius:99px;overflow:hidden"><div style="height:100%;width:' + p + '%;background:' + color + ';border-radius:99px"></div></div><span style="font-size:var(--fs-xs);font-weight:600;color:var(--text-strong);font-variant-numeric:tabular-nums;min-width:30px;text-align:right">' + p + '%</span></div>';
  }

  function getFiltered() {
    var rows = DATA.slice();
    if (state.status === 'active') rows = rows.filter(function (r) { return r.status === 'active'; });
    if (state.status === 'completed') rows = rows.filter(function (r) { return r.status === 'completed'; });
    if (state.trade) rows = rows.filter(function (r) { return r.trade === state.trade; });
    if (state.search) {
      var q = state.search.toLowerCase();
      rows = rows.filter(function (r) { return (r.id + ' ' + r.address + ' ' + r.customer).toLowerCase().indexOf(q) !== -1; });
    }
    if (state.sort === 'amount-high') rows.sort(function (a,b) { return b.amount - a.amount; });
    else if (state.sort === 'progress') rows.sort(function (a,b) { return b.progress - a.progress; });
    return rows;
  }

  function render() {
    var rows = getFiltered();
    var body = document.getElementById('projBody');
    var empty = document.getElementById('emptyState');
    var pg = document.getElementById('pagination');
    var scroll = document.querySelector('.table-scroll');

    if (!rows.length) {
      body.innerHTML = ''; scroll.classList.add('hidden'); pg.classList.add('hidden'); empty.classList.remove('hidden'); return;
    }
    scroll.classList.remove('hidden'); pg.classList.remove('hidden'); empty.classList.add('hidden');

    body.innerHTML = rows.map(function (r) {
      return '<tr data-id="' + r.id + '" style="cursor:pointer">' +
        '<td><div class="est-id-cell"><div class="est-id-cell__icon est-id-cell__icon--emerald"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div><div class="est-id-cell__text"><div class="est-id-cell__id">' + r.id + '</div></div></div></td>' +
        '<td class="text-sm">' + escapeHtml(r.customer) + '</td>' +
        '<td class="text-sm" style="max-width:260px">' + escapeHtml(r.address) + '</td>' +
        '<td class="text-sm">' + r.trade + '</td>' +
        '<td>' + statusChip(r.status) + '</td>' +
        '<td>' + progressBar(r.progress) + '</td>' +
        '<td class="num"><span class="amount-cell">$' + r.amount.toLocaleString() + '</span></td>' +
        '<td class="text-xs text-muted">' + r.started + '</td>' +
      '</tr>';
    }).join('');

    document.getElementById('paginationInfo').textContent = 'Showing 1–' + rows.length + ' of 218 projects';
  }

  ['projSearch','projStatus','projTrade','projSort'].forEach(function (id) {
    var el = document.getElementById(id);
    el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', function () {
      state.search = document.getElementById('projSearch').value.trim();
      state.status = document.getElementById('projStatus').value;
      state.trade = document.getElementById('projTrade').value;
      state.sort = document.getElementById('projSort').value;
      render();
    });
  });

  document.getElementById('projBody').addEventListener('click', function (e) {
    var row = e.target.closest('tr[data-id]');
    if (row) window.location.href = 'estimate-review.html';
  });

  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) app.classList.toggle('is-nav-open');
    else app.classList.toggle('is-collapsed');
  });

  render();
})();