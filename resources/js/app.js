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
