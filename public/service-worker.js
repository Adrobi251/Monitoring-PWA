// Service worker PWA: hanya cache aset statis publik, tidak pernah cache halaman privat.
const CACHE_NAME = 'konsultasi-pwa-v3';
const PRECACHE = [
    '/offline.html',
    '/manifest.webmanifest',
    '/icon-192.png',
    '/icon-512.png',
    '/apple-touch-icon.png',
];

const STATIC_PREFIXES = [
    '/build/',
    '/icon-',
    '/apple-touch-icon',
    '/favicon.ico',
    '/manifest.webmanifest',
    '/offline.html',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(PRECACHE))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((names) => Promise.all(
                names.filter((name) => name !== CACHE_NAME).map((name) => caches.delete(name))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Jangan pernah menyentuh request non-GET (POST/PUT/DELETE) atau lintas origin.
    if (request.method !== 'GET' || !request.url.startsWith(self.location.origin)) {
        return;
    }

    // Navigasi halaman: selalu jaringan, fallback offline. HTML tidak pernah dicache.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html'))
        );
        return;
    }

    const url = new URL(request.url);
    const isStatic = STATIC_PREFIXES.some((prefix) => url.pathname.startsWith(prefix));

    if (!isStatic) {
        // Response dinamis (JSON, gambar privat, dsb.) dibiarkan lewat tanpa cache.
        return;
    }

    event.respondWith(
        caches.match(request).then((cached) => {
            if (cached) {
                return cached;
            }

            return fetch(request).then((response) => {
                if (response.ok && response.type === 'basic') {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                }

                return response;
            });
        })
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
