# Pengujian PWA

## Komponen

- Manifest: `public/manifest.webmanifest` (nama, short_name, start_url, display standalone,
  theme_color `#176B52`, ikon 192/512).
- Ikon: `public/icon-192.png`, `public/icon-512.png`, `public/apple-touch-icon.png`,
  `public/favicon.ico`.
- Service worker: `public/service-worker.js` (versi cache `konsultasi-pwa-v3`).
- Offline fallback: `public/offline.html`.
- Registrasi: `resources/views/layouts/app.blade.php` (navigator.serviceWorker.register).
- MIME/headers: `public/.htaccess`.

## Strategi cache

- Precache: aset statis (CSS/JS build, ikon, manifest, offline.html).
- Runtime: network-first untuk navigasi HTML; cache-first untuk aset statis.
- Halaman privat (`/dashboard`, `/mahasiswa/*`, `/dosen/*`, `/profile`, `/notifications`)
  **tidak** disimpan di cache publik.

## Langkah pengujian manual

1. **Manifest dapat dibaca**
   - Buka DevTools → Application → Manifest.
   - Pastikan tidak ada error, ikon 192 & 512 tersedia, `display: standalone`.

2. **Service worker terdaftar**
   - Application → Service Workers: status "activated and is running".
   - Scope `/`.

3. **Aset statis tercache**
   - Application → Cache Storage: cache `konsultasi-pwa-v3` berisi CSS/JS/ikon.

4. **Offline fallback**
   - DevTools → Network → centang "Offline".
   - Reload halaman: tampil `/offline.html`.
   - Matikan offline; reload: aplikasi kembali normal.

5. **Halaman privat tidak bocor**
   - Setelah login, buka halaman privat.
   - Offline, buka URL privat: harus tampil offline fallback, **bukan** konten privat dari cache.

6. **Installability**
   - Browser (Chrome/Edge) menampilkan ikon install di address bar.
   - Klik install → aplikasi terpasang, dapat dibuka dari home screen/start menu.
   - Catatan: memerlukan HTTPS atau localhost.

## Verifikasi otomatis

- `PwaTest` (5 test) di `tests/Feature/PwaTest.php`: manifest readable, service worker
  registered, offline fallback, private pages not cached.

## Versi dan pembaruan cache

- Naikkan versi cache di `public/service-worker.js` setiap perubahan aset:
  `const CACHE_VERSION = 'konsultasi-pwa-v4';`
- Pengguna lama akan memperbarui cache otomatis saat service worker baru aktif.

## Keterbatasan

- Installasi PWA memerlukan HTTPS (atau localhost); `http://konsultasi-layanan.test` tanpa
  HTTPS tidak mendaftarkan service worker.
- Browser lama (Safari < 16.4) memiliki dukungan terbatas untuk PWA.
- Push notification tidak diimplementasikan (hanya notifikasi in-app database).
- Offline write (pengajuan saat offline) tidak didukung; pengguna harus online.
