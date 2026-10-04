<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'nama',
        'poin',
        'stok',
        'ket',
    ];

    public function penukaran()
    {
        return $this->hasMany(Penukaran::class);
    }
}
