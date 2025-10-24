<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roti;
use App\Models\RotiMasuk;
use App\Models\RotiKeluar;
use App\Models\Bar;
use App\Models\BarMasuk;
use App\Models\BarKeluar;
use App\Models\Kitchen;
use App\Models\KitchenMasuk;
use App\Models\KitchenKeluar;
use Carbon\Carbon;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;


class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function filter(Request $request)
    {
        $request->validate([
            'kategori' => 'required|in:roti,bar,kitchen',
            'bulan'    => 'required|integer|min:1|max:12',
            'tahun'    => 'required|integer',
        ]);

        $kategori = $request->kategori;
        $bulan    = $request->bulan;
        $tahun    = $request->tahun;

        // Tentukan model sesuai kategori
        switch ($kategori) {
            case 'roti':
                $model      = Roti::class;
                $masukModel = RotiMasuk::class;
                $keluarModel = RotiKeluar::class;
                break;
            case 'bar':
                $model      = Bar::class;
                $masukModel = BarMasuk::class;
                $keluarModel = BarKeluar::class;
                break;
            case 'kitchen':
                $model      = Kitchen::class;
                $masukModel = KitchenMasuk::class;
                $keluarModel = KitchenKeluar::class;
                break;
        }

        $items = $model::all();
        $laporan = [];

        foreach ($items as $item) {
            // Hitung barang datang
            $totalMasuk = $masukModel::where('tanggal', '>=', Carbon::create($tahun, $bulan, 1)->startOfMonth())
                ->where('tanggal', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth())
                ->where($kategori . '_id', $item->id)
                ->sum('jumlah');

            // Hitung barang terpakai
            $totalKeluar = $keluarModel::where('tanggal', '>=', Carbon::create($tahun, $bulan, 1)->startOfMonth())
                ->where('tanggal', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth())
                ->where($kategori . '_id', $item->id)
                ->sum('jumlah');

            // Hitung sisa (stok akhir)
            $stokAkhir = $item->stok ?? ($totalMasuk - $totalKeluar);

            $laporan[] = [
                'kd_barang' => $item->kd_kitchen ?? $item->kd_roti ?? $item->kd_bar,
                'nama' => $item->nama ?? $item->nama_roti ?? $item->nama_bar,
                'satuan' => $item->satuan,
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'sisa' => $stokAkhir,
            ];
        }

        return view('laporan.index', compact('laporan', 'kategori', 'bulan', 'tahun'));
    }

    public function cetakPdf(Request $request)
    {
        $kategori = $request->kategori;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = $this->filter($request)->getData(); // ambil data laporan
        $pdf = Pdf::loadView('laporan.pdf', [
            'laporan' => $data['laporan'],
            'kategori' => $kategori,
            'bulan' => $bulan,
            'tahun' => $tahun
        ])->setPaper('A4', 'portrait');


        return $pdf->download("laporan-{$kategori}-{$bulan}-{$tahun}.pdf");
    }

    public function cekStokMinimal(Request $request)
    {
        $kategori = $request->input('kategori', 'roti'); // default roti

        switch ($kategori) {
            case 'bar':
                $model = \App\Models\Bar::class;
                $masuk = \App\Models\BarMasuk::class;
                $keluar = \App\Models\BarKeluar::class;
                break;
            case 'kitchen':
                $model = \App\Models\Kitchen::class;
                $masuk = \App\Models\KitchenMasuk::class;
                $keluar = \App\Models\KitchenKeluar::class;
                break;
            default:
                $model = \App\Models\Roti::class;
                $masuk = \App\Models\RotiMasuk::class;
                $keluar = \App\Models\RotiKeluar::class;
                break;
        }

        $barangList = $model::all();
        $today = now()->toDateString();
        $hasil = [];

        foreach ($barangList as $barang) {
            // total masuk s/d hari ini
            $totalMasuk = $masuk::where('roti_id', $barang->id ?? null)
                ->orWhere('bar_id', $barang->id ?? null)
                ->orWhere('kitchen_id', $barang->id ?? null)
                ->sum('jumlah');

            // total keluar s/d hari ini
            $totalKeluar = $keluar::where('roti_id', $barang->id ?? null)
                ->orWhere('bar_id', $barang->id ?? null)
                ->orWhere('kitchen_id', $barang->id ?? null)
                ->sum('jumlah');

            $stokSisa = $totalMasuk - $totalKeluar;
            $batasMinimal = $barang->stok_minimal + ($barang->stok_minimal * 0.1);

            if ($stokSisa <= $batasMinimal) {
                $hasil[] = [
                    'kd_barang'    => $barang->kd_roti ?? $barang->kd_bar ?? $barang->kd_kitchen,
                    'nama'         => $barang->nama,
                    'satuan'       => $barang->satuan,
                    'stok_sisa'    => $stokSisa,
                    'stok_minimal' => $barang->stok_minimal,
                ];
            }
        }

        return view('home', [
            'kategori' => $kategori,
            'hasil'    => $hasil,
        ]);
    }
}
