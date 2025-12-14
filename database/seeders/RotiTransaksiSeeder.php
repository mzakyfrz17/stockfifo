<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Roti;
use App\Models\RotiMasuk;
use App\Models\RotiKeluar;
use Carbon\Carbon;

class RotiTransaksiSeeder extends Seeder
{
    public function run()
    {
        $userId = 3; // pastikan user id ini ada
        $startDate = Carbon::now()->subDays(29);

        foreach (Roti::all() as $roti) {

            for ($i = 0; $i < 30; $i++) {

                $tanggal = $startDate->copy()->addDays($i)->toDateString();

                // =====================
                // ROTI MASUK
                // =====================
                $jumlahMasuk = rand(20, 50);

                $masuk = RotiMasuk::create([
                    'roti_id' => $roti->id,
                    'user_id' => $userId,
                    'tanggal' => $tanggal,
                    'jumlah'  => $jumlahMasuk,
                    'sisa'    => $jumlahMasuk, // FIFO
                ]);

                // =====================
                // ROTI KELUAR
                // =====================
                $jumlahKeluar = rand(10, 40);

                RotiKeluar::create([
                    'roti_id' => $roti->id,
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
