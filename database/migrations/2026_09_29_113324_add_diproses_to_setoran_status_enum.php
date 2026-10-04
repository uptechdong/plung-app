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
        DB::statement("ALTER TABLE setoran MODIFY COLUMN status ENUM('diproses','disetujui','ditolak') NOT NULL DEFAULT 'diproses'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setoran_status_enum', function (Blueprint $table) {
        });
    }
};
