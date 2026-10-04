<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penukaran extends Model
{
    protected $table = 'penukaran';

    protected $fillable = [
        'kode_kupon',
        'user_id',
        'reward_id',
        'poin',
        'tgl',
        'diambil',
    ];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
            'tgl' => 'date',
            'diambil' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }
}
