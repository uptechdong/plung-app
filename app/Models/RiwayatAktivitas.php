<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatAktivitas extends Model
{
    protected $table = 'riwayat_aktivitas';

    protected $fillable = [
        'user_id',
        'jenis',
        'rincian',
        'status',
        'poin',
        'setoran_id',
        'penukaran_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setoran()
    {
        return $this->belongsTo(Setoran::class);
    }

    public function penukaran()
    {
        return $this->belongsTo(Penukaran::class);
    }
}
