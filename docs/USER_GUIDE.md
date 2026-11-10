# Panduan Pengguna

## Akun demo

| Peran | Email | Password | Keterangan |
|---|---|---|---|
| Dosen | `dosen@test.com` | `password` | NIDN `D001`, id 3 |
| Mahasiswa | `mahasiswa@test.com` | `password` | NIM `123456789`, id 4, dosen wali id 3 |

Akun bimbingan contoh (password `password`): `ahmad@test.com`, `siti@test.com`,
`rizky@test.com`. Akun `belum@test.com` (Putri Amelia) sengaja tanpa dosen wali.

## Alur mahasiswa

1. **Login** di `/login` dengan email dan password.
2. **Dashboard** (`/mahasiswa/dashboard`): ringkasan pengajuan (total, menunggu, disetujui,
   selesai) dan aksi cepat.
3. **Lihat jadwal** (`/mahasiswa/dashboard/jadwal`): slot aktif dosen wali yang belum penuh.
4. **Ajukan konsultasi** (`/mahasiswa/pengajuan/create`):
   - Pilih jadwal, isi topik dan deskripsi.
   - Slot yang sudah diajukan/disetujui tidak dapat dipilih ulang.
   - Slot yang sudah lewat tidak dapat diajukan.
5. **Riwayat** (`/mahasiswa/dashboard/riwayat`): daftar pengajuan dengan status dan filter.
6. **Detail pengajuan** (`/mahasiswa/pengajuan/{id}`): status, jadwal, catatan (jika selesai).
7. **Notifikasi**: ikon lonceng; klik untuk menandai dibaca dan membuka detail.
8. **Profil** (`/profile`): ubah nama dan email; ganti password.

## Alur dosen

1. **Login** dengan akun dosen.
2. **Dashboard** (`/dosen/dashboard`): pengajuan pending, jadwal mendatang, ringkasan
   mahasiswa bimbingan.
3. **Kelola jadwal** (`/dosen/dashboard/jadwal`):
   - Tambah slot (tanggal, jam mulai/selesai, lokasi, status).
   - Sistem menolak slot yang bentrok (slot berdampingan diizinkan).
   - Slot dengan riwayat pengajuan tidak dapat dihapus; nonaktifkan via Edit.
   - Filter berdasarkan status (aktif/nonaktif).
4. **Proses pengajuan** (`/dosen/dashboard/riwayat`):
   - Buka detail, setujui atau tolak (alasan wajib saat menolak).
   - Slot tidak bisa disetujui untuk dua mahasiswa.
   - Hanya jadwal aktif dan belum lewat yang dapat disetujui.
5. **Mahasiswa bimbingan** (`/dosen/mahasiswa`):
   - Cari berdasarkan nama/NIM/email.
   - Tugaskan mahasiswa ke dosen wali melalui NIM.
   - Lihat detail dan riwayat pengajuan mahasiswa.
6. **Catatan konsultasi**: pada pengajuan berstatus disetujui, dosen menambahkan ringkasan,
   arahan, tindak lanjut. Pengajuan otomatis menjadi selesai. Satu catatan per pengajuan.
7. **Notifikasi**: diberi tahu saat ada pengajuan baru dan saat jadwal yang disetujui
   berubah.

## Filter dan pencarian

- Riwayat dosen: filter status (`pending`/`approved`/`rejected`/`completed`) dan pencarian
  topik/nama mahasiswa.
- Daftar mahasiswa: pencarian nama/NIM/email.
- Daftar jadwal: filter status.

## Penggunaan di perangkat mobile

- Sidebar menjadi drawer (tombol hamburger).
- Tabel dapat digeser horizontal bila diperlukan; kolom disusun agar tetap terbaca.
- Form dan tombol memiliki ukuran sentuh yang nyaman.
- Dark mode mengikuti preferensi sistem atau pilihan manual.

## Mode offline

- Saat koneksi terputus, halaman offline (`/offline.html`) ditampilkan.
- Aset statis (CSS/JS/ikon) tersedia dari cache.
- Form pengajuan tidak dapat dikirim saat offline; pengguna diminta mencoba kembali.
- Data privat tidak disimpan di cache publik.

## Batasan

- Aplikasi memerlukan koneksi internet untuk pengajuan dan persetujuan.
- Instalasi PWA memerlukan HTTPS (atau localhost).
- Notifikasi memerlukan queue `sync` (default demo) atau worker aktif.
