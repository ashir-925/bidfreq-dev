(() => {
    const activeButtons = new Map();
    const activeForms = new Map();
    const icons = {
        success: '<path d="M5 12l4 4L19 6"/>',
        danger: '<circle cx="12" cy="12" r="9"/><path d="M9 9l6 6m0-6l-6 6"/>',
        warning: '<path d="M12 3l10 18H2zM12 9v4m0 4h.01"/>',
        info: '<circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/>',
    };

    function toast(kind, title, message = '') {
        const region = document.getElementById('toastRegion');
        if (!region) return false;

        const style = Object.hasOwn(icons, kind) ? kind : 'info';
        const item = document.createElement('div');
        item.className = 'toast toast--' + style;
        // Only the fixed SVG icons are HTML. All message text is added as text.
        item.innerHTML = '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + icons[style] + '</svg>';

        const content = document.createElement('div');
        content.setAttribute('role', style === 'danger' ? 'alert' : 'status');
        const heading = document.createElement('div');
        heading.className = 'toast__title';
        heading.textContent = title;
        content.append(heading);

        if (message) {
            const detail = document.createElement('div');
            detail.className = 'toast__msg';
            detail.textContent = message;
            content.append(detail);
        }

        const dismiss = document.createElement('button');
        dismiss.type = 'button';
        dismiss.className = 'toast__dismiss';
        dismiss.setAttribute('aria-label', 'Dismiss notification');
        dismiss.textContent = '×';
        item.append(content, dismiss);
        region.append(item);

        let timeout;
        const remove = () => {
            clearTimeout(timeout);
            item.remove();
        };

        dismiss.addEventListener('click', remove);
        if (style !== 'danger') {
            const schedule = () => {
                clearTimeout(timeout);
                if (!item.matches(':hover, :focus-within')) timeout = setTimeout(remove, 6000);
            };
            item.addEventListener('mouseenter', () => clearTimeout(timeout));
            item.addEventListener('focusin', () => clearTimeout(timeout));
            item.addEventListener('mouseleave', schedule);
            item.addEventListener('focusout', () => setTimeout(schedule, 0));
            schedule();
        }

        return true;
    }

    function start(button, label = 'Please wait…') {
        if (!button || activeButtons.has(button) || button.disabled) return false;

        const original = Array.from(button.childNodes);
        const spinner = document.createElement('span');
        spinner.className = button.matches('.btn--primary, .btn--danger') ? 'spinner spinner--sm spinner--white' : 'spinner spinner--sm';
        spinner.setAttribute('aria-hidden', 'true');

        const status = document.createElement('span');
        status.textContent = label;
        button.setAttribute('aria-busy', 'true');
        button.disabled = true;
        button.replaceChildren(spinner, status);

        activeButtons.set(button, original);
        return true;
    }

    function stop(button) {
        const original = activeButtons.get(button);
        if (!original) return;

        button.replaceChildren(...original);
        button.removeAttribute('aria-busy');
        button.disabled = false;
        activeButtons.delete(button);
    }

    window.bidFreqFeedback = { toast, start, stop };

    document.addEventListener('submit', (event) => {
        if (event.defaultPrevented) return;

        const form = event.target;
        if (activeForms.has(form)) {
            event.preventDefault();
            return;
        }
        const button = event.submitter || form.querySelector('button[type="submit"]');
        if (!start(button, button?.dataset.loadingText)) return;

        // Disabled controls are excluded from native form data.
        const submitter = event.submitter;
        let valueField;
        if (submitter?.name) {
            valueField = document.createElement('input');
            valueField.type = 'hidden';
            valueField.name = submitter.name;
            valueField.value = submitter.value;
            form.append(valueField);
        }
        activeForms.set(form, { button, valueField });
    });

    window.addEventListener('pageshow', (event) => {
        if (!event.persisted) return;
        for (const { valueField } of activeForms.values()) valueField?.remove();
        for (const button of activeButtons.keys()) stop(button);
        activeForms.clear();
    });

    document.querySelectorAll('[data-error-field]').forEach((error) => {
        const form = error.closest('form');
        const field = form?.elements.namedItem(error.dataset.errorField);
        if (!(field instanceof HTMLElement)) return;

        field.setAttribute('aria-invalid', 'true');
        const descriptions = new Set((field.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean));
        descriptions.add(error.id);
        field.setAttribute('aria-describedby', Array.from(descriptions).join(' '));
        const inputClass = ['input', 'select', 'textarea'].find((name) => field.classList.contains(name));
        if (inputClass) field.classList.add(inputClass + '--error');
    });

    document.querySelectorAll('[data-feedback-kind]').forEach((message) => {
        const kind = message.dataset.feedbackKind;
        const title = message.dataset.feedbackTitle || (kind === 'danger' ? 'Something went wrong' : 'Success');
        if (!toast(kind, title, message.textContent.trim())) return;

        message.hidden = true;
        const fallback = message.closest('[data-feedback-fallback]');
        if (fallback && !fallback.querySelector('[data-feedback-kind]:not([hidden])')) {
            fallback.hidden = true;
        }
    });
})();
