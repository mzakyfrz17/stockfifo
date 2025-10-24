<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kitchen extends Model
{
    protected $table = 'kitchen';
    protected $fillable = ['kd_kitchen', 'nama', 'satuan', 'stok_minimal'];

    public function masuk()
    {
        return $this->hasMany(KitchenMasuk::class);
    }

    public function keluar()
    {
        return $this->hasMany(KitchenKeluar::class);
    }
}
