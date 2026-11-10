<?php

namespace Tests\Feature;

use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use App\Notifications\PengajuanBaruNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function dosen(): User
    {
        return User::factory()->create(['role' => 'dosen', 'nidn' => 'D001']);
    }

    private function mahasiswa(User $dosen, string $nim, string $name): User
    {
        return User::factory()->create([
            'role' => 'mahasiswa',
            'nim' => $nim,
            'name' => $name,
            'dosen_wali_id' => $dosen->id,
        ]);
    }

    public function test_dosen_riwayat_can_be_filtered_by_status_and_keyword()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen, 'NIM001', 'Ahmad Fauzi');

        PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'topik' => 'Konsultasi KRS Semester Ganjil',
            'status' => 'pending',
        ]);

        PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'topik' => 'Konsultasi Skripsi',
            'status' => 'approved',
        ]);

        $this->actingAs($dosen)
            ->get(route('dosen.riwayat', ['status' => 'pending']))
            ->assertOk()
            ->assertSee('Konsultasi KRS Semester Ganjil')
            ->assertDontSee('Konsultasi Skripsi');

        $this->actingAs($dosen)
            ->get(route('dosen.riwayat', ['q' => 'Skripsi']))
            ->assertOk()
            ->assertSee('Konsultasi Skripsi')
            ->assertDontSee('Konsultasi KRS Semester Ganjil');

        $this->actingAs($dosen)
            ->get(route('dosen.riwayat', ['q' => 'Ahmad']))
            ->assertOk()
            ->assertSee('Konsultasi KRS Semester Ganjil')
            ->assertSee('Konsultasi Skripsi');
    }

    public function test_dosen_can_search_guided_mahasiswa_by_name_nim_or_email()
    {
        $dosen = $this->dosen();
        $this->mahasiswa($dosen, 'NIM001', 'Ahmad Fauzi');
        $this->mahasiswa($dosen, 'NIM002', 'Siti Rahma');

        $this->actingAs($dosen)
            ->get(route('dosen.mahasiswa.index', ['q' => 'Siti']))
            ->assertOk()
            ->assertSee('Siti Rahma')
            ->assertDontSee('Ahmad Fauzi');

        $this->actingAs($dosen)
            ->get(route('dosen.mahasiswa.index', ['q' => 'NIM001']))
            ->assertOk()
            ->assertSee('Ahmad Fauzi')
            ->assertDontSee('Siti Rahma');
    }

    public function test_dosen_jadwal_can_be_filtered_by_status()
    {
        $dosen = $this->dosen();

        JadwalKonsultasi::factory()->create([
            'dosen_id' => $dosen->id,
            'lokasi' => 'Ruang Jadwal Aktif',
            'tanggal' => now()->addDay()->toDateString(),
            'status' => 'active',
        ]);

        JadwalKonsultasi::factory()->create([
            'dosen_id' => $dosen->id,
            'lokasi' => 'Ruang Jadwal Nonaktif',
            'tanggal' => now()->addDays(2)->toDateString(),
            'status' => 'inactive',
        ]);

        $this->actingAs($dosen)
            ->get(route('dosen.jadwal', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee('Ruang Jadwal Nonaktif')
            ->assertDontSee('Ruang Jadwal Aktif');
    }

    public function test_user_can_mark_own_notification_as_read()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen, 'NIM001', 'Ahmad Fauzi');

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
        ]);

        $mahasiswa->notify(new PengajuanBaruNotification($pengajuan));
        $notification = $mahasiswa->notifications()->first();

        $this->actingAs($mahasiswa)
            ->post(route('notifications.read', $notification->id))
            ->assertRedirect(route('mahasiswa.pengajuan.show', $pengajuan));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_own_notifications_as_read()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen, 'NIM001', 'Ahmad Fauzi');

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
        ]);

        $mahasiswa->notify(new PengajuanBaruNotification($pengajuan));
        $mahasiswa->notify(new PengajuanBaruNotification($pengajuan));

        $this->assertSame(2, $mahasiswa->unreadNotifications()->count());

        $this->actingAs($mahasiswa)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $mahasiswa->unreadNotifications()->count());
    }
}
