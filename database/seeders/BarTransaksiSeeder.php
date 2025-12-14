<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bar;
use App\Models\BarMasuk;
use App\Models\BarKeluar;
use Carbon\Carbon;

class BarTransaksiSeeder extends Seeder
{
    public function run()
    {
        $userId = 2; // pastikan user id 1 ada
        $startDate = Carbon::now()->subDays(29);

        foreach (Bar::all() as $bar) {

            for ($i = 0; $i < 30; $i++) {

                $tanggal = $startDate->copy()->addDays($i)->toDateString();

                // =====================
                // BARANG MASUK
                // =====================
                $jumlahMasuk = rand(5, 20);

                $masuk = BarMasuk::create([
                    'bar_id'  => $bar->id,
                    'user_id' => $userId,
                    'tanggal' => $tanggal,
                    'jumlah'  => $jumlahMasuk,
                    'sisa'    => $jumlahMasuk, // FIFO
                ]);

                // =====================
                // BARANG KELUAR
                // =====================
                $jumlahKeluar = rand(1, 10);

                BarKeluar::create([
                    'bar_id'  => $bar->id,
                    'user_id' => $userId,
                    'tanggal' => $tanggal,
                    'jumlah'  => $jumlahKeluar,
                ]);

                // simulasi FIFO sederhana
                $masuk->update([
                    'sisa' => max(0, $jumlahMasuk - $jumlahKeluar)
                ]);
            }
        }
    }
}
