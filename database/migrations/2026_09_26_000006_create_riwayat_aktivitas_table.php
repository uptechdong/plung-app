<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('jenis', ['setor_sampah', 'penukaran_hadiah']);
            $table->string('rincian');
            $table->enum('status', ['diproses', 'disetujui', 'ditolak', 'belum_diambil', 'sudah_diambil']);
            $table->integer('poin');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_aktivitas');
    }
};
