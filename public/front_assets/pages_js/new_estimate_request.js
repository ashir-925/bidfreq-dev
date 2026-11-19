(function () {
  'use strict';

  // ================================================================
  // STATE
  // ================================================================
  var state = {
    currentStep: 1,
    tradePhotos: {}, // trade -> [{ id, name, size, dataUrl }]; kept when deselected
    files: []       // { id, name, size, kind }
  };

  var MIN_PHOTOS = 2;

  // ================================================================
  // STEP NAVIGATION
  // ================================================================
  function showStep(n) {
    state.currentStep = n;

    document.querySelectorAll('.step-panel').forEach(function (p) {
      p.classList.toggle('is-active', parseInt(p.dataset.step, 10) === n);
    });

    // Step indicator
    document.querySelectorAll('#stepIndicator .step').forEach(function (s) {
      var sn = parseInt(s.dataset.step, 10);
      s.classList.remove('is-active', 'is-done');
      if (sn < n) s.classList.add('is-done');
      if (sn === n) s.classList.add('is-active');
      var numEl = s.querySelector('.step__num');
      if (sn < n) numEl.textContent = '✓';
      else numEl.textContent = sn;
    });
    document.querySelectorAll('#stepIndicator .step__connector').forEach(function (c, i) {
      c.classList.toggle('is-done', (i + 1) < n);
    });

    // Scroll top
    window.scrollTo({ top: 0, behavior: 'smooth' });

    if (n === 5) renderReview();
  }

  function validateStep(n) {
    if (n === 1) {
      var ok = true;
      ['streetAddress', 'city', 'state', 'zipCode'].forEach(function (id) {
        var el = document.getElementById(id);
        if (!el.value.trim()) {
          el.classList.add(el.tagName === 'SELECT' ? 'select--error' : 'input--error');
          ok = false;
        }
      });
      return ok;
    }
    if (n === 2) {
      var selected = getSelectedTrades();
      document.getElementById('tradeError').classList.toggle('hidden', selected.length > 0);
      if (!selected.length) return false;
      var ok = true;
      selected.forEach(function (trade) {
        var d = tradeDescEls[trade].querySelector('.textarea');
        if (!d.value.trim()) { d.classList.add('textarea--error'); ok = false; }
      });
      return ok;
    }
    if (n === 3) return photosSatisfied();
    return true;
  }

  function gotoNext(target) {
    if (!validateStep(state.currentStep)) return;
    showStep(target);
  }

  document.querySelectorAll('[data-next]').forEach(function (btn) {
    btn.addEventListener('click', function () { gotoNext(parseInt(btn.dataset.next, 10)); });
  });
  document.querySelectorAll('[data-prev]').forEach(function (btn) {
    btn.addEventListener('click', function () { showStep(parseInt(btn.dataset.prev, 10)); });
  });
  document.querySelectorAll('[data-goto]').forEach(function (btn) {
    btn.addEventListener('click', function () { showStep(parseInt(btn.dataset.goto, 10)); });
  });

  // Clear error on input
  document.querySelectorAll('.input, .select, .textarea').forEach(function (el) {
    el.addEventListener('input', function () {
      el.classList.remove('input--error', 'select--error', 'textarea--error');
    });
    el.addEventListener('change', function () {
      el.classList.remove('input--error', 'select--error', 'textarea--error');
    });
  });

  // ================================================================
  // TRADES — one required description per selected trade
  // ================================================================
  var TRADES = ['General Repair', 'Electrical', 'Plumbing', 'HVAC', 'Roofing',
                'Flooring', 'Drywall / Paint', 'Foundation', 'Other'];
  var tradeOptions = document.getElementById('tradeOptions');
  var tradeDescWrap = document.getElementById('tradeDescriptions');
  var tradeDescEls = {}; // trade -> form-group; kept when deselected so text is preserved

  tradeOptions.innerHTML = TRADES.map(function (trade) {
    return '<label class="check">' +
        '<input type="checkbox" name="trade" value="' + escapeHtml(trade) + '">' +
        '<span class="check__box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>' +
        '<span class="check__label">' + escapeHtml(trade) + '</span>' +
      '</label>';
  }).join('');

  function getSelectedTrades() {
    return Array.prototype.map.call(
      tradeOptions.querySelectorAll('input[name="trade"]:checked'),
      function (cb) { return cb.value; });
  }

  function getTradeDescriptions() {
    return getSelectedTrades().map(function (trade) {
      return { trade: trade, description: tradeDescEls[trade].querySelector('.textarea').value.trim() };
    });
  }

  function buildTradeDesc(trade, idx) {
    var id = 'description-' + idx;
    var g = document.createElement('div');
    g.className = 'form-group';
    g.innerHTML =
      '<label class="label" for="' + id + '">' +
        'Description of the repair / work needed — ' + escapeHtml(trade) + ' <span class="req">*</span>' +
      '</label>' +
      '<textarea class="textarea" id="' + id + '" required maxlength="2000"' +
        ' placeholder="Describe the ' + escapeHtml(trade) + ' work: location, quantities, materials, and damage extent."' +
        ' style="min-height:170px"></textarea>' +
      '<div class="flex items-center justify-between mt-2">' +
        '<span class="form-hint">Be as specific as possible. Mention quantities, materials, and damage extent.</span>' +
        '<span class="form-hint text-mono"><span class="char-count">0</span> / 2000</span>' +
      '</div>';
    var ta = g.querySelector('.textarea');
    var cc = g.querySelector('.char-count');
    ta.addEventListener('input', function () {
      if (this.value.length > 2000) this.value = this.value.slice(0, 2000);
      cc.textContent = this.value.length;
      ta.classList.remove('textarea--error');
    });
    return g;
  }

  function syncTradeDescriptions() {
    var boxes = tradeOptions.querySelectorAll('input[name="trade"]');
    Array.prototype.forEach.call(boxes, function (cb, idx) {
      var g = tradeDescEls[cb.value];
      if (cb.checked && !g) g = tradeDescEls[cb.value] = buildTradeDesc(cb.value, idx);
      if (!g) return;
      var ta = g.querySelector('.textarea');
      if (cb.checked) {
        ta.disabled = false;
        tradeDescWrap.appendChild(g); // re-append keeps option order
      } else if (g.parentNode) {
        // Detached + disabled: excluded from validation/submission, text kept
        ta.disabled = true;
        ta.classList.remove('textarea--error');
        tradeDescWrap.removeChild(g);
      }
    });
    if (getSelectedTrades().length) document.getElementById('tradeError').classList.add('hidden');
    syncTradePhotos();
  }

  tradeOptions.addEventListener('change', syncTradeDescriptions);

  // ================================================================
  // PHOTO UPLOAD — MANDATORY GATE (one field per selected trade)
  // ================================================================
  var tradePhotoWrap  = document.getElementById('tradePhotoFields');
  var tradePhotoEmpty = document.getElementById('tradePhotoEmpty');
  var photosNext      = document.getElementById('photosNext');
  var tradePhotoEls   = {}; // trade -> field element; kept when deselected so photos are restored

  function buildTradePhotoField(trade) {
    var f = document.createElement('div');
    f.className = 'form-group';
    f.innerHTML =
      '<label class="label">Repair-area photos — ' + escapeHtml(trade) + ' <span class="req">*</span></label>' +
      '<div class="photo-gate" role="status">' +
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' +
        '<div class="photo-gate__body">' +
          '<div class="photo-gate__title"></div>' +
          '<div class="photo-gate__text"></div>' +
        '</div>' +
      '</div>' +
      '<div class="dropzone" role="button" tabindex="0">' +
        '<input type="file" accept="image/*" multiple hidden>' +
        '<div class="dropzone__icon">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>' +
        '</div>' +
        '<div class="dropzone__title">Drag &amp; drop ' + escapeHtml(trade) + ' repair-area photos</div>' +
        '<div class="dropzone__hint">' +
          'or <span style="color:var(--blue-600);font-weight:600">click to browse</span> · JPG, PNG, HEIC · Max 25 MB each' +
        '</div>' +
      '</div>' +
      '<div class="uploader" aria-live="polite"></div>';

    var zone  = f.querySelector('.dropzone');
    var input = f.querySelector('input[type="file"]');
    zone.addEventListener('click', function () { input.click(); });
    zone.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); }
    });
    zone.addEventListener('dragover', function (e) {
      e.preventDefault(); this.classList.add('is-dragover');
    });
    zone.addEventListener('dragleave', function () {
      this.classList.remove('is-dragover');
    });
    zone.addEventListener('drop', function (e) {
      e.preventDefault(); this.classList.remove('is-dragover');
      handlePhotos(trade, e.dataTransfer.files);
    });
    input.addEventListener('click', function (e) { e.stopPropagation(); });
    input.addEventListener('change', function () {
      handlePhotos(trade, this.files);
      this.value = '';
    });
    return f;
  }

  function syncTradePhotos() {
    var selected = getSelectedTrades();
    TRADES.forEach(function (trade) {
      var f = tradePhotoEls[trade];
      if (selected.indexOf(trade) !== -1) {
        if (!f) {
          f = tradePhotoEls[trade] = buildTradePhotoField(trade);
          state.tradePhotos[trade] = state.tradePhotos[trade] || [];
          renderPhotos(trade);
        }
        tradePhotoWrap.appendChild(f); // re-append keeps option order
      } else if (f && f.parentNode) {
        tradePhotoWrap.removeChild(f); // photos stay in state.tradePhotos
      }
    });
    tradePhotoEmpty.classList.toggle('hidden', selected.length > 0);
    updatePhotosNext();
  }

  function handlePhotos(trade, files) {
    Array.prototype.forEach.call(files, function (file) {
      if (!file.type.startsWith('image/')) return;
      var reader = new FileReader();
      reader.onload = function (ev) {
        state.tradePhotos[trade].push({
          id: 'p_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7),
          name: file.name,
          size: file.size,
          dataUrl: ev.target.result
        });
        renderPhotos(trade);
      };
      reader.readAsDataURL(file);
    });
  }

  function removePhoto(trade, id) {
    state.tradePhotos[trade] = state.tradePhotos[trade].filter(function (p) { return p.id !== id; });
    renderPhotos(trade);
  }

  // Photos for currently selected trades only, in option order
  function getSelectedTradePhotos() {
    return getSelectedTrades().map(function (trade) {
      return { trade: trade, photos: (state.tradePhotos[trade] || []).slice() };
    });
  }

  function photosSatisfied() {
    var groups = getSelectedTradePhotos();
    return groups.length > 0 && groups.every(function (g) { return g.photos.length >= MIN_PHOTOS; });
  }

  function updatePhotosNext() {
    photosNext.disabled = !photosSatisfied();
  }

  function renderPhotos(trade) {
    var f = tradePhotoEls[trade];
    var photos = state.tradePhotos[trade];

    // Grid
    var grid = f.querySelector('.uploader');
    grid.innerHTML = '';
    photos.forEach(function (p, i) {
      var tile = document.createElement('div');
      tile.className = 'uploader__tile';
      tile.innerHTML =
        '<img src="' + p.dataUrl + '" alt="' + escapeHtml(p.name) + '">' +
        '<span class="uploader__tag">Photo ' + (i + 1) + '</span>' +
        '<span class="uploader__name">' + escapeHtml(p.name) + '</span>' +
        '<button type="button" class="uploader__remove" aria-label="Remove photo">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
        '</button>';
      tile.querySelector('.uploader__remove').addEventListener('click', function (e) {
        e.stopPropagation();
        removePhoto(trade, p.id);
      });
      grid.appendChild(tile);
    });

    // Gate state
    var count = photos.length;
    var ok = count >= MIN_PHOTOS;
    f.querySelector('.photo-gate').classList.toggle('is-satisfied', ok);
    var title = f.querySelector('.photo-gate__title');
    var text  = f.querySelector('.photo-gate__text');
    if (ok) {
      title.textContent = 'Photo requirement met ✓';
      text.innerHTML =
        'You\'ve uploaded <span class="photo-gate__count">' + count + '</span> photo' + (count === 1 ? '' : 's') +
        '. You can continue or add more.';
    } else {
      title.textContent = 'Minimum ' + MIN_PHOTOS + ' photos required to continue';
      text.innerHTML =
        'You\'ve uploaded <span class="photo-gate__count">' + count + ' of ' + MIN_PHOTOS + '</span> required photos.';
    }

    updatePhotosNext();
  }

  syncTradePhotos();

  // ================================================================
  // SUPPORTING FILES
  // ================================================================
  var docDropzone   = document.getElementById('docDropzone');
  var docInput      = document.getElementById('docInput');
  var list          = document.getElementById('supportingList');

  function wireDropzone(zone, input, kind) {
    zone.addEventListener('click', function () { input.click(); });
    zone.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); }
    });
    zone.addEventListener('dragover', function (e) { e.preventDefault(); this.classList.add('is-dragover'); });
    zone.addEventListener('dragleave', function () { this.classList.remove('is-dragover'); });
    zone.addEventListener('drop', function (e) {
      e.preventDefault(); this.classList.remove('is-dragover');
      addFiles(e.dataTransfer.files, kind);
    });
    input.addEventListener('change', function () {
      addFiles(this.files, kind);
      this.value = '';
    });
  }
  wireDropzone(docDropzone, docInput, 'file');

  function addFiles(fileList, kind) {
    Array.prototype.forEach.call(fileList, function (f) {
      state.files.push({
        id: 'f_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7),
        name: f.name,
        size: f.size,
        kind: kind
      });
    });
    renderSupporting();
  }

  function removeFile(id) {
    state.files = state.files.filter(function (f) { return f.id !== id; });
    renderSupporting();
  }

  function renderSupporting() {
    list.innerHTML = '';
    state.files.forEach(function (f) {
      var row = document.createElement('div');
      row.className = 'file-row';
      row.innerHTML =
        '<div class="file-row__icon">' + iconFor(f.kind) + '</div>' +
        '<div class="file-row__body">' +
          '<div class="file-row__name">' + escapeHtml(f.name) + '</div>' +
          '<div class="file-row__meta">' + prettySize(f.size) + ' · ' + (f.kind === 'video' ? 'Video / Audio' : 'Document') + '</div>' +
        '</div>' +
        '<button type="button" class="file-row__remove" aria-label="Remove file">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
        '</button>';
      row.querySelector('.file-row__remove').addEventListener('click', function () { removeFile(f.id); });
      list.appendChild(row);
    });
  }

  function iconFor(kind) {
    if (kind === 'video') {
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>';
    }
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>';
  }

  // ================================================================
  // REVIEW RENDER
  // ================================================================
  function renderReview() {
    var v = function (id) { return (document.getElementById(id) || {}).value || ''; };
    var t = function (id) { return (document.getElementById(id) || {}).textContent || ''; };

    // Property
    setText('rv-propertyName', v('propertyName') || '—');
    var addr = [v('streetAddress'), v('city'), v('state')].filter(Boolean).join(', ');
    setText('rv-address', addr || '—');
    setText('rv-zip', v('zipCode') || '—');
    var ptSel = document.getElementById('propertyType');
    setText('rv-propertyType', ptSel.options[ptSel.selectedIndex] ? ptSel.options[ptSel.selectedIndex].text : '—');

    // Description
    setText('rv-trade', getSelectedTrades().join(', ') || '—');
    var urgencyEl = document.querySelector('input[name="urgency"]:checked');
    setText('rv-urgency', urgencyEl && urgencyEl.value === 'urgent' ? 'Urgent' : 'Normal');
    setText('rv-description', getTradeDescriptions().map(function (d) {
      return d.trade + ':\n' + d.description;
    }).join('\n\n') || '—');

    // Photos
    var photoGroups = getSelectedTradePhotos();
    setText('rv-photoCount', photoGroups.reduce(function (n, g) { return n + g.photos.length; }, 0));
    var thumbWrap = document.getElementById('rv-photoThumbs');
    thumbWrap.innerHTML = '';
    photoGroups.forEach(function (g) {
      var group = document.createElement('div');
      group.className = 'mb-2';
      group.innerHTML = '<div class="text-sm" style="font-weight:600;margin-bottom:4px">' + escapeHtml(g.trade) +
        ' <span class="text-xs text-muted">· ' + g.photos.length + ' photo(s)</span></div>';
      var thumbs = document.createElement('div');
      thumbs.className = 'review-thumbs';
      thumbs.setAttribute('data-trade', g.trade);
      g.photos.forEach(function (p) {
        var t = document.createElement('div');
        t.className = 'review-thumb';
        t.innerHTML = '<img src="' + p.dataUrl + '" alt="">';
        thumbs.appendChild(t);
      });
      group.appendChild(thumbs);
      thumbWrap.appendChild(group);
    });

    // Supporting
    var sup = document.getElementById('rv-supporting');
    if (state.files.length) {
      sup.innerHTML = state.files.map(function (f) {
        return '<div class="flex items-center gap-2" style="padding:4px 0">' +
          '<span style="color:var(--blue-600)">' + iconFor(f.kind) + '</span>' +
          '<span>' + escapeHtml(f.name) + '</span>' +
          '<span class="text-xs text-muted">· ' + prettySize(f.size) + '</span>' +
        '</div>';
      }).join('');
    } else {
      sup.textContent = 'No supporting files uploaded.';
    }
  }

  function setText(id, val) {
    var el = document.getElementById(id);
    if (el) el.textContent = val;
  }

  // ================================================================
  // CONSENT + SUBMIT
  // ================================================================
  var consent = document.getElementById('consentCheck');
  var submitBtn = document.getElementById('submitBtn');

  consent.addEventListener('change', function () {
    submitBtn.disabled = !this.checked;
  });

  document.getElementById('wizardForm').addEventListener('submit', function (e) {
    e.preventDefault();
    if (!consent.checked) return;
    if (!validateStep(2)) { showStep(2); return; }
    if (!photosSatisfied()) { showStep(3); return; }

    // Each selected trade is submitted with its own description and photos
    state.tradeDescriptions = getTradeDescriptions();
    state.tradeSubmission = getTradeDescriptions().map(function (d) {
      d.photos = (state.tradePhotos[d.trade] || []).slice();
      return d;
    });

    var original = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner spinner--sm spinner--white"></span> Submitting…';

    setTimeout(function () {
      document.getElementById('wizardHead').classList.add('hidden');
      document.getElementById('stepIndicator').classList.add('hidden');
      document.querySelectorAll('.step-panel').forEach(function (p) { p.classList.add('hidden'); });
      document.getElementById('successState').classList.remove('hidden');

      // Animate progress
      var prog = document.getElementById('successProgress');
      var pct = 8;
      var t = setInterval(function () {
        pct = Math.min(pct + Math.random() * 8, 94);
        prog.style.width = pct + '%';
        if (pct >= 94) clearInterval(t);
      }, 400);

      window.scrollTo({ top: 0, behavior: 'smooth' });
    }, 900);
  });

  // ================================================================
  // UTILITIES
  // ================================================================
  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c];
    });
  }

  function prettySize(bytes) {
    if (!bytes) return '0 B';
    var units = ['B','KB','MB','GB'];
    var i = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(i ? 1 : 0) + ' ' + units[i];
  }

  // ================================================================
  // NAV TOGGLE
  // ================================================================
  document.getElementById('navToggle').addEventListener('click', function () {
    var app = document.getElementById('app');
    if (window.matchMedia('(max-width: 860px)').matches) {
      app.classList.toggle('is-nav-open');
    } else {
      app.classList.toggle('is-collapsed');
    }
  });
})();