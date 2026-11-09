<?php

namespace Database\Factories;

use App\Models\CatatanKonsultasi;
use App\Models\PengajuanKonsultasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatatanKonsultasi>
 */
class CatatanKonsultasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pengajuan = PengajuanKonsultasi::factory()->create();
        return [
            'pengajuan_id' => $pengajuan->id,
            'dosen_id' => $pengajuan->dosen_id,
            'ringkasan' => $this->faker->sentence(),
            'arahan' => $this->faker->paragraph(),
            'tindak_lanjut' => $this->faker->paragraph(),
        ];
    }
}