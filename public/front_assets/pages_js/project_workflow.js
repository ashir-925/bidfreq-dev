document.addEventListener('click', (event) => {
    const photo = event.target.closest('[data-add-photo]');
    if (photo) {
        const rows = photo.form.querySelector('[data-photo-rows]');
        const index = rows.children.length;
        if (index >= 8) return;
        const row = rows.firstElementChild.cloneNode(true);
        row.querySelectorAll('input').forEach((input) => {
            input.name = input.name.replace(/\[\d+\]/, '[' + index + ']');
            input.value = '';
        });
        row.querySelectorAll('[data-selected-file]').forEach((selection) => selection.remove());
        rows.append(row);
        photo.disabled = index === 7;
        return;
    }

    const button = event.target.closest('[data-add-order-line], [data-remove-order-line]');
    if (!button) return;
    const rows = button.form.querySelector('[data-order-rows]');
    if (button.hasAttribute('data-add-order-line')) {
        if (rows.children.length >= 50) return;
        const row = rows.firstElementChild.cloneNode(true);
        for (const input of row.querySelectorAll('input, select')) {
            if (input.type === 'checkbox') input.checked = false;
            else input.value = '';
        }
        rows.append(row);
    } else if (rows.children.length > 1) {
        button.closest('[data-order-row]').remove();
    }
    Array.from(rows.children).forEach((row, index) => {
        row.querySelectorAll('[name]').forEach((input) => {
            input.name = input.name.replace(/lines\[\d+\]/, 'lines[' + index + ']');
        });
    });
});


const selectionUrls = new WeakMap();

document.addEventListener('change', (event) => {
    const input = event.target;
    if (!input.matches('[data-project-upload] input[type=file]')) return;

    const previous = selectionUrls.get(input);
    if (previous) URL.revokeObjectURL(previous);
    selectionUrls.delete(input);
    input.closest('label').nextElementSibling?.matches('[data-selected-file]')
        && input.closest('label').nextElementSibling.remove();
    const file = input.files[0];
    if (!file) return;

    const selection = document.createElement('div');
    selection.className = 'project-upload-selection';
    selection.dataset.selectedFile = '';
    if (['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        const preview = document.createElement('img');
        const url = URL.createObjectURL(file);
        selectionUrls.set(input, url);
        preview.src = url;
        preview.alt = 'Selected image preview';
        selection.append(preview);
    }
    const filename = document.createElement('span');
    filename.textContent = file.name + ' · ' + (file.type || 'File type unavailable');
    const remove = document.createElement('button');
    remove.type = 'button';
    remove.className = 'btn btn--ghost btn--sm';
    remove.textContent = 'Remove selection';
    remove.addEventListener('click', () => {
        input.value = '';
        input.dispatchEvent(new Event('change', { bubbles: true }));
        input.focus();
    });
    selection.append(filename, remove);
    input.closest('label').after(selection);
});

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (form.matches('[data-confirm-file-removal]') && !window.confirm('Permanently delete this project file?')) {
        event.preventDefault();
        return;
    }
    if (!form.matches('[data-project-upload]')) return;
    event.preventDefault();
    if (form.dataset.uploading) return;

    const button = event.submitter || form.querySelector('button:not([type])');
    const feedback = form.querySelector('[data-upload-feedback]');
    const request = new XMLHttpRequest();
    const data = new FormData(form);
    form.dataset.uploading = 'true';
    if (button) button.disabled = true;
    feedback.textContent = 'Uploading…';
    request.open('POST', form.action);
    request.setRequestHeader('Accept', 'application/json');
    request.responseType = 'json';
    request.upload.addEventListener('progress', (update) => {
        if (update.lengthComputable) {
            feedback.textContent = update.loaded === update.total
                ? 'Upload sent. Saving…'
                : 'Uploading ' + Math.round(update.loaded * 100 / update.total) + '%…';
        }
    });
    request.addEventListener('load', () => {
        if (request.status >= 200 && request.status < 300 && request.response?.saved === true) {
            feedback.textContent = 'Saved. Reloading project…';
            window.location.reload();
            return;
        }
        const errors = Object.values(request.response?.errors || {}).flat();
        feedback.textContent = errors.join(' ') || request.response?.message || 'The upload could not be saved. Your form is still available.';
    });
    request.addEventListener('error', () => {
        feedback.textContent = 'The connection was interrupted. Check the project before retrying.';
    });
    request.addEventListener('loadend', () => {
        if (request.response?.saved === true) return;
        delete form.dataset.uploading;
        if (button) button.disabled = false;
    });
    request.send(data);
}, true);
