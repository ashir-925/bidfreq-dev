@extends('front.layout.app')
@section('title', 'Dashboard · BidFreq™')


@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/new_estimate_request.css') }}">
@endpush

@section('content')

   <main class="app-content">

      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Home</a>
        <span class="breadcrumbs__sep">/</span>
        <a href="{{ route('estimate-request') }}">Estimate Requests</a>
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
            <a href="{{ route('estimate-request') }}" class="btn btn--ghost">
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

        <form id="wizardForm" method="POST" action="{{ route('repair-requests.store') }}" enctype="multipart/form-data" novalidate>
          @csrf
          <input type="hidden" name="submission_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
          <div class="alert alert--danger hidden mb-4" id="requestErrors" role="alert" tabindex="-1"></div>
          <noscript><p class="alert alert--warning">Enable JavaScript to use the estimate request wizard.</p></noscript>
          @env('local')
              <p class="alert alert--info">Development uses Gemini Free. Submit sample repair evidence without personal or confidential information.</p>
          @endenv

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
                  <input class="input" id="propertyName" name="property_name" type="text" placeholder="e.g. Oak Ridge Unit 4B">
                  <div class="form-hint">A short internal label to help you identify this property later.</div>
                </div>

                <div class="form-group">
                  <label class="label" for="streetAddress">
                    Street address <span class="req">*</span>
                  </label>
                  <input class="input" id="streetAddress" name="street_address" type="text"
                         placeholder="421 Oak Ridge Dr" autocomplete="street-address" required>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label class="label" for="city">City <span class="req">*</span></label>
                    <input class="input" id="city" name="city" type="text" placeholder="Austin" autocomplete="address-level2" required>
                  </div>
                  <div class="form-group">
                    <label class="label" for="state">State <span class="req">*</span></label>
                    <select class="select" id="state" name="state" required>
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
                    <input class="input" id="zipCode" name="zip" type="text" inputmode="numeric"
                           pattern="[0-9]{5}(-[0-9]{4})?" maxlength="10"
                           placeholder="78745" autocomplete="postal-code" required>
                    <div class="form-hint">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="vertical-align:-1px;margin-right:2px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                      Used to identify the property.
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="label" for="propertyType">Property type</label>
                    <select class="select" id="propertyType" name="property_type">
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

              <div class="panel__body">
                <div class="form-row">
                  <div class="form-group">
                    <label class="label" for="homeSqft">Total home area (sq ft) <span class="req">*</span></label>
                    <input class="input" id="homeSqft" name="home_sqft" type="number" min="0.01" step="0.01" required>
                  </div>
                  <div class="form-group">
                    <label class="label" for="stories">Stories <span class="req">*</span></label>
                    <input class="input" id="stories" name="stories" type="number" min="1" max="255" step="1" required>
                  </div>
                </div>
                <div class="form-group">
                  <span class="label">Damaged-area measurements (optional)</span>
                  <p class="form-hint">Enter an area in square feet or dimensions in feet for each affected area.</p>
                  <div id="dimensions"></div>
                  <button type="button" class="btn btn--secondary btn--sm" id="addDimension">Add area</button>
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
                  <label class="label" for="priority">Priority</label>
                  <select id="priority" name="priority" class="select">
                    <option value="low">Low</option>
                    <option value="normal" selected>Normal</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="label" for="nte">Not-to-Exceed amount (USD, optional)</label>
                  <input class="input" id="nte" name="nte" type="number" min="0" step="0.01">
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
                  <div class="panel__head-sub">Add at least two repair photos across the request.</div>
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
                  <input type="file" id="docInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.webp,.mp3,.m4a,.wav,.ogg,.mp4,.webm" multiple hidden>
                  <div class="dropzone__icon" style="width:40px;height:40px;margin-bottom:var(--s-3)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  </div>
                  <div class="dropzone__title" style="font-size:var(--fs-md)">Reports & measurements</div>
                  <div class="dropzone__hint" style="font-size:var(--fs-xs)">PDF, Office, text, images, audio, or video · 12 MB total across uploads</div>
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
                    <dt>Home size</dt>       <dd id="rv-home">—</dd>
                    <dt>Damaged areas</dt>   <dd id="rv-dimensions">—</dd>
                    <dt>Not-to-Exceed</dt>   <dd id="rv-nte">—</dd>
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
                    <dt>Priority</dt>      <dd id="rv-urgency">—</dd>
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
                    <input type="checkbox" id="consentCheck" name="consent" value="1">
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

        </div>

    </main>
@endsection
@push('scripts')

   

   

    <script src="{{ asset('front_assets/pages_js/new_estimate_request.js') }}"></script>


@endpush
