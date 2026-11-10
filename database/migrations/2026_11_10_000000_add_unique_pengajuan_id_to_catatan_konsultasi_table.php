<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catatan_konsultasi', function (Blueprint $table) {
            $table->unique('pengajuan_id');
        });
    }

    public function down(): void
    {
        Schema::table('catatan_konsultasi', function (Blueprint $table) {
            $table->dropUnique(['pengajuan_id']);
        });
    }
};
