(() => {
    document.querySelector('[data-estimate-print]')?.addEventListener('click', () => window.print());
    let active;
    let opener;
    function close() {
        active?.classList.remove('is-open');
        active = null;
        opener?.focus();
    }
    document.querySelectorAll('[data-open-estimate-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            active = document.getElementById(button.dataset.openEstimateModal);
            if (!active) return;
            opener = button;
            active.classList.add('is-open');
            active.querySelector('input:not([type="hidden"]), textarea, button')?.focus();
        });
    });
    document.querySelectorAll('[data-close-estimate-modal]').forEach((button) => button.addEventListener('click', close));
    document.querySelectorAll('.modal-overlay').forEach((element) => element.addEventListener('click', (event) => {
        if (event.target === element) close();
    }));
    document.querySelector('[data-open-on-load]')?.click();
    document.addEventListener('keydown', (event) => { if (active && event.key === 'Escape') close(); });
})();
