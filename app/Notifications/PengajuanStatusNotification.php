<?php

namespace App\Notifications;

use App\Models\PengajuanKonsultasi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PengajuanStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $pengajuan;
    public $status;
    public $alasan;

    public function __construct(PengajuanKonsultasi $pengajuan, $status, $alasan = null)
    {
        $this->pengajuan = $pengajuan;
        $this->status = $status;
        $this->alasan = $alasan;
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
            'status' => $this->status,
            'alasan' => $this->alasan,
        ];
    }
}
