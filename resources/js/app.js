import './bootstrap';
import Alpine from 'alpinejs';

import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

window.Alpine = Alpine;

Alpine.data('scrollSpy', (selector = 'section[id]') => ({
    active: null,
    init() {
        const sections = Array.from(document.querySelectorAll(selector));
        if (!sections.length) return;

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) this.active = entry.target.id;
                });
            },
            { rootMargin: '-45% 0px -50% 0px', threshold: 0 }
        );

        sections.forEach((section) => observer.observe(section));
    },
}));

Alpine.data('countUp', (end = 0, duration = 1400) => ({
    shown: '0',
    init() {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const target = Number(end) || 0;

        if (reduce || target === 0) {
            this.shown = target.toLocaleString('id-ID');
            return;
        }

        const run = () => {
            const start = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                // easeOutExpo
                const eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                this.shown = Math.round(target * eased).toLocaleString('id-ID');

                if (progress < 1) requestAnimationFrame(tick);
            };

            requestAnimationFrame(tick);
        };

        const el = this.$el;
        const io = new IntersectionObserver(
            (entries) => {
                if (entries.some((e) => e.isIntersecting)) {
                    run();
                    io.disconnect();
                }
            },
            { threshold: 0.4 }
        );

        io.observe(el);
    },
}));

Alpine.data('gallery', (count = 1) => ({
    index: 0,
    count,
    go(i) {
        this.index = (i + this.count) % this.count;
    },
    prev() {
        this.go(this.index - 1);
    },
    next() {
        this.go(this.index + 1);
    },
}));

/**
 * Collapsible filter panel: collapsed on small screens, always open from the
 * `lg` breakpoint upwards. Tracks the media query reactively so resizing the
 * window keeps the panel in the correct state.
 */
Alpine.data('filterPanel', (desktopBreakpoint = '(min-width: 1024px)') => ({
    open: false,
    desktop: window.matchMedia(desktopBreakpoint).matches,

    init() {
        const mq = window.matchMedia(desktopBreakpoint);
        mq.addEventListener('change', (event) => {
            this.desktop = event.matches;
        });
    },

    toggle() {
        this.open = !this.open;
    },
}));

Alpine.start();

/* -------------------------------------------------------------------------
   Scroll reveal — progressive enhancement.
   If JS is unavailable every `.reveal` element stays visible.
   ------------------------------------------------------------------------- */
const reveal = () => {
    const items = document.querySelectorAll('.reveal:not(.is-visible)');

    if (!('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
    );

    items.forEach((el) => observer.observe(el));
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', reveal);
} else {
    reveal();
}
