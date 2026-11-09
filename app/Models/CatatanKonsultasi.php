<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'catatan_konsultasi';

    protected $fillable = [
        'pengajuan_id',
        'dosen_id',
        'ringkasan',
        'arahan',
        'tindak_lanjut',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKonsultasi::class, 'pengajuan_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}
