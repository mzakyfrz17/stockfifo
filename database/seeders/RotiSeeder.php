<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Roti;

class RotiSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['kd_roti' => 'RT001', 'nama' => 'Roti Tawar', 'satuan' => 'Pcs', 'stok_minimal' => 20],
            ['kd_roti' => 'RT002', 'nama' => 'Roti Manis', 'satuan' => 'Pcs', 'stok_minimal' => 20],
            ['kd_roti' => 'RT003', 'nama' => 'Roti Coklat', 'satuan' => 'Pcs', 'stok_minimal' => 15],
            ['kd_roti' => 'RT004', 'nama' => 'Roti Keju', 'satuan' => 'Pcs', 'stok_minimal' => 15],
            ['kd_roti' => 'RT005', 'nama' => 'Roti Gandum', 'satuan' => 'Pcs', 'stok_minimal' => 10],
            ['kd_roti' => 'RT006', 'nama' => 'Croissant', 'satuan' => 'Pcs', 'stok_minimal' => 10],
            ['kd_roti' => 'RT007', 'nama' => 'Donat', 'satuan' => 'Pcs', 'stok_minimal' => 25],
            ['kd_roti' => 'RT008', 'nama' => 'Roti Sosis', 'satuan' => 'Pcs', 'stok_minimal' => 15],
            ['kd_roti' => 'RT009', 'nama' => 'Roti Pisang', 'satuan' => 'Pcs', 'stok_minimal' => 15],
            ['kd_roti' => 'RT010', 'nama' => 'Roti Kismis', 'satuan' => 'Pcs', 'stok_minimal' => 10],
        ];

        Roti::insert($data);
    }
}
