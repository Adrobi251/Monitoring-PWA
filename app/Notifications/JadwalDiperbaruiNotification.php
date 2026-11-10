<?php

namespace App\Notifications;

use App\Models\PengajuanKonsultasi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class JadwalDiperbaruiNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PengajuanKonsultasi $pengajuan)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'pengajuan_id' => $this->pengajuan->id,
            'jadwal_id' => $this->pengajuan->jadwal_id,
            'topik' => 'Jadwal konsultasi diperbarui',
        ];
    }
}
