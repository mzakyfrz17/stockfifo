<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\PDF;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $role = Auth::user()->role;

        // kalau manager bisa pilih kategori, kalau bukan otomatis sesuai role
        if ($role === 'manager') {
            $kategori = $request->input('kategori', 'roti');
        } else {
            $kategori = $role; // otomatis pakai role user
        }

        $hasil = $this->getStokMinimal($kategori);

        return view('home', compact('kategori', 'hasil', 'role'));
    }

    public function cetakPdf(Request $request)
    {
        $role = Auth::user()->role;

        if ($role === 'manager') {
            $kategori = $request->input('kategori', 'roti');
        } else {
            $kategori = $role;
        }

        $tanggalCetak = now()->format('d-m-Y H:i');
        $hasil = $this->getStokMinimal($kategori);

        $pdf = PDF::loadView('pdf.stok-minimal', [
            'kategori' => $kategori,
            'hasil' => $hasil,
            'tanggalCetak' => $tanggalCetak
        ])->setPaper('A4', 'portrait');

        return $pdf->download("laporan-stok-minimal-{$kategori}-{$tanggalCetak}.pdf");
    }

    private function getStokMinimal($kategori)
    {
        switch ($kategori) {
            case 'bar':
                $model = \App\Models\Bar::class;
                $masuk = \App\Models\BarMasuk::class;
                $keluar = \App\Models\BarKeluar::class;
                $kolomId = 'bar_id';
                $kolomKode = 'kd_bar';
                break;

            case 'kitchen':
                $model = \App\Models\Kitchen::class;
                $masuk = \App\Models\KitchenMasuk::class;
                $keluar = \App\Models\KitchenKeluar::class;
                $kolomId = 'kitchen_id';
                $kolomKode = 'kd_kitchen';
                break;

            default: // roti
                $model = \App\Models\Roti::class;
                $masuk = \App\Models\RotiMasuk::class;
                $keluar = \App\Models\RotiKeluar::class;
                $kolomId = 'roti_id';
                $kolomKode = 'kd_roti';
                break;
        }

        $barangList = $model::all();
        $hasil = [];

        foreach ($barangList as $barang) {
            $totalMasuk = $masuk::where($kolomId, $barang->id)->sum('jumlah');
            $totalKeluar = $keluar::where($kolomId, $barang->id)->sum('jumlah');
            $stokSisa = $totalMasuk - $totalKeluar;
            $batasMinimal = $barang->stok_minimal + ($barang->stok_minimal * 0.1);

            if ($stokSisa <= $batasMinimal) {
                $hasil[] = [
                    'kd_barang'    => $barang->$kolomKode,
                    'nama'         => $barang->nama,
                    'satuan'       => $barang->satuan,
                    'stok_sisa'    => $stokSisa,
                    'stok_minimal' => $barang->stok_minimal,
                ];
            }
        }

        return $hasil;
    }
}
