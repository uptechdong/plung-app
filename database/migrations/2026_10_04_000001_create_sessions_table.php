<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini dibuat MANUAL di database (lewat phpMyAdmin atau
     * `php artisan session:table` versi lama), sehingga file migration-nya
     * tidak pernah ikut tersimpan. Akibatnya `php artisan migrate:fresh`
     * menjatuhkan tabel `sessions` dan aplikasi langsung rusak, karena
     * .env memakai SESSION_DRIVER=database.
     *
     * File ini dibuat ulang secara manual -- bukan lewat `php artisan
     * session:table` -- karena perintah itu salah mendeteksi: ia menganggap
     * migration `0001_01_01_000000_create_users_table.php` sudah membuat
     * tabel sessions. Padahal di Laravel 11+ file tersebut tidak menyentuh
     * tabel sessions sama sekali.
     *
     * Karena tabelnya sudah ada di database, `up()` mengecek keberadaan
     * tabel lebih dulu supaya migration ini aman dijalankan di mesin yang
     * tabelnya sudah ada maupun yang belum sama sekali.
     */
    public function up(): void
    {
        if (Schema::hasTable('sessions')) {
            return;
        }

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};