<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roti extends Model
{
    protected $table = 'roti';
    protected $fillable = ['kd_roti', 'nama', 'satuan', 'stok_minimal'];

    public function masuk()
    {
        return $this->hasMany(RotiMasuk::class);
    }

    public function keluar()
    {
        return $this->hasMany(RotiKeluar::class);
    }
}
