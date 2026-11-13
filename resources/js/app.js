import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// PWA: daftarkan service worker hanya pada secure context (HTTPS atau localhost).
if ('serviceWorker' in navigator && window.isSecureContext) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').catch(() => {});
    });
}

// Indikator offline global.
const offlineBanner = document.querySelector('[data-offline-banner]');

function syncOfflineBanner() {
    if (!offlineBanner) {
        return;
    }

    offlineBanner.style.display = navigator.onLine ? 'none' : 'flex';
}

window.addEventListener('online', syncOfflineBanner);
window.addEventListener('offline', syncOfflineBanner);
syncOfflineBanner();

// Jangan biarkan form terlihat berhasil saat perangkat offline.
document.addEventListener('submit', (event) => {
    if (navigator.onLine || !(event.target instanceof HTMLFormElement)) {
        return;
    }

    event.preventDefault();
    syncOfflineBanner();
    offlineBanner?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}, true);

// Liquid glass: spekular mengikuti pointer (DESIGN.md §3.5).
(function liquidGlass() {
    const finePointer = window.matchMedia('(pointer: fine)').matches;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!finePointer || reducedMotion) {
        return;
    }

    let frame = 0;
    let pending = null;

    const flush = () => {
        frame = 0;

        if (!pending) {
            return;
        }

        const surface = pending.target instanceof Element ? pending.target.closest('.glass') : null;
        const event = pending;
        pending = null;

        if (!surface) {
            return;
        }

        const rect = surface.getBoundingClientRect();
        surface.style.setProperty('--liquid-spec-x', `${event.clientX - rect.left}px`);
        surface.style.setProperty('--liquid-spec-y', `${event.clientY - rect.top}px`);
    };

    document.addEventListener('pointermove', (event) => {
        pending = event;

        if (!frame) {
            frame = requestAnimationFrame(flush);
        }
    }, { passive: true });
})();

// Press glow: bloom di titik tekan, lalu mengikuti drag (DESIGN.md 3.6).
(function pressFeedback() {
    let pressed = null;
    let pending = null;
    let frame = 0;

    const flush = () => {
        frame = 0;

        if (!pressed || !pending) {
            return;
        }

        const rect = pressed.getBoundingClientRect();
        pressed.style.setProperty('--press-x', `${pending.clientX - rect.left}px`);
        pressed.style.setProperty('--press-y', `${pending.clientY - rect.top}px`);
        pending = null;
    };

    document.addEventListener('pointerdown', (event) => {
        const target = event.target instanceof Element ? event.target.closest('.press-glow') : null;

        if (!target) {
            return;
        }

        // Langsung di set (bukan rAF) supaya tap cepat tetap mendapat titik yang benar.
        const rect = target.getBoundingClientRect();
        target.style.setProperty('--press-x', `${event.clientX - rect.left}px`);
        target.style.setProperty('--press-y', `${event.clientY - rect.top}px`);
        pressed = target;
    }, { passive: true });

    // Hanya perlu mengikuti jari saat gerak tidak dikurangi pengguna.
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.addEventListener('pointermove', (event) => {
            if (!pressed) {
                return;
            }

            pending = event;

            if (!frame) {
                frame = requestAnimationFrame(flush);
            }
        }, { passive: true });
    }

    ['pointerup', 'pointercancel'].forEach((type) => {
        document.addEventListener(type, () => {
            pressed = null;
        }, { passive: true });
    });
})();
