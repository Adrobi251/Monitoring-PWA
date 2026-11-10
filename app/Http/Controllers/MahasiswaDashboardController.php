<?php

namespace App\Http\Controllers;

use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dosenWali = $user->dosenWali;

        $pengajuan = $user->pengajuanSebagaiMahasiswa();

        $stats = [
            'total_pengajuan' => (clone $pengajuan)->count(),
            'pending' => (clone $pengajuan)->where('status', 'pending')->count(),
            'approved' => (clone $pengajuan)->where('status', 'approved')->count(),
            'completed' => (clone $pengajuan)->where('status', 'completed')->count(),
        ];

        $jadwalTerdekat = $user->pengajuanSebagaiMahasiswa()
            ->where('status', 'approved')
            ->whereHas('jadwal', fn ($q) => $q->whereDate('tanggal', '>=', now()->toDateString()))
            ->with('jadwal')
            ->get()
            ->sortBy(fn ($item) => $item->jadwal->tanggal->format('Y-m-d').' '.$item->jadwal->jam_mulai)
            ->first();

        $riwayatTerbaru = $user->pengajuanSebagaiMahasiswa()
            ->with(['jadwal', 'dosen'])
            ->latest()
            ->limit(5)
            ->get();

        return view('mahasiswa.dashboard', compact('user', 'dosenWali', 'stats', 'jadwalTerdekat', 'riwayatTerbaru'));
    }

    public function dosenWali()
    {
        $user = Auth::user();
        $dosenWali = $user->dosenWali;

        $jadwal = $dosenWali
            ? JadwalKonsultasi::where('dosen_id', $dosenWali->id)
                ->where('status', 'active')
                ->whereDate('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal')
                ->orderBy('jam_mulai')
                ->get()
            : collect();

        $totalKonsultasi = $user->pengajuanSebagaiMahasiswa()
            ->whereIn('status', ['approved', 'completed'])
            ->count();

        return view('mahasiswa.dosen-wali', compact('user', 'dosenWali', 'jadwal', 'totalKonsultasi'));
    }

    public function jadwal()
    {
        $user = Auth::user();

        $jadwal = JadwalKonsultasi::where('dosen_id', $user->dosen_wali_id)
            ->where('status', 'active')
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->withCount(['pengajuan as terisi_count' => fn ($q) => $q->whereIn('status', ['pending', 'approved'])])
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        return view('mahasiswa.jadwal', compact('jadwal'));
    }

    public function riwayat(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:pending,approved,rejected,completed',
            'q' => 'nullable|string|max:100',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $riwayat = Auth::user()->pengajuanSebagaiMahasiswa()
            ->with(['jadwal', 'dosen'])
            ->when($validated['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($validated['q'] ?? null, fn ($q, $term) => $q->where('topik', 'like', '%'.$term.'%'))
            ->when($validated['from'] ?? null, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($validated['to'] ?? null, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('mahasiswa.riwayat', compact('riwayat'));
    }
}
