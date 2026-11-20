@extends('front.layout.app')
@section('title', 'Dashboard · BidFreq™')


@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/estimate_request_detail.css') }}">
@endpush

@section('content')

   <main class="app-content">

      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Home</a>
        <span class="breadcrumbs__sep">/</span>
        <a href="{{ route('estimate-request') }}">Estimate Requests</a>
        <span class="breadcrumbs__sep">/</span>
        <span class="breadcrumbs__current">EST-2026-0040</span>
      </nav>

      <!-- ============================================================
           HEADER ACTIONS
           ============================================================ -->
      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-header__title">Estimate Details</h1>
          <p class="page-header__subtitle">
            Human-verified estimate ready for your review and approval.
          </p>
        </div>
        <div class="page-header__actions">
          <button class="btn btn--secondary" id="downloadPdf">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download PDF
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

        <!-- ============ LEFT: MAIN CONTENT ============ -->
        <div class="detail-main">

          <!-- HEADER CARD -->
          <section class="est-header">
            <div class="est-header__top">
              <div class="est-header__eyebrow">Estimate ID</div>
              <div class="est-header__id">
                EST-2026-0040
                <span class="chip status--awaiting-customer" style="background:rgba(59,130,246,.15);color:#93C5FD;border-color:transparent">
                  <span class="chip__dot"></span>
                  Awaiting Customer
                </span>
              </div>
              <div class="est-header__property">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>1502 Palm Ave, Houston, TX 77002</span>
                <span class="dot"></span>
                <span>Plumbing</span>
                <span class="dot"></span>
                <span>5 photos</span>
              </div>
            </div>

            <div class="est-header__meta">
              <div class="est-header__meta-item">
                <div class="est-header__meta-label">Submitted</div>
                <div class="est-header__meta-value">Aug 9, 2026 · 9:14 AM</div>
              </div>
              <div class="est-header__meta-item">
                <div class="est-header__meta-label">Verified by</div>
                <div class="est-header__meta-value">B. Hayes · BidFreq Estimator</div>
              </div>
              <div class="est-header__meta-item">
                <div class="est-header__meta-label">Sent to you</div>
                <div class="est-header__meta-value">Aug 10, 2026 · 2:46 PM</div>
              </div>
              <div class="est-header__meta-item">
                <div class="est-header__meta-label">Valid until</div>
                <div class="est-header__meta-value">Sep 9, 2026</div>
              </div>
            </div>
          </section>

          <!-- SCOPE OF WORK -->
          <section class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon sec__icon--emerald">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
                <div>
                  <div class="sec__title">Scope of Work</div>
                  <div class="sec__sub">Reviewed and approved by a BidFreq human estimator</div>
                </div>
              </div>
              <span class="tag-verified">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Human Verified
              </span>
            </div>

            <div class="sec__body">

              <p class="text-sm mb-5" style="line-height:1.65">
                Repair water damage to kitchen ceiling and adjacent wall resulting from a slow leak
                originating from the upstairs bathroom. Work includes removal of affected drywall,
                inspection for hidden moisture, replacement, texture, prime, and paint to match
                existing finish. All work performed to industry standards and local building codes.
              </p>

              <!-- Trade group: Drywall & Paint -->
              <div class="trade-group">
                <div class="trade-group__head">
                  <div class="trade-group__title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    Drywall &amp; Paint
                  </div>
                  <div class="trade-group__total">$1,180.00</div>
                </div>
                <table class="line-items">
                  <thead>
                    <tr>
                      <th style="width:55%">Description</th>
                      <th>Qty</th>
                      <th class="num">Unit</th>
                      <th class="num">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="desc">
                        <strong>Remove damaged drywall</strong>
                        <span>Ceiling (3 sq ft) + wall below stain (9 sq ft). Includes disposal.</span>
                      </td>
                      <td>
                        <span class="qty-cell"><strong>12</strong><span>sq ft</span></span>
                      </td>
                      <td class="num">$4.50</td>
                      <td class="num">$54.00</td>
                    </tr>
                    <tr>
                      <td class="desc">
                        <strong>Inspect &amp; treat for moisture / mold</strong>
                        <span>Antimicrobial application to exposed framing and backing.</span>
                      </td>
                      <td>
                        <span class="qty-cell"><strong>1</strong><span>area</span></span>
                      </td>
                      <td class="num">$65.00</td>
                      <td class="num">$65.00</td>
                    </tr>
                    <tr>
                      <td class="desc">
                        <strong>Install new drywall</strong>
                        <span>1/2" moisture-resistant board, taped, mudded, and sanded.</span>
                      </td>
                      <td>
                        <span class="qty-cell"><strong>12</strong><span>sq ft</span></span>
                      </td>
                      <td class="num">$8.00</td>
                      <td class="num">$96.00</td>
                    </tr>
                    <tr>
                      <td class="desc">
                        <strong>Texture &amp; match finish</strong>
                        <span>Orange peel texture to match adjacent ceiling and wall.</span>
                      </td>
                      <td>
                        <span class="qty-cell"><strong>12</strong><span>sq ft</span></span>
                      </td>
                      <td class="num">$5.50</td>
                      <td class="num">$66.00</td>
                    </tr>
                    <tr>
                      <td class="desc">
                        <strong>Prime &amp; paint</strong>
                        <span>Two coats premium interior paint, color-matched to existing.</span>
                      </td>
                      <td>
                        <span class="qty-cell"><strong>12</strong><span>sq ft</span></span>
                      </td>
                      <td class="num">$7.50</td>
                      <td class="num">$90.00</td>
                    </tr>
                    <tr>
                      <td class="desc">
                        <strong>Materials &amp; supplies</strong>
                        <span>Drywall, joint compound, tape, texture, primer, paint, drop cloths.</span>
                      </td>
                      <td>
                        <span class="qty-cell"><strong>1</strong><span>lot</span></span>
                      </td>
                      <td class="num">$209.00</td>
                      <td class="num">$209.00</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Trade group: Plumbing -->
              <div class="trade-group">
                <div class="trade-group__head">
                  <div class="trade-group__title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4h6v6"/><path d="M20 4L9 15"/><path d="M14 20H8v-6"/><path d="M20 20L9 9"/></svg>
                    Plumbing
                  </div>
                  <div class="trade-group__total">$285.00</div>
                </div>
                <table class="line-items">
                  <thead>
                    <tr>
                      <th style="width:55%">Description</th>
                      <th>Qty</th>
                      <th class="num">Unit</th>
                      <th class="num">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="desc">
                        <strong>Locate &amp; repair leak source</strong>
                        <span>Upstairs bathroom — shower drain gasket replacement and reseal.</span>
                      </td>
                      <td>
                        <span class="qty-cell"><strong>1</strong><span>repair</span></span>
                      </td>
                      <td class="num">$185.00</td>
                      <td class="num">$185.00</td>
                    </tr>
                    <tr>
                      <td class="desc">
                        <strong>Replace shower drain gasket &amp; sealant</strong>
                        <span>Includes materials and cleanup.</span>
                      </td>
                      <td>
                        <span class="qty-cell"><strong>1</strong><span>kit</span></span>
                      </td>
                      <td class="num">$100.00</td>
                      <td class="num">$100.00</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- TOTALS -->
              <div class="totals">
                <div class="totals__row">
                  <span class="totals__label">Drywall &amp; Paint subtotal</span>
                  <span class="totals__value">$1,180.00</span>
                </div>
                <div class="totals__row">
                  <span class="totals__label">Plumbing subtotal</span>
                  <span class="totals__value">$285.00</span>
                </div>
                <div class="totals__row">
                  <span class="totals__label">Materials</span>
                  <span class="totals__value">$309.00</span>
                </div>
                <div class="totals__row">
                  <span class="totals__label">Labor</span>
                  <span class="totals__value">$1,020.00</span>
                </div>
                <div class="totals__row">
                  <span class="totals__label">Permits &amp; fees <span class="text-xs text-muted">(none required)</span></span>
                  <span class="totals__value">$0.00</span>
                </div>
                <div class="totals__row">
                  <span class="totals__label">Tax (8.25%)</span>
                  <span class="totals__value">$153.50</span>
                </div>
                <div class="totals__row totals__row--grand">
                  <span>Final Estimate</span>
                  <span class="totals__value">$1,482.50</span>
                </div>
              </div>

            </div>
          </section>

          <!-- ASSUMPTIONS -->
          <section class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </div>
                <div>
                  <div class="sec__title">Assumptions</div>
                  <div class="sec__sub">Conditions this estimate is based on</div>
                </div>
              </div>
            </div>
            <div class="sec__body">
              <div class="list-block">
                <div class="list-item">
                  <div class="list-item__marker list-item__marker--assume">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </div>
                  <div><strong>Access is available during normal business hours.</strong> Work area will be clear of furniture and personal items.</div>
                </div>
                <div class="list-item">
                  <div class="list-item__marker list-item__marker--assume">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </div>
                  <div><strong>No mold remediation is required.</strong> Photos show no visible mold growth; hidden mold would be handled separately.</div>
                </div>
                <div class="list-item">
                  <div class="list-item__marker list-item__marker--assume">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </div>
                  <div><strong>Paint color can be matched to existing.</strong> If exact match is unavailable, a close match will be used at no additional cost.</div>
                </div>
                <div class="list-item">
                  <div class="list-item__marker list-item__marker--assume">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </div>
                  <div><strong>Plumbing leak repair resolves the water intrusion.</strong> Additional leaks discovered during work would be quoted separately.</div>
                </div>
              </div>
            </div>
          </section>

          <!-- EXCLUSIONS -->
          <section class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div>
                  <div class="sec__title">Exclusions</div>
                  <div class="sec__sub">Items not included in this estimate</div>
                </div>
              </div>
            </div>
            <div class="sec__body">
              <div class="list-block">
                <div class="list-item">
                  <div class="list-item__marker list-item__marker--exclude">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </div>
                  <div><strong>Mold remediation</strong> — if hidden mold is discovered, a separate specialty quote will be provided.</div>
                </div>
                <div class="list-item">
                  <div class="list-item__marker list-item__marker--exclude">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </div>
                  <div><strong>Structural repairs</strong> — if framing damage is found after opening the wall, additional work will be quoted.</div>
                </div>
                <div class="list-item">
                  <div class="list-item__marker list-item__marker--exclude">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </div>
                  <div><strong>Upstairs bathroom renovation</strong> — only the drain seal is repaired, not fixtures or tile.</div>
                </div>
                <div class="list-item">
                  <div class="list-item__marker list-item__marker--exclude">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </div>
                  <div><strong>Relocation or storage of contents</strong> — occupants are responsible for clearing the work area.</div>
                </div>
              </div>
            </div>
          </section>

          <!-- RISK FLAGS -->
          <section class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon sec__icon--warning">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div>
                  <div class="sec__title">Risk Flags</div>
                  <div class="sec__sub">Items that may affect final cost</div>
                </div>
              </div>
              <span class="chip chip--warning">
                <span class="chip__dot"></span>
                2 flagged
              </span>
            </div>
            <div class="sec__body">
              <div class="list-block">
                <div class="list-item">
                  <div class="list-item__marker list-item__marker--risk-high">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                  </div>
                  <div>
                    <strong>Hidden moisture risk — HIGH.</strong>
                    Wet framing was not directly visible in the submitted photos. If hidden moisture or mold is found once drywall is removed, additional remediation may be required and will be quoted separately.
                  </div>
                </div>
                <div class="list-item">
                  <div class="list-item__marker list-item__marker--risk-warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                  </div>
                  <div>
                    <strong>Paint match sensitivity — MODERATE.</strong>
                    If existing paint is faded or was custom-mixed, exact color match cannot be guaranteed. A close match will be used.
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- PHOTOS -->
          <section class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div>
                  <div class="sec__title">Submitted Evidence</div>
                  <div class="sec__sub">5 photos uploaded with the original request</div>
                </div>
              </div>
              <button class="btn btn--ghost btn--sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                View all
              </button>
            </div>
            <div class="sec__body">
              <div class="photo-grid">
                <div class="photo-tile">
                  <div class="photo-tile__img">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                  </div>
                  <span class="photo-tile__badge">1</span>
                  <div class="photo-tile__label">Ceiling — wide view</div>
                </div>
                <div class="photo-tile">
                  <div class="photo-tile__img">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                  </div>
                  <span class="photo-tile__badge">2</span>
                  <div class="photo-tile__label">Stain — close-up</div>
                </div>
                <div class="photo-tile">
                  <div class="photo-tile__img">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                  </div>
                  <span class="photo-tile__badge">3</span>
                  <div class="photo-tile__label">Wall — soft spot</div>
                </div>
                <div class="photo-tile">
                  <div class="photo-tile__img">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                  </div>
                  <span class="photo-tile__badge">4</span>
                  <div class="photo-tile__label">Ceiling — scale ref</div>
                </div>
                <div class="photo-tile">
                  <div class="photo-tile__img">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                  </div>
                  <span class="photo-tile__badge">5</span>
                  <div class="photo-tile__label">Upstairs drain</div>
                </div>
              </div>
            </div>
          </section>

        </div>

        <!-- ============ RIGHT: STICKY SIDEBAR ============ -->
        <aside class="detail-side">

          <!-- ACTION CARD -->
          <div class="action-card">
            <div class="action-card__top">
              <div class="action-card__label">Final Estimate</div>
              <div class="action-card__amount">$1,482.50</div>
              <div class="action-card__sub">Human-verified · valid through Sep 9, 2026</div>
            </div>
            <div class="action-card__body">
              <button class="btn btn--success btn--lg btn--block" id="approveBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Approve Estimate
              </button>
              <button class="btn btn--secondary btn--block" id="revisionBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                Request Revision
              </button>
              <button class="btn btn--danger-outline btn--block" id="declineBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Decline
              </button>
              <div class="divider" style="margin:var(--s-2) 0"></div>
              <button class="btn btn--ghost btn--block btn--sm" id="downloadPdfSide">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download PDF
              </button>
            </div>
          </div>

          <!-- AI CONFIDENCE -->
          <div class="sec">
            <div class="sec__body" style="padding:0">
              <div style="padding:var(--s-4) var(--s-5);border-bottom:1px solid var(--border)">
                <div class="conf-widget">
                  <div class="conf-widget__head">
                    <span>
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
                      AI Confidence
                    </span>
                    <span class="conf-widget__value">94%</span>
                  </div>
                  <div class="confidence">
                    <div class="confidence__bar" style="height:8px">
                      <div class="confidence__fill confidence__fill--high" style="width:94%"></div>
                    </div>
                  </div>
                  <div class="conf-widget__note">
                    Initial AI draft was 82% confident. Human estimator revised quantities and line items, raising final confidence to 94%.
                  </div>
                </div>
              </div>

              <div style="padding:var(--s-2) var(--s-3)">
                <div class="status-row">
                  <span class="status-row__label">AI draft</span>
                  <span class="status-row__value">
                    <span class="tag-ai" style="padding:2px 8px">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
                      82%
                    </span>
                  </span>
                </div>
                <div class="status-row">
                  <span class="status-row__label">Human verified</span>
                  <span class="status-row__value">
                    <span class="tag-verified" style="padding:2px 8px">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                      B. Hayes
                    </span>
                  </span>
                </div>
                <div class="status-row">
                  <span class="status-row__label">Edits made</span>
                  <span class="status-row__value">3 line items</span>
                </div>
                <div class="status-row">
                  <span class="status-row__label">Localized pricing</span>
                  <span class="status-row__value">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--emerald-600)"><polyline points="20 6 9 17 4 12"/></svg>
                    ZIP 77002
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- TIMELINE -->
          <div class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                  <div class="sec__title">Status history</div>
                  <div class="sec__sub">End-to-end timeline</div>
                </div>
              </div>
            </div>
            <div class="mini-timeline">
              <div class="mini-tl-item is-done">
                <div class="mini-tl-dot"></div>
                <div class="mini-tl-body">
                  <div class="mini-tl-title">Request submitted</div>
                  <div class="mini-tl-meta">Aug 9 · 9:14 AM</div>
                </div>
              </div>
              <div class="mini-tl-item is-done">
                <div class="mini-tl-dot"></div>
                <div class="mini-tl-body">
                  <div class="mini-tl-title">AI analysis complete</div>
                  <div class="mini-tl-meta">Aug 9 · 9:17 AM · 82% confidence</div>
                </div>
              </div>
              <div class="mini-tl-item is-done">
                <div class="mini-tl-dot"></div>
                <div class="mini-tl-body">
                  <div class="mini-tl-title">Estimator verified &amp; adjusted</div>
                  <div class="mini-tl-meta">Aug 10 · 2:45 PM · B. Hayes</div>
                </div>
              </div>
              <div class="mini-tl-item is-done">
                <div class="mini-tl-dot"></div>
                <div class="mini-tl-body">
                  <div class="mini-tl-title">PDF issued &amp; emailed</div>
                  <div class="mini-tl-meta">Aug 10 · 2:46 PM</div>
                </div>
              </div>
              <div class="mini-tl-item is-active">
                <div class="mini-tl-dot"></div>
                <div class="mini-tl-body">
                  <div class="mini-tl-title">Awaiting your decision</div>
                  <div class="mini-tl-meta">Approve, decline, or request revision</div>
                </div>
              </div>
            </div>
          </div>

          <!-- HELP CARD -->
          <!-- <div class="card">
            <div class="card__body">
              <div class="flex items-start gap-3">
                <div class="stat-card__icon stat-card__icon--violet" style="width:36px;height:36px">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div>
                  <h4 style="font-size:var(--fs-sm);margin-bottom:4px">Questions about this estimate?</h4>
                  <p class="text-xs text-muted mb-3">
                    Estimator B. Hayes can answer questions directly.
                  </p>
                  <button class="btn btn--subtle btn--sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Send a message
                  </button>
                </div>
              </div>
            </div>
          </div> -->

        </aside>
      </div>

    </main>
<div class="modal-overlay" id="approveModal" onclick="if(event.target===this)this.classList.remove('is-open')">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="ap-title">
    <div class="modal__header">
      <div>
        <div class="modal__title" id="ap-title">Approve this estimate?</div>
        <div class="modal__subtitle">By approving you accept the scope and total amount shown.</div>
      </div>
      <button class="modal__close" aria-label="Close" onclick="document.getElementById('approveModal').classList.remove('is-open')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal__body">
      <div class="panel-verified mb-5">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs" style="color:var(--emerald-700);text-transform:uppercase;letter-spacing:.08em;font-weight:600">Final Estimate</div>
            <div class="text-sm text-muted mt-1">EST-2026-0040 · 1502 Palm Ave</div>
          </div>
          <span class="text-mono" style="font-size:var(--fs-2xl);font-weight:var(--fw-bold);color:var(--text-strong)">$1,482.50</span>
        </div>
      </div>

      <label class="check mb-4">
        <input type="checkbox" id="approveConsent">
        <span class="check__box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </span>
        <span class="check__label">
          <strong>I agree to the scope, price, and terms above</strong>
          <span class="check__desc">Your typed name below acts as a legally binding electronic signature.</span>
        </span>
      </label>

      <div class="form-group" style="margin-bottom:0">
        <label class="label" for="approveSign">Type your full name to sign <span class="req">*</span></label>
        <input class="input" id="approveSign" type="text" placeholder="e.g. Jane Doe">
      </div>
    </div>
    <div class="modal__footer">
      <button class="btn btn--ghost" onclick="document.getElementById('approveModal').classList.remove('is-open')">Cancel</button>
      <button class="btn btn--success" id="approveConfirm" disabled>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        Approve &amp; Move to Project
      </button>
    </div>
  </div>
</div>

<!-- REQUEST REVISION -->
<div class="modal-overlay" id="revisionModal" onclick="if(event.target===this)this.classList.remove('is-open')">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="rv-title">
    <div class="modal__header">
      <div>
        <div class="modal__title" id="rv-title">Request a revision</div>
        <div class="modal__subtitle">The estimate will return to a BidFreq estimator for review.</div>
      </div>
      <button class="modal__close" aria-label="Close" onclick="document.getElementById('revisionModal').classList.remove('is-open')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal__body">
      <div class="form-group">
        <label class="label" for="revReason">Reason for revision <span class="req">*</span></label>
        <select class="select" id="revReason">
          <option value="">Select a reason…</option>
          <option>Scope is incorrect</option>
          <option>Pricing seems off</option>
          <option>Missing line item</option>
          <option>Additional damage discovered</option>
          <option>I want to change the scope of work</option>
          <option>Other</option>
        </select>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="label" for="revNotes">Additional notes <span class="req">*</span></label>
        <textarea class="textarea" id="revNotes" placeholder="Explain what needs to change so the estimator can revise accurately…"></textarea>
      </div>
      <label class="check mt-4">
        <input type="checkbox">
        <span class="check__box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </span>
        <span class="check__label">
          <strong>Upload additional photos</strong>
          <span class="check__desc">Optional — helps the estimator revise more accurately.</span>
        </span>
      </label>
    </div>
    <div class="modal__footer">
      <button class="btn btn--ghost" onclick="document.getElementById('revisionModal').classList.remove('is-open')">Cancel</button>
      <button class="btn btn--primary" id="revisionConfirm" disabled>Send Revision Request</button>
    </div>
  </div>
</div>

<!-- DECLINE -->
<div class="modal-overlay" id="declineModal" onclick="if(event.target===this)this.classList.remove('is-open')">
  <div class="modal modal--sm" role="dialog" aria-modal="true" aria-labelledby="dc-title">
    <div class="modal__header">
      <div>
        <div class="modal__title" id="dc-title">Decline this estimate?</div>
        <div class="modal__subtitle">This action will mark the estimate as declined.</div>
      </div>
      <button class="modal__close" aria-label="Close" onclick="document.getElementById('declineModal').classList.remove('is-open')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal__body">
      <div class="form-group" style="margin-bottom:0">
        <label class="label" for="declineReason">Why are you declining? <span class="label__hint">optional</span></label>
        <textarea class="textarea" id="declineReason" placeholder="Share feedback to help us improve (optional)…" style="min-height:80px"></textarea>
      </div>
      <div class="alert alert--danger mt-4 mb-0">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <div class="alert__body">This cannot be undone. You can submit a new request later.</div>
      </div>
    </div>
    <div class="modal__footer">
      <button class="btn btn--ghost" onclick="document.getElementById('declineModal').classList.remove('is-open')">Cancel</button>
      <button class="btn btn--danger" id="declineConfirm">Decline Estimate</button>
    </div>
  </div>
</div>
@endsection
@push('scripts')

    
    <script src="{{ asset('front_assets/pages_js/estimate_request_detail.js') }}"></script>
@endpush
