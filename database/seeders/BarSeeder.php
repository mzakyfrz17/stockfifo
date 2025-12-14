<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bar;

class BarSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['kd_bar' => 'BR001', 'nama' => 'Vodka', 'satuan' => 'Botol', 'stok_minimal' => 5],
            ['kd_bar' => 'BR002', 'nama' => 'Whiskey', 'satuan' => 'Botol', 'stok_minimal' => 5],
            ['kd_bar' => 'BR003', 'nama' => 'Rum', 'satuan' => 'Botol', 'stok_minimal' => 5],
            ['kd_bar' => 'BR004', 'nama' => 'Gin', 'satuan' => 'Botol', 'stok_minimal' => 5],
            ['kd_bar' => 'BR005', 'nama' => 'Tequila', 'satuan' => 'Botol', 'stok_minimal' => 5],
            ['kd_bar' => 'BR006', 'nama' => 'Beer', 'satuan' => 'Botol', 'stok_minimal' => 20],
            ['kd_bar' => 'BR007', 'nama' => 'Wine Merah', 'satuan' => 'Botol', 'stok_minimal' => 5],
            ['kd_bar' => 'BR008', 'nama' => 'Wine Putih', 'satuan' => 'Botol', 'stok_minimal' => 5],
            ['kd_bar' => 'BR009', 'nama' => 'Triple Sec', 'satuan' => 'Botol', 'stok_minimal' => 3],
            ['kd_bar' => 'BR010', 'nama' => 'Vermouth', 'satuan' => 'Botol', 'stok_minimal' => 3],
        ];

        Bar::insert($data);
    }
}
