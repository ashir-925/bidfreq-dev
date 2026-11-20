(() => {
    document.querySelectorAll('[data-estimate-row]').forEach((row) => {
        row.addEventListener('click', (event) => {
            if (event.target.closest('a, button, input, select, label')) return;
            row.querySelector('[data-estimate-link]')?.click();
        });
    });
})();
