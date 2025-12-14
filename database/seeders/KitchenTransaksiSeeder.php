<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kitchen;
use App\Models\KitchenMasuk;
use App\Models\KitchenKeluar;
use Carbon\Carbon;

class KitchenTransaksiSeeder extends Seeder
{
    public function run()
    {
        $userId = 1; // pastikan user id ini ada
        $startDate = Carbon::now()->subDays(29);

        foreach (Kitchen::all() as $kitchen) {

            for ($i = 0; $i < 30; $i++) {

                $tanggal = $startDate->copy()->addDays($i)->toDateString();

                // ====== BARANG MASUK ======
                $jumlahMasuk = rand(10, 50);

                $masuk = KitchenMasuk::create([
                    'kitchen_id' => $kitchen->id,
                    'user_id'    => $userId,
                    'tanggal'    => $tanggal,
                    'jumlah'     => $jumlahMasuk,
                    'sisa'       => $jumlahMasuk, // penting untuk FIFO
                ]);

                // ====== BARANG KELUAR ======
                $jumlahKeluar = rand(5, 20);

                KitchenKeluar::create([
                    'kitchen_id' => $kitchen->id,
                    'user_id'    => $userId,
                    'tanggal'    => $tanggal,
                    'jumlah'     => $jumlahKeluar,
                ]);

                // simulasi FIFO (kurangi sisa barang masuk)
                $masuk->update([
                    'sisa' => max(0, $jumlahMasuk - $jumlahKeluar)
                ]);
            }
        }
    }
}
