<?php

namespace Tests\Feature;

use App\Models\CatatanKonsultasi;
use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_login_redirects_to_mahasiswa_dashboard()
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@test.com',
            'password' => 'password',
            'nim' => '123456789',
        ]);
        $loginResponse = $this->post(route('login'), [
            'email' => 'mahasiswa@test.com',
            'password' => 'password',
        ]);
        $loginResponse->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_dosen_login_redirects_to_dosen_dashboard()
    {
        $dosen = User::factory()->create([
            'role' => 'dosen',
            'email' => 'dosen@test.com',
            'password' => 'password',
            'nidn' => 'D001',
        ]);
        $loginResponse = $this->post(route('login'), [
            'email' => 'dosen@test.com',
            'password' => 'password',
        ]);
        $loginResponse->assertRedirect(route('dosen.dashboard'));
    }

    public function test_mahasiswa_can_ajukan_konsultasi()
    {
        $dosen = User::factory()->create([
            'role' => 'dosen',
            'email' => 'dosen@test.com',
            'password' => 'password',
            'nidn' => 'D001',
        ]);
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@test.com',
            'password' => 'password',
            'nim' => '123456789',
            'dosen_wali_id' => $dosen->id,
        ]);
        $jadwal = JadwalKonsultasi::factory()->create([
            'dosen_id' => $dosen->id,
            'status' => 'active',
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
        ]);

        $response = $this->actingAs($mahasiswa)
            ->post(route('mahasiswa.pengajuan.store'), [
                'jadwal_id' => $jadwal->id,
                'topik' => 'Perubahan Jadwal',
                'deskripsi' => 'Mohon jadwal berubah',
            ]);

        $response->assertSessionHas('success');
        $this->assertTrue(PengajuanKonsultasi::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_id', $jadwal->id)
            ->where('status', 'pending')
            ->exists());
    }

    public function test_dosen_can_approve_pengajuan_and_prevent_double_booking()
    {
        $dosen = User::factory()->create([
            'role' => 'dosen',
            'email' => 'dosen@test.com',
            'password' => 'password',
            'nidn' => 'D001',
        ]);
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@test.com',
            'password' => 'password',
            'nim' => '123456789',
        ]);

        $jadwal1 = JadwalKonsultasi::factory()->create([
            'dosen_id' => $dosen->id,
            'status' => 'active',
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
        ]);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal1->id,
            'status' => 'pending',
        ]);

        // Approve the first jadwal - should succeed
        $response = $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.status', $pengajuan), [
                'status' => 'approved',
                'alasan_penolakan' => 'Mohon maaf, sudah ada jadwal yang lebih sesuai.',
            ]);

        $this->assertEquals('approved', $pengajuan->refresh()->status);

        // Try to approve the second jadwal (should fail due to double booking on same jadwal)
        $pengajuan2 = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal1->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.status', $pengajuan2), [
                'status' => 'approved',
                'alasan_penolakan' => 'Test reason',
            ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_dosen_cannot_access_mahasiswa_catatan()
    {
        $dosen = User::factory()->create([
            'role' => 'dosen',
            'email' => 'dosen@test.com',
            'password' => 'password',
            'nidn' => 'D001',
        ]);
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@test.com',
            'password' => 'password',
            'nim' => '123456789',
        ]);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'status' => 'approved',
        ]);

        // Dosen tries to access catatan - should be forbidden
        $response = $this->actingAs($dosen)
            ->get(route('mahasiswa.pengajuan.catatan.show', $pengajuan));

        $response->assertStatus(403);
    }

    public function test_mahasiswa_can_read_own_pengajuan_and_catatan()
    {
        $dosen = User::factory()->create([
            'role' => 'dosen',
            'email' => 'dosen@test.com',
            'password' => 'password',
            'nidn' => 'D001',
        ]);
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@test.com',
            'password' => 'password',
            'nim' => '123456789',
        ]);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'status' => 'approved',
        ]);

        $catatan = CatatanKonsultasi::factory()->create([
            'pengajuan_id' => $pengajuan->id,
            'dosen_id' => $dosen->id,
        ]);

        // Mahasiswa can access own pengajuan and catatan
        $response = $this->actingAs($mahasiswa)
            ->get(route('mahasiswa.pengajuan.catatan.show', $pengajuan));

        $response->assertStatus(200);
        $response->assertSee('Catatan Konsultasi');
    }

    public function test_dosen_can_assign_mahasiswa_by_nim()
    {
        $dosen = User::factory()->create([
            'role' => 'dosen',
            'email' => 'dosen@test.com',
            'password' => 'password',
            'nidn' => 'D001',
        ]);
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@test.com',
            'password' => 'password',
            'nim' => 'NIM001',
        ]);

        $response = $this->actingAs($dosen)
            ->post(route('dosen.mahasiswa.assign'), ['nim' => 'NIM001']);

        $response->assertSessionHas('success');
        $this->assertEquals($dosen->id, $mahasiswa->refresh()->dosen_wali_id);
    }

    public function test_duplicate_nim_is_rejected_by_database()
    {
        User::factory()->create([
            'role' => 'mahasiswa',
            'nim' => 'NIM001',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create([
            'role' => 'mahasiswa',
            'nim' => 'NIM001',
        ]);
    }
}