<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKonsultasi;
use App\Models\JadwalKonsultasi;
use Illuminate\Support\Facades\Auth;

class MahasiswaDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dosenWali = $user->dosenWali;

        $stats = [
            'total_pengajuan' => $user->pengajuanSebagaiMahasiswa()->count(),
            'pending' => $user->pengajuanSebagaiMahasiswa()->where('status', 'pending')->count(),
            'approved' => $user->pengajuanSebagaiMahasiswa()->where('status', 'approved')->count(),
            'completed' => $user->pengajuanSebagaiMahasiswa()->where('status', 'completed')->count(),
        ];

        $jadwalTerdekat = $user->pengajuanSebagaiMahasiswa()
            ->where('status', 'approved')
            ->with('jadwal')
            ->orderBy('created_at', 'asc')
            ->first();

        $riwayatTerbaru = $user->pengajuanSebagaiMahasiswa()
            ->with('jadwal')
            ->latest()
            ->limit(5)
            ->get();

        return view('mahasiswa.dashboard', compact('user', 'dosenWali', 'stats', 'jadwalTerdekat', 'riwayatTerbaru'));
    }

    public function jadwal()
    {
        $jadwal = JadwalKonsultasi::where('dosen_id', Auth::user()->dosen_wali_id)
            ->where('status', 'active')
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('mahasiswa.jadwal', compact('jadwal'));
    }

    public function riwayat()
    {
        $riwayat = Auth::user()->pengajuanSebagaiMahasiswa()
            ->with(['jadwal', 'dosen'])
            ->latest()
            ->paginate(10);

        return view('mahasiswa.riwayat', compact('riwayat'));
    }
}
