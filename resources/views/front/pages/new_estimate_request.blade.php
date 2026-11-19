@extends('front.layout.app')
@section('title', 'Dashboard · BidFreq™')


@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/new_estimate_request.css') }}">
@endpush

@section('content')

   <main class="app-content">

      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="customer-dashboard.html">Home</a>
        <span class="breadcrumbs__sep">/</span>
        <a href="estimate-requests.html">Estimate Requests</a>
        <span class="breadcrumbs__sep">/</span>
        <span class="breadcrumbs__current">New Request</span>
      </nav>

      <div class="wizard">

        <!-- ============================================================
             HEAD + STEP INDICATOR
             ============================================================ -->
        <header class="page-header wizard__head" id="wizardHead">
          <div class="page-header__text">
            <h1 class="page-header__title">New estimate request</h1>
            <p class="page-header__subtitle">
              Provide property details, a description, and the required photos. BidFreq AI will draft
              an initial estimate for human verification.
            </p>
          </div>
          <div class="page-header__actions">
            <a href="estimate-requests.html" class="btn btn--ghost">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
              Save & exit
            </a>
          </div>
        </header>

        <!-- Steps -->
        <nav class="steps" id="stepIndicator" aria-label="Progress">
          <div class="step is-active" data-step="1">
            <div class="step__num">1</div>
            <div class="step__label">Property</div>
          </div>
          <div class="step__connector"></div>
          <div class="step" data-step="2">
            <div class="step__num">2</div>
            <div class="step__label">Description</div>
          </div>
          <div class="step__connector"></div>
          <div class="step" data-step="3">
            <div class="step__num">3</div>
            <div class="step__label">Photos</div>
          </div>
          <div class="step__connector"></div>
          <div class="step" data-step="4">
            <div class="step__num">4</div>
            <div class="step__label">Supporting files</div>
          </div>
          <div class="step__connector"></div>
          <div class="step" data-step="5">
            <div class="step__num">5</div>
            <div class="step__label">Review & submit</div>
          </div>
        </nav>

        <form id="wizardForm" novalidate>

          <!-- ============================================================
               STEP 1 — PROPERTY
               ============================================================ -->
          <section class="step-panel is-active" data-step="1" aria-labelledby="s1-title">
            <div class="panel">
              <div class="panel__head">
                <div class="panel__head-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div>
                  <div class="panel__head-title" id="s1-title">Property information</div>
                  <div class="panel__head-sub">Where is the work needed? This drives localized pricing.</div>
                </div>
              </div>

              <div class="panel__body">
                <div class="form-group">
                  <label class="label" for="propertyName">
                    Property name / reference
                    <span class="label__hint">optional</span>
                  </label>
                  <input class="input" id="propertyName" type="text" placeholder="e.g. Oak Ridge Unit 4B">
                  <div class="form-hint">A short internal label to help you identify this property later.</div>
                </div>

                <div class="form-group">
                  <label class="label" for="streetAddress">
                    Street address <span class="req">*</span>
                  </label>
                  <input class="input" id="streetAddress" type="text"
                         placeholder="421 Oak Ridge Dr" autocomplete="street-address" required>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label class="label" for="city">City <span class="req">*</span></label>
                    <input class="input" id="city" type="text" placeholder="Austin" autocomplete="address-level2" required>
                  </div>
                  <div class="form-group">
                    <label class="label" for="state">State <span class="req">*</span></label>
                    <select class="select" id="state" required>
                      <option value="">Select state</option>
                      <option value="TX" selected>Texas</option>
                      <option value="CA">California</option>
                      <option value="NY">New York</option>
                      <option value="FL">Florida</option>
                      <option value="IL">Illinois</option>
                      <option value="AZ">Arizona</option>
                      <option value="GA">Georgia</option>
                      <option value="NC">North Carolina</option>
                    </select>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label class="label" for="zipCode">
                      ZIP code <span class="req">*</span>
                    </label>
                    <input class="input" id="zipCode" type="text" inputmode="numeric"
                           pattern="[0-9]{5}(-[0-9]{4})?" maxlength="10"
                           placeholder="78745" autocomplete="postal-code" required>
                    <div class="form-hint">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="vertical-align:-1px;margin-right:2px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                      Used for localized pricing (SOW §3.4).
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="label" for="propertyType">Property type</label>
                    <select class="select" id="propertyType">
                      <option value="single-family">Single-family home</option>
                      <option value="multi-family">Multi-family / apartment</option>
                      <option value="condo">Condo / townhome</option>
                      <option value="commercial">Commercial</option>
                      <option value="industrial">Industrial</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="panel__foot">
                <div></div>
                <div class="panel__foot-actions">
                  <button type="button" class="btn btn--primary" data-next="2">
                    Continue
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </section>

          <!-- ============================================================
               STEP 2 — DESCRIPTION
               ============================================================ -->
          <section class="step-panel" data-step="2" aria-labelledby="s2-title">
            <div class="panel">
              <div class="panel__head">
                <div class="panel__head-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div>
                  <div class="panel__head-title" id="s2-title">Description of work</div>
                  <div class="panel__head-sub">Tell us what happened and what needs repair.</div>
                </div>
              </div>

              <div class="panel__body">
                <div class="form-group">
                  <span class="label" id="tradeLabel">Primary trade</span>
                  <div class="trade-options" id="tradeOptions" role="group" aria-labelledby="tradeLabel">
                    <!-- Checkboxes rendered from TRADES in the script below -->
                  </div>
                  <div class="form-hint">Select all that apply. Best guess — AI will refine this during analysis.</div>
                  <div class="form-error hidden" id="tradeError">Select at least one trade.</div>
                </div>

                <!-- One required description per selected trade (rendered by JS) -->
                <div id="tradeDescriptions"></div>

                <div class="form-group">
                  <label class="label">Is the repair urgent?</label>
                  <div class="flex flex-wrap gap-5">
                    <label class="check check--radio">
                      <input type="radio" name="urgency" value="normal" checked>
                      <span class="check__box"></span>
                      <span class="check__label">Normal — standard turnaround</span>
                    </label>
                    <label class="check check--radio">
                      <input type="radio" name="urgency" value="urgent">
                      <span class="check__box"></span>
                      <span class="check__label">Urgent — safety or habitability concern</span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="panel__foot">
                <button type="button" class="btn btn--ghost" data-prev="1">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                  Back
                </button>
                <div class="panel__foot-actions">
                  <button type="button" class="btn btn--primary" data-next="3">
                    Continue
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </section>

          <!-- ============================================================
               STEP 3 — PHOTOS (MANDATORY GATE)
               ============================================================ -->
          <section class="step-panel" data-step="3" aria-labelledby="s3-title">
            <div class="panel">
              <div class="panel__head">
                <div class="panel__head-icon panel__head-icon--warning">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div>
                  <div class="panel__head-title" id="s3-title">Repair-area photos <span class="req" style="color:var(--danger-600)">*</span></div>
                  <div class="panel__head-sub">Required — the request cannot proceed without photos (SOW §3.2).</div>
                </div>
              </div>

              <div class="panel__body">

                <!-- One upload field per selected trade (rendered by JS) -->
                <div id="tradePhotoFields"></div>
                <div class="form-hint" id="tradePhotoEmpty">Select at least one trade under “Primary trade” to upload repair-area photos.</div>

                <!-- Photo tips -->
                <div class="photo-tips">
                  <div class="photo-tip">
                    <strong>📸 Show the full area</strong>
                    Wide shot showing the entire affected space.
                  </div>
                  <div class="photo-tip">
                    <strong>🔍 Close-up of damage</strong>
                    Detail shot of the specific damage or defect.
                  </div>
                  <div class="photo-tip">
                    <strong>📐 Include scale</strong>
                    Something for size reference (tape measure, hand).
                  </div>
                  <div class="photo-tip">
                    <strong>💡 Good lighting</strong>
                    Turn on lights, avoid glare and shadows.
                  </div>
                </div>

              </div>

              <div class="panel__foot">
                <button type="button" class="btn btn--ghost" data-prev="2">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                  Back
                </button>
                <div class="panel__foot-actions">
                  <button type="button" class="btn btn--primary" id="photosNext" data-next="4" disabled>
                    Continue
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </section>

          <!-- ============================================================
               STEP 4 — SUPPORTING FILES (OPTIONAL)
               ============================================================ -->
          <section class="step-panel" data-step="4" aria-labelledby="s4-title">
            <div class="panel">
              <div class="panel__head">
                <div class="panel__head-icon panel__head-icon--emerald">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
                <div>
                  <div class="panel__head-title" id="s4-title">
                    Supporting files
                    <span class="label__hint" style="font-weight:500;color:var(--text-muted)">optional</span>
                  </div>
                  <div class="panel__head-sub">Inspection reports, measurements, and other documents.</div>
                </div>
              </div>

              <div class="panel__body">

                <div class="dropzone" id="docDropzone" role="button" tabindex="0" style="padding:var(--s-6) var(--s-4)">
                  <input type="file" id="docInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,image/*" multiple hidden>
                  <div class="dropzone__icon" style="width:40px;height:40px;margin-bottom:var(--s-3)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  </div>
                  <div class="dropzone__title" style="font-size:var(--fs-md)">Reports & measurements</div>
                  <div class="dropzone__hint" style="font-size:var(--fs-xs)">PDF, DOC, XLS, TXT · Max 50 MB</div>
                </div>

                <div class="file-list" id="supportingList" aria-live="polite"></div>

                <div class="alert alert--info mt-6 mb-0">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                  <div class="alert__body">
                    <div class="alert__title">More evidence = better estimates</div>
                    Supporting files improve AI accuracy and reduce the chance of the estimator requesting additional information.
                  </div>
                </div>

              </div>

              <div class="panel__foot">
                <button type="button" class="btn btn--ghost" data-prev="3">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                  Back
                </button>
                <div class="panel__foot-actions">
                  <button type="button" class="btn btn--secondary" data-next="5">Skip</button>
                  <button type="button" class="btn btn--primary" data-next="5">
                    Continue
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </section>

          <!-- ============================================================
               STEP 5 — REVIEW & SUBMIT
               ============================================================ -->
          <section class="step-panel" data-step="5" aria-labelledby="s5-title">
            <div class="panel">
              <div class="panel__head">
                <div class="panel__head-icon panel__head-icon--violet">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div>
                  <div class="panel__head-title" id="s5-title">Review &amp; submit</div>
                  <div class="panel__head-sub">Confirm the details before sending to BidFreq.</div>
                </div>
              </div>

              <div class="panel__body">

                <div class="submit-summary">
                  <div class="submit-summary__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
                  </div>
                  <div>
                    <div class="submit-summary__title">What happens next?</div>
                    <div class="submit-summary__text">
                      1. BidFreq's AI analyzes your photos and description to draft an initial Scope of Work.
                      2. A BidFreq human estimator reviews and verifies the estimate.
                      3. You'll receive a professional PDF estimate to approve, decline, or request revisions.
                    </div>
                  </div>
                </div>

                <!-- Property -->
                <div class="review-block">
                  <div class="review-block__head">
                    <span class="review-block__title">Property</span>
                    <button type="button" class="review-block__edit" data-goto="1">Edit</button>
                  </div>
                  <dl class="review-grid">
                    <dt>Reference</dt>       <dd id="rv-propertyName">—</dd>
                    <dt>Address</dt>         <dd id="rv-address">—</dd>
                    <dt>ZIP code</dt>        <dd id="rv-zip" class="text-mono">—</dd>
                    <dt>Property type</dt>   <dd id="rv-propertyType">—</dd>
                  </dl>
                </div>

                <!-- Description -->
                <div class="review-block">
                  <div class="review-block__head">
                    <span class="review-block__title">Description</span>
                    <button type="button" class="review-block__edit" data-goto="2">Edit</button>
                  </div>
                  <dl class="review-grid">
                    <dt>Primary trade</dt><dd id="rv-trade">—</dd>
                    <dt>Urgency</dt>      <dd id="rv-urgency">—</dd>
                    <dt>Details</dt>      <dd id="rv-description" style="white-space:pre-wrap">—</dd>
                  </dl>
                </div>

                <!-- Photos -->
                <div class="review-block">
                  <div class="review-block__head">
                    <span class="review-block__title">Photos <span class="tag-verified" style="margin-left:6px">Required ✓</span></span>
                    <button type="button" class="review-block__edit" data-goto="3">Edit</button>
                  </div>
                  <div class="text-sm text-muted mb-2"><span id="rv-photoCount">0</span> photo(s) uploaded</div>
                  <div id="rv-photoThumbs"></div>
                </div>

                <!-- Supporting -->
                <div class="review-block">
                  <div class="review-block__head">
                    <span class="review-block__title">Supporting files</span>
                    <button type="button" class="review-block__edit" data-goto="4">Edit</button>
                  </div>
                  <div id="rv-supporting" class="text-sm text-muted">No supporting files uploaded.</div>
                </div>

                <!-- Consent -->
                <div class="review-block" style="margin-top:var(--s-4)">
                  <label class="check">
                    <input type="checkbox" id="consentCheck">
                    <span class="check__box">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <span class="check__label">
                      <strong>I confirm the information is accurate <span class="req" style="color:var(--danger-600)">*</span></strong>
                      <span class="check__desc">
                        I understand AI-generated estimates are drafts and remain subject to BidFreq's mandatory human verification before final delivery.
                      </span>
                    </span>
                  </label>
                </div>

              </div>

              <div class="panel__foot">
                <button type="button" class="btn btn--ghost" data-prev="4">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                  Back
                </button>
                <div class="panel__foot-actions">
                  <button type="submit" class="btn btn--primary btn--lg" id="submitBtn" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Submit Estimate Request
                  </button>
                </div>
              </div>
            </div>
          </section>

        </form>

        <!-- ============================================================
             SUCCESS STATE (shown after submit)
             ============================================================ -->
        <section id="successState" class="success-card hidden" aria-live="polite">
          <div class="success-card__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <h2 class="success-card__title">Request submitted</h2>
          <p class="success-card__text">
            Your estimate request <strong id="successEstId" class="text-mono">EST-2026-0043</strong> has been received.
            BidFreq AI is now analyzing your photos and description. You'll be notified once a human-verified estimate is ready.
          </p>

          <div class="card mb-6" style="text-align:left;max-width:440px;margin-left:auto;margin-right:auto">
            <div class="card__body">
              <div class="flex items-center justify-between mb-3">
                <span class="chip chip--violet"><span class="chip__dot"></span>AI Processing</span>
                <span class="text-xs text-muted">Just now</span>
              </div>
              <div class="progress progress--sm mb-2">
                <div class="progress__fill" id="successProgress" style="width:8%;background:var(--violet-600)"></div>
              </div>
              <div class="text-xs text-muted">Estimated draft ready in ~2 minutes</div>
            </div>
          </div>

          <div class="success-card__actions">
            <a href="estimate-requests.html" class="btn btn--primary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              View my requests
            </a>
            <a href="customer-dashboard.html" class="btn btn--secondary">
              Back to dashboard
            </a>
          </div>
        </section>

      </div>

    </main>
@endsection
@push('scripts')

   

   

    <script src="{{ asset('front_assets/pages_js/new_estimate_request.js') }}"></script>


@endpush
