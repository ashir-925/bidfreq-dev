(() => {
    const list = document.getElementById('manual-lines');
    const template = document.getElementById('manual-line-template');
    const addButton = document.getElementById('manual-add');

    function update() {
        Array.from(list.children).forEach((row, index) => {
            row.querySelectorAll('[data-field]').forEach((field) => {
                field.name = 'lines[' + index + '][' + field.dataset.field + ']';
            });
            row.querySelector('[data-remove]').disabled = list.children.length === 1;
        });
        addButton.disabled = list.children.length >= 100;
    }

    function add() {
        const row = template.content.firstElementChild.cloneNode(true);
        const task = row.querySelector('[data-field="task_id"]');
        const rate = row.querySelector('[data-field="rate"]');
        task.addEventListener('change', () => {
            const quote = task.selectedOptions[0]?.dataset.quote === '1';
            rate.disabled = !quote;
            rate.required = quote;
            if (!quote) rate.value = '';
        });
        row.querySelector('[data-remove]').addEventListener('click', () => {
            row.remove();
            update();
        });
        list.append(row);
        update();
    }

    addButton.addEventListener('click', add);
    add();
})();
