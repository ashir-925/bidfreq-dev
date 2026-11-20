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
      ['streetAddress', 'city', 'state', 'zipCode', 'homeSqft', 'stories'].forEach(function (id) {
        var el = document.getElementById(id);
        if (!el.value.trim() || !el.checkValidity()) {
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
      '<label class="label">Repair-area photos — ' + escapeHtml(trade) + '</label>' +
      '<div class="photo-gate" role="status">' +
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' +
        '<div class="photo-gate__body">' +
          '<div class="photo-gate__title"></div>' +
          '<div class="photo-gate__text"></div>' +
        '</div>' +
      '</div>' +
      '<div class="dropzone" role="button" tabindex="0">' +
        '<input type="file" accept="image/jpeg,image/png,image/webp" multiple hidden>' +
        '<div class="dropzone__icon">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>' +
        '</div>' +
        '<div class="dropzone__title">Drag &amp; drop ' + escapeHtml(trade) + ' repair-area photos</div>' +
        '<div class="dropzone__hint">' +
          'or <span style="color:var(--blue-600);font-weight:600">click to browse</span> · JPEG, PNG, WebP · Up to 5 MB each' +
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
      if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
        window.bidFreqFeedback.toast('danger', 'Photo could not be added', 'Use JPEG, PNG or WebP files up to 5 MB each.');
        return;
      }

      state.tradePhotos[trade].push({
        id: 'p_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7),
        name: file.name,
        size: file.size,
        file: file,
        dataUrl: URL.createObjectURL(file)
      });
      renderPhotos(trade);
    });
  }

  function removePhoto(trade, id) {
    state.tradePhotos[trade] = state.tradePhotos[trade].filter(function (photo) {
      if (photo.id !== id) return true;
      URL.revokeObjectURL(photo.dataUrl);
      return false;
    });
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
    return groups.reduce(function (total, group) { return total + group.photos.length; }, 0) >= MIN_PHOTOS;
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

    var count = photos.length;
    f.querySelector('.photo-gate').classList.toggle('is-satisfied', count > 0);
    f.querySelector('.photo-gate__title').textContent = count + (count === 1 ? ' photo added' : ' photos added');
    f.querySelector('.photo-gate__text').textContent = 'At least two photos are required across the entire request.';

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
        file: f,
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
    setText('rv-home', v('homeSqft') + ' sq ft / ' + v('stories') + ' stories');
    setText('rv-nte', v('nte') === '' ? 'Not specified' : '$' + v('nte'));
    setText('rv-dimensions', Array.from(dimensions.children).map(function (row) {
      return Array.from(row.querySelectorAll('[data-dimension]')).filter(function (input) {
        return input.value !== '';
      }).map(function (input) {
        return input.dataset.dimension === 'label' ? input.value : input.dataset.dimension + ': ' + input.value;
      }).join(' · ');
    }).filter(Boolean).join('; ') || 'Not specified');
    var ptSel = document.getElementById('propertyType');
    setText('rv-propertyType', ptSel.options[ptSel.selectedIndex] ? ptSel.options[ptSel.selectedIndex].text : '—');

    // Description
    setText('rv-trade', getSelectedTrades().join(', ') || '—');
    var urgencyEl = document.querySelector('input[name="urgency"]:checked');
    setText('rv-urgency', priority.options[priority.selectedIndex].text);
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

  var dimensions = document.getElementById('dimensions');
  var addDimension = document.getElementById('addDimension');
  var dimensionSequence = 0;
  addDimension.addEventListener('click', function () {
    var row = document.createElement('fieldset');
    row.className = 'form-group';
    var id = ++dimensionSequence;
    row.innerHTML = '<legend class="label">Damaged area</legend><div class="form-row">' +
      [['label', 'Area label'], ['length', 'Length (ft)'], ['width', 'Width (ft)'],
        ['height', 'Height (ft)'], ['area', 'Area (sq ft)']].map(function (pair) {
        var key = pair[0];
        var controlId = 'dimension-' + id + '-' + key;
        return '<div class="form-group"><label class="label" for="' + controlId + '">' + pair[1] +
          '</label><input class="input" id="' + controlId + '" data-dimension="' + key + '" ' +
          (key === 'label' ? 'type="text" maxlength="100"' : 'type="number" min="0.01" step="any"') + '></div>';
      }).join('') + '</div><button type="button" class="btn btn--ghost btn--sm">Remove area</button>';
    row.querySelector('button').addEventListener('click', function () {
      row.remove();
      addDimension.focus();
      addDimension.disabled = false;
    });
    dimensions.append(row);
    addDimension.disabled = dimensions.children.length >= 20;
    row.querySelector('input').focus();
  });

  var form = document.getElementById('wizardForm');
  var errors = document.getElementById('requestErrors');

  function displayErrors(messages, fields) {
    var list = document.createElement('ul');
    messages.forEach(function (message) {
      var li = document.createElement('li');
      li.textContent = message;
      list.append(li);
    });
    errors.replaceChildren(list);
    errors.classList.remove('hidden');
    Object.keys(fields || {}).forEach(function (name) {
      var field = form.elements.namedItem(name);
      if (field instanceof HTMLElement) {
        field.setAttribute('aria-invalid', 'true');
        field.setAttribute('aria-describedby', errors.id);
      }
    });
    errors.focus();
    window.bidFreqFeedback.toast('danger', 'Check your request', messages[0]);
  }

  form.addEventListener('submit', async function (event) {
    event.preventDefault();
    if (!consent.checked || submitBtn.disabled) return;
    for (var step = 1; step <= 3; step++) {
      if (!validateStep(step)) {
        showStep(step);
        displayErrors([step === 3 ? 'Add at least two repair photos.' : 'Complete the required fields.']);
        return;
      }
    }

    var data = new FormData(form);
    data.delete('trade');
    getTradeDescriptions().forEach(function (item, index) {
      data.append('descriptions[' + index + '][trade]', item.trade);
      data.append('descriptions[' + index + '][description]', item.description);
    });
    Array.from(dimensions.children).forEach(function (row, index) {
      row.querySelectorAll('[data-dimension]').forEach(function (input) {
        data.append('dimensions[' + index + '][' + input.dataset.dimension + ']', input.value);
      });
    });
    var photoCount = 0;
    var bytes = 0;
    getSelectedTradePhotos().forEach(function (group) {
      group.photos.forEach(function (photo) {
        photoCount++;
        bytes += photo.file.size;
        data.append('photos[]', photo.file, photo.name);
        data.append('photo_trades[]', group.trade);
      });
    });
    state.files.forEach(function (attachment) {
      bytes += attachment.file.size;
      data.append('documents[]', attachment.file, attachment.name);
    });
    if (photoCount > 14 || state.files.length > 6 || bytes > 12 * 1024 * 1024) {
      displayErrors(['Use up to 14 photos and six supporting files, with a combined size of 12 MB or less.']);
      return;
    }

    errors.classList.add('hidden');
    if (!window.bidFreqFeedback.start(submitBtn, 'Submitting request…')) return;
    try {
      var response = await fetch(form.action, {
        method: 'POST', body: data, headers: { Accept: 'application/json' }
      });
      var payload = await response.json();
      if (!response.ok) {
        var messages = Object.values(payload.errors || {}).flat();
        displayErrors(messages.length ? messages : [payload.message || 'We could not submit your request.'], payload.errors);
        return;
      }
      if (!payload.redirect) throw new Error('Missing confirmation page');
      window.location.assign(payload.redirect);
    } catch (error) {
      displayErrors(['We could not confirm submission. Please try again.']);
    } finally {
      window.bidFreqFeedback.stop(submitBtn);
    }
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