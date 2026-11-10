<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function read(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $pengajuanId = $notification->data['pengajuan_id'] ?? null;

        if ($pengajuanId) {
            return Auth::user()->role === 'dosen'
                ? redirect()->route('dosen.riwayat.show', $pengajuanId)
                : redirect()->route('mahasiswa.pengajuan.show', $pengajuanId);
        }

        return redirect()->route('notifications.index');
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
