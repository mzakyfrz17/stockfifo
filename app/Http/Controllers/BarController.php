<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use App\Models\BarMasuk;
use App\Models\BarKeluar;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BarController extends Controller
{
    // =====================
    // MASTER DATA
    // =====================
    public function index()
    {
        $bar = Bar::all();
        return view('bar.index', compact('bar'));
    }

    public function storeBar(Request $request)
    {
        $request->validate([
            'kd_bar'        => 'required|unique:bar,kd_bar',
            'nama'          => 'required',
            'satuan'        => 'required',
            'stok_minimal'  => 'required|integer',
        ]);

        Bar::create($request->all());
        return redirect()->route('bar.index')->with('success', 'Data bar berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $bar = Bar::findOrFail($id);

        $request->validate([
            'kd_bar'        => 'required|unique:bar,kd_bar,' . $bar->id,
            'nama'          => 'required',
            'satuan'        => 'required',
            'stok_minimal'  => 'required|integer',
        ]);

        $bar->update($request->all());
        return redirect()->route('bar.index')->with('success', 'Data bar berhasil diupdate');
    }

    public function destroy($id)
    {
        Bar::findOrFail($id)->delete();
        return back()->with('success', 'Data bar berhasil dihapus');
    }

    // =====================
    // BARANG MASUK
    // =====================
    public function masuk(Request $request)
    {
        $request->validate([
            'bar_id'   => 'required|exists:bar,id',
            'tanggal'  => 'required|date',
            'jumlah'   => 'required|integer|min:1',
        ]);

        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input hanya untuk hari ini');
        }

        BarMasuk::updateOrCreate(
            [
                'bar_id'  => $request->bar_id,
                'tanggal' => $request->tanggal,
            ],
            [
                'jumlah'  => $request->jumlah,
                'sisa'    => 0, // reset dulu
                'user_id' => Auth::id(),
            ]
        );

        $this->recalculateFIFO($request->bar_id);

        return back()->with('success', 'Barang masuk disimpan');
    }

    // =====================
    // BARANG KELUAR
    // =====================
    public function keluar(Request $request)
    {
        $request->validate([
            'bar_id'   => 'required|exists:bar,id',
            'tanggal'  => 'required|date',
            'jumlah'   => 'required|integer|min:1',
        ]);

        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input hanya untuk hari ini');
        }

        BarKeluar::updateOrCreate(
            [
                'bar_id'  => $request->bar_id,
                'tanggal' => $request->tanggal,
            ],
            [
                'jumlah'  => $request->jumlah,
                'user_id' => Auth::id(),
            ]
        );

        $this->recalculateFIFO($request->bar_id);

        return back()->with('success', 'Barang keluar disimpan (FIFO)');
    }

    // =====================
    // FIFO RESET & HITUNG ULANG
    // =====================
    private function recalculateFIFO($barId)
    {
        $masuk = BarMasuk::where('bar_id', $barId)
            ->orderBy('tanggal')
            ->orderBy('created_at')
            ->get();

        $keluarTotal = BarKeluar::where('bar_id', $barId)->sum('jumlah');

        // reset sisa = jumlah
        foreach ($masuk as $m) {
            $m->sisa = $m->jumlah;
            $m->save();
        }

        // FIFO potong ulang
        foreach ($masuk as $m) {
            if ($keluarTotal <= 0) break;

            if ($m->sisa >= $keluarTotal) {
                $m->sisa -= $keluarTotal;
                $keluarTotal = 0;
            } else {
                $keluarTotal -= $m->sisa;
                $m->sisa = 0;
            }
            $m->save();
        }
    }

    // =====================
    // DETAIL LAPORAN
    // =====================
    public function detail()
    {
        $laporan = [];
        $hariIni = Carbon::today()->toDateString();

        foreach (Bar::all() as $bar) {

            // TOTAL SEBELUM HARI INI
            $totalMasukSebelum = BarMasuk::where('bar_id', $bar->id)
                ->where('tanggal', '<', $hariIni)
                ->sum('jumlah');

            $totalKeluarSebelum = BarKeluar::where('bar_id', $bar->id)
                ->where('tanggal', '<', $hariIni)
                ->sum('jumlah');

            $stokAwal = $totalMasukSebelum - $totalKeluarSebelum;

            // HARI INI
            $barangDatang = BarMasuk::where('bar_id', $bar->id)
                ->where('tanggal', $hariIni)
                ->sum('jumlah');

            $barangTerpakai = BarKeluar::where('bar_id', $bar->id)
                ->where('tanggal', $hariIni)
                ->sum('jumlah');

            $stokAkhir = $stokAwal + $barangDatang - $barangTerpakai;

            // FIFO DETAIL
            $fifo = BarMasuk::with('user')
                ->where('bar_id', $bar->id)
                ->orderBy('tanggal')
                ->get()
                ->map(fn($m) => [
                    'tanggal_masuk' => $m->tanggal,
                    'jumlah'        => $m->jumlah,
                    'sisa'          => $m->sisa,
                    'user'          => $m->user->name ?? '-',
                ]);

            $userMasuk = BarMasuk::with('user')
                ->where('bar_id', $bar->id)
                ->where('tanggal', $hariIni)
                ->latest()
                ->first();

            $userKeluar = BarKeluar::with('user')
                ->where('bar_id', $bar->id)
                ->where('tanggal', $hariIni)
                ->latest()
                ->first();

            $laporan[] = [
                'bar' => $bar,
                'detail' => [[
                    'id'              => $bar->id . '_' . $hariIni,
                    'tanggal'         => $hariIni,
                    'stok_awal'       => max(0, $stokAwal),
                    'barang_datang'   => $barangDatang,
                    'barang_terpakai' => $barangTerpakai,
                    'stok_akhir'      => max(0, $stokAkhir),
                    'stok_minimal'    => $bar->stok_minimal,
                    'satuan'          => $bar->satuan,
                    'user_masuk'      => $userMasuk->user->name ?? '-',
                    'user_keluar'     => $userKeluar->user->name ?? '-',
                    'fifo'            => $fifo,
                ]]
            ];
        }

        return view('bar.detail', compact('laporan'));
    }
}
