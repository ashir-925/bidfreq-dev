const dashboardClock = document.querySelector('[data-dashboard-clock]');

if (dashboardClock) {
    const time = dashboardClock.querySelector('[data-dashboard-time]');
    const date = dashboardClock.querySelector('[data-dashboard-date]');
    const greeting = document.querySelector('[data-dashboard-greeting]');
    const timeFormat = new Intl.DateTimeFormat(undefined, {
        hour: 'numeric',
        minute: '2-digit',
        second: '2-digit',
        timeZoneName: 'short',
    });
    const dateFormat = new Intl.DateTimeFormat(undefined, {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });

    function updateClock() {
        const now = new Date();
        const hour = now.getHours();
        const greetingText = hour < 12 ? 'Good morning, Admin' : hour < 18 ? 'Good afternoon, Admin' : 'Good evening, Admin';
        const dateText = dateFormat.format(now);

        time.dateTime = now.toISOString();
        time.textContent = timeFormat.format(now);
        if (date.textContent !== dateText) {
            date.textContent = dateText;
        }
        if (greeting.textContent !== greetingText) {
            greeting.textContent = greetingText;
        }
    }

    updateClock();
    window.setInterval(updateClock, 1000);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            updateClock();
        }
    });
}
