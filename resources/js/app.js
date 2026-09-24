// Mobile navigation toggle.
const toggle = document.querySelector('[data-menu-toggle]');
const menu = document.querySelector('[data-menu]');

toggle?.addEventListener('click', () => {
    const open = menu.classList.toggle('hidden') === false;
    toggle.setAttribute('aria-expanded', String(open));
    toggle.querySelector('[data-menu-open]')?.classList.toggle('hidden', open);
    toggle.querySelector('[data-menu-close]')?.classList.toggle('hidden', !open);
});

// Close dropdowns (e.g. language switcher) when clicking outside them.
document.addEventListener('click', (event) => {
    document.querySelectorAll('details[data-dropdown][open]').forEach((dropdown) => {
        if (!dropdown.contains(event.target)) dropdown.removeAttribute('open');
    });
});

// Count-up animation for the statistics band.
const counters = document.querySelectorAll('[data-count]');
const formatter = new Intl.NumberFormat(document.documentElement.lang || 'en');

if (counters.length && 'IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            observer.unobserve(entry.target);

            const el = entry.target;
            const target = Number(el.dataset.count);
            const duration = 1600;
            const start = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = formatter.format(Math.round(target * eased));
                if (progress < 1) requestAnimationFrame(tick);
            };

            requestAnimationFrame(tick);
        });
    }, { threshold: 0.4 });

    counters.forEach((el) => {
        el.textContent = '0';
        observer.observe(el);
    });
}
