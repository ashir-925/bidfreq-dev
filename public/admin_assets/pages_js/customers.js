(function () {
  'use strict';

  var DATA = [
    { id:'ORG-001', name:'Oak Ridge Property Mgmt', initials:'OR', contact:'Jane Doe', email:'jane@oakridge.com', plan:'Enterprise', status:'active', properties:24, estimates:124, gmv:184200, health:94, last:'2 hours ago' },
    { id:'ORG-002', name:'Lone Star Realty', initials:'LS', contact:'Marcus Webb', email:'marcus@lonestar.com', plan:'Growth', status:'active', properties:18, estimates:76, gmv:98240, health:88, last:'1 day ago' },
    { id:'ORG-003', name:'Bluebonnet Homes', initials:'BH', contact:'Alicia Reyes', email:'alicia@bluebonnet.com', plan:'Growth', status:'active', properties:31, estimates:112, gmv:142500, health:91, last:'4 hours ago' },
    { id:'ORG-004', name:'Vista Realty Co.', initials:'VR', contact:'Daniel Park', email:'d.park@vistarealty.com', plan:'Starter', status:'trial', properties:6, estimates:14, gmv:14200, health:76, last:'3 days ago' },
    { id:'ORG-005', name:'Capitol Holdings', initials:'CH', contact:'Reena Shah', email:'reena@capitol.com', plan:'Enterprise', status:'active', properties:42, estimates:198, gmv:312800, health:96, last:'30 min ago' },
    { id:'ORG-006', name:'Northgate Partners', initials:'NP', contact:'Tom Nguyen', email:'tom@northgate.com', plan:'Growth', status:'active', properties:12, estimates:48, gmv:62400, health:82, last:'6 hours ago' },
    { id:'ORG-007', name:'Gulf Coast Realty', initials:'GC', contact:'Priya Menon', email:'priya@gulfcoast.com', plan:'Starter', status:'active', properties:5, estimates:22, gmv:28600, health:79, last:'2 days ago' },
    { id:'ORG-008', name:'Hill Country Homes', initials:'HC', contact:'Bryan Fox', email:'bryan@hillcountry.com', plan:'Growth', status:'active', properties:9, estimates:36, gmv:48100, health:87, last:'Yesterday' },
    { id:'ORG-009', name:'Southside Property', initials:'SP', contact:'Naomi Cole', email:'naomi@southside.com', plan:'Starter', status:'suspended', properties:4, estimates:11, gmv:9600, health:42, last:'32 days ago' },
    { id:'ORG-010', name:'Bayou Properties', initials:'BP', contact:'Carlos Diaz', email:'carlos@bayou.com', plan:'Growth', status:'active', properties:14, estimates:64, gmv:84600, health:85, last:'8 hours ago' },
    { id:'ORG-011', name:'Downtown Lofts LLC', initials:'DL', contact:'Sarah Kim', email:'sarah@downtownlofts.com', plan:'Enterprise', status:'active', properties:27, estimates:143, gmv:224100, health:93, last:'1 hour ago' },
    { id:'ORG-012', name:'Lone Oak Realty', initials:'LO', contact:'Peter Walsh', email:'peter@loneoak.com', plan:'Trial', status:'trial', properties:3, estimates:6, gmv:4200, health:68, last:'5 days ago' }
  ];

  var state = {
    search: '', plan: '', status: '', sort: 'gmv',
    selected: new Set(), page: 1, perPage: 10
  };

  function statusBadge(s) {
    var map = { active:'Active', trial:'Trial', suspended:'Suspended' };
    return '<span class="status-badge status-badge--' + s + '"><span class="status-badge__dot"></span>' + map[s] + '</span>';
  }

  function healthBar(h) {
    var color = h >= 85 ? 'var(--emerald-600)' : h >= 70 ? 'var(--blue-600)' : h >= 50 ? 'var(--warning-600)' : 'var(--danger-600)';
    return '<div style="display:flex;align-items:center;gap:8px">' +
      '<div style="flex:1;height:5px;background:var(--n-200);border-radius:99px;overflow:hidden;min-width:60px"><div style="height:100%;width:' + h + '%;background:' + color + ';border-radius:99px"></div></div>' +
      '<span style="font-size:var(--fs-xs);font-weight:600;font-variant-numeric:tabular-nums;color:var(--text-strong)">' + h + '</span>' +
    '</div>';
  }

  function formatMoney(n) {
    if (n >= 1000) return '$' + (n/1000).toFixed(1) + 'K';
    return '$' + n.toLocaleString();
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c];
    });
  }

  function getFiltered() {
    var rows = DATA.slice();
    if (state.plan) rows = rows.filter(function(r){ return r.plan === state.plan; });
    if (state.status) rows = rows.filter(function(r){ return r.status === state.status; });
    if (state.search) {
      var q = state.search.toLowerCase();
      rows = rows.filter(function(r){
        return (r.name + ' ' + r.contact + ' ' + r.email).toLowerCase().indexOf(q) !== -1;
      });
    }
    if (state.sort === 'gmv') rows.sort(function(a,b){ return b.gmv - a.gmv; });
    else if (state.sort === 'name') rows.sort(function(a,b){ return a.name.localeCompare(b.name); });
    else if (state.sort === 'newest') rows.sort(function(a,b){ return b.id.localeCompare(a.id); });
    return rows;
  }

  function render() {
    var rows = getFiltered();
    var body = document.getElementById('custBody');
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
        return '<tr data-id="' + r.id + '" data-name="' + escapeHtml(r.name) + '">' +
          '<td class="table__checkbox"><label class="check" style="margin:0">' +
            '<input type="checkbox" class="rowCheck" data-id="' + r.id + '" ' + checked + '>' +
            '<span class="check__box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>' +
          '</label></td>' +
          '<td><div class="org-cell">' +
            '<div class="org-cell__logo">' + r.initials + '</div>' +
            '<div><div class="org-cell__name">' + escapeHtml(r.name) + '</div><div class="org-cell__meta text-mono">' + r.id + '</div></div>' +
          '</div></td>' +
          '<td><div class="text-sm"><div style="font-weight:600;color:var(--text-strong)">' + escapeHtml(r.contact) + '</div><div class="text-xs text-muted">' + escapeHtml(r.email) + '</div></div></td>' +
          '<td class="text-sm">' + r.plan + '</td>' +
          '<td class="text-sm num" style="font-variant-numeric:tabular-nums">' + r.properties + '</td>' +
          '<td class="text-sm num" style="font-variant-numeric:tabular-nums">' + r.estimates + '</td>' +
          '<td class="num"><span class="amount-cell">' + formatMoney(r.gmv) + '</span></td>' +
          '<td style="min-width:130px">' + healthBar(r.health) + '</td>' +
          '<td class="text-xs text-muted">' + r.last + '</td>' +
          '<td class="actions">' +
            '<button class="icon-btn tooltip" data-tip="Open" aria-label="Open" onclick="event.stopPropagation();openDrawer(\'' + r.id + '\',\'' + escapeHtml(r.name) + '\',\'' + r.plan + '\',\'' + r.status + '\')">' +
              '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>' +
            '</button>' +
          '</td>' +
        '</tr>';
      }).join('');

      document.getElementById('paginationInfo').textContent =
        'Showing ' + (start + 1) + '–' + (start + paged.length) + ' of ' + rows.length + ' customers';
    }

    document.getElementById('selectAll').checked = rows.length > 0 && rows.every(function (r) { return state.selected.has(r.id); });
    updateBulkBar();
  }

  function updateBulkBar() {
    var bar = document.getElementById('bulkBar');
    document.getElementById('bulkCount').textContent = state.selected.size;
    bar.classList.toggle('is-visible', state.selected.size > 0);
  }

  window.openDrawer = function (id, name, plan, status) {
    document.getElementById('cdTitle').textContent = name;
    document.getElementById('cdSubtitle').textContent = plan + ' · ' + status.charAt(0).toUpperCase() + status.slice(1);
    document.getElementById('custDrawer').classList.add('is-open');
  };

  document.getElementById('selectAll').addEventListener('change', function () {
    var rows = getFiltered();
    if (this.checked) rows.forEach(function (r) { state.selected.add(r.id); });
    else rows.forEach(function (r) { state.selected.delete(r.id); });
    render();
  });

  document.getElementById('clearSelection').addEventListener('click', function () {
    state.selected.clear(); render();
  });

  document.getElementById('custBody').addEventListener('click', function (e) {
    var row = e.target.closest('tr[data-id]');
    if (!row) return;
    var id = row.dataset.id;
    var check = e.target.closest('.rowCheck');
    if (check) {
      if (check.checked) state.selected.add(id); else state.selected.delete(id);
      updateBulkBar(); return;
    }
    if (e.target.closest('.icon-btn')) return;
    var item = DATA.find(function(r){ return r.id === id; });
    if (item) window.openDrawer(item.id, item.name, item.plan, item.status);
  });

  ['custSearch','planFilter','statusFilter','sortFilter'].forEach(function (id) {
    var el = document.getElementById(id);
    el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', function () {
      state.search = document.getElementById('custSearch').value.trim();
      state.plan = document.getElementById('planFilter').value;
      state.status = document.getElementById('statusFilter').value;
      state.sort = document.getElementById('sortFilter').value;
      state.page = 1;
      render();
    });
  });

  document.getElementById('clearFilters').addEventListener('click', clearAll);
  document.getElementById('clearEmptyFilters').addEventListener('click', clearAll);
  function clearAll() {
    document.getElementById('custSearch').value = '';
    document.getElementById('planFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('sortFilter').value = 'gmv';
    state.search=''; state.plan=''; state.status=''; state.sort='gmv'; state.page=1;
    render();
  }

  document.getElementById('inviteCustomerBtn').addEventListener('click', function () {
    toast('info','Invite customer','An invitation form would open here.');
  });

  function toast(kind, title, msg) {
    var icons = {
      success:'<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
      info:'<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>'
    };
    var el = document.createElement('div');
    el.className = 'toast toast--' + kind;
    el.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + (icons[kind]||icons.info) + '</svg>' +
      '<div><div class="toast__title">' + title + '</div><div class="toast__msg">' + msg + '</div></div>';
    document.getElementById('toastRegion').appendChild(el);
    setTimeout(function () {
      el.style.transition = 'opacity .3s, transform .3s';
      el.style.opacity = '0'; el.style.transform = 'translateX(20px)';
      setTimeout(function () { el.remove(); }, 320);
    }, 3000);
  }

  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) app.classList.toggle('is-nav-open');
    else app.classList.toggle('is-collapsed');
  });

  render();
})();