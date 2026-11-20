@extends('front.layout.app')
@section('title', 'Dashboard · BidFreq™')


@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/project_detail.css') }}">
@endpush

@section('content')
  <main class="app-content">

      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Home</a>
        <span class="breadcrumbs__sep">/</span>
        <a href="{{ route('projects') }}">Approved Projects</a>
        <span class="breadcrumbs__sep">/</span>
        <span class="breadcrumbs__current">PRJ-2026-0021</span>
      </nav>

      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-header__title">Project Details</h1>
          <p class="page-header__subtitle">
            Approved project record retaining the approved estimate, property information,
            approval status, and related estimate history (SOW §3.9).
          </p>
        </div>
        <div class="page-header__actions">
          <button class="btn btn--secondary" id="downloadPdf">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download Project PDF
          </button>
          <button class="btn btn--ghost" onclick="window.print()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print
          </button>
        </div>
      </header>

      <!-- ============================================================
           MAIN GRID
           ============================================================ -->
      <div class="detail-grid">

        <!-- ============ LEFT: MAIN ============ -->
        <div class="detail-main">

          <!-- HEADER BANNER -->
          <section class="proj-header">
            <div class="proj-header__top">
              <div class="proj-header__eyebrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Approved Project
              </div>
              <div class="proj-header__id">
                PRJ-2026-0021
                <span class="chip chip--warning" style="background:rgba(245,158,11,.18);color:#FCD34D;border-color:transparent">
                  <span class="chip__dot"></span>
                  In Progress
                </span>
              </div>
              <div class="proj-header__property">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>305 Meadow Rd, Austin, TX 78704</span>
                <span class="dot"></span>
                <span>HVAC</span>
                <span class="dot"></span>
                <span>From EST-2026-0038</span>
              </div>

              <div class="proj-header__progress">
                <div>
                  <div class="proj-header__progress-label">Project Progress</div>
                  <div class="proj-header__progress-value">60%</div>
                </div>
                <div class="proj-header__progress-bar">
                  <div class="proj-header__progress-fill" style="width:60%"></div>
                </div>
                <div class="text-sm" style="color:#C7D2E7;min-width:auto">
                  Est. completion <strong style="color:#fff">Aug 22, 2026</strong>
                </div>
              </div>
            </div>

            <div class="proj-header__meta">
              <div class="proj-header__meta-item">
                <div class="proj-header__meta-label">Approved On</div>
                <div class="proj-header__meta-value">Aug 8, 2026 · 4:12 PM</div>
              </div>
              <div class="proj-header__meta-item">
                <div class="proj-header__meta-label">Approved By</div>
                <div class="proj-header__meta-value">Jane Doe · Org Admin</div>
              </div>
              <div class="proj-header__meta-item">
                <div class="proj-header__meta-label">Signed Acceptance</div>
                <div class="proj-header__meta-value">
                  <span class="tag-verified" style="padding:2px 8px">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    E-Signed
                  </span>
                </div>
              </div>
              <div class="proj-header__meta-item">
                <div class="proj-header__meta-label">Est. Completion</div>
                <div class="proj-header__meta-value">Aug 22, 2026</div>
              </div>
            </div>
          </section>

          <!-- LIFECYCLE STAGES -->
          <section class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon sec__icon--emerald">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                  <div class="sec__title">Project Lifecycle</div>
                  <div class="sec__sub">Approval → scheduling → work → completion</div>
                </div>
              </div>
            </div>
            <div class="sec__body">
              <div class="lifecycle">
                <div class="lifecycle__step is-done">
                  <div class="lifecycle__marker">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </div>
                  <div class="lifecycle__label">Estimate approved</div>
                </div>
                <div class="lifecycle__step is-done">
                  <div class="lifecycle__marker">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </div>
                  <div class="lifecycle__label">Scheduled</div>
                </div>
                <div class="lifecycle__step is-active">
                  <div class="lifecycle__marker">3</div>
                  <div class="lifecycle__label">Work in progress</div>
                </div>
                <div class="lifecycle__step">
                  <div class="lifecycle__marker">4</div>
                  <div class="lifecycle__label">Inspection</div>
                </div>
                <div class="lifecycle__step">
                  <div class="lifecycle__marker">5</div>
                  <div class="lifecycle__label">Completed</div>
                </div>
              </div>

              <div class="alert alert--info mb-0">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <div class="alert__body">
                  <div class="alert__title">Work in progress</div>
                  HVAC installation underway. Expected completion Aug 22, 2026. You'll be notified when inspection is scheduled.
                </div>
              </div>
            </div>
          </section>

          <!-- APPROVED ESTIMATE SNAPSHOT -->
          <section class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon sec__icon--violet">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                  <div class="sec__title">Approved Estimate</div>
                  <div class="sec__sub">Frozen snapshot of the estimate as approved</div>
                </div>
              </div>
              <a href="{{ route('estimate_request_detail') }}" class="btn btn--ghost btn--sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                View full estimate
              </a>
            </div>
            <div class="sec__body">
              <div class="estimate-snapshot">
                <div class="estimate-snapshot__head">
                  <div class="estimate-snapshot__title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
                    AI-Drafted · Human-Verified
                  </div>
                  <span class="tag-verified">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    B. Hayes
                  </span>
                </div>
                <div class="estimate-snapshot__body">
                  <div class="estimate-snapshot__amount">$4,950.00</div>
                  <div class="estimate-snapshot__sub">Approved final amount · includes tax · valid through Sep 8, 2026</div>

                  <div class="estimate-snapshot__line">
                    <span>Trade</span>
                    <span>HVAC</span>
                  </div>
                  <div class="estimate-snapshot__line">
                    <span>Line items</span>
                    <span>6</span>
                  </div>
                  <div class="estimate-snapshot__line">
                    <span>Materials</span>
                    <span>$1,840.00</span>
                  </div>
                  <div class="estimate-snapshot__line">
                    <span>Labor</span>
                    <span>$2,520.00</span>
                  </div>
                  <div class="estimate-snapshot__line">
                    <span>Permits &amp; fees</span>
                    <span>$175.00</span>
                  </div>
                  <div class="estimate-snapshot__line">
                    <span>Tax (8.25%)</span>
                    <span>$415.00</span>
                  </div>
                  <div class="estimate-snapshot__line">
                    <span>Final total</span>
                    <span style="color:var(--emerald-700);font-weight:700;font-variant-numeric:tabular-nums">$4,950.00</span>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- LINKED RECORDS -->
          <section class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                </div>
                <div>
                  <div class="sec__title">Linked Records</div>
                  <div class="sec__sub">Every record connected to this project</div>
                </div>
              </div>
            </div>
            <div class="sec__body" style="padding:0">

              <div class="link-row" onclick="location.href=this.dataset.href" data-href="{{ route('estimate_request_detail') }}">
                <div class="link-row__icon link-row__icon--violet">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="link-row__body">
                  <div class="link-row__label">Approved Estimate</div>
                  <div class="link-row__id">EST-2026-0038</div>
                  <div class="link-row__meta">Approved Aug 8, 2026 · $4,950.00</div>
                </div>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-faint)"><line x1="9" y1="18" x2="15" y2="12"/><line x1="15" y1="6" x2="9" y2="12"/></svg>
              </div>

              <div class="link-row" onclick="location.href=this.dataset.href" data-href="{{ route('estimate-request') }}">
                <div class="link-row__icon link-row__icon--blue">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
                </div>
                <div class="link-row__body">
                  <div class="link-row__label">Original Request</div>
                  <div class="link-row__id">REQ-2026-0038</div>
                  <div class="link-row__meta">Submitted Aug 4, 2026 · 7 photos · 1 inspection report</div>
                </div>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-faint)"><line x1="9" y1="18" x2="15" y2="12"/><line x1="15" y1="6" x2="9" y2="12"/></svg>
              </div>

              <div class="link-row" onclick="location.href=this.dataset.href" data-href="{{ route('profile') }}">
                <div class="link-row__icon link-row__icon--emerald">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                </div>
                <div class="link-row__body">
                  <div class="link-row__label">Organization</div>
                  <div class="link-row__id">Oak Ridge Property Mgmt</div>
                  <div class="link-row__meta">Owner of record · 24 active properties</div>
                </div>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-faint)"><line x1="9" y1="18" x2="15" y2="12"/><line x1="15" y1="6" x2="9" y2="12"/></svg>
              </div>

            </div>
          </section>

          <!-- DOCUMENTS -->
          <section class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                  <div class="sec__title">Documents</div>
                  <div class="sec__sub">4 files linked to this project</div>
                </div>
              </div>
              <button class="btn btn--ghost btn--sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload
              </button>
            </div>
            <div class="sec__body" style="padding:0">

              <div class="doc-row">
                <div class="doc-row__icon doc-row__icon--red">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="doc-row__body">
                  <div class="doc-row__name">BidFreq_Estimate_EST-2026-0038.pdf</div>
                  <div class="doc-row__meta">Final approved estimate · 248 KB · Aug 8, 2026</div>
                </div>
                <button class="doc-row__action" aria-label="Download">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </button>
              </div>

              <div class="doc-row">
                <div class="doc-row__icon doc-row__icon--violet">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="doc-row__body">
                  <div class="doc-row__name">Inspection_Report_Aug_2026.pdf</div>
                  <div class="doc-row__meta">Uploaded by customer · 1.2 MB · Aug 4, 2026</div>
                </div>
                <button class="doc-row__action" aria-label="Download">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </button>
              </div>

              <div class="doc-row">
                <div class="doc-row__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div class="doc-row__body">
                  <div class="doc-row__name">Project_Photos_7_items.zip</div>
                  <div class="doc-row__meta">Original submitted photos · 18.4 MB · Aug 4, 2026</div>
                </div>
                <button class="doc-row__action" aria-label="Download">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </button>
              </div>

              <div class="doc-row">
                <div class="doc-row__icon doc-row__icon--red">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="doc-row__body">
                  <div class="doc-row__name">Signed_Acceptance_Aug_8_2026.pdf</div>
                  <div class="doc-row__meta">E-signed by Jane Doe · 96 KB · Aug 8, 2026</div>
                </div>
                <button class="doc-row__action" aria-label="Download">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </button>
              </div>

            </div>
          </section>

        </div>

        <!-- ============ RIGHT: SIDEBAR ============ -->
        <aside class="detail-side">

          <!-- ACTION CARD -->
          <div class="action-card">
            <div class="action-card__top">
              <div class="action-card__label">Approved Amount</div>
              <div class="action-card__amount">$4,950.00</div>
              <div class="action-card__sub">Approved Aug 8, 2026 · E-signed</div>
            </div>
            <div class="action-card__body">
              <button class="btn btn--secondary btn--block" id="downloadPdfSide">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download Project PDF
              </button>
              <button class="btn btn--subtle btn--block" id="messageBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Message Project Team
              </button>
              <div class="divider" style="margin:var(--s-2) 0"></div>
              <button class="btn btn--ghost btn--block btn--sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/></svg>
                Email project summary
              </button>
            </div>
          </div>

          <!-- PROJECT STATUS -->
          <div class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                  <div class="sec__title">Status</div>
                  <div class="sec__sub">Current project state</div>
                </div>
              </div>
            </div>
            <div>
              <div class="status-row">
                <span class="status-row__label">Approval status</span>
                <span class="status-row__value">
                  <span class="chip status--project" style="padding:2px 8px"><span class="chip__dot"></span>Approved</span>
                </span>
              </div>
              <div class="status-row">
                <span class="status-row__label">Work status</span>
                <span class="status-row__value">
                  <span class="chip chip--warning" style="padding:2px 8px"><span class="chip__dot"></span>In Progress</span>
                </span>
              </div>
              <div class="status-row">
                <span class="status-row__label">E-signed by</span>
                <span class="status-row__value">Jane Doe</span>
              </div>
              <div class="status-row">
                <span class="status-row__label">Signed on</span>
                <span class="status-row__value">Aug 8, 2026</span>
              </div>
              <div class="status-row">
                <span class="status-row__label">Localized ZIP</span>
                <span class="status-row__value">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--emerald-600)"><polyline points="20 6 9 17 4 12"/></svg>
                  78704
                </span>
              </div>
            </div>
          </div>

          <!-- TIMELINE -->
          <div class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                  <div class="sec__title">History</div>
                  <div class="sec__sub">All project events</div>
                </div>
              </div>
            </div>
            <div class="timeline-list">
              <div class="mini-tl-item is-done">
                <div class="mini-tl-dot"></div>
                <div class="mini-tl-body">
                  <div class="mini-tl-title">Request submitted</div>
                  <div class="mini-tl-meta">Aug 4, 2026 · 9:42 AM<br>7 photos uploaded</div>
                </div>
              </div>
              <div class="mini-tl-item is-done">
                <div class="mini-tl-dot"></div>
                <div class="mini-tl-body">
                  <div class="mini-tl-title">Estimate issued</div>
                  <div class="mini-tl-meta">Aug 6, 2026 · 11:08 AM<br>Human-verified by B. Hayes</div>
                </div>
              </div>
              <div class="mini-tl-item is-done">
                <div class="mini-tl-dot"></div>
                <div class="mini-tl-body">
                  <div class="mini-tl-title">Approved &amp; e-signed</div>
                  <div class="mini-tl-meta">Aug 8, 2026 · 4:12 PM<br>By Jane Doe</div>
                </div>
              </div>
              <div class="mini-tl-item is-done">
                <div class="mini-tl-dot"></div>
                <div class="mini-tl-body">
                  <div class="mini-tl-title">Project scheduled</div>
                  <div class="mini-tl-meta">Aug 10, 2026<br>Start date Aug 15, 2026</div>
                </div>
              </div>
              <div class="mini-tl-item is-active">
                <div class="mini-tl-dot"></div>
                <div class="mini-tl-body">
                  <div class="mini-tl-title">Work in progress</div>
                  <div class="mini-tl-meta">60% complete · expected Aug 22</div>
                </div>
              </div>
              <div class="mini-tl-item">
                <div class="mini-tl-dot"></div>
                <div class="mini-tl-body">
                  <div class="mini-tl-title">Inspection &amp; completion</div>
                  <div class="mini-tl-meta">Pending</div>
                </div>
              </div>
            </div>
          </div>

          <!-- HELP -->
          <div class="card">
            <div class="card__body">
              <div class="flex items-start gap-3">
                <div class="stat-card__icon" style="width:36px;height:36px">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div>
                  <h4 style="font-size:var(--fs-sm);margin-bottom:4px">Need help with this project?</h4>
                  <p class="text-xs text-muted mb-3">
                    Our support team can help with scheduling, changes, or questions.
                  </p>
                  <button class="btn btn--subtle btn--sm" id="supportBtn">
                    Contact support
                  </button>
                </div>
              </div>
            </div>
          </div>

        </aside>
      </div>

    </main>
@endsection
@push('scripts')

   
    <script src="{{ asset('front_assets/pages_js/project_detail.js') }}"></script>
@endpush
