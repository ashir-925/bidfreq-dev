document.querySelectorAll('[data-stripe-payment]').forEach((form) => {
    const prepare = form.querySelector('[data-prepare-payment]');
    const confirm = form.querySelector('[data-confirm-payment]');
    const check = form.querySelector('[data-check-payment]');
    const feedback = form.querySelector('[data-payment-feedback]');
    const fields = form.querySelector('[data-payment-fields]');
    const amount = form.elements.amount;
    let stripe, card, secret;

    async function request(url) {
        const response = await fetch(url, {
            method: 'POST',
            headers: { Accept: 'application/json' },
            body: new FormData(form),
            credentials: 'same-origin',
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.errors?.amount?.[0] || data.message || 'Payment is temporarily unavailable.');
        return data;
    }

    form.addEventListener('submit', (event) => event.preventDefault());
    prepare.addEventListener('click', async () => {
        if (!form.reportValidity()) return;
        prepare.disabled = true;
        feedback.textContent = 'Preparing the secure card form…';
        try {
            if (!window.Stripe) throw new Error('The secure card form could not load. Refresh to try again.');
            secret = (await request(form.action)).client_secret;
            stripe = window.Stripe(form.dataset.key);
            fields.hidden = false;
            card = stripe.elements().create('card', { hidePostalCode: false });
            card.mount(form.querySelector('[data-stripe-card]'));
            amount.readOnly = true;
            feedback.textContent = '';
        } catch (error) {
            feedback.textContent = error.message;
            prepare.disabled = false;
        }
    });

    check.addEventListener('click', async () => {
        check.disabled = true;
        try {
            const result = await request(form.dataset.verifyUrl);
            if (result.status === 'completed' || result.status === 'cancelled') {
                window.location.reload();
                return;
            }
            feedback.textContent = result.status === 'failed'
                ? 'Your card payment was declined. Check the card details and try again.'
                : 'No payment has been confirmed. Check again shortly if you already submitted your card.';
            if (result.status === 'failed') confirm.disabled = false;
        } catch (error) {
            feedback.textContent = error.message;
        } finally {
            check.disabled = false;
        }
    });

    confirm.addEventListener('click', async () => {
        confirm.disabled = true;
        feedback.textContent = 'Confirming your card payment…';
        try {
            let cardError;
            try {
                const result = await stripe.confirmCardPayment(secret, { payment_method: { card } });
                cardError = result.error?.message;
            } catch {
                cardError = 'The card connection was interrupted.';
            }
            const result = await request(form.dataset.verifyUrl);
            if (result.status === 'completed') {
                feedback.textContent = 'Payment verified. Updating your invoice…';
                window.location.reload();
                return;
            }
            feedback.textContent = cardError || 'Payment is awaiting confirmation. Use Check card payment before trying again.';
            if (result.status === 'failed') confirm.disabled = false;
        } catch (error) {
            feedback.textContent = error.message + ' Return to this invoice to check the current attempt.';
        }
    });
});

