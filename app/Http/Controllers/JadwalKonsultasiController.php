<?php

namespace App\Http\Controllers;

use App\Models\JadwalKonsultasi;
use App\Notifications\JadwalDiperbaruiNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JadwalKonsultasiController extends Controller
{
    public function index()
    {
        $jadwal = Auth::user()->jadwalKonsultasi()
            ->latest()
            ->paginate(10);

        return view('dosen.jadwal', compact('jadwal'));
    }

    public function create()
    {
        return view('dosen.jadwal-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['dosen_id'] = Auth::id();

        if ($this->waktuSudahLewat($validated['tanggal'], $validated['jam_mulai'])) {
            return back()->withErrors(['tanggal' => 'Jadwal tidak boleh dimulai pada waktu yang sudah lewat.'])->withInput();
        }

        try {
            DB::transaction(function () use ($validated) {
                JadwalKonsultasi::where('dosen_id', Auth::id())
                    ->where('tanggal', $validated['tanggal'])
                    ->lockForUpdate()
                    ->get();

                if ($this->jadwalBentrok($validated)) {
                    throw new \RuntimeException('Jadwal bentrok dengan jadwal yang sudah ada.');
                }

                JadwalKonsultasi::create($validated);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['tanggal' => $e->getMessage()])->withInput();
        }

        return redirect()->route('dosen.jadwal')->with('success', 'Jadwal konsultasi berhasil ditambahkan.');
    }

    public function edit(JadwalKonsultasi $jadwal)
    {
        // Authorization: hanya dosen pemilik jadwal
        if ($jadwal->dosen_id !== Auth::id()) {
            abort(403);
        }

        // Cek apakah jadwal sudah dipakai pengajuan approved
        $sudahDipakai = $jadwal->pengajuan()->where('status', 'approved')->exists();
        
        return view('dosen.jadwal-edit', compact('jadwal', 'sudahDipakai'));
    }

    public function update(Request $request, JadwalKonsultasi $jadwal)
    {
        if ($jadwal->dosen_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Cek bentrok (exclude current jadwal)
        if ($this->waktuSudahLewat($validated['tanggal'], $validated['jam_mulai'])) {
            return back()->withErrors(['tanggal' => 'Jadwal tidak boleh dimulai pada waktu yang sudah lewat.'])->withInput();
        }

        if ($this->jadwalBentrok($validated, $jadwal->id)) {
            return back()->withErrors(['tanggal' => 'Jadwal bentrok dengan jadwal yang sudah ada.'])->withInput();
        }

        $jadwal->update($validated);

        $jadwal->pengajuan()
            ->where('status', 'approved')
            ->with('mahasiswa')
            ->get()
            ->each(fn ($pengajuan) => $pengajuan->mahasiswa?->notify(new JadwalDiperbaruiNotification($pengajuan)));

        return redirect()->route('dosen.jadwal')->with('success', 'Jadwal konsultasi berhasil diperbarui.');
    }

    public function destroy(JadwalKonsultasi $jadwal)
    {
        if ($jadwal->dosen_id !== Auth::id()) {
            abort(403);
        }

        // Jadwal yang sudah memiliki riwayat pengajuan tidak boleh dihapus
        // (PRD §8.11): utamakan penonaktifan melalui menu Edit.
        if ($jadwal->pengajuan()->exists()) {
            return back()->withErrors(['jadwal' => 'Jadwal tidak dapat dihapus karena sudah memiliki riwayat pengajuan. Nonaktifkan jadwal ini melalui menu Edit.']);
        }

        $jadwal->delete();

        return redirect()->route('dosen.jadwal')->with('success', 'Jadwal konsultasi berhasil dihapus.');
    }

    private function jadwalBentrok(array $validated, ?int $kecualiId = null): bool
    {
        return JadwalKonsultasi::where('dosen_id', Auth::id())
            ->where('tanggal', $validated['tanggal'])
            ->when($kecualiId, fn ($query) => $query->where('id', '!=', $kecualiId))
            ->where('status', 'active')
            ->where('jam_mulai', '<', $validated['jam_selesai'])
            ->where('jam_selesai', '>', $validated['jam_mulai'])
            ->exists();
    }

    private function waktuSudahLewat(string $tanggal, string $jamMulai): bool
    {
        return Carbon::parse($tanggal.' '.$jamMulai)->isPast();
    }
}