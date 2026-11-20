(function () {
  'use strict';

  var ESTIMATORS = [
    { id:'u1', name:'B. Hayes', initials:'BH', role:'Senior Estimator', status:'online', open:5, verifiedToday:6, turnaround:'1.4d', quality:96, specialties:['General Repair','Drywall'] },
    { id:'u2', name:'M. Alvarez', initials:'MA', role:'Estimator', status:'online', open:3, verifiedToday:4, turnaround:'1.6d', quality:94, specialties:['Roofing','HVAC'] },
    { id:'u3', name:'R. Kapoor', initials:'RK', role:'Estimator', status:'online', open:7, verifiedToday:3, turnaround:'2.1d', quality:91, specialties:['Electrical','Plumbing'] },
    { id:'u4', name:'T. Nakamura', initials:'TN', role:'Junior Estimator', status:'away', open:2, verifiedToday:1, turnaround:'2.6d', quality:88, specialties:['General Repair'] },
    { id:'u5', name:'S. Okafor', initials:'SO', role:'Estimator', status:'online', open:4, verifiedToday:5, turnaround:'1.7d', quality:93, specialties:['Flooring','Drywall'] },
    { id:'u6', name:'L. Petrov', initials:'LP', role:'Junior Estimator', status:'offline', open:0, verifiedToday:0, turnaround:'—', quality:'—', specialties:['Trainee'] }
  ];

  var STATUS_LABEL = { online:'Online', away:'Away', offline:'Offline' };

  function workloadColor(n) {
    if (n >= 7) return 'warning';
    if (n >= 5) return '';
    return 'emerald';
  }

  function render() {
    document.getElementById('estimatorGrid').innerHTML = ESTIMATORS.map(function (u) {
      var pct = Math.min((u.open / 8) * 100, 100);
      var fillClass = workloadColor(u.open);
      return '<article class="est-card" data-id="' + u.id + '">' +
        '<div class="est-card__head">' +
          '<div class="est-card__avatar">' + u.initials +
            '<span class="est-card__status est-card__status--' + u.status + '"></span>' +
          '</div>' +
          '<div style="flex:1;min-width:0">' +
            '<div class="est-card__name">' + u.name + '</div>' +
            '<div class="est-card__role">' + u.role + ' · ' + STATUS_LABEL[u.status] + '</div>' +
          '</div>' +
          '<button class="icon-btn tooltip" data-tip="More" aria-label="More">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>' +
          '</button>' +
        '</div>' +
        '<div class="est-card__stats">' +
          '<div><div class="est-card__stat-label">Open</div><div class="est-card__stat-value">' + u.open + '</div></div>' +
          '<div><div class="est-card__stat-label">Verified today</div><div class="est-card__stat-value est-card__stat-value--emerald">' + u.verifiedToday + '</div></div>' +
          '<div><div class="est-card__stat-label">Avg. turnaround</div><div class="est-card__stat-value">' + u.turnaround + '</div></div>' +
          '<div><div class="est-card__stat-label">Quality score</div><div class="est-card__stat-value">' + (u.quality === '—' ? '—' : u.quality + '%') + '</div></div>' +
        '</div>' +
        '<div class="est-card__workload">' +
          '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px">' +
            '<span class="text-xs text-muted" style="font-weight:600">Workload</span>' +
            '<span class="text-xs text-muted" style="font-variant-numeric:tabular-nums">' + u.open + ' / 8 capacity</span>' +
          '</div>' +
          '<div class="workload-row__bar"><div class="workload-row__fill' + (fillClass ? ' workload-row__fill--' + fillClass : '') + '" style="width:' + pct + '%"></div></div>' +
        '</div>' +
        '<div class="est-card__actions">' +
          '<button class="btn btn--secondary btn--sm">View profile</button>' +
          '<button class="btn btn--primary btn--sm">Assign queue</button>' +
        '</div>' +
      '</article>';
    }).join('');
  }

  document.getElementById('inviteEstimatorBtn').addEventListener('click', function () {
    toast('info', 'Invite estimator', 'An invitation form would open here.');
  });

  function toast(kind, title, msg) {
    window.bidFreqFeedback.toast(kind, title, msg);
  }

  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) app.classList.toggle('is-nav-open');
    else app.classList.toggle('is-collapsed');
  });

  render();
})();