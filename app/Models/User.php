<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'nama',
        'rumah',
        'wa',
        'username',
        'password',
        'poin',
        'foto',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function setoran()
    {
        return $this->hasMany(Setoran::class);
    }

    public function penukaran()
    {
        return $this->hasMany(Penukaran::class);
    }

    public function riwayatAktivitas()
    {
        return $this->hasMany(RiwayatAktivitas::class);
    }

    public function isPengurus(): bool
    {
        return $this->role === 'pengurus';
    }

    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }
}
