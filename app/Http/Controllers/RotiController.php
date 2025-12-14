<?php

namespace App\Http\Controllers;

use App\Models\Roti;
use App\Models\RotiMasuk;
use App\Models\RotiKeluar;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RotiController extends Controller
{
    // =====================
    // MASTER DATA
    // =====================
    public function index()
    {
        $roti = Roti::all();
        return view('roti.index', compact('roti'));
    }

    public function storeRoti(Request $request)
    {
        $request->validate([
            'kd_roti'       => 'required|unique:roti,kd_roti',
            'nama'          => 'required',
            'satuan'        => 'required',
            'stok_minimal'  => 'required|integer',
        ]);

        Roti::create($request->all());
        return redirect()->route('roti.index')->with('success', 'Data roti berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $roti = Roti::findOrFail($id);

        $request->validate([
            'kd_roti'       => 'required|unique:roti,kd_roti,' . $roti->id,
            'nama'          => 'required',
            'satuan'        => 'required',
            'stok_minimal'  => 'required|integer',
        ]);

        $roti->update($request->all());
        return redirect()->route('roti.index')->with('success', 'Data roti berhasil diupdate');
    }

    public function destroy($id)
    {
        Roti::findOrFail($id)->delete();
        return back()->with('success', 'Data roti berhasil dihapus');
    }

    // =====================
    // BARANG MASUK
    // =====================
    public function masuk(Request $request)
    {
        $request->validate([
            'roti_id'  => 'required|exists:roti,id',
            'tanggal'  => 'required|date',
            'jumlah'   => 'required|integer|min:1',
        ]);

        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input hanya untuk hari ini');
        }

        RotiMasuk::updateOrCreate(
            [
                'roti_id' => $request->roti_id,
                'tanggal' => $request->tanggal,
            ],
            [
                'jumlah'  => $request->jumlah,
                'sisa'    => 0, // reset dulu
                'user_id' => Auth::id(),
            ]
        );

        $this->recalculateFIFO($request->roti_id);

        return back()->with('success', 'Barang masuk disimpan');
    }

    // =====================
    // BARANG KELUAR
    // =====================
    public function keluar(Request $request)
    {
        $request->validate([
            'roti_id'  => 'required|exists:roti,id',
            'tanggal'  => 'required|date',
            'jumlah'   => 'required|integer|min:1',
        ]);

        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input hanya untuk hari ini');
        }

        RotiKeluar::updateOrCreate(
            [
                'roti_id' => $request->roti_id,
                'tanggal' => $request->tanggal,
            ],
            [
                'jumlah'  => $request->jumlah,
                'user_id' => Auth::id(),
            ]
        );

        $this->recalculateFIFO($request->roti_id);

        return back()->with('success', 'Barang keluar disimpan (FIFO)');
    }

    // =====================
    // FIFO RESET & HITUNG ULANG
    // =====================
    private function recalculateFIFO($rotiId)
    {
        $masuk = RotiMasuk::where('roti_id', $rotiId)
            ->orderBy('tanggal')
            ->orderBy('created_at')
            ->get();

        $keluarTotal = RotiKeluar::where('roti_id', $rotiId)->sum('jumlah');

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

        foreach (Roti::all() as $roti) {

            // TOTAL SEBELUM HARI INI
            $totalMasukSebelum = RotiMasuk::where('roti_id', $roti->id)
                ->where('tanggal', '<', $hariIni)
                ->sum('jumlah');

            $totalKeluarSebelum = RotiKeluar::where('roti_id', $roti->id)
                ->where('tanggal', '<', $hariIni)
                ->sum('jumlah');

            $stokAwal = $totalMasukSebelum - $totalKeluarSebelum;

            // HARI INI
            $barangDatang = RotiMasuk::where('roti_id', $roti->id)
                ->where('tanggal', $hariIni)
                ->sum('jumlah');

            $barangTerpakai = RotiKeluar::where('roti_id', $roti->id)
                ->where('tanggal', $hariIni)
                ->sum('jumlah');

            $stokAkhir = $stokAwal + $barangDatang - $barangTerpakai;

            // FIFO DETAIL
            $fifo = RotiMasuk::with('user')
                ->where('roti_id', $roti->id)
                ->orderBy('tanggal')
                ->get()
                ->map(fn($m) => [
                    'tanggal_masuk' => $m->tanggal,
                    'jumlah'        => $m->jumlah,
                    'sisa'          => $m->sisa,
                    'user'          => $m->user->name ?? '-',
                ]);

            $userMasuk = RotiMasuk::with('user')
                ->where('roti_id', $roti->id)
                ->where('tanggal', $hariIni)
                ->latest()
                ->first();

            $userKeluar = RotiKeluar::with('user')
                ->where('roti_id', $roti->id)
                ->where('tanggal', $hariIni)
                ->latest()
                ->first();

            $laporan[] = [
                'roti' => $roti,
                'detail' => [[
                    'id'              => $roti->id . '_' . $hariIni,
                    'tanggal'         => $hariIni,
                    'stok_awal'       => max(0, $stokAwal),
                    'barang_datang'   => $barangDatang,
                    'barang_terpakai' => $barangTerpakai,
                    'stok_akhir'      => max(0, $stokAkhir),
                    'stok_minimal'    => $roti->stok_minimal,
                    'satuan'          => $roti->satuan,
                    'user_masuk'      => $userMasuk->user->name ?? '-',
                    'user_keluar'     => $userKeluar->user->name ?? '-',
                    'fifo'            => $fifo,
                ]]
            ];
        }

        return view('roti.detail', compact('laporan'));
    }
}
