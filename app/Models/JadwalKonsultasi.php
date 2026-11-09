<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'jadwal_konsultasi';

    protected $fillable = [
        'dosen_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function pengajuan()
    {
        return $this->hasMany(PengajuanKonsultasi::class, 'jadwal_id');
    }
}
