<?php

namespace App\Http\Controllers;

use App\Models\Roti;
use App\Models\RotiMasuk;
use App\Models\RotiKeluar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RotiController extends Controller
{
    // =====================
    // MASTER DATA ROTI
    // =====================

    public function index()
    {
        $roti = Roti::all();
        return view('roti.index', compact('roti'));
    }

    public function storeRoti(Request $request)
    {
        $request->validate([
            'kd_roti' => 'required|unique:roti,kd_roti',
            'nama' => 'required',
            'satuan' => 'required',
            'stok_minimal' => 'required|integer',
        ]);

        Roti::create($request->all());
        return redirect()->route('roti.index')->with('success', 'Data roti berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $roti = Roti::findOrFail($id);

        $request->validate([
            'kd_roti' => 'required|unique:roti,kd_roti,' . $roti->id,
            'nama' => 'required',
            'satuan' => 'required',
            'stok_minimal' => 'required|integer',
        ]);

        $roti->update($request->all());
        return redirect()->route('roti.index')->with('success', 'Data roti berhasil diupdate');
    }

    public function destroy($id)
    {
        $roti = Roti::findOrFail($id);
        $roti->delete();
        return redirect()->route('roti.index')->with('success', 'Data roti berhasil dihapus');
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
            'roti_id' => 'required|exists:roti,id',
            'tanggal' => 'required|date',
            'jumlah'  => 'required|integer|min:1',
        ]);

        // hanya boleh input hari ini
        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input barang masuk hanya bisa untuk hari ini!');
        }

        // cek data masuk untuk hari ini
        $existing = RotiMasuk::where('roti_id', $request->roti_id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            // kalau sudah ada → update jumlah (replace)
            $existing->jumlah = $request->jumlah;
            $existing->sisa   = $request->jumlah;
            $existing->save();
        } else {
            // kalau belum ada → buat data baru (tidak menimpa data kemarin)
            RotiMasuk::create([
                'roti_id' => $request->roti_id,
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
            'roti_id' => 'required|exists:roti,id',
            'tanggal' => 'required|date',
            'jumlah'  => 'required|integer|min:1',
        ]);

        // hanya boleh input hari ini
        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input barang keluar hanya bisa untuk hari ini!');
        }

        // cek data keluar untuk hari ini
        $existing = RotiKeluar::where('roti_id', $request->roti_id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            // update data keluar hari ini
            $existing->jumlah = $request->jumlah;
            $existing->save();
        } else {
            // buat baru (tidak menimpa kemarin)
            RotiKeluar::create([
                'roti_id' => $request->roti_id,
                'tanggal' => Carbon::today()->toDateString(),
                'jumlah'  => $request->jumlah,
            ]);
        }

        // FIFO jalan seperti biasa
        $jumlahKeluar = $request->jumlah;

        $stokMasuk = RotiMasuk::where('roti_id', $request->roti_id)
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
        $rotiList = Roti::all();
        $laporan = [];

        foreach ($rotiList as $roti) {
            $masuk = RotiMasuk::where('roti_id', $roti->id)->orderBy('tanggal')->get();
            $keluar = RotiKeluar::where('roti_id', $roti->id)->orderBy('tanggal')->get();

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
                $fifoHistori = RotiMasuk::where('roti_id', $roti->id)
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
                        'id'              => $roti->id . '_' . $tgl,
                        'tanggal'         => $tgl,
                        'stok_awal'       => $stokAwal,       // stok awal = stok akhir kemarin
                        'barang_datang'   => $barangDatang,
                        'barang_terpakai' => $barangTerpakai,
                        'stok_akhir'      => $stokAkhir,      // stok akhir hari ini
                        'stok_minimal'    => $roti->stok_minimal,
                        'satuan'          => $roti->satuan,
                        'fifo'            => $fifoHistori,
                    ];
                }

                // simpan stok akhir hari ini → jadi stok awal besok
                $stokAwal = $stokAkhir;
            }

            $laporan[] = [
                'roti'   => $roti,
                'detail' => $detailHari,
            ];
        }

        return view('roti.detail', compact('laporan'));
    }
}
