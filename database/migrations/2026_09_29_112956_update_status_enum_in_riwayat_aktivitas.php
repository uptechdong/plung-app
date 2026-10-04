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
        DB::statement("ALTER TABLE riwayat_aktivitas MODIFY COLUMN status ENUM('diproses','disetujui','ditolak','belum_diambil','sudah_diambil','belum_ditukar','sudah_ditukar') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_aktivitas', function (Blueprint $table) {
            //
        });
    }
};
