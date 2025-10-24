<?php

// app/Models/RotiMasuk.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RotiMasuk extends Model
{
    protected $table = 'roti_masuk';
    protected $fillable = ['roti_id', 'tanggal', 'jumlah', 'sisa'];

    public function roti()
    {
        return $this->belongsTo(Roti::class);
    }
}
