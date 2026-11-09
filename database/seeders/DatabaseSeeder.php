<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create a dosen
        $dosen = User::create([
            'name' => 'Dr. Dosen Wali',
            'email' => 'dosen@test.com',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'nidn' => 'D001',
        ]);

        // Create a mahasiswa linked to dosen
        User::create([
            'name' => 'Test Mahasiswa',
            'email' => 'mahasiswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'nim' => '123456789',
            'dosen_wali_id' => $dosen->id,
        ]);
    }
}
