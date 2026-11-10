<?php

namespace Database\Seeders;

use App\Models\CatatanKonsultasi;
use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use App\Notifications\PengajuanBaruNotification;
use App\Notifications\PengajuanStatusNotification;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $dosen = User::where('email', 'dosen@test.com')->first();
        $mahasiswa = User::where('email', 'mahasiswa@test.com')->first();

        if (! $dosen || ! $mahasiswa) {
            $this->command->warn('Akun demo tidak ditemukan. Jalankan DatabaseSeeder terlebih dahulu.');

            return;
        }

        $mahasiswa->update(['dosen_wali_id' => $dosen->id]);

        $bimbingan = collect([
            ['name' => 'Ahmad Fauzi', 'nim' => '2023002', 'email' => 'ahmad@test.com'],
            ['name' => 'Siti Rahma', 'nim' => '2023003', 'email' => 'siti@test.com'],
            ['name' => 'Rizky Pratama', 'nim' => '2023004', 'email' => 'rizky@test.com'],
        ])->map(fn ($data) => User::firstOrCreate(
            ['email' => $data['email']],
            $data + ['role' => 'mahasiswa', 'password' => bcrypt('password'), 'dosen_wali_id' => $dosen->id]
        ));

        // Mahasiswa tanpa dosen wali untuk menguji alur penugasan NIM.
        User::firstOrCreate(
            ['email' => 'belum@test.com'],
            ['name' => 'Putri Amelia', 'nim' => '2023999', 'role' => 'mahasiswa', 'password' => bcrypt('password')]
        );

        $jadwal = collect([
            ['tanggal' => now()->addDays(2), 'jam_mulai' => '09:00', 'jam_selesai' => '10:00', 'lokasi' => 'Ruang Dosen Lt. 2', 'status' => 'active'],
            ['tanggal' => now()->addDays(3), 'jam_mulai' => '13:00', 'jam_selesai' => '14:00', 'lokasi' => 'Google Meet', 'status' => 'active'],
            ['tanggal' => now()->addDays(5), 'jam_mulai' => '10:00', 'jam_selesai' => '11:00', 'lokasi' => 'Ruang Dosen Lt. 2', 'status' => 'active'],
            ['tanggal' => now()->subDays(3), 'jam_mulai' => '09:00', 'jam_selesai' => '10:00', 'lokasi' => 'Ruang Dosen Lt. 2', 'status' => 'active'],
            ['tanggal' => now()->subDays(10), 'jam_mulai' => '14:00', 'jam_selesai' => '15:00', 'lokasi' => 'Google Meet', 'status' => 'inactive'],
        ])->map(fn ($data) => JadwalKonsultasi::firstOrCreate(
            ['dosen_id' => $dosen->id, 'tanggal' => $data['tanggal']->toDateString(), 'jam_mulai' => $data['jam_mulai']],
            $data + ['dosen_id' => $dosen->id]
        ));

        $pending = $this->pengajuan($jadwal[0], $mahasiswa, [
            'topik' => 'Konsultasi rencana studi semester ganjil',
            'deskripsi' => 'Saya ingin berkonsultasi mengenai rencana pengambilan mata kuliah semester ganjil dan beban SKS yang sesuai.',
            'status' => 'pending',
        ]);

        $approved = $this->pengajuan($jadwal[1], $bimbingan[0], [
            'topik' => 'Konsultasi mata kuliah ulang',
            'deskripsi' => 'Mohon arahan terkait mata kuliah yang perlu diulang dan strategi perbaikan nilai.',
            'status' => 'approved',
        ]);

        $completed = $this->pengajuan($jadwal[3], $bimbingan[1], [
            'topik' => 'Konsultasi progres skripsi',
            'deskripsi' => 'Diskusi progres bab 3 dan kendala pengumpulan data penelitian.',
            'status' => 'completed',
        ]);

        $rejected = $this->pengajuan($jadwal[2], $bimbingan[2], [
            'topik' => 'Konsultasi beasiswa',
            'deskripsi' => 'Ingin menanyakan kelengkapan berkas pendaftaran beasiswa prestasi.',
            'status' => 'rejected',
            'alasan_penolakan' => 'Topik beasiswa ditangani oleh bagian kemahasiswaan, bukan dosen wali.',
        ]);

        if ($completed->wasRecentlyCreated) {
            CatatanKonsultasi::create([
                'pengajuan_id' => $completed->id,
                'dosen_id' => $dosen->id,
                'ringkasan' => 'Mahasiswa menyampaikan progres bab 3 sudah 70% dan kendala pada izin pengambilan data.',
                'arahan' => 'Lengkapi surat izin penelitian dan jadwalkan bimbingan lanjutan setelah data terkumpul.',
                'tindak_lanjut' => 'Kirim draf bab 3 melalui email sebelum pertemuan berikutnya.',
            ]);
        }

        if ($pending->wasRecentlyCreated) {
            $dosen->notifyNow(new PengajuanBaruNotification($pending));
        }

        if ($approved->wasRecentlyCreated) {
            $mahasiswa->notifyNow(new PengajuanStatusNotification($approved, 'approved'));
        }

        if ($completed->wasRecentlyCreated) {
            $mahasiswa->notifyNow(new PengajuanStatusNotification($completed, 'completed'));
        }

        $this->command->info('Data demo QA berhasil dibuat untuk '.$dosen->email.'.');
    }

    private function pengajuan(JadwalKonsultasi $jadwal, User $mahasiswa, array $attributes): PengajuanKonsultasi
    {
        return PengajuanKonsultasi::firstOrCreate(
            ['jadwal_id' => $jadwal->id],
            $attributes + [
                'mahasiswa_id' => $mahasiswa->id,
                'dosen_id' => $jadwal->dosen_id,
            ]
        );
    }
}
