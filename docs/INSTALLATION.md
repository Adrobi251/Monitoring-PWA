# Panduan Instalasi

## Prasyarat

- PHP 8.3+ (dengan ekstensi: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo)
- Composer 2+
- Node.js 20+ dan npm
- MySQL 8+ (atau MariaDB 10.6+)
- Git

## Langkah instalasi (lokal)

1. **Clone repository**

   ```powershell
   git clone https://github.com/Adrobi251/Monitoring-PWA.git "konsultasi layanan"
   cd "konsultasi layanan"
   ```

2. **Dependensi PHP**

   ```powershell
   composer install
   ```

3. **Konfigurasi lingkungan**

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

   Edit `.env`:

   ```env
   APP_ENV=local
   APP_DEBUG=false
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=konsultasi
   DB_USERNAME=root
   DB_PASSWORD=

   QUEUE_CONNECTION=sync
   SESSION_DRIVER=database
   ```

   Buat database MySQL:

   ```sql
   CREATE DATABASE konsultasi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. **Migrasi dan akun awal**

   ```powershell
   php artisan migrate --seed
   ```

   Perintah ini menjalankan `DatabaseSeeder` dan membuat dua akun awal: `dosen@test.com`
   dan `mahasiswa@test.com` (password `password`). Untuk instalasi tanpa akun contoh,
   gunakan `php artisan migrate` tanpa `--seed`, lalu tambahkan akun dosen secara manual.

5. **Aset frontend**

   ```powershell
   npm install
   npm run build
   ```

6. **Jalankan aplikasi**

   ```powershell
   php artisan serve
   ```

   Buka `http://127.0.0.1:8000`.

## Catatan Laragon (Windows)

- Apache Laragon dapat dipakai sebagai virtual host (mis. `http://konsultasi-layanan.test`).
- Jika Apache tidak berjalan, gunakan `php artisan serve` (port 8000).
- Pastikan `public/.htaccess` tidak diubah; MIME PWA diatur di sana.

## Produksi / HTTPS

1. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain-anda`.
2. Set `SESSION_SECURE_COOKIE=true` (wajib saat HTTPS) dan `SESSION_SAME_SITE=lax`.
3. Gunakan queue worker jika `QUEUE_CONNECTION` bukan `sync`:
   `php artisan queue:work --daemon`.
4. Jalankan `php artisan config:cache`, `route:cache`, `view:cache`.
5. PWA memerlukan HTTPS (atau localhost) agar service worker terdaftar.
6. Matikan `expose_php` di `php.ini` (`expose_php=Off`) sebagai penguat tambahan.
7. Pastikan `storage/` dan `bootstrap/cache/` writable oleh web server.

## Troubleshooting

| Masalah | Solusi |
|---|---|
| `SQLSTATE[HY000] [1049] Unknown database` | Buat database `konsultasi` terlebih dahulu. |
| `Class "..." not found` | `composer dump-autoload`. |
| Aset tidak termuat / tampilan polos | Jalankan `npm run build`; cek `public/build/`. |
| Service worker tidak terdaftar | Butuh HTTPS/localhost; cek tab Application di DevTools. |
| Login tidak tersimpan (cookie hilang) | Saat HTTP lokal, jangan set `SESSION_SECURE_COOKIE=true`. |
| Cache lama PWA | Naikkan versi cache di `public/service-worker.js` (`konsultasi-pwa-v4`). |
| Migration error "table already exists" | `php artisan migrate:fresh --seed` (hati-hati: hapus data). |
