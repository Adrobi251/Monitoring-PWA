<?php

namespace App\Http\Controllers;

use App\Models\CatatanKonsultasi;
use App\Models\PengajuanKonsultasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CatatanKonsultasiController extends Controller
{
    public function create(PengajuanKonsultasi $pengajuan)
    {
        if ($pengajuan->dosen_id !== Auth::id()) {
            abort(403);
        }

        if ($pengajuan->status !== 'approved') {
            return redirect()->route('dosen.riwayat.show', $pengajuan)
                ->withErrors(['status' => 'Catatan hanya dapat ditambahkan pada pengajuan yang sudah disetujui.']);
        }

        if ($pengajuan->catatan()->exists()) {
            return redirect()->route('dosen.riwayat.show', $pengajuan)
                ->withErrors(['status' => 'Pengajuan ini sudah memiliki catatan konsultasi.']);
        }

        return view('catatan.create', compact('pengajuan'));
    }

    public function store(Request $request, PengajuanKonsultasi $pengajuan)
    {
        if ($pengajuan->dosen_id !== Auth::id()) {
            abort(403);
        }

        if ($pengajuan->status !== 'approved') {
            return redirect()->route('dosen.riwayat.show', $pengajuan)
                ->withErrors(['status' => 'Catatan hanya dapat ditambahkan pada pengajuan yang sudah disetujui.']);
        }

        if ($pengajuan->catatan()->exists()) {
            return redirect()->route('dosen.riwayat.show', $pengajuan)
                ->withErrors(['status' => 'Pengajuan ini sudah memiliki catatan konsultasi.']);
        }

        $validated = $request->validate([
            'ringkasan' => ['required', 'string', 'max:255'],
            'arahan' => ['required', 'string', 'max:255'],
            'tindak_lanjut' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($pengajuan, $validated) {
                $locked = PengajuanKonsultasi::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();

                if ($locked->status !== 'approved' || $locked->catatan()->exists()) {
                    throw new \RuntimeException('Catatan hanya dapat dibuat pada pengajuan yang disetujui dan belum memiliki catatan.');
                }

                CatatanKonsultasi::create([
                    'pengajuan_id' => $locked->id,
                    'dosen_id' => $locked->dosen_id,
                    'ringkasan' => $validated['ringkasan'],
                    'arahan' => $validated['arahan'],
                    'tindak_lanjut' => $validated['tindak_lanjut'] ?? null,
                ]);

                $locked->update(['status' => 'completed']);
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('dosen.riwayat.show', $pengajuan)->withErrors(['status' => $e->getMessage()]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }

            return redirect()->route('dosen.riwayat.show', $pengajuan)->withErrors(['status' => 'Pengajuan ini sudah memiliki catatan konsultasi.']);
        }

        $pengajuan->mahasiswa->notify(new \App\Notifications\PengajuanStatusNotification($pengajuan, 'completed'));

        return redirect()->route('dosen.riwayat.show', $pengajuan)->with('success', 'Catatan konsultasi berhasil disimpan dan status konsultasi menjadi selesai.');
    }

    public function edit(PengajuanKonsultasi $pengajuan, CatatanKonsultasi $catatan)
    {
        if ($catatan->dosen_id !== Auth::id() || $catatan->pengajuan_id !== $pengajuan->id) {
            abort(403);
        }

        return view('catatan.edit', compact('pengajuan', 'catatan'));
    }

    public function update(Request $request, PengajuanKonsultasi $pengajuan, CatatanKonsultasi $catatan)
    {
        if ($catatan->dosen_id !== Auth::id() || $catatan->pengajuan_id !== $pengajuan->id) {
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

        if (! $catatan) {
            abort(404);
        }

        return view('catatan.show', compact('catatan'));
    }
}
