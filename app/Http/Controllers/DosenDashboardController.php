<?php

namespace App\Http\Controllers;

use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $pengajuan = $user->pengajuanSebagaiDosen();

        $stats = [
            'total_mahasiswa' => $user->mahasiswaBimbingan()->where('role', 'mahasiswa')->count(),
            'total_pengajuan' => (clone $pengajuan)->count(),
            'pending' => (clone $pengajuan)->where('status', 'pending')->count(),
            'approved' => (clone $pengajuan)->where('status', 'approved')->count(),
            'completed' => (clone $pengajuan)->where('status', 'completed')->count(),
        ];

        $jadwalMendatang = $user->jadwalKonsultasi()
            ->where('status', 'active')
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->withCount(['pengajuan as terisi_count' => fn ($q) => $q->whereIn('status', ['pending', 'approved'])])
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->limit(5)
            ->get();

        $riwayatTerbaru = $user->pengajuanSebagaiDosen()
            ->with(['mahasiswa', 'jadwal'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dosen.dashboard', compact('user', 'stats', 'jadwalMendatang', 'riwayatTerbaru'));
    }

    public function jadwal(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:active,inactive',
        ]);

        $jadwal = Auth::user()->jadwalKonsultasi()
            ->when($validated['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->withCount(['pengajuan as terisi_count' => fn ($q) => $q->whereIn('status', ['pending', 'approved'])])
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_mulai')
            ->paginate(10)
            ->withQueryString();

        return view('dosen.jadwal', compact('jadwal'));
    }

    public function riwayat(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:pending,approved,rejected,completed',
            'q' => 'nullable|string|max:100',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $riwayat = Auth::user()->pengajuanSebagaiDosen()
            ->with(['mahasiswa', 'jadwal'])
            ->when($validated['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($validated['q'] ?? null, function ($q, $term) {
                $q->where(function ($query) use ($term) {
                    $query->where('topik', 'like', '%'.$term.'%')
                        ->orWhereHas('mahasiswa', fn ($m) => $m->where('name', 'like', '%'.$term.'%')
                            ->orWhere('nim', 'like', '%'.$term.'%'));
                });
            })
            ->when($validated['from'] ?? null, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($validated['to'] ?? null, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dosen.riwayat', compact('riwayat'));
    }

    public function riwayatShow(PengajuanKonsultasi $pengajuan)
    {
        if ($pengajuan->dosen_id !== Auth::id()) {
            abort(403);
        }

        $pengajuan->load(['mahasiswa', 'jadwal', 'catatan']);

        return view('dosen.riwayat-show', compact('pengajuan'));
    }

    public function mahasiswa(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
        ]);

        $mahasiswa = Auth::user()->mahasiswaBimbingan()
            ->where('role', 'mahasiswa')
            ->when($validated['q'] ?? null, function ($q, $term) {
                $q->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%'.$term.'%')
                        ->orWhere('nim', 'like', '%'.$term.'%')
                        ->orWhere('email', 'like', '%'.$term.'%');
                });
            })
            ->withCount(['pengajuanSebagaiMahasiswa as pengajuan_count'])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('dosen.mahasiswa.index', compact('mahasiswa'));
    }

    public function mahasiswaShow(User $mahasiswa)
    {
        if ($mahasiswa->dosen_wali_id !== Auth::id()) {
            abort(403);
        }

        $pengajuan = $mahasiswa->pengajuanSebagaiMahasiswa()
            ->where('dosen_id', Auth::id())
            ->with('jadwal')
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => $mahasiswa->pengajuanSebagaiMahasiswa()->where('dosen_id', Auth::id())->count(),
            'pending' => $mahasiswa->pengajuanSebagaiMahasiswa()->where('dosen_id', Auth::id())->where('status', 'pending')->count(),
            'completed' => $mahasiswa->pengajuanSebagaiMahasiswa()->where('dosen_id', Auth::id())->where('status', 'completed')->count(),
        ];

        return view('dosen.mahasiswa.show', compact('mahasiswa', 'pengajuan', 'stats'));
    }

    public function assignMahasiswa(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:20',
        ]);

        $mahasiswa = User::where('role', 'mahasiswa')->where('nim', $validated['nim'])->first();

        if (! $mahasiswa) {
            return back()->withErrors(['nim' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.']);
        }

        if ($mahasiswa->dosen_wali_id !== null) {
            return back()->withErrors(['nim' => 'Mahasiswa ini sudah memiliki dosen wali.']);
        }

        $mahasiswa->update(['dosen_wali_id' => Auth::id()]);

        return back()->with('success', 'Mahasiswa berhasil ditugaskan ke Anda.');
    }
}
