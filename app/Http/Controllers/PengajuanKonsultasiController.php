<?php

namespace App\Http\Controllers;

use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanKonsultasiController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();

        $jadwal = JadwalKonsultasi::where('dosen_id', $user->dosen_wali_id)
            ->where('status', 'active')
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->withCount(['pengajuan as terisi_count' => fn ($q) => $q->whereIn('status', ['pending', 'approved'])])
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get()
            ->filter(fn ($item) => $item->terisi_count === 0)
            ->filter(fn ($item) => Carbon::parse($item->tanggal->toDateString().' '.$item->jam_mulai)->isFuture())
            ->values();

        $selectedJadwal = $request->integer('jadwal');

        return view('mahasiswa.pengajuan-create', compact('jadwal', 'selectedJadwal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_id' => 'required|exists:jadwal_konsultasi,id',
            'topik' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:2000',
        ]);

        $jadwal = JadwalKonsultasi::findOrFail($validated['jadwal_id']);

        if ($jadwal->dosen_id !== Auth::user()->dosen_wali_id) {
            return back()->withErrors(['jadwal_id' => 'Jadwal tidak ditemukan atau bukan milik dosen wali Anda.'])->withInput();
        }

        if ($jadwal->status !== 'active') {
            return back()->withErrors(['jadwal_id' => 'Jadwal tidak aktif.'])->withInput();
        }

        if (Carbon::parse($jadwal->tanggal->toDateString().' '.$jadwal->jam_mulai)->isPast()) {
            return back()->withErrors(['jadwal_id' => 'Jadwal tidak boleh menggunakan waktu yang sudah berlalu.'])->withInput();
        }

        try {
            $pengajuan = DB::transaction(function () use ($jadwal, $validated) {
                // Lock the slot so two students cannot book it at the same time.
                JadwalKonsultasi::whereKey($jadwal->id)->lockForUpdate()->first();

                $sudahAda = PengajuanKonsultasi::where('jadwal_id', $jadwal->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->exists();

                if ($sudahAda) {
                    throw new \RuntimeException('Jadwal ini sudah diajukan atau disetujui untuk konsultasi lain.');
                }

                return PengajuanKonsultasi::create([
                    'mahasiswa_id' => Auth::id(),
                    'dosen_id' => $jadwal->dosen_id,
                    'jadwal_id' => $jadwal->id,
                    'topik' => $validated['topik'],
                    'deskripsi' => $validated['deskripsi'],
                    'status' => 'pending',
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['jadwal_id' => $e->getMessage()])->withInput();
        }

        $dosen = User::find($jadwal->dosen_id);
        if ($dosen) {
            $dosen->notify(new \App\Notifications\PengajuanBaruNotification($pengajuan));
        }

        return redirect()->route('mahasiswa.pengajuan.show', $pengajuan)
            ->with('success', 'Pengajuan konsultasi berhasil dikirim.');
    }

    public function show(PengajuanKonsultasi $pengajuan)
    {
        if ($pengajuan->mahasiswa_id !== Auth::id()) {
            abort(403);
        }

        $pengajuan->load(['jadwal', 'dosen', 'catatan']);

        return view('mahasiswa.pengajuan-show', compact('pengajuan'));
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
        if ($pengajuan->dosen_id !== Auth::id()) {
            abort(403);
        }

        if ($pengajuan->status !== 'pending') {
            return back()->withErrors(['status' => 'Hanya pengajuan berstatus menunggu yang dapat diputuskan.']);
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
                $locked = PengajuanKonsultasi::where('id', $pengajuan->id)->lockForUpdate()->firstOrFail();

                if ($locked->status !== 'pending') {
                    throw new \RuntimeException('Hanya pengajuan berstatus menunggu yang dapat diputuskan.');
                }

                if ($validated['status'] === 'approved') {
                    $jadwal = JadwalKonsultasi::whereKey($locked->jadwal_id)->lockForUpdate()->first();

                    if (! $jadwal || $jadwal->status !== 'active') {
                        throw new \RuntimeException('Jadwal konsultasi sudah tidak aktif.');
                    }

                    if (Carbon::parse($jadwal->tanggal->toDateString().' '.$jadwal->jam_mulai)->isPast()) {
                        throw new \RuntimeException('Jadwal konsultasi sudah berlalu.');
                    }

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

        $pengajuan->mahasiswa->notify(new \App\Notifications\PengajuanStatusNotification($pengajuan, $validated['status'], $validated['alasan_penolakan'] ?? null));

        return redirect()->route('dosen.riwayat.show', $pengajuan)->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
