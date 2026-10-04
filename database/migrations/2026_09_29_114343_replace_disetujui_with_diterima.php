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
        DB::statement("ALTER TABLE setoran MODIFY COLUMN status ENUM('diproses','disetujui','ditolak','diterima') NOT NULL DEFAULT 'diproses'");

        DB::table('setoran')->where('status', 'disetujui')->update(['status' => 'diterima']);

        DB::statement("ALTER TABLE setoran MODIFY COLUMN status ENUM('diproses','diterima','ditolak') NOT NULL DEFAULT 'diproses'");

        DB::statement("ALTER TABLE riwayat_aktivitas MODIFY COLUMN status ENUM('diproses','disetujui','ditolak','belum_diambil','sudah_diambil','belum_ditukar','sudah_ditukar','diterima') NOT NULL");

        DB::table('riwayat_aktivitas')->where('status', 'disetujui')->update(['status' => 'diterima']);

        DB::statement("ALTER TABLE riwayat_aktivitas MODIFY COLUMN status ENUM('diproses','diterima','ditolak','belum_diambil','sudah_diambil','belum_ditukar','sudah_ditukar') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
