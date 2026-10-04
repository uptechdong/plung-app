<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    protected $table = 'setoran';

    protected $fillable = [
        'user_id',
        'kategori_id',
        'berat',
        'satuan',
        'tgl',
        'catatan',
        'foto',
        'status',
        'poin',
    ];

    protected function casts(): array
    {
        return [
            'tgl' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }

    /**
     * Urutkan transaksi dari yang paling baru (atas) ke paling lama (bawah).
     *
     * `tgl` hanya menyimpan tanggal (tanpa jam), jadi perlu diurutkan juga
     * berdasarkan `created_at` agar transaksi di hari yang sama tetap terurut
     * dari yang paling baru terkirim.
     */
    public function scopeTerbaru(Builder $query): Builder
    {
        return $query
            ->orderByDesc('tgl')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }
}
