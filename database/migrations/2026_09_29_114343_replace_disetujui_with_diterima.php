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
        // Step 1: Tambahkan 'diterima' ke ENUM setoran (sementara ada 4 nilai)
        DB::statement("ALTER TABLE setoran MODIFY COLUMN status ENUM('diproses','disetujui','ditolak','diterima') NOT NULL DEFAULT 'diproses'");

        // Step 2: Update data existing dari 'disetujui' ke 'diterima'
        DB::table('setoran')->where('status', 'disetujui')->update(['status' => 'diterima']);

        // Step 3: Hapus 'disetujui' dari ENUM setoran
        DB::statement("ALTER TABLE setoran MODIFY COLUMN status ENUM('diproses','diterima','ditolak') NOT NULL DEFAULT 'diproses'");

        // Step 4: Tambahkan 'diterima' ke ENUM riwayat_aktivitas
        DB::statement("ALTER TABLE riwayat_aktivitas MODIFY COLUMN status ENUM('diproses','disetujui','ditolak','belum_diambil','sudah_diambil','belum_ditukar','sudah_ditukar','diterima') NOT NULL");

        // Step 5: Update data existing
        DB::table('riwayat_aktivitas')->where('status', 'disetujui')->update(['status' => 'diterima']);

        // Step 6: Hapus 'disetujui' dari ENUM riwayat_aktivitas
        DB::statement("ALTER TABLE riwayat_aktivitas MODIFY COLUMN status ENUM('diproses','diterima','ditolak','belum_diambil','sudah_diambil','belum_ditukar','sudah_ditukar') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
