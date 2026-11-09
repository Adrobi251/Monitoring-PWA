<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_konsultasi';

    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'jadwal_id',
        'topik',
        'deskripsi',
        'status',
        'alasan_penolakan',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalKonsultasi::class, 'jadwal_id');
    }

    public function catatan()
    {
        return $this->hasOne(CatatanKonsultasi::class, 'pengajuan_id');
    }
}
