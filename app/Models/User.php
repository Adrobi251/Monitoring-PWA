<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'nim', 'nidn', 'dosen_wali_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function dosenWali()
    {
        return $this->belongsTo(User::class, 'dosen_wali_id');
    }

    public function mahasiswaBimbingan()
    {
        return $this->hasMany(User::class, 'dosen_wali_id');
    }

    public function jadwalKonsultasi()
    {
        return $this->hasMany(JadwalKonsultasi::class, 'dosen_id');
    }

    public function pengajuanSebagaiMahasiswa()
    {
        return $this->hasMany(PengajuanKonsultasi::class, 'mahasiswa_id');
    }

    public function pengajuanSebagaiDosen()
    {
        return $this->hasMany(PengajuanKonsultasi::class, 'dosen_id');
    }
}
