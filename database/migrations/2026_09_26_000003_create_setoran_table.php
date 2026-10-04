<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategori_sampah')->onDelete('cascade');
            $table->decimal('berat', 8, 2);
            $table->string('satuan');
            $table->date('tgl');
            $table->text('catatan')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->unsignedInteger('poin');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran');
    }
};
