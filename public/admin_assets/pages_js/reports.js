(function () {
  'use strict';

  // Build simple bar chart
  (function () {
    var days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun','Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    var verified = [42, 38, 51, 47, 55, 28, 22, 46, 49, 53, 61, 58, 32, 25];
    var returned = [8, 6, 9, 7, 10, 4, 3, 7, 8, 9, 11, 10, 5, 4];
    var max = Math.max.apply(null, verified);
    var chart = document.getElementById('throughputChart');

    chart.innerHTML = days.map(function (d, i) {
      var vH = (verified[i] / max) * 100;
      var rH = (returned[i] / max) * 100;
      return '<div class="bar-chart__col">' +
        '<div class="bar-chart__stack" style="justify-content:flex-end">' +
          '<div class="bar-chart__bar bar-chart__bar--primary" style="height:' + vH + '%"></div>' +
          '<div class="bar-chart__bar bar-chart__bar--secondary" style="height:' + rH + '%"></div>' +
        '</div>' +
        '<div class="bar-chart__label">' + d + '</div>' +
      '</div>';
    }).join('');
  })();

  document.getElementById('dateRange').addEventListener('change', function () {
    toast('info', 'Range updated', 'Showing data for ' + this.value + '.');
  });

  function toast(kind, title, msg) {
    window.bidFreqFeedback.toast(kind, title, msg);
  }

  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) app.classList.toggle('is-nav-open');
    else app.classList.toggle('is-collapsed');
  });
})();
