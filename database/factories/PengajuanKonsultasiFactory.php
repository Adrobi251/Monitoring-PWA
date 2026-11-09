<?php

namespace Database\Factories;

use App\Models\JadwalKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengajuanKonsultasi>
 */
class PengajuanKonsultasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jadwal = JadwalKonsultasi::factory()->create();
        return [
            'mahasiswa_id' => User::factory(),
            'dosen_id' => $jadwal->dosen_id,
            'jadwal_id' => $jadwal->id,
            'topik' => $this->faker->word(),
            'deskripsi' => $this->faker->sentence(),
            'status' => 'pending',
            'alasan_penolakan' => null,
        ];
    }
}