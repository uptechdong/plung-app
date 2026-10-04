<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('nama')->after('id');
            $table->string('username')->unique()->after('nama');
            $table->string('rumah')->nullable()->after('username');
            $table->string('wa')->nullable()->after('rumah');
            $table->unsignedInteger('poin')->default(0)->after('password');
            $table->string('foto')->nullable()->after('poin');
            $table->enum('role', ['warga', 'pengurus'])->default('warga')->after('foto');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama', 'rumah', 'wa', 'poin', 'foto', 'role']);
        });
    }
};
