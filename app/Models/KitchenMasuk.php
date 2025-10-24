<?php

// app/Models/RotiMasuk.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenMasuk extends Model
{
    protected $table = 'kitchen_masuk';
    protected $fillable = ['kitchen_id', 'tanggal', 'jumlah', 'sisa'];

    public function kitchen()
    {
        return $this->belongsTo(Kitchen::class);
    }
}
