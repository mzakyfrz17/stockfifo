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
            'nama'         => 'required',
            'satuan'       => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'stok_minimal' => 'required|integer',
        ]);

        // Generate kode RT001
        $last = Roti::orderBy('id', 'desc')->first();
        $number = $last ? intval(substr($last->kd_roti, 2)) + 1 : 1;
        $kd_roti = 'RT' . str_pad($number, 3, '0', STR_PAD_LEFT);

        Roti::create([
            'kd_roti'       => $kd_roti,
            'nama'          => $request->nama,
            'satuan'        => $request->satuan,
            'stok_minimal'  => $request->stok_minimal,
        ]);

        return redirect()->route('roti.index')
            ->with('success', 'Data roti berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $roti = Roti::findOrFail($id);

        $request->validate([
            'nama'         => 'required',
            'satuan'       => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'stok_minimal' => 'required|integer',
        ]);

        $roti->update([
            'nama'         => $request->nama,
            'satuan'       => $request->satuan,
            'stok_minimal' => $request->stok_minimal,
        ]);

        return redirect()->route('roti.index')
            ->with('success', 'Data roti berhasil diupdate');
    }


    public function destroy($id)
    {
        $roti = Roti::findOrFail($id);

        if ($roti->masuk()->exists() || $roti->keluar()->exists()) {
            return redirect()->route('roti.index')
                ->with('error', 'Data roti tidak bisa dihapus karena sudah memiliki transaksi');
        }

        $roti->delete();

        return redirect()->route('roti.index')
            ->with('success', 'Data roti berhasil dihapus');
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

            // =====================
            // TOTAL SEBELUM HARI INI
            // =====================
            $totalMasukSebelum = RotiMasuk::where('roti_id', $roti->id)
                ->where('tanggal', '<', $hariIni)
                ->sum('jumlah');

            $totalKeluarSebelum = RotiKeluar::where('roti_id', $roti->id)
                ->where('tanggal', '<', $hariIni)
                ->sum('jumlah');

            $stokAwal = $totalMasukSebelum - $totalKeluarSebelum;

            // =====================
            // HARI INI
            // =====================
            $barangDatang = RotiMasuk::where('roti_id', $roti->id)
                ->where('tanggal', $hariIni)
                ->sum('jumlah');

            $barangTerpakai = RotiKeluar::where('roti_id', $roti->id)
                ->where('tanggal', $hariIni)
                ->sum('jumlah');

            $stokAkhir = $stokAwal + $barangDatang - $barangTerpakai;

            // =====================
            // FIFO (SAMA SEPERTI KITCHEN & BAR)
            // =====================
            $fifo = RotiMasuk::where('roti_id', $roti->id)
                ->orderBy('tanggal')
                ->get()
                ->map(function ($m) use ($roti) {

                    // Barang terpakai sesuai tanggal FIFO
                    $barangTerpakaiTanggal = RotiKeluar::where('roti_id', $roti->id)
                        ->where('tanggal', $m->tanggal)
                        ->sum('jumlah');

                    return [
                        'kode_barang'      => Carbon::parse($m->tanggal)->format('dmY'),
                        'tanggal'          => $m->tanggal,
                        'barang_masuk'     => $m->jumlah,
                        'barang_terpakai'  => $barangTerpakaiTanggal,
                        'jumlah'           => $m->jumlah,
                        'sisa'             => $m->sisa,
                    ];
                });


            // USER HARI INI
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

            // =====================
            // REKOMENDASI FIFO
            // =====================
            $rekomendasi = RotiMasuk::where('roti_id', $roti->id)
                ->where('sisa', '>', 0)
                ->orderBy('tanggal')
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
                    'rekomendasi'     => $rekomendasi
                        ? Carbon::parse($rekomendasi->tanggal)->format('dmY')
                        : '-',
                ]]
            ];
        }

        return view('roti.detail', compact('laporan'));
    }
}
