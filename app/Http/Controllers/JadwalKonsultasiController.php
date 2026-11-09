<?php

namespace App\Http\Controllers;

use App\Models\JadwalKonsultasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Cek bentrok jadwal
        $bentrok = JadwalKonsultasi::where('dosen_id', Auth::id())
            ->where('tanggal', $validated['tanggal'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                      ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                      });
            })
            ->where('status', 'active')
            ->exists();

        if ($bentrok) {
            return back()->withErrors(['tanggal' => 'Jadwal bentrok dengan jadwal yang sudah ada.'])->withInput();
        }

        JadwalKonsultasi::create($validated);

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
        $bentrok = JadwalKonsultasi::where('dosen_id', Auth::id())
            ->where('id', '!=', $jadwal->id)
            ->where('tanggal', $validated['tanggal'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                      ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                      });
            })
            ->where('status', 'active')
            ->exists();

        if ($bentrok) {
            return back()->withErrors(['tanggal' => 'Jadwal bentrok dengan jadwal yang sudah ada.'])->withInput();
        }

        $jadwal->update($validated);

        return redirect()->route('dosen.jadwal')->with('success', 'Jadwal konsultasi berhasil diperbarui.');
    }

    public function destroy(JadwalKonsultasi $jadwal)
    {
        if ($jadwal->dosen_id !== Auth::id()) {
            abort(403);
        }

        // Cek apakah jadwal sudah dipakai pengajuan approved
        $sudahDipakai = $jadwal->pengajuan()->where('status', 'approved')->exists();
        
        if ($sudahDipakai) {
            return back()->withErrors(['jadwal' => 'Jadwal tidak dapat dihapus karena sudah digunakan untuk konsultasi.']);
        }

        $jadwal->delete();

        return redirect()->route('dosen.jadwal')->with('success', 'Jadwal konsultasi berhasil dihapus.');
    }
}