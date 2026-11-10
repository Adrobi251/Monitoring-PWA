<?php

namespace Tests\Feature;

use App\Models\CatatanKonsultasi;
use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BusinessRulesTest extends TestCase
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

    private function jadwal(User $dosen, array $attributes = []): JadwalKonsultasi
    {
        // Insert date-only, like MySQL DATE, so overlap queries behave as in production.
        $data = array_merge([
            'dosen_id' => $dosen->id,
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'lokasi' => 'Ruang Konsultasi',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ], $attributes);

        return JadwalKonsultasi::findOrFail(DB::table('jadwal_konsultasi')->insertGetId($data));
    }

    private function jadwalPayload(array $attributes = []): array
    {
        return array_merge([
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'lokasi' => 'Ruang Konsultasi',
            'status' => 'active',
        ], $attributes);
    }

    public function test_dosen_can_create_update_and_delete_unused_jadwal()
    {
        $dosen = $this->dosen();

        $this->actingAs($dosen)
            ->post(route('dosen.jadwal.store'), $this->jadwalPayload())
            ->assertRedirect(route('dosen.jadwal'));

        $jadwal = JadwalKonsultasi::firstOrFail();

        $this->actingAs($dosen)
            ->put(route('dosen.jadwal.update', $jadwal), $this->jadwalPayload([
                'jam_mulai' => '13:00',
                'jam_selesai' => '14:00',
            ]))
            ->assertRedirect(route('dosen.jadwal'));

        $this->assertSame('13:00', $jadwal->refresh()->jam_mulai);

        $this->actingAs($dosen)
            ->delete(route('dosen.jadwal.destroy', $jadwal))
            ->assertRedirect(route('dosen.jadwal'));

        $this->assertDatabaseMissing('jadwal_konsultasi', ['id' => $jadwal->id]);
    }

    public function test_overlapping_jadwal_is_rejected()
    {
        $dosen = $this->dosen();
        $this->jadwal($dosen, ['jam_mulai' => '09:00', 'jam_selesai' => '10:00']);

        $this->actingAs($dosen)
            ->post(route('dosen.jadwal.store'), $this->jadwalPayload([
                'jam_mulai' => '09:30',
                'jam_selesai' => '10:30',
            ]))
            ->assertSessionHasErrors('tanggal');

        $this->assertSame(1, JadwalKonsultasi::count());
    }

    public function test_adjacent_jadwal_is_allowed()
    {
        $dosen = $this->dosen();
        $this->jadwal($dosen, ['jam_mulai' => '09:00', 'jam_selesai' => '10:00']);

        $this->actingAs($dosen)
            ->post(route('dosen.jadwal.store'), $this->jadwalPayload([
                'jam_mulai' => '10:00',
                'jam_selesai' => '11:00',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dosen.jadwal'));

        $this->assertSame(2, JadwalKonsultasi::count());
    }

    public function test_jadwal_cannot_end_before_it_starts()
    {
        $dosen = $this->dosen();

        $this->actingAs($dosen)
            ->post(route('dosen.jadwal.store'), $this->jadwalPayload([
                'jam_mulai' => '10:00',
                'jam_selesai' => '09:00',
            ]))
            ->assertSessionHasErrors('jam_selesai');

        $this->assertSame(0, JadwalKonsultasi::count());
    }

    public function test_jadwal_earlier_today_is_rejected()
    {
        $this->travelTo(now()->setTime(12, 0));
        $dosen = $this->dosen();

        $this->actingAs($dosen)
            ->post(route('dosen.jadwal.store'), $this->jadwalPayload([
                'tanggal' => now()->toDateString(),
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:00',
            ]))
            ->assertSessionHasErrors('tanggal');

        $this->assertSame(0, JadwalKonsultasi::count());
    }

    public function test_editing_a_jadwal_does_not_conflict_with_itself()
    {
        $dosen = $this->dosen();
        $jadwal = $this->jadwal($dosen);

        $this->actingAs($dosen)
            ->put(route('dosen.jadwal.update', $jadwal), $this->jadwalPayload([
                'lokasi' => 'Ruang Baru',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dosen.jadwal'));

        $this->assertSame('Ruang Baru', $jadwal->refresh()->lokasi);
    }

    public function test_mahasiswa_can_submit_and_view_own_pengajuan()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $jadwal = $this->jadwal($dosen);

        $this->actingAs($mahasiswa)
            ->post(route('mahasiswa.pengajuan.store'), [
                'jadwal_id' => $jadwal->id,
                'topik' => 'Konsultasi KRS',
                'deskripsi' => 'Mohon bimbingan pengisian KRS.',
            ])
            ->assertRedirect();

        $pengajuan = PengajuanKonsultasi::firstOrFail();

        $this->assertSame('pending', $pengajuan->status);
        $this->assertSame($mahasiswa->id, $pengajuan->mahasiswa_id);
        $this->assertSame($dosen->id, $pengajuan->dosen_id);

        $this->actingAs($mahasiswa)
            ->get(route('mahasiswa.pengajuan.show', $pengajuan))
            ->assertOk()
            ->assertSee('Konsultasi KRS');
    }

    public function test_pengajuan_requires_an_active_jadwal()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $jadwal = $this->jadwal($dosen, ['status' => 'inactive']);

        $this->actingAs($mahasiswa)
            ->post(route('mahasiswa.pengajuan.store'), [
                'jadwal_id' => $jadwal->id,
                'topik' => 'Topik',
                'deskripsi' => 'Deskripsi',
            ])
            ->assertSessionHasErrors('jadwal_id');

        $this->assertSame(0, PengajuanKonsultasi::count());
    }

    public function test_pengajuan_cannot_use_another_dosens_jadwal()
    {
        $dosenA = $this->dosen('D001');
        $dosenB = $this->dosen('D002');
        $mahasiswa = $this->mahasiswa($dosenA);
        $jadwal = $this->jadwal($dosenB);

        $this->actingAs($mahasiswa)
            ->post(route('mahasiswa.pengajuan.store'), [
                'jadwal_id' => $jadwal->id,
                'topik' => 'Topik',
                'deskripsi' => 'Deskripsi',
            ])
            ->assertSessionHasErrors('jadwal_id');

        $this->assertSame(0, PengajuanKonsultasi::count());
    }

    public function test_pengajuan_slot_with_pending_or_approved_submission_is_blocked()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $jadwal = $this->jadwal($dosen);

        PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'pending',
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('mahasiswa.pengajuan.store'), [
                'jadwal_id' => $jadwal->id,
                'topik' => 'Topik kedua',
                'deskripsi' => 'Deskripsi kedua',
            ])
            ->assertSessionHasErrors('jadwal_id');

        $this->assertSame(1, PengajuanKonsultasi::count());
    }

    public function test_pengajuan_slot_can_be_rebooked_after_rejection()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $jadwal = $this->jadwal($dosen);

        PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'rejected',
            'alasan_penolakan' => 'Jadwal penuh.',
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('mahasiswa.pengajuan.store'), [
                'jadwal_id' => $jadwal->id,
                'topik' => 'Topik baru',
                'deskripsi' => 'Deskripsi baru',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, PengajuanKonsultasi::count());
    }

    public function test_pengajuan_for_slot_earlier_today_is_rejected()
    {
        $this->travelTo(now()->setTime(12, 0));
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $jadwal = $this->jadwal($dosen, [
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('mahasiswa.pengajuan.store'), [
                'jadwal_id' => $jadwal->id,
                'topik' => 'Topik',
                'deskripsi' => 'Deskripsi',
            ])
            ->assertSessionHasErrors('jadwal_id');

        $this->assertSame(0, PengajuanKonsultasi::count());
    }

    public function test_jadwal_with_pending_pengajuan_cannot_be_deleted()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $jadwal = $this->jadwal($dosen);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'pending',
        ]);

        $this->actingAs($dosen)
            ->delete(route('dosen.jadwal.destroy', $jadwal))
            ->assertSessionHasErrors('jadwal');

        $this->assertDatabaseHas('jadwal_konsultasi', ['id' => $jadwal->id]);
        $this->assertDatabaseHas('pengajuan_konsultasi', ['id' => $pengajuan->id]);
    }

    public function test_jadwal_with_completed_history_cannot_be_deleted()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $jadwal = $this->jadwal($dosen);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'completed',
        ]);

        $catatan = CatatanKonsultasi::factory()->create([
            'pengajuan_id' => $pengajuan->id,
            'dosen_id' => $dosen->id,
        ]);

        $this->actingAs($dosen)
            ->delete(route('dosen.jadwal.destroy', $jadwal))
            ->assertSessionHasErrors('jadwal');

        $this->assertDatabaseHas('jadwal_konsultasi', ['id' => $jadwal->id]);
        $this->assertDatabaseHas('pengajuan_konsultasi', ['id' => $pengajuan->id]);
        $this->assertDatabaseHas('catatan_konsultasi', ['id' => $catatan->id]);
    }

    public function test_jadwal_with_approved_pengajuan_cannot_be_deleted()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $jadwal = $this->jadwal($dosen);

        PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'approved',
        ]);

        $this->actingAs($dosen)
            ->delete(route('dosen.jadwal.destroy', $jadwal))
            ->assertSessionHasErrors('jadwal');

        $this->assertDatabaseHas('jadwal_konsultasi', ['id' => $jadwal->id]);
    }

    public function test_dosen_can_approve_pending_pengajuan_and_mahasiswa_is_notified()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $jadwal = $this->jadwal($dosen);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'pending',
        ]);

        $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.status', $pengajuan), ['status' => 'approved'])
            ->assertRedirect(route('dosen.riwayat.show', $pengajuan));

        $this->assertSame('approved', $pengajuan->refresh()->status);
        $this->assertSame(1, $mahasiswa->notifications()->count());
    }

    public function test_pengajuan_cannot_be_approved_when_jadwal_is_inactive()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $jadwal = $this->jadwal($dosen);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'pending',
        ]);

        $jadwal->update(['status' => 'inactive']);

        $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.status', $pengajuan), ['status' => 'approved'])
            ->assertSessionHasErrors('status');

        $this->assertSame('pending', $pengajuan->refresh()->status);
    }

    public function test_pengajuan_cannot_be_approved_when_jadwal_is_in_the_past()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $jadwal = $this->jadwal($dosen, [
            'tanggal' => now()->subDay()->toDateString(),
        ]);

        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'pending',
        ]);

        $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.status', $pengajuan), ['status' => 'approved'])
            ->assertSessionHasErrors('status');

        $this->assertSame('pending', $pengajuan->refresh()->status);
    }

    public function test_rejection_requires_a_reason()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'status' => 'pending',
        ]);

        $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.status', $pengajuan), ['status' => 'rejected'])
            ->assertSessionHasErrors('alasan_penolakan');

        $this->assertSame('pending', $pengajuan->refresh()->status);
    }

    public function test_rejection_with_reason_is_saved()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'status' => 'pending',
        ]);

        $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.status', $pengajuan), [
                'status' => 'rejected',
                'alasan_penolakan' => 'Jadwal bentrok dengan kegiatan lain.',
            ])
            ->assertSessionHasNoErrors();

        $pengajuan->refresh();

        $this->assertSame('rejected', $pengajuan->status);
        $this->assertSame('Jadwal bentrok dengan kegiatan lain.', $pengajuan->alasan_penolakan);
    }

    public function test_final_pengajuan_status_cannot_be_changed_again()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'status' => 'approved',
        ]);

        $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.status', $pengajuan), [
                'status' => 'rejected',
                'alasan_penolakan' => 'Berubah pikiran.',
            ])
            ->assertSessionHasErrors('status');

        $this->assertSame('approved', $pengajuan->refresh()->status);
    }

    public function test_second_pending_pengajuan_on_same_slot_cannot_be_approved()
    {
        $dosen = $this->dosen();
        $mahasiswaA = $this->mahasiswa($dosen, 'NIM001');
        $mahasiswaB = $this->mahasiswa($dosen, 'NIM002');
        $jadwal = $this->jadwal($dosen);

        $pertama = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswaA->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'pending',
        ]);

        $kedua = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswaB->id,
            'dosen_id' => $dosen->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'pending',
        ]);

        $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.status', $pertama), ['status' => 'approved'])
            ->assertSessionHasNoErrors();

        $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.status', $kedua), ['status' => 'approved'])
            ->assertSessionHasErrors('status');

        $this->assertSame('approved', $pertama->refresh()->status);
        $this->assertSame('pending', $kedua->refresh()->status);
    }

    public function test_catatan_requires_an_approved_pengajuan()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'status' => 'pending',
        ]);

        $this->actingAs($dosen)
            ->post(route('dosen.pengajuan.catatan.store', $pengajuan), [
                'ringkasan' => 'Ringkasan',
                'arahan' => 'Arahan',
            ])
            ->assertSessionHasErrors('status');

        $this->assertSame(0, CatatanKonsultasi::count());
        $this->assertSame('pending', $pengajuan->refresh()->status);
    }

    public function test_catatan_completes_pengajuan_and_cannot_be_duplicated()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'status' => 'approved',
        ]);

        $payload = [
            'ringkasan' => 'Membahas rencana studi.',
            'arahan' => 'Ambil mata kuliah sesuai semester.',
            'tindak_lanjut' => 'Cek KRS pekan depan.',
        ];

        $this->actingAs($dosen)
            ->post(route('dosen.pengajuan.catatan.store', $pengajuan), $payload)
            ->assertSessionHasNoErrors();

        $this->assertSame('completed', $pengajuan->refresh()->status);
        $this->assertSame(1, CatatanKonsultasi::count());

        $this->actingAs($dosen)
            ->post(route('dosen.pengajuan.catatan.store', $pengajuan), $payload)
            ->assertSessionHasErrors('status');

        $this->assertSame(1, CatatanKonsultasi::count());
    }

    public function test_dosen_can_update_own_catatan()
    {
        $dosen = $this->dosen();
        $mahasiswa = $this->mahasiswa($dosen);
        $pengajuan = PengajuanKonsultasi::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'status' => 'approved',
        ]);

        $catatan = CatatanKonsultasi::factory()->create([
            'pengajuan_id' => $pengajuan->id,
            'dosen_id' => $dosen->id,
        ]);

        $this->actingAs($dosen)
            ->put(route('dosen.pengajuan.catatan.update', [$pengajuan, $catatan]), [
                'ringkasan' => 'Ringkasan revisi',
                'arahan' => 'Arahan revisi',
                'tindak_lanjut' => null,
            ])
            ->assertRedirect(route('dosen.riwayat.show', $pengajuan));

        $this->assertSame('Ringkasan revisi', $catatan->refresh()->ringkasan);
    }
}
