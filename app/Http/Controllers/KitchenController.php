<?php

namespace App\Http\Controllers;

use App\Models\Kitchen;
use App\Models\KitchenMasuk;
use App\Models\KitchenKeluar;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KitchenController extends Controller
{
    // =====================
    // MASTER DATA
    // =====================
    public function index()
    {
        $kitchen = Kitchen::all();
        return view('kitchen.index', compact('kitchen'));
    }

    public function storeKitchen(Request $request)
    {
        $request->validate([
            'nama'         => 'required',
            'satuan'       => 'required|regex:/^[a-zA-Z\s]+$/',
            'stok_minimal' => 'required|integer',
        ], [
            'satuan.regex' => 'Satuan hanya boleh huruf'
        ]);

        // Generate kode kitchen otomatis
        $last = Kitchen::orderBy('id', 'desc')->first();
        $number = $last ? intval(substr($last->kd_kitchen, 2)) + 1 : 1;
        $kdKitchen = 'KT' . str_pad($number, 3, '0', STR_PAD_LEFT);

        Kitchen::create([
            'kd_kitchen'   => $kdKitchen,
            'nama'         => $request->nama,
            'satuan'       => $request->satuan,
            'stok_minimal' => $request->stok_minimal,
        ]);

        return redirect()->route('kitchen.index')
            ->with('success', 'Data kitchen berhasil ditambahkan');
    }


    public function update(Request $request, $id)
    {
        $kitchen = Kitchen::findOrFail($id);

        $request->validate([
            'nama'         => 'required',
            'satuan'       => 'required|regex:/^[a-zA-Z\s]+$/',
            'stok_minimal' => 'required|integer',
        ], [
            'satuan.regex' => 'Satuan hanya boleh huruf'
        ]);

        $kitchen->update([
            'nama'         => $request->nama,
            'satuan'       => $request->satuan,
            'stok_minimal' => $request->stok_minimal,
        ]);

        return redirect()->route('kitchen.index')
            ->with('success', 'Data kitchen berhasil diupdate');
    }


    public function destroy($id)
    {
        $kitchen = Kitchen::with(['masuk', 'keluar'])->findOrFail($id);

        if ($kitchen->masuk->count() > 0 || $kitchen->keluar->count() > 0) {
            return redirect()->back()->with(
                'error',
                'Data tidak bisa dihapus karena sudah memiliki transaksi masuk / keluar'
            );
        }

        $kitchen->delete();

        return redirect()->back()->with('success', 'Data kitchen berhasil dihapus');
    }



    // =====================
    // BARANG MASUK
    // =====================
    public function masuk(Request $request)
    {
        $request->validate([
            'kitchen_id' => 'required|exists:kitchen,id',
            'tanggal'    => 'required|date',
            'jumlah'     => 'required|integer|min:1',
        ]);

        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input hanya untuk hari ini');
        }

        $masuk = KitchenMasuk::updateOrCreate(
            [
                'kitchen_id' => $request->kitchen_id,
                'tanggal'    => $request->tanggal,
            ],
            [
                'jumlah'  => $request->jumlah,
                'sisa'    => 0, // reset dulu
                'user_id' => Auth::id(),
            ]
        );

        $this->recalculateFIFO($request->kitchen_id);

        return back()->with('success', 'Barang masuk disimpan');
    }

    // =====================
    // BARANG KELUAR
    // =====================
    public function keluar(Request $request)
    {
        $request->validate([
            'kitchen_id' => 'required|exists:kitchen,id',
            'tanggal'    => 'required|date',
            'jumlah'     => 'required|integer|min:1',
        ]);

        if ($request->tanggal !== Carbon::today()->toDateString()) {
            return back()->with('error', 'Input hanya untuk hari ini');
        }

        KitchenKeluar::updateOrCreate(
            [
                'kitchen_id' => $request->kitchen_id,
                'tanggal'    => $request->tanggal,
            ],
            [
                'jumlah'  => $request->jumlah,
                'user_id' => Auth::id(),
            ]
        );

        $this->recalculateFIFO($request->kitchen_id);

        return back()->with('success', 'Barang keluar disimpan (FIFO)');
    }

    // =====================
    // FIFO RESET & HITUNG ULANG
    // =====================
    private function recalculateFIFO($kitchenId)
    {
        $masuk = KitchenMasuk::where('kitchen_id', $kitchenId)
            ->orderBy('tanggal')
            ->orderBy('created_at')
            ->get();

        $keluarTotal = KitchenKeluar::where('kitchen_id', $kitchenId)->sum('jumlah');

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
        $kemarin = Carbon::yesterday()->toDateString();

        foreach (Kitchen::all() as $kitchen) {

            // =====================
            // TOTAL SEBELUM HARI INI
            // =====================
            $totalMasukSebelum = KitchenMasuk::where('kitchen_id', $kitchen->id)
                ->where('tanggal', '<', $hariIni)
                ->sum('jumlah');

            $totalKeluarSebelum = KitchenKeluar::where('kitchen_id', $kitchen->id)
                ->where('tanggal', '<', $hariIni)
                ->sum('jumlah');

            // STOK AWAL HARI INI (stok akhir kemarin)
            $stokAwal = $totalMasukSebelum - $totalKeluarSebelum;

            // =====================
            // HARI INI
            // =====================
            $barangDatang = KitchenMasuk::where('kitchen_id', $kitchen->id)
                ->where('tanggal', $hariIni)
                ->sum('jumlah');

            $barangTerpakai = KitchenKeluar::where('kitchen_id', $kitchen->id)
                ->where('tanggal', $hariIni)
                ->sum('jumlah');

            $stokAkhir = $stokAwal + $barangDatang - $barangTerpakai;

            // DATA FIFO (sisa per batch)
            $fifo = KitchenMasuk::with('user')
                ->where('kitchen_id', $kitchen->id)
                ->orderBy('tanggal')
                ->get()
                ->map(fn($m) => [
                    'tanggal_masuk' => $m->tanggal,
                    'jumlah'        => $m->jumlah,
                    'sisa'          => $m->sisa,
                    'user'          => $m->user->name ?? '-',
                ]);
            $userMasuk = KitchenMasuk::with('user')
                ->where('kitchen_id', $kitchen->id)
                ->where('tanggal', $hariIni)
                ->latest()
                ->first();

            $userKeluar = KitchenKeluar::with('user')
                ->where('kitchen_id', $kitchen->id)
                ->where('tanggal', $hariIni)
                ->latest()
                ->first();
            $laporan[] = [
                'kitchen' => $kitchen,
                'detail'  => [[
                    'id'              => $kitchen->id . '_' . $hariIni,
                    'tanggal'         => $hariIni,
                    'stok_awal'       => max(0, $stokAwal),
                    'barang_datang'   => $barangDatang,
                    'barang_terpakai' => $barangTerpakai,
                    'stok_akhir'      => max(0, $stokAkhir),
                    'stok_minimal'    => $kitchen->stok_minimal,
                    'satuan'          => $kitchen->satuan,
                    'user_masuk'  => $userMasuk->user->name ?? '-',
                    'user_keluar' => $userKeluar->user->name ?? '-',
                    'fifo'            => $fifo,
                ]]
            ];
        }

        return view('kitchen.detail', compact('laporan'));
    }
}
