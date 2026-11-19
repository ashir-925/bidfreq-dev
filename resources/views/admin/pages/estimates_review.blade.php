@extends('admin.layout.app')
@section('title', 'Estimator Console · BidFreq™')


@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('admin_assets/pages_css/dashboard.css') }}"> --}}
@endpush

@section('content')
     <main class="app-content">

      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="dashboard.html">Console</a>
        <span class="breadcrumbs__sep">/</span>
        <a href="estimates.html">Estimate Queue</a>
        <span class="breadcrumbs__sep">/</span>
        <span class="breadcrumbs__current">EST-2026-0042</span>
      </nav>

      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-header__title">Review &amp; Verify Estimate</h1>
          <p class="page-header__subtitle">
            Edit the AI draft, add internal notes, and issue the final human-verified estimate
            to the customer (SOW §3.3).
          </p>
        </div>
        <div class="page-header__actions">
          <button class="btn btn--secondary" id="saveDraftBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save draft
          </button>
          <button class="btn btn--ghost" id="returnBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            Return to AI
          </button>
        </div>
      </header>

      <div class="review-grid">

        <!-- ============ LEFT: MAIN ============ -->
        <div class="review-main">

          <!-- HEADER -->
          <section class="rev-header">
            <div class="rev-header__top">
              <div class="rev-header__eyebrow">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
                AI Draft · 82% confidence · Awaiting Human Verification
              </div>
              <div class="rev-header__id">
                EST-2026-0042
                <span class="chip chip--danger" style="background:rgba(239,68,68,.15);color:#FCA5A5;border-color:transparent">
                  <span class="chip__dot"></span>
                  High Priority
                </span>
              </div>
              <div class="rev-header__property">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>421 Oak Ridge Dr, Austin, TX 78745</span>
                <span class="dot"></span>
                <span>General Repair</span>
                <span class="dot"></span>
                <span>6 photos</span>
              </div>
            </div>
            <div class="rev-header__meta">
              <div>
                <div class="rev-header__meta-label">Customer</div>
                <div class="rev-header__meta-value">Oak Ridge Property Mgmt</div>
              </div>
              <div>
                <div class="rev-header__meta-label">Submitted</div>
                <div class="rev-header__meta-value">Aug 11, 2026 · 12:18 PM</div>
              </div>
              <div>
                <div class="rev-header__meta-label">AI drafted</div>
                <div class="rev-header__meta-value">Aug 11, 2026 · 12:19 PM</div>
              </div>
              <div>
                <div class="rev-header__meta-label">SLA remaining</div>
                <div class="rev-header__meta-value" style="color:#B45309">1h 42m</div>
              </div>
            </div>
          </section>

          <!-- AI DRAFT vs VERIFIED -->
          <section class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon sec__icon--violet">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
                </div>
                <div>
                  <div class="sec__title">Scope of Work</div>
                  <div class="sec__sub">Edit line items before verifying — changes are logged</div>
                </div>
              </div>
              <span class="tag-ai">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
                AI draft v1
              </span>
            </div>

            <div class="sec__body">

              <div class="diff-banner">
                <div class="diff-banner__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </div>
                <div class="diff-banner__text">
                  <strong>You are editing the AI-generated draft.</strong>
                  Every change you make is recorded for audit and used to improve future AI drafts.
                </div>
                <span class="chip chip--violet" style="padding:4px 10px">
                  <span class="chip__dot"></span>
                  3 edits
                </span>
              </div>

              <div class="trade-group">
                <div class="trade-group__head">
                  <div class="trade-group__title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    General Repair
                  </div>
                  <div class="trade-group__total">$2,840.00</div>
                </div>
                <table class="line-items">
                  <thead>
                    <tr>
                      <th style="width:48%">Description</th>
                      <th>Qty</th>
                      <th class="num">Unit</th>
                      <th class="num">Total</th>
                      <th style="width:70px"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="desc">
                        <strong>Remove damaged drywall</strong>
                        <span>Ceiling &amp; wall section. Includes disposal.</span>
                      </td>
                      <td><span class="qty-cell"><strong>24</strong><span>sq ft</span></span></td>
                      <td class="num">$4.50</td>
                      <td class="num">$108.00</td>
                      <td>
                        <div class="item-actions">
                          <button class="item-action" aria-label="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                          </button>
                          <button class="item-action item-action--danger" aria-label="Delete">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                          </button>
                        </div>
                      </td>
                    </tr>

                    <tr class="line-item-edited">
                      <td class="desc">
                        <strong>Install new drywall</strong>
                        <span>1/2" moisture-resistant board, taped &amp; sanded.</span>
                      </td>
                      <td>
                        <input class="line-edit" value="24" style="width:60px" aria-label="Quantity">
                      </td>
                      <td class="num">$8.00</td>
                      <td class="num">$192.00</td>
                      <td>
                        <div class="item-actions">
                          <button class="item-action" aria-label="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                          </button>
                          <button class="item-action item-action--danger" aria-label="Delete">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                          </button>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td class="desc">
                        <strong>Texture &amp; match finish</strong>
                        <span>Orange peel texture to match adjacent ceiling.</span>
                      </td>
                      <td><span class="qty-cell"><strong>24</strong><span>sq ft</span></span></td>
                      <td class="num">$5.50</td>
                      <td class="num">$132.00</td>
                      <td>
                        <div class="item-actions">
                          <button class="item-action" aria-label="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                          </button>
                          <button class="item-action item-action--danger" aria-label="Delete">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                          </button>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td class="desc">
                        <strong>Prime &amp; paint</strong>
                        <span>Two coats premium interior paint, color-matched.</span>
                      </td>
                      <td><span class="qty-cell"><strong>24</strong><span>sq ft</span></span></td>
                      <td class="num">$7.50</td>
                      <td class="num">$180.00</td>
                      <td>
                        <div class="item-actions">
                          <button class="item-action" aria-label="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                          </button>
                          <button class="item-action item-action--danger" aria-label="Delete">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                          </button>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td class="desc">
                        <strong>Materials &amp; supplies</strong>
                        <span>Drywall, joint compound, tape, primer, paint.</span>
                      </td>
                      <td><span class="qty-cell"><strong>1</strong><span>lot</span></span></td>
                      <td class="num">$412.00</td>
                      <td class="num">$412.00</td>
                      <td>
                        <div class="item-actions">
                          <button class="item-action" aria-label="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                          </button>
                          <button class="item-action item-action--danger" aria-label="Delete">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <button class="btn btn--secondary btn--block btn--sm" id="addLineBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add line item
              </button>

              <!-- TOTALS -->
              <div class="totals">
                <div class="totals__row">
                  <span class="totals__label">Labor subtotal</span>
                  <span class="totals__value">$1,750.00</span>
                </div>
                <div class="totals__row">
                  <span class="totals__label">Materials subtotal</span>
                  <span class="totals__value">$412.00</span>
                </div>
                <div class="totals__row">
                  <span class="totals__label">Permits &amp; fees</span>
                  <span class="totals__value">$0.00</span>
                </div>
                <div class="totals__row">
                  <span class="totals__label">Tax (8.25%)</span>
                  <span class="totals__value">$178.36</span>
                </div>
                <div class="totals__row totals__row--grand">
                  <span>Final Estimate (verified)</span>
                  <span class="totals__value">$2,840.00</span>
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
                  <div class="sec__title">AI-detected risk flags</div>
                  <div class="sec__sub">Review before verifying. Confirm or dismiss.</div>
                </div>
              </div>
            </div>
            <div class="sec__body">
              <div class="flag flag--warning">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <div>
                  <div class="flag__title">Possible hidden moisture</div>
                  <div class="flag__body">
                    Photo #3 shows discoloration beyond visible stain edge. Consider adding an inspection line item or note in the customer-facing scope.
                  </div>
                </div>
                <button class="btn btn--ghost btn--sm" style="margin-left:auto">Dismiss</button>
              </div>
            </div>
          </section>

          <!-- INTERNAL NOTES -->
          <section class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div>
                  <div class="sec__title">Notes &amp; audit log</div>
                  <div class="sec__sub">Internal notes are not shown to the customer</div>
                </div>
              </div>
            </div>
            <div class="sec__body" style="padding:0">
              <div class="note-box" style="border:none;border-radius:0">
                <div class="note-box__tabs">
                  <button class="note-box__tab is-active" data-tab="internal">Internal notes</button>
                  <button class="note-box__tab" data-tab="customer">Customer-facing note</button>
                </div>
                <div class="note-box__body">
                  <textarea placeholder="Add internal notes for your team…" id="internalNote">Reached out to customer for an additional photo of the wall cavity. Will add line item if hidden moisture is confirmed.</textarea>
                </div>
                <div class="note-box__foot">
                  <span class="note-box__meta">Visible to BidFreq estimators only</span>
                  <button class="btn btn--primary btn--sm" id="postNote">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Post note
                  </button>
                </div>
              </div>
            </div>
          </section>

        </div>

        <!-- ============ RIGHT: SIDEBAR ============ -->
        <aside class="review-side">

          <!-- ACTION CARD -->
          <div class="action-card">
            <div class="action-card__top">
              <div class="action-card__label">Draft Total</div>
              <div class="action-card__amount">$2,840.00</div>
              <div class="action-card__sub">AI confidence 82% · valid 30 days</div>
            </div>
            <div class="action-card__body">
              <button class="btn btn--success btn--lg btn--block" id="verifyBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Verify &amp; Send to Customer
              </button>
              <button class="btn btn--secondary btn--block" id="revisionBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                Request Info from Customer
              </button>
              <button class="btn btn--danger-outline btn--block" id="returnToAiBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                Return to AI
              </button>
              <div class="divider" style="margin:var(--s-2) 0"></div>
              <button class="btn btn--ghost btn--block btn--sm" id="previewBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Preview PDF
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
                    <span class="conf-widget__value">82%</span>
                  </div>
                  <div class="confidence">
                    <div class="confidence__bar" style="height:8px">
                      <div class="confidence__fill" style="width:82%"></div>
                    </div>
                  </div>
                  <div class="conf-widget__note">
                    Confidence based on photo clarity, description completeness, and localized pricing coverage for ZIP 78745.
                  </div>
                </div>
              </div>

              <div style="padding:var(--s-2) var(--s-3)">
                <div class="status-row">
                  <span class="status-row__label">Photo clarity</span>
                  <span class="status-row__value">Good · 6/6</span>
                </div>
                <div class="status-row">
                  <span class="status-row__label">Description detail</span>
                  <span class="status-row__value">Medium</span>
                </div>
                <div class="status-row">
                  <span class="status-row__label">Pricing coverage</span>
                  <span class="status-row__value" style="color:var(--emerald-600)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    98%
                  </span>
                </div>
                <div class="status-row">
                  <span class="status-row__label">Historical match</span>
                  <span class="status-row__value">12 similar jobs</span>
                </div>
              </div>
            </div>
          </div>

          <!-- ORIGINAL REQUEST PHOTOS -->
          <div class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div>
                  <div class="sec__title">Submitted Evidence</div>
                  <div class="sec__sub">6 photos from customer</div>
                </div>
              </div>
            </div>
            <div class="sec__body">
              <div class="photo-grid">
                <div class="photo-tile">
                  <div class="photo-tile__img"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
                  <span class="photo-tile__badge">1</span>
                  <div class="photo-tile__label">Ceiling — wide</div>
                </div>
                <div class="photo-tile">
                  <div class="photo-tile__img"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
                  <span class="photo-tile__badge">2</span>
                  <div class="photo-tile__label">Stain close-up</div>
                </div>
                <div class="photo-tile">
                  <div class="photo-tile__img"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
                  <span class="photo-tile__badge">3</span>
                  <div class="photo-tile__label">Wall soft spot</div>
                </div>
                <div class="photo-tile">
                  <div class="photo-tile__img"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
                  <span class="photo-tile__badge">4</span>
                  <div class="photo-tile__label">Scale ref</div>
                </div>
                <div class="photo-tile">
                  <div class="photo-tile__img"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
                  <span class="photo-tile__badge">5</span>
                  <div class="photo-tile__label">Upstairs drain</div>
                </div>
                <div class="photo-tile">
                  <div class="photo-tile__img"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
                  <span class="photo-tile__badge">6</span>
                  <div class="photo-tile__label">Adjacent wall</div>
                </div>
              </div>
            </div>
          </div>

          <!-- CUSTOMER -->
          <div class="sec">
            <div class="sec__head">
              <div class="sec__head-left">
                <div class="sec__icon sec__icon--emerald">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/></svg>
                </div>
                <div>
                  <div class="sec__title">Customer</div>
                  <div class="sec__sub">Account context</div>
                </div>
              </div>
            </div>
            <div>
              <div class="status-row">
                <span class="status-row__label">Organization</span>
                <span class="status-row__value">Oak Ridge PM</span>
              </div>
              <div class="status-row">
                <span class="status-row__label">Projects</span>
                <span class="status-row__value">38</span>
              </div>
              <div class="status-row">
                <span class="status-row__label">Approval rate</span>
                <span class="status-row__value" style="color:var(--emerald-600)">94%</span>
              </div>
              <div class="status-row">
                <span class="status-row__label">Avg. revision</span>
                <span class="status-row__value">0.4 / estimate</span>
              </div>
              <div class="status-row">
                <span class="status-row__label">Prefers contact</span>
                <span class="status-row__value">Email</span>
              </div>
            </div>
          </div>

          <!-- SLA TIMER -->
          <div class="sec">
            <div class="sec__body">
              <div class="conf-widget" style="background:var(--warning-50);border-color:var(--warning-100)">
                <div class="conf-widget__head" style="color:#B45309">
                  <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    SLA Countdown
                  </span>
                  <span class="conf-widget__value" style="color:#B45309">1h 42m</span>
                </div>
                <div class="conf-widget__note" style="color:#B45309">
                  Customer expects verified estimate within 4 hours. On track but review soon.
                </div>
              </div>
            </div>
          </div>

        </aside>
      </div>

    </main>
@endsection
@push('scripts')
<div class="toast-region" id="toastRegion"></div>
    <script src="{{ asset('admin_assets/pages_js/estimate_review.js') }}"></script>
@endpush
