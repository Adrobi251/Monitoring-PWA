<?php

namespace Database\Factories;

use App\Models\JadwalKonsultasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JadwalKonsultasi>
 */
class JadwalKonsultasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dosen_id' => User::factory(),
            'tanggal' => $this->faker->date(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'lokasi' => 'Ruang Konsultasi',
            'status' => 'active',
        ];
    }
}
