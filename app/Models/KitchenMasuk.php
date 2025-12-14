<?php

// app/Models/RotiMasuk.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenMasuk extends Model
{
    protected $table = 'kitchen_masuk';
    protected $fillable = ['kitchen_id', 'user_id', 'tanggal', 'jumlah', 'sisa'];

    public function kitchen()
    {
        return $this->belongsTo(Kitchen::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
