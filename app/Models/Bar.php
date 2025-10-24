<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bar extends Model
{
    protected $table = 'bar';
    protected $fillable = ['kd_bar', 'nama', 'satuan', 'stok_minimal'];

    public function masuk()
    {
        return $this->hasMany(BarMasuk::class);
    }

    public function keluar()
    {
        return $this->hasMany(BarKeluar::class);
    }
}
