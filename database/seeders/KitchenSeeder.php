<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kitchen;

class KitchenSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['kd_kitchen' => 'KT001', 'nama' => 'Beras', 'satuan' => 'Kg', 'stok_minimal' => 20],
            ['kd_kitchen' => 'KT002', 'nama' => 'Gula', 'satuan' => 'Kg', 'stok_minimal' => 10],
            ['kd_kitchen' => 'KT003', 'nama' => 'Minyak Goreng', 'satuan' => 'Liter', 'stok_minimal' => 15],
            ['kd_kitchen' => 'KT004', 'nama' => 'Telur', 'satuan' => 'Butir', 'stok_minimal' => 100],
            ['kd_kitchen' => 'KT005', 'nama' => 'Tepung', 'satuan' => 'Kg', 'stok_minimal' => 10],
            ['kd_kitchen' => 'KT006', 'nama' => 'Garam', 'satuan' => 'Kg', 'stok_minimal' => 5],
            ['kd_kitchen' => 'KT007', 'nama' => 'Kecap', 'satuan' => 'Botol', 'stok_minimal' => 5],
            ['kd_kitchen' => 'KT008', 'nama' => 'Susu', 'satuan' => 'Liter', 'stok_minimal' => 10],
            ['kd_kitchen' => 'KT009', 'nama' => 'Mentega', 'satuan' => 'Kg', 'stok_minimal' => 5],
            ['kd_kitchen' => 'KT010', 'nama' => 'Keju', 'satuan' => 'Kg', 'stok_minimal' => 5],
        ];

        Kitchen::insert($data);
    }
}
