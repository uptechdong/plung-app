<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriSampah extends Model
{
    protected $table = 'kategori_sampah';

    protected $fillable = [
        'nama',
        'deskripsi',
        'warna',
        'poin_per_unit',
        'satuan',
    ];

    public function setoran()
    {
        return $this->hasMany(Setoran::class);
    }
}
