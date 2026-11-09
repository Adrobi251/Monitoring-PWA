<?php

namespace App\Notifications;

use App\Models\PengajuanKonsultasi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PengajuanBaruNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $pengajuan;

    public function __construct(PengajuanKonsultasi $pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'pengajuan_id' => $this->pengajuan->id,
            'mahasiswa_id' => $this->pengajuan->mahasiswa_id,
            'dosen_id' => $this->pengajuan->dosen_id,
            'jadwal_id' => $this->pengajuan->jadwal_id,
            'topik' => $this->pengajuan->topik,
            'status' => $this->pengajuan->status,
        ];
    }
}
