 // Sidebar / mobile nav toggle
  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) {
      app.classList.toggle('is-nav-open');
    } else {
      app.classList.toggle('is-collapsed');
    }
  });

  // Clickable rows → navigate to detail
  document.querySelectorAll('.est-row, .action-item').forEach(function (row) {
    row.addEventListener('click', function (e) {
      if (e.target.closest('a, button')) return;
      window.location.href = window.bidFreqRoutes['estimate_request_detail'];
    });
  });

  // Escape closes mobile nav
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.getElementById('app').classList.remove('is-nav-open');
    }
  });