@extends('admin.layout.app')
@section('title', 'Estimator Console · BidFreq™')


@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('admin_assets/pages_css/dashboard.css') }}"> --}}
@endpush

@section('content')
      <main class="app-content">

      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Console</a>
        <span class="breadcrumbs__sep">/</span>
        <span class="breadcrumbs__current">Reports</span>
      </nav>

      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-header__title">Reports &amp; Analytics</h1>
          <p class="page-header__subtitle">Verification throughput, AI accuracy, turnaround trends, and team performance.</p>
        </div>
        <div class="page-header__actions">
          <div class="filter-bar__group" style="margin-right:var(--s-2)">
            <select id="dateRange">
              <option>Last 30 days</option>
              <option>Last 7 days</option>
              <option>This month</option>
              <option>Last quarter</option>
              <option>Year to date</option>
            </select>
          </div>
          <button class="btn btn--secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export
          </button>
        </div>
      </header>

      <!-- KPI cards -->
      <section class="report-kpis">
        <div class="report-kpi">
          <div class="report-kpi__head">
            <span class="report-kpi__label">Estimates Verified</span>
            <div class="report-kpi__icon report-kpi__icon--violet"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
          </div>
          <div class="report-kpi__value">1,248</div>
          <div class="report-kpi__delta report-kpi__delta--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
            +18.2% <span>vs. previous period</span>
          </div>
        </div>
        <div class="report-kpi">
          <div class="report-kpi__head">
            <span class="report-kpi__label">Avg. Turnaround</span>
            <div class="report-kpi__icon report-kpi__icon--blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
          </div>
          <div class="report-kpi__value">1.8<span style="font-size:14px;color:var(--text-muted);font-weight:500"> days</span></div>
          <div class="report-kpi__delta report-kpi__delta--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            −0.4 days <span>faster than target</span>
          </div>
        </div>
        <div class="report-kpi">
          <div class="report-kpi__head">
            <span class="report-kpi__label">AI Edit Rate</span>
            <div class="report-kpi__icon report-kpi__icon--warning"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></div>
          </div>
          <div class="report-kpi__value">38%</div>
          <div class="report-kpi__delta report-kpi__delta--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            −6 pts <span>improvement</span>
          </div>
        </div>
        <div class="report-kpi">
          <div class="report-kpi__head">
            <span class="report-kpi__label">Customer Satisfaction</span>
            <div class="report-kpi__icon report-kpi__icon--emerald"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg></div>
          </div>
          <div class="report-kpi__value">4.7<span style="font-size:14px;color:var(--text-muted);font-weight:500"> / 5</span></div>
          <div class="report-kpi__delta report-kpi__delta--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
            +0.2 <span>vs. last month</span>
          </div>
        </div>
      </section>

      <!-- Charts row -->
      <div class="admin-grid mb-5">
        <!-- <div class="chart-wrap">
          <div class="chart-head">
            <div><div class="chart-head__title">Verification Throughput</div><div class="chart-head__sub">Estimates verified per day, last 14 days</div></div>
            <div class="chart-head__legend">
              <span class="chart-head__legend-item"><span class="chart-head__legend-dot" style="background:var(--blue-500)"></span>Verified</span>
              <span class="chart-head__legend-item"><span class="chart-head__legend-dot" style="background:var(--violet-500)"></span>Returned to AI</span>
            </div>
          </div>
          <div class="chart-body">
            <div class="bar-chart" id="throughputChart"></div>
          </div>
        </div> -->

        <div class="chart-wrap">
          <div class="chart-head">
            <div><div class="chart-head__title">Estimates by Trade</div><div class="chart-head__sub">Distribution across last 30 days</div></div>
          </div>
          <div class="chart-body">
            <div class="donut-wrap">
              <div class="donut" style="background:conic-gradient(var(--violet-500) 0 32%, var(--blue-500) 32% 55%, var(--emerald-500) 55% 73%, var(--warning-500) 73% 87%, var(--danger-500) 87% 100%)">
                <span class="donut__value">1,248</span>
              </div>
              <div class="donut__legend">
                <div class="donut__legend-row"><span class="donut__legend-label"><span class="donut__legend-dot" style="background:var(--violet-500)"></span>General Repair</span><span class="donut__legend-value">32%</span></div>
                <div class="donut__legend-row"><span class="donut__legend-label"><span class="donut__legend-dot" style="background:var(--blue-500)"></span>Roofing / HVAC</span><span class="donut__legend-value">23%</span></div>
                <div class="donut__legend-row"><span class="donut__legend-label"><span class="donut__legend-dot" style="background:var(--emerald-500)"></span>Plumbing</span><span class="donut__legend-value">18%</span></div>
                <div class="donut__legend-row"><span class="donut__legend-label"><span class="donut__legend-dot" style="background:var(--warning-500)"></span>Electrical</span><span class="donut__legend-value">14%</span></div>
                <div class="donut__legend-row"><span class="donut__legend-label"><span class="donut__legend-dot" style="background:var(--danger-500)"></span>Other</span><span class="donut__legend-value">13%</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Second row -->
      <div class="admin-grid">
        <div class="chart-wrap">
          <div class="chart-head">
            <div><div class="chart-head__title">Estimator Performance</div><div class="chart-head__sub">Volume vs. quality, last 30 days</div></div>
          </div>
          <div class="chart-body" style="padding:0">
            <table class="table" style="border-radius:0">
              <thead>
                <tr>
                  <th>Estimator</th>
                  <th class="num">Verified</th>
                  <th class="num">Avg. turnaround</th>
                  <th class="num">Quality</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><div class="estimator-chip"><span class="estimator-chip__avatar">BH</span>B. Hayes</div></td>
                  <td class="num" style="font-variant-numeric:tabular-nums">142</td>
                  <td class="num">1.4d</td>
                  <td class="num"><strong style="color:var(--emerald-600)">96%</strong></td>
                  
                </tr>
                <tr>
                  <td><div class="estimator-chip"><span class="estimator-chip__avatar">MA</span>M. Alvarez</div></td>
                  <td class="num" style="font-variant-numeric:tabular-nums">124</td>
                  <td class="num">1.6d</td>
                  <td class="num"><strong style="color:var(--emerald-600)">94%</strong></td>
                  
                </tr>
                <tr>
                  <td><div class="estimator-chip"><span class="estimator-chip__avatar">RK</span>R. Kapoor</div></td>
                  <td class="num" style="font-variant-numeric:tabular-nums">98</td>
                  <td class="num">2.1d</td>
                  <td class="num"><strong style="color:var(--blue-600)">91%</strong></td>
                  
                </tr>
                <tr>
                  <td><div class="estimator-chip"><span class="estimator-chip__avatar">SO</span>S. Okafor</div></td>
                  <td class="num" style="font-variant-numeric:tabular-nums">76</td>
                  <td class="num">1.7d</td>
                  <td class="num"><strong style="color:var(--emerald-600)">93%</strong></td>
                  
                </tr>
                <tr>
                  <td><div class="estimator-chip"><span class="estimator-chip__avatar">TN</span>T. Nakamura</div></td>
                  <td class="num" style="font-variant-numeric:tabular-nums">42</td>
                  <td class="num">2.6d</td>
                  <td class="num"><strong style="color:var(--warning-600)">88%</strong></td>
                  
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="chart-wrap">
          <div class="chart-head">
            <div><div class="chart-head__title">SLA Compliance</div><div class="chart-head__sub">Last 30 days</div></div>
          </div>
          <div class="chart-body">
            <div class="conf-widget" style="background:var(--emerald-50);border-color:var(--emerald-100)">
              <div class="conf-widget__head" style="color:var(--emerald-700)">
                <span>On-time verification</span>
                <span class="conf-widget__value">98.4%</span>
              </div>
              <div class="confidence">
                <div class="confidence__bar" style="height:8px">
                  <div class="confidence__fill confidence__fill--high" style="width:98.4%"></div>
                </div>
              </div>
              <div class="conf-widget__note" style="color:var(--emerald-700)">
                Target: 95% · exceeded by 3.4 pts
              </div>
            </div>
            <div class="status-row mt-4"><span class="status-row__label">SLA breaches</span><span class="status-row__value" style="color:var(--danger-600)">2</span></div>
            <div class="status-row"><span class="status-row__label">Warnings sent</span><span class="status-row__value">14</span></div>
            <div class="status-row"><span class="status-row__label">Reassigned due to SLA</span><span class="status-row__value">5</span></div>
            <div class="status-row"><span class="status-row__label">Median verification time</span><span class="status-row__value">1.2 days</span></div>
          </div>
        </div>
      </div>

    </main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/reports.js') }}"></script>
@endpush
