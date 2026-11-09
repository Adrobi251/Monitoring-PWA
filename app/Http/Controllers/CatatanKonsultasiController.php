<?php

namespace App\Http\Controllers;

use App\Models\CatatanKonsultasi;
use App\Models\PengajuanKonsultasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatatanKonsultasiController extends Controller
{
    public function create(PengajuanKonsultasi $pengajuan)
    {
        if ($pengajuan->dosen_id !== Auth::id()) {
            abort(403);
        }

        return view('catatan.create', compact('pengajuan'));
    }

    public function store(Request $request, PengajuanKonsultasi $pengajuan)
    {
        if ($pengajuan->dosen_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'ringkasan' => ['required', 'string', 'max:255'],
            'arahan' => ['required', 'string', 'max:255'],
            'tindak_lanjut' => ['nullable', 'string', 'max:255'],
        ]);

        CatatanKonsultasi::create([
            'pengajuan_id' => $pengajuan->id,
            'dosen_id' => $pengajuan->dosen_id,
            'ringkasan' => $validated['ringkasan'],
            'arahan' => $validated['arahan'],
            'tindak_lanjut' => $validated['tindak_lanjut'] ?? null,
        ]);

        return redirect()->route('dosen.riwayat.show', $pengajuan)->with('success', 'Catatan konsultasi berhasil disimpan.');
    }

    public function edit(CatatanKonsultasi $catatan)
    {
        if ($catatan->dosen_id !== Auth::id()) {
            abort(403);
        }

        return view('catatan.edit', compact('catatan'));
    }

    public function update(Request $request, CatatanKonsultasi $catatan)
    {
        if ($catatan->dosen_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'ringkasan' => ['required', 'string', 'max:255'],
            'arahan' => ['required', 'string', 'max:255'],
            'tindak_lanjut' => ['nullable', 'string', 'max:255'],
        ]);

        $catatan->update($validated);

        return redirect()->route('dosen.riwayat.show', $catatan->pengajuan)->with('success', 'Catatan konsultasi berhasil diperbarui.');
    }

    public function show(PengajuanKonsultasi $pengajuan)
    {
        // Authorization
        if (Auth::user()->role === 'mahasiswa') {
            if ($pengajuan->mahasiswa_id !== Auth::id()) {
                abort(403);
            }
        } elseif (Auth::user()->role === 'dosen') {
            if ($pengajuan->dosen_id !== Auth::id()) {
                abort(403);
            }
        }

        $catatan = CatatanKonsultasi::where('pengajuan_id', $pengajuan->id)->first();

        if (!$catatan) {
            abort(404);
        }

        return view('catatan.show', compact('catatan'));
    }
}
