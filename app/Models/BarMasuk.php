<?php

// app/Models/RotiMasuk.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarMasuk extends Model
{
    protected $table = 'bar_masuk';
    protected $fillable = ['bar_id', 'user_id', 'tanggal', 'jumlah', 'sisa'];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
