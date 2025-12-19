<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Roti;
use App\Models\User;

class RotiKeluar extends Model
{
    protected $table = 'roti_keluar';

    protected $fillable = [
        'roti_id',
        'user_id',
        'tanggal',
        'jumlah'
    ];

    /**
     * Relasi ke tabel roti
     */
    public function roti()
    {
        return $this->belongsTo(Roti::class, 'roti_id');
    }

    /**
     * Relasi ke tabel users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
