<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use App\Models\BarMasuk;
use App\Models\BarKeluar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BarController extends Controller
{
    // =====================
    // MASTER DATA Bar
    // =====================

    public function index()
    {
        $bar = Bar::all();
        return view('bar.index', compact('bar'));
    }

    public function storeBar(Request $request)
    {
        $request->validate([
            'kd_bar' => 'required|unique:bar,kd_bar',
            'nama' => 'required',
            'satuan' => 'required',
            'stok_minimal' => 'required|integer',
        ]);

        Bar::create($request->all());
        return redirect()->route('bar.index')->with('success', 'Data bar berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $bar = Bar::findOrFail($id);

        $request->validate([
            'kd_bar' => 'required|unique:bar,kd_bar,' . $bar->id,
            'nama' => 'required',
            'satuan' => 'required',
            'stok_minimal' => 'required|integer',
        ]);

        $bar->update($request->all());
        return redirect()->route('bar.index')->with('success', 'Data bar berhasil diupdate');
    }

    public function destroy($id)
    {
        $bar = Bar::findOrFail($id);
        $bar->delete();
        return redirect()->route('bar.index')->with('success', 'Data bar berhasil dihapus');
    }

    // =====================
    // FIFO
    // =====================

    // =====================
    // BARANG MASUK
    // =====================
    public function masuk(Request $request)
    {
        $request->validate([
            'bar_id' => 'required|exists:bar,id',
            'tanggal' => 'required|date',
            'jumlah'  => 'required|integer|min:1',
        ]);

        // hanya boleh input hari ini
        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input barang masuk hanya bisa untuk hari ini!');
        }

        // cek data masuk untuk hari ini
        $existing = BarMasuk::where('bar_id', $request->bar_id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            // kalau sudah ada → update jumlah (replace)
            $existing->jumlah = $request->jumlah;
            $existing->sisa   = $request->jumlah;
            $existing->save();
        } else {
            // kalau belum ada → buat data baru (tidak menimpa data kemarin)
            BarMasuk::create([
                'bar_id' => $request->bar_id,
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
            'bar_id' => 'required|exists:bar,id',
            'tanggal' => 'required|date',
            'jumlah'  => 'required|integer|min:1',
        ]);

        // hanya boleh input hari ini
        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input barang keluar hanya bisa untuk hari ini!');
        }

        // cek data keluar untuk hari ini
        $existing = BarKeluar::where('bar_id', $request->bar_id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            // update data keluar hari ini
            $existing->jumlah = $request->jumlah;
            $existing->save();
        } else {
            // buat baru (tidak menimpa kemarin)
            BarKeluar::create([
                'bar_id' => $request->bar_id,
                'tanggal' => Carbon::today()->toDateString(),
                'jumlah'  => $request->jumlah,
            ]);
        }

        // FIFO jalan seperti biasa
        $jumlahKeluar = $request->jumlah;

        $stokMasuk = BarMasuk::where('bar_id', $request->bar_id)
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
        $barList = Bar::all();
        $laporan = [];

        foreach ($barList as $bar) {
            $masuk = BarMasuk::where('bar_id', $bar->id)->orderBy('tanggal')->get();
            $keluar = BarKeluar::where('bar_id', $bar->id)->orderBy('tanggal')->get();

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
                $fifoHistori = BarMasuk::where('bar_id', $bar->id)
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
                        'id'              => $bar->id . '_' . $tgl,
                        'tanggal'         => $tgl,
                        'stok_awal'       => $stokAwal,       // stok awal = stok akhir kemarin
                        'barang_datang'   => $barangDatang,
                        'barang_terpakai' => $barangTerpakai,
                        'stok_akhir'      => $stokAkhir,      // stok akhir hari ini
                        'stok_minimal'    => $bar->stok_minimal,
                        'satuan'          => $bar->satuan,
                        'fifo'            => $fifoHistori,
                    ];
                }

                // simpan stok akhir hari ini → jadi stok awal besok
                $stokAwal = $stokAkhir;
            }

            $laporan[] = [
                'bar'   => $bar,
                'detail' => $detailHari,
            ];
        }

        return view('bar.detail', compact('laporan'));
    }
}
