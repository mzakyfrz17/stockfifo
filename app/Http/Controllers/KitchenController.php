<?php

namespace App\Http\Controllers;

use App\Models\Kitchen;
use App\Models\KitchenMasuk;
use App\Models\KitchenKeluar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KitchenController extends Controller
{
    // =====================
    // MASTER DATA Kitchen
    // =====================

    public function index()
    {
        $kitchen = Kitchen::all();
        return view('kitchen.index', compact('kitchen'));
    }

    public function storeKitchen(Request $request)
    {
        $request->validate([
            'kd_kitchen'    => 'required|unique:kitchen,kd_kitchen',
            'nama'          => 'required',
            'satuan'        => 'required',
            'stok_minimal'  => 'required|integer',
        ]);

        Kitchen::create($request->all());
        return redirect()->route('kitchen.index')->with('success', 'Data kitchen berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $kitchen = Kitchen::findOrFail($id);

        $request->validate([
            'kd_kitchen'    => 'required|unique:kitchen,kd_kitchen,' . $kitchen->id,
            'nama'          => 'required',
            'satuan'        => 'required',
            'stok_minimal'  => 'required|integer',
        ]);

        $kitchen->update($request->all());
        return redirect()->route('kitchen.index')->with('success', 'Data kitchen berhasil diupdate');
    }

    public function destroy($id)
    {
        $kitchen = Kitchen::findOrFail($id);
        $kitchen->delete();
        return redirect()->route('kitchen.index')->with('success', 'Data kitchen berhasil dihapus');
    }

    // =====================
    // FIFO
    // =====================

    // =====================
    // BARANG MASUK
    // =====================
    // =====================
    // BARANG MASUK
    // =====================
    public function masuk(Request $request)
    {
        $request->validate([
            'kitchen_id' => 'required|exists:kitchen,id',
            'tanggal' => 'required|date',
            'jumlah'  => 'required|integer|min:1',
        ]);

        // hanya boleh input hari ini
        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input barang masuk hanya bisa untuk hari ini!');
        }

        // cek data masuk untuk hari ini
        $existing = KitchenMasuk::where('kitchen_id', $request->kitchen_id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            // kalau sudah ada → update jumlah (replace)
            $existing->jumlah = $request->jumlah;
            $existing->sisa   = $request->jumlah;
            $existing->save();
        } else {
            // kalau belum ada → buat data baru (tidak menimpa data kemarin)
            KitchenMasuk::create([
                'kitchen_id' => $request->kitchen_id,
                'tanggal' => Carbon::today()->toDateString(),
                'jumlah'  => $request->jumlah,
                'sisa'    => $request->jumlah,
            ]);
        }

        return back()->with('success', 'Barang masuk berhasil dicatat/diupdate');
    }

    // =====================
    // BARANG KELUAR
    // =====================
    public function keluar(Request $request)
    {
        $request->validate([
            'kitchen_id' => 'required|exists:kitchen,id',
            'tanggal' => 'required|date',
            'jumlah'  => 'required|integer|min:1',
        ]);

        // hanya boleh input hari ini
        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input barang keluar hanya bisa untuk hari ini!');
        }

        // cek data keluar untuk hari ini
        $existing = KitchenKeluar::where('kitchen_id', $request->kitchen_id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            // update data keluar hari ini
            $existing->jumlah = $request->jumlah;
            $existing->save();
        } else {
            // buat baru (tidak menimpa kemarin)
            KitchenKeluar::create([
                'kitchen_id' => $request->kitchen_id,
                'tanggal' => Carbon::today()->toDateString(),
                'jumlah'  => $request->jumlah,
            ]);
        }

        // FIFO jalan seperti biasa
        $jumlahKeluar = $request->jumlah;

        $stokMasuk = KitchenMasuk::where('kitchen_id', $request->kitchen_id)
            ->where('sisa', '>', 0)
            ->orderBy('tanggal', 'asc')
            ->get();

        foreach ($stokMasuk as $batch) {
            if ($jumlahKeluar <= 0) break;

            if ($batch->sisa >= $jumlahKeluar) {
                $batch->sisa -= $jumlahKeluar;
                $batch->save();
                $jumlahKeluar = 0;
            } else {
                $jumlahKeluar -= $batch->sisa;
                $batch->sisa = 0;
                $batch->save();
            }
        }

        return back()->with('success', 'Barang keluar berhasil dicatat/diupdate (FIFO)');
    }


    public function detail()
    {
        $kitchenList = Kitchen::all();
        $laporan = [];

        foreach ($kitchenList as $kitchen) {
            $masuk = KitchenMasuk::where('kitchen_id', $kitchen->id)->orderBy('tanggal')->get();
            $keluar = KitchenKeluar::where('kitchen_id', $kitchen->id)->orderBy('tanggal')->get();

            $tanggalAwal = $masuk->min('tanggal') ?? $keluar->min('tanggal') ?? Carbon::now()->toDateString();
            $tanggalAkhir = Carbon::now()->toDateString();

            $periode = new \DatePeriod(
                new \DateTime($tanggalAwal),
                new \DateInterval('P1D'),
                new \DateTime(Carbon::parse($tanggalAkhir)->addDay()->toDateString())
            );

            $stokAwal = 0; // stok awal hari pertama = 0
            $detailHari = [];

            foreach ($periode as $tanggal) {
                $tgl = $tanggal->format('Y-m-d');

                $barangDatang   = $masuk->where('tanggal', $tgl)->sum('jumlah');
                $barangTerpakai = $keluar->where('tanggal', $tgl)->sum('jumlah');

                // hitung stok akhir = stok awal + masuk - keluar
                $stokAkhir = $stokAwal + $barangDatang - $barangTerpakai;

                // histori FIFO
                $fifoHistori = KitchenMasuk::where('kitchen_id', $kitchen->id)
                    ->orderBy('tanggal', 'desc') // terbaru dulu
                    ->orderBy('created_at', 'desc') // kalau tanggal sama, paling baru ditampilkan dulu
                    ->get()
                    ->map(fn($m) => [
                        'tanggal_masuk' => $m->tanggal,
                        'jumlah'        => $m->jumlah,
                        'sisa'          => $m->sisa
                    ]);

                // hanya simpan detail untuk hari ini
                if ($tgl == Carbon::today()->toDateString()) {
                    $detailHari[] = [
                        'id'              => $kitchen->id . '_' . $tgl,
                        'tanggal'         => $tgl,
                        'stok_awal'       => $stokAwal,       // stok awal = stok akhir kemarin
                        'barang_datang'   => $barangDatang,
                        'barang_terpakai' => $barangTerpakai,
                        'stok_akhir'      => $stokAkhir,      // stok akhir hari ini
                        'stok_minimal'    => $kitchen->stok_minimal,
                        'satuan'          => $kitchen->satuan,
                        'fifo'            => $fifoHistori,
                    ];
                }

                // simpan stok akhir hari ini → jadi stok awal besok
                $stokAwal = $stokAkhir;
            }

            $laporan[] = [
                'kitchen'   => $kitchen,
                'detail' => $detailHari,
            ];
        }

        return view('kitchen.detail', compact('laporan'));
    }
}
