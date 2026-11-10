<?php

namespace Tests\Feature;

use App\Models\CatatanKonsultasi;
use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use App\Notifications\PengajuanBaruNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function dosen(string $nidn = 'D001'): User
    {
        return User::factory()->create([
            'role' => 'dosen',
            'nidn' => $nidn,
        ]);
    }

    private function mahasiswa(User $dosen, string $nim = 'NIM001'): User
    {
        return User::factory()->create([
            'role' => 'mahasiswa',
            'nim' => $nim,
            'dosen_wali_id' => $dosen->id,
        ]);
    }

    public function test_role_middleware_blocks_cross_role_access()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);

        $this->actingAs($mahasiswa)->get(route('dosen.dashboard'))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('dosen.jadwal'))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('dosen.mahasiswa.index'))->assertForbidden();

        $this->actingAs($dosen)->get(route('mahasiswa.dashboard'))->assertForbidden();
        $this->actingAs($dosen)->get(route('mahasiswa.pengajuan.create'))->assertForbidden();
        $this->actingAs($dosen)->get(route('mahasiswa.riwayat'))->assertForbidden();
    }

    public function test_mahasiswa_cannot_view_another_mahasiswas_pengajuan()
    {
        $dosen = $this->dosen();
        $pemilik = $this->mahasiswa($dosen, 'NIM001');
        $lain = $this->mahasiswa($dosen, 'NIM002');

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $pemilik->id,
            'dosen_id' => $dosen->id,
        ]);

        $this->actingAs($lain)
            ->get(route('mahasiswa.pengajuan.show', $pengajuan))
            ->assertForbidden();

        $this->actingAs($pemilik)
            ->get(route('mahasiswa.pengajuan.show', $pengajuan))
            ->assertOk();
    }

    public function test_dosen_cannot_view_another_dosens_pengajuan()
    {
        $dosenA = $this->dosen('D001');
        $dosenB = $this->dosen('D002');
        $mahasiswa = $this->mahasiswa($dosenA);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosenA->id,
        ]);

        $this->actingAs($dosenB)
            ->get(route('dosen.riwayat.show', $pengajuan))
            ->assertForbidden();

        $this->actingAs($dosenB)
            ->put(route('dosen.pengajuan.status', $pengajuan), ['status' => 'approved'])
            ->assertForbidden();
    }

    public function test_dosen_cannot_edit_update_or_delete_another_dosens_jadwal()
    {
        $dosenA = $this->dosen('D001');
        $dosenB = $this->dosen('D002');

        $jadwal = JadwalKonsultasi::factory()->create([
            'dosen_id' => $dosenA->id,
            'tanggal' => now()->addDay()->toDateString(),
        ]);

        $payload = [
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:00',
            'lokasi' => 'Ruang B',
            'status' => 'active',
        ];

        $this->actingAs($dosenB)->get(route('dosen.jadwal.edit', $jadwal))->assertForbidden();
        $this->actingAs($dosenB)->put(route('dosen.jadwal.update', $jadwal), $payload)->assertForbidden();
        $this->actingAs($dosenB)->delete(route('dosen.jadwal.destroy', $jadwal))->assertForbidden();

        $this->assertDatabaseHas('jadwal_konsultasi', [
            'id' => $jadwal->id,
            'jam_mulai' => $jadwal->jam_mulai,
        ]);
    }

    public function test_dosen_cannot_manage_another_dosens_catatan()
    {
        $dosenA = $this->dosen('D001');
        $dosenB = $this->dosen('D002');
        $mahasiswa = $this->mahasiswa($dosenA);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosenA->id,
            'status' => 'approved',
        ]);

        $catatan = CatatanKonsultasi::factory()->create([
            'pengajuan_id' => $pengajuan->id,
            'dosen_id' => $dosenA->id,
        ]);

        $payload = [
            'ringkasan' => 'Diubah dosen lain',
            'arahan' => 'Tidak boleh',
            'tindak_lanjut' => null,
        ];

        $this->actingAs($dosenB)->get(route('dosen.pengajuan.catatan.edit', [$pengajuan, $catatan]))->assertForbidden();
        $this->actingAs($dosenB)->put(route('dosen.pengajuan.catatan.update', [$pengajuan, $catatan]), $payload)->assertForbidden();

        $this->assertDatabaseHas('catatan_konsultasi', [
            'id' => $catatan->id,
            'ringkasan' => $catatan->ringkasan,
        ]);
    }

    public function test_dosen_cannot_view_mahasiswa_outside_their_guidance()
    {
        $dosenA = $this->dosen('D001');
        $dosenB = $this->dosen('D002');
        $mahasiswa = $this->mahasiswa($dosenA);

        $this->actingAs($dosenB)
            ->get(route('dosen.mahasiswa.show', $mahasiswa))
            ->assertForbidden();
    }

    public function test_notifications_are_private_to_their_owner()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $lain = User::factory()->create(['role' => 'mahasiswa', 'nim' => 'NIM999']);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
        ]);

        $dosen->notify(new PengajuanBaruNotification($pengajuan));
        $notificationId = $dosen->notifications()->first()->id;

        $this->actingAs($lain)
            ->post(route('notifications.read', $notificationId))
            ->assertNotFound();

        $this->assertNull($dosen->notifications()->first()->read_at);
    }

    public function test_guest_cannot_access_notifications()
    {
        $this->get(route('notifications.index'))->assertRedirect(route('login'));
        $this->post(route('notifications.read-all'))->assertRedirect(route('login'));
    }

    public function test_mahasiswa_cannot_use_dosen_only_endpoints()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);

        $jadwal = JadwalKonsultasi::factory()->create([
            'dosen_id' => $dosen->id,
            'tanggal' => now()->addDay()->toDateString(),
        ]);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('dosen.jadwal.store'), [
                'tanggal' => now()->addDay()->toDateString(),
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:00',
                'status' => 'active',
            ])
            ->assertForbidden();

        $this->actingAs($mahasiswa)
            ->put(route('dosen.pengajuan.status', $pengajuan), ['status' => 'approved'])
            ->assertForbidden();

        $this->actingAs($mahasiswa)
            ->delete(route('dosen.jadwal.destroy', $jadwal))
            ->assertForbidden();
    }
}
