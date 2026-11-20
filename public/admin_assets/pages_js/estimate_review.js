(() => {
    const dialog = document.getElementById('review-evidence-preview');
    const photos = new Map(Array.from(document.querySelectorAll('[data-file-number]'), (photo) => [photo.dataset.previewFile, photo]));
    let previewOpener;

    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-preview-file]');
        const photo = photos.get(opener?.dataset.previewFile);
        if (!dialog || !photo || dialog.open) return;
        previewOpener = opener;
        const image = dialog.querySelector('img');
        const source = photo.querySelector('img')?.src;
        image.hidden = !source;
        if (source) image.src = source;
        else image.removeAttribute('src');
        dialog.querySelector('[data-preview-fallback]').hidden = Boolean(source);
        dialog.querySelector('h2').textContent = '#' + photo.dataset.fileNumber + ' · ' + photo.dataset.fileName;
        dialog.querySelector('[data-preview-download]').href = photo.dataset.fileDownload;
        dialog.showModal();
    });
    dialog?.querySelector('[data-close-preview]').addEventListener('click', () => dialog.close());
    dialog?.addEventListener('close', () => previewOpener?.focus());
    dialog?.querySelector('img').addEventListener('error', (event) => {
        event.currentTarget.hidden = true;
        dialog.querySelector('[data-preview-fallback]').hidden = false;
    });
    document.querySelectorAll('[data-evidence-links]').forEach((group) => {
        group.addEventListener('change', (event) => {
            if (!event.target.matches('[data-link-file]')) return;
            const chip = Array.from(group.querySelectorAll('[data-evidence-chip]'))
                .find((item) => item.dataset.evidenceChip === event.target.value);
            if (chip) chip.hidden = !event.target.checked;
        });
    });

    function openEditor(toggle, open) {
        const editor = document.getElementById(toggle.dataset.lineEditor);
        editor.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        return editor;
    }

    document.getElementById('review-lines')?.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-line-editor]');
        if (toggle) openEditor(toggle, toggle.getAttribute('aria-expanded') !== 'true');
    });

    const add = document.getElementById('add-review-line');
    if (!add) return;
    const container = document.getElementById('review-lines');
    const template = document.getElementById('review-line-template');
    const originalCount = Number(add.dataset.nextIndex);
    const library = document.getElementById('review-tasks');
    const catalog = Array.from(library.content.querySelectorAll('button'));
    const pickers = new WeakMap();

    function mountPicker(field) {
        const wrapper = document.createElement('div');
        wrapper.className = 'review-picker';
        field.after(wrapper);
        const search = document.createElement('input');
        search.className = 'input';
        search.type = 'search';
        search.placeholder = library.dataset.searchPlaceholder;
        search.setAttribute('aria-label', field.getAttribute('aria-label'));
        if (field.id) {
            search.id = field.id;
            field.removeAttribute('id');
        }
        search.setAttribute('aria-expanded', 'false');
        const results = document.createElement('div');
        results.className = 'review-picker__results';
        results.hidden = true;
        wrapper.append(search, results);
        let current = catalog.find((option) => option.dataset.taskId === field.value);
        const selectionLabel = () => current ? current.dataset.taskId + ' · ' + current.dataset.taskName : field.value;
        search.value = selectionLabel();
        pickers.set(field, search);

        function close() {
            search.value = selectionLabel();
            search.setAttribute('aria-expanded', 'false');
            results.hidden = true;
        }

        function render(query) {
            const terms = query.toLowerCase().trim().split(/\s+/);
            const matches = catalog.filter((option) => terms.every((term) =>
                (option.dataset.taskId + ' ' + option.dataset.taskName).toLowerCase().includes(term)));
            results.replaceChildren(...matches.map((option) => {
                const choice = option.cloneNode(true);
                choice.addEventListener('click', () => {
                    current = option;
                    field.value = option.dataset.taskId;
                    search.focus();
                    close();
                });
                return choice;
            }));
            if (!matches.length) results.textContent = 'No matching Library tasks.';
            search.setAttribute('aria-expanded', 'true');
            results.hidden = false;
        }

        search.addEventListener('focus', () => { search.select(); render(''); });
        search.addEventListener('click', () => { if (results.hidden) render(''); });
        search.addEventListener('input', () => render(search.value));
        wrapper.addEventListener('focusout', (event) => { if (!wrapper.contains(event.relatedTarget)) close(); });
        wrapper.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') { search.focus(); close(); }
            if (event.key === 'ArrowDown' && event.target === search) {
                event.preventDefault();
                if (results.hidden) render('');
                results.querySelector('button')?.focus();
            }
            if (event.key === 'Enter' && event.target === search) {
                event.preventDefault();
                if (!results.hidden) results.querySelector('button')?.click();
            }
        });
        return search;
    }

    container.querySelectorAll('input[name$="[task_id]"]').forEach(mountPicker);
    container.addEventListener('click', (event) => {
        const button = event.target.closest('[data-resolve-line]');
        if (!button) return;
        const target = button.dataset.resolveLine;
        if (target === 'evidence') {
            location.hash = 'submitted-evidence';
            document.querySelector('#submitted-evidence button')?.focus({ preventScroll: true });
            return;
        }
        const rowToggle = button.closest('tr').querySelector('[data-line-editor]');
        const field = openEditor(rowToggle, true).querySelector('input[name$="[' + target + ']"]');
        const options = field?.closest('details');
        if (options) options.open = true;
        (pickers.get(field) ?? field)?.focus();
    });

    function nameRows() {
        Array.from(container.querySelectorAll('[data-added-line]')).forEach((row, index) => {
            row.querySelectorAll('[data-review-field]').forEach((input) => {
                input.name = 'lines[' + (originalCount + index) + '][' + input.dataset.reviewField + ']';
            });
        });
    }
    add.addEventListener('click', () => {
        const row = template.content.firstElementChild.cloneNode(true);
        row.dataset.addedLine = '';
        row.querySelector('[data-remove-line]').addEventListener('click', () => { row.remove(); nameRows(); });
        container.append(row);
        nameRows();
        mountPicker(row.querySelector('[data-review-field="task_id"]')).focus();
    });
})();
