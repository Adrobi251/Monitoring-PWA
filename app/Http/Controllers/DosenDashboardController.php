<?php

namespace App\Http\Controllers;

use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenDashboardController extends Controller
{
    // ... existing methods ...

    public function assignMahasiswa(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:20',
        ]);

        $mahasiswa = User::where('role', 'mahasiswa')->where('nim', $validated['nim'])->first();

        if (!$mahasiswa) {
            return back()->withErrors(['nim' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.']);
        }

        if ($mahasiswa->dosen_wali_id !== null) {
            return back()->withErrors(['nim' => 'Mahasiswa ini sudah memiliki dosen wali.']);
        }

        $mahasiswa->update(['dosen_wali_id' => Auth::id()]);

        return back()->with('success', 'Mahasiswa berhasil ditugaskan ke Anda.');
    }
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_mahasiswa' => $user->mahasiswaBimbingan()->count(),
            'total_pengajuan' => $user->pengajuanSebagaiDosen()->count(),
            'pending' => $user->pengajuanSebagaiDosen()->where('status', 'pending')->count(),
            'approved' => $user->pengajuanSebagaiDosen()->where('status', 'approved')->count(),
        ];

        $jadwalMendatang = $user->jadwalKonsultasi()
            ->where('status', 'active')
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->first();

        $riwayatTerbaru = $user->pengajuanSebagaiDosen()
            ->with(['mahasiswa', 'jadwal'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dosen.dashboard', compact('user', 'stats', 'jadwalMendatang', 'riwayatTerbaru'));
    }

    public function jadwal()
    {
        $jadwal = Auth::user()->jadwalKonsultasi()
            ->latest()
            ->paginate(10);

        return view('dosen.jadwal', compact('jadwal'));
    }

    public function riwayat()
    {
        $riwayat = Auth::user()->pengajuanSebagaiDosen()
            ->with(['mahasiswa', 'jadwal'])
            ->latest()
            ->paginate(10);

        return view('dosen.riwayat', compact('riwayat'));
    }

    public function riwayatShow(PengajuanKonsultasi $pengajuan)
    {
        if ($pengajuan->dosen_id !== Auth::id()) {
            abort(403);
        }

        $catatan = $pengajuan->catatan;
        return view('dosen.riwayat-show', compact('pengajuan', 'catatan'));
    }
}
