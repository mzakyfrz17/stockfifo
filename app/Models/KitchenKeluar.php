<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenKeluar extends Model
{
    protected $table = 'kitchen_keluar';
    protected $fillable = ['kitchen_id', 'user_id', 'tanggal', 'jumlah'];

    public function kitchen()
    {
        return $this->belongsTo(Kitchen::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
