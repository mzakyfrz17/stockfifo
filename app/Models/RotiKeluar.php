<?php
// app/Models/RotiKeluar.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RotiKeluar extends Model
{
    protected $table = 'roti_keluar';
    protected $fillable = ['roti_id', 'tanggal', 'jumlah'];

    public function roti()
    {
        return $this->belongsTo(Roti::class);
    }
}
