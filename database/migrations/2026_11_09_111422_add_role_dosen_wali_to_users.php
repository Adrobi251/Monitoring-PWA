<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['mahasiswa', 'dosen'])->default('mahasiswa');
            $table->string('nim')->nullable();
            $table->string('nidn')->nullable();
            $table->foreignId('dosen_wali_id')->nullable()->after('nidn')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['dosen_wali_id']);
            $table->dropColumn(['role', 'nim', 'nidn', 'dosen_wali_id']);
        });
    }
};
