<?php

namespace App\Http\Controllers;

use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanKonsultasiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_id' => 'required|exists:jadwal_konsultasi,id',
            'topik' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        $jadwal = JadwalKonsultasi::findOrFail($validated['jadwal_id']);

        // Validasi: dosen wali yang sama dan status active
        if ($jadwal->dosen_id !== Auth::user()->dosen_wali_id) {
            return back()->withErrors(['jadwal_id' => 'Jadwal tidak ditemukan atau bukan milik dosen wali Anda.']);
        }

        if ($jadwal->status !== 'active') {
            return back()->withErrors(['jadwal_id' => 'Jadwal tidak aktif.']);
        }

        // Validasi: mahasiswa tidak bisa submit jika sudah ada pengajuan pending/approved untuk jadwal yang sama
        $sudahAda = PengajuanKonsultasi::where('mahasiswa_id', Auth::id())
            ->where('jadwal_id', $jadwal->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($sudahAda) {
            return back()->withErrors(['jadwal_id' => 'Anda sudah mengajukan konsultasi untuk jadwal ini.']);
        }

        // Validasi: jadwal belum di masa lalu
        if ($jadwal->tanggal < now()->toDateString()) {
            return back()->withErrors(['jadwal_id' => 'Jadwal tidak boleh menggunakan waktu yang sudah berlalu.']);
        }

        $pengajuan = PengajuanKonsultasi::create([
            'mahasiswa_id' => Auth::id(),
            'dosen_id' => $jadwal->dosen_id,
            'jadwal_id' => $jadwal->id,
            'topik' => $validated['topik'],
            'deskripsi' => $validated['deskripsi'],
            'status' => 'pending',
        ]);

        // Kirim notifikasi ke dosen wali
        $dosen = User::find($jadwal->dosen_id);
        if ($dosen) {
            $dosen->notify(new \App\Notifications\PengajuanBaruNotification($pengajuan));
        }

        return redirect()->route('mahasiswa.jadwal')->with('success', 'Pengajuan konsultasi berhasil dikirim.');
    }

    public function riwayat()
    {
        $riwayat = Auth::user()->pengajuanSebagaiMahasiswa()
            ->with(['jadwal', 'dosen'])
            ->latest()
            ->paginate(10);

        return view('mahasiswa.riwayat', compact('riwayat'));
    }

    public function updateStatus(Request $request, PengajuanKonsultasi $pengajuan)
    {
        // Authorization: dosen pemanggil harus sama dengan dosen pengajuan
        if ($pengajuan->dosen_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'alasan_penolakan' => 'nullable|string|required_if:status,rejected',
        ]);

        if ($validated['status'] === 'rejected' && empty($validated['alasan_penolakan'])) {
            return back()->withErrors(['alasan_penolakan' => 'Alasan penolakan wajib diisi.']);
        }

        try {
            DB::transaction(function () use ($pengajuan, $validated) {
                // Lock the pengajuan record to prevent concurrent updates
                $locked = PengajuanKonsultasi::where('id', $pengajuan->id)->lockForUpdate()->firstOrFail();

                if ($validated['status'] === 'approved') {
                    // Ensure no other approved pengajuan exists for the same jadwal
                    $existingApproved = PengajuanKonsultasi::where('jadwal_id', $locked->jadwal_id)
                        ->where('status', 'approved')
                        ->where('id', '!=', $locked->id)
                        ->lockForUpdate()
                        ->exists();

                    if ($existingApproved) {
                        throw new \RuntimeException('Jadwal ini sudah disetujui untuk mahasiswa lain.');
                    }
                }

                $locked->update([
                    'status' => $validated['status'],
                    'alasan_penolakan' => $validated['status'] === 'rejected' ? $validated['alasan_penolakan'] : null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        // Notify mahasiswa
        $pengajuan->mahasiswa->notify(new \App\Notifications\PengajuanStatusNotification($pengajuan, $validated['status'], $validated['alasan_penolakan'] ?? null));

        return redirect()->route('dosen.riwayat')->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
