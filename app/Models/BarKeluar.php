<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarKeluar extends Model
{
    protected $table = 'bar_keluar';
    protected $fillable = ['bar_id', 'tanggal', 'jumlah'];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }
}
