<?php

namespace Tests\Feature;

use App\Models\CatatanKonsultasi;
use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function mahasiswaWithDosen(): array
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'nidn' => 'D001']);
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'nim' => '123456789',
            'dosen_wali_id' => $dosen->id,
        ]);

        return [$dosen, $mahasiswa];
    }

    public function test_guest_pages_render(): void
    {
        $this->get('/')->assertOk();
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
        $this->get(route('password.request'))->assertOk();
    }

    public function test_mahasiswa_pages_render(): void
    {
        [$dosen, $mahasiswa] = $this->mahasiswaWithDosen();

        $jadwal = JadwalKonsultasi::factory()->create([
            'dosen_id' => $dosen->id,
            'tanggal' => now()->addDay()->toDateString(),
        ]);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'approved',
        ]);

        CatatanKonsultasi::factory()->create([
            'pengajuan_id' => $pengajuan->id,
            'dosen_id' => $dosen->id,
        ]);

        $this->actingAs($mahasiswa)->get(route('mahasiswa.dashboard'))->assertOk();
        $this->actingAs($mahasiswa)->get(route('mahasiswa.jadwal'))->assertOk();
        $this->actingAs($mahasiswa)->get(route('mahasiswa.riwayat'))->assertOk();
        $this->actingAs($mahasiswa)->get(route('mahasiswa.riwayat', ['status' => 'approved', 'q' => 'topik']))->assertOk();
        $this->actingAs($mahasiswa)->get(route('mahasiswa.dosen-wali'))->assertOk();
        $this->actingAs($mahasiswa)->get(route('mahasiswa.pengajuan.create'))->assertOk();
        $this->actingAs($mahasiswa)->get(route('mahasiswa.pengajuan.show', $pengajuan))->assertOk();
        $this->actingAs($mahasiswa)->get(route('mahasiswa.pengajuan.catatan.show', $pengajuan))->assertOk();
        $this->actingAs($mahasiswa)->get(route('profile.edit'))->assertOk();
        $this->actingAs($mahasiswa)->get(route('notifications.index'))->assertOk();
    }

    public function test_dosen_pages_render(): void
    {
        [$dosen, $mahasiswa] = $this->mahasiswaWithDosen();

        $this->actingAs($dosen)->get(route('dosen.dashboard'))->assertOk();

        $jadwal = JadwalKonsultasi::factory()->create([
            'dosen_id' => $dosen->id,
            'tanggal' => now()->addDay()->toDateString(),
        ]);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'approved',
        ]);

        $catatan = CatatanKonsultasi::factory()->create([
            'pengajuan_id' => $pengajuan->id,
            'dosen_id' => $dosen->id,
        ]);

        $this->actingAs($dosen)->get(route('dosen.dashboard'))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.jadwal'))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.jadwal', ['status' => 'active']))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.jadwal.create'))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.jadwal.edit', $jadwal))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.riwayat'))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.riwayat', ['status' => 'approved', 'q' => '123456789']))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.riwayat.show', $pengajuan))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.mahasiswa.index'))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.mahasiswa.index', ['q' => '123456789']))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.mahasiswa.show', $mahasiswa))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.pengajuan.catatan.edit', [$pengajuan, $catatan]))->assertOk();
        $this->actingAs($dosen)->get(route('profile.edit'))->assertOk();
        $this->actingAs($dosen)->get(route('notifications.index'))->assertOk();
    }

    public function test_slot_already_pending_cannot_be_booked_again(): void
    {
        [$dosen, $mahasiswa] = $this->mahasiswaWithDosen();
        $other = User::factory()->create([
            'role' => 'mahasiswa',
            'nim' => '987654321',
            'dosen_wali_id' => $dosen->id,
        ]);

        $jadwal = JadwalKonsultasi::factory()->create([
            'dosen_id' => $dosen->id,
            'tanggal' => now()->addDay()->toDateString(),
            'status' => 'active',
        ]);

        PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $other->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'pending',
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('mahasiswa.pengajuan.store'), [
                'jadwal_id' => $jadwal->id,
                'topik' => 'Konsultasi',
                'deskripsi' => 'Deskripsi',
            ])
            ->assertSessionHasErrors('jadwal_id');
    }

    public function test_catatan_marks_pengajuan_completed_and_notifies_mahasiswa(): void
    {
        [$dosen, $mahasiswa] = $this->mahasiswaWithDosen();

        $jadwal = JadwalKonsultasi::factory()->create([
            'dosen_id' => $dosen->id,
            'tanggal' => now()->addDay()->toDateString(),
        ]);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'approved',
        ]);

        $this->actingAs($dosen)
            ->post(route('dosen.pengajuan.catatan.store', $pengajuan), [
                'ringkasan' => 'Ringkasan konsultasi',
                'arahan' => 'Arahan konsultasi',
                'tindak_lanjut' => 'Tindak lanjut',
            ])
            ->assertRedirect(route('dosen.riwayat.show', $pengajuan));

        $this->assertEquals('completed', $pengajuan->refresh()->status);
        $this->assertDatabaseCount('catatan_konsultasi', 1);
        $this->assertEquals(1, $mahasiswa->notifications()->count());
    }

    public function test_dosen_can_approve_from_detail_page(): void
    {
        [$dosen, $mahasiswa] = $this->mahasiswaWithDosen();

        $jadwal = JadwalKonsultasi::factory()->create([
            'dosen_id' => $dosen->id,
            'tanggal' => now()->addDay()->toDateString(),
        ]);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'pending',
        ]);

        $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.status', $pengajuan), ['status' => 'approved'])
            ->assertRedirect(route('dosen.riwayat.show', $pengajuan));

        $this->assertEquals('approved', $pengajuan->refresh()->status);
        $this->assertEquals(1, $mahasiswa->notifications()->count());
    }

    public function test_notification_can_be_marked_read(): void
    {
        [$dosen, $mahasiswa] = $this->mahasiswaWithDosen();

        $jadwal = JadwalKonsultasi::factory()->create(['dosen_id' => $dosen->id]);
        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
        ]);

        $mahasiswa->notify(new \App\Notifications\PengajuanStatusNotification($pengajuan, 'approved'));
        $notification = $mahasiswa->notifications()->first();

        $this->actingAs($mahasiswa)
            ->post(route('notifications.read', $notification->id))
            ->assertRedirect(route('mahasiswa.pengajuan.show', $pengajuan));

        $this->assertNotNull($notification->fresh()->read_at);
    }
}
