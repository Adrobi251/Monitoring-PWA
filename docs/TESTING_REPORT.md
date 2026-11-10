# Laporan Pengujian

## Ringkasan

- Framework: **PHPUnit** (konfigurasi proyek, SQLite in-memory untuk test).
- Hasil rilis ini: **94 test, 381 assertion, 0 gagal**.
- Cakupan: autentikasi, otorisasi, aturan bisnis jadwal/pengajuan/catatan, filter/notifikasi,
  PWA, UI smoke, profil, keamanan header.

## Suite dan hasil

| Suite | Test | Lulus | Gagal | Fokus |
|---|---:|---:|---:|---|
| `AuthSecurityTest` | 9 | 9 | 0 | guest redirect, password salah, rate limit, logout, role forcing, NIM unik, mass assignment, hashing, header keamanan |
| `AuthorizationTest` | 9 | 9 | 0 | lintas role, IDOR pengajuan/jadwal/catatan, mahasiswa luar bimbingan, isolasi notifikasi |
| `BusinessRulesTest` | 25 | 25 | 0 | CRUD jadwal, overlap, slot berdampingan, waktu lewat, double booking, approve/reject, catatan, integritas hapus |
| `FilterAndNotificationTest` | 5 | 5 | 0 | filter riwayat, pencarian mahasiswa, filter jadwal, notifikasi baca/baca-semua |
| `ConsultationFlowTest` | 14 | 14 | 0 | alur end-to-end mahasiswa-dosen |
| `PwaTest` | 5 | 5 | 0 | manifest, service worker, offline fallback, cache privat |
| `ProfileTest` | 4 | 4 | 0 | update profil, ganti password, hapus akun |
| `UiSmokeTest` | 18 | 18 | 0 | render halaman utama tanpa error |
| `ExampleTest` | 1 | 1 | 0 | smoke default |
| **Total** | **94** | **94** | **0** | |

## Audit keamanan

- Dependensi: `composer audit` (0 advisory), `npm audit --omit=dev` (0 vulnerability).
- HTTP nyata (artisan serve):
  - POST `/login` tanpa CSRF → **419**.
  - `/.env` → **404**.
  - Cookie session: `httponly; samesite=lax`.
  - Header keamanan hadir; `X-Powered-By` tidak terekspos.
- Validasi: NIM unik, email unik, mass assignment dibatasi, password di-hash.
- Otorisasi: semua skenario IDOR lulus.

## Temuan Phase 5 dan status perbaikan (Phase 6)

| Kode | Temuan | Status |
|---|---|---|
| H1 | Registrasi NIM duplikat → 500 + info leak | Diperbaiki (validasi + `APP_DEBUG=false`) |
| H2 | Hapus jadwal dengan pengajuan pending → cascade | Diperbaiki (blokir hapus) |
| H3 | Hapus jadwal dengan riwayat completed → cascade | Diperbaiki (blokir hapus) |
| M1 | Slot berdampingan ditolak | Diperbaiki (overlap standar) |
| M2 | Jadwal hari-ini lewat masih dibuat | Diperbaiki (cek tanggal+jam) |
| M3 | Pengajuan slot hari-ini lewat diterima | Diperbaiki (cek tanggal+jam) |
| M4 | Approve saat jadwal nonaktif | Diperbaiki (guard aktif) |
| M5 | Approve saat jadwal lewat | Diperbaiki (guard waktu) |
| M6 | Overflow 390px (3 halaman) | Diperbaiki (`grid-cols-1` dasar) |
| M7 | Race duplicate catatan | Diperbaiki (lock + unique index) |
| M8 | Edit jadwal approved tanpa notifikasi | Diperbaiki (notifikasi baru) |

## Pengujian responsif

- 22 halaman diuji pada viewport 1440/1280/768/390/360 menggunakan emulasi perangkat CDP.
- Hasil awal: 3 halaman overflow di 390px (penyebab `grid gap-6 lg:grid-cols-3` tanpa kolom
  dasar). Setelah perbaikan, pola diterapkan ke 12 view; tidak ada overflow tidak disengaja.
- Tabel menggunakan `min-w-[640px]` dengan scroll horizontal di layar kecil.

## Pengujian PWA

Lihat [Pengujian PWA](PWA_TESTING.md). Ringkas: manifest valid, service worker v3 terdaftar,
offline fallback tampil, aset statis tercache, halaman privat tidak bocor, installable pada
browser yang mendukung (memerlukan HTTPS/localhost).

## Batasan pengujian

1. Double booking konkuren tidak dapat dibuktikan di SQLite in-memory; diuji secara sekuensial
   plus audit kode `lockForUpdate`. Jalur MySQL nyata belum dieksekusi.
2. Viewport 390/360 diukur via CDP device emulation (Edge headless minimum 496px).
3. Tidak ada audit aksesibilitas otomatis (axe/Lighthouse).
4. Pengujian beban (load testing) tidak dilakukan.
5. Seluruh test berjalan pada `APP_ENV=testing` (SQLite), bukan MySQL produksi.

## Cara menjalankan ulang

```powershell
php artisan test
php artisan test --filter=AuthSecurityTest
php artisan test --filter=BusinessRulesTest
php artisan test --filter=PwaTest
```

Setiap perubahan kode harus diikuti `php artisan test` dengan hasil 0 gagal sebelum dinyatakan
selesai.
