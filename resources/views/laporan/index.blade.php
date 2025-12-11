@extends('layouts.template')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Laporan Bulanan</div>
            <div class="card-body">
                <form action="{{ route('laporan.filter') }}" method="GET" class="row mb-4">
                    <div class="col-md-3">
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="roti">Roti</option>
                            <option value="bar">Bar</option>
                            <option value="kitchen">Kitchen</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="bulan" class="form-control" required>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" type="submit">Tampilkan</button>
                    </div>
                </form>

                @isset($laporan)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Total Datang</th>
                                <th>Total Terpakai</th>
                                <th>Sisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporan as $row)
                                <tr>
                                    <td>{{ $row['kd_barang'] }}</td>
                                    <td>{{ $row['nama'] }}</td>
                                    <td>{{ $row['satuan'] }}</td>
                                    <td>{{ $row['total_masuk'] }}</td>
                                    <td>{{ $row['total_keluar'] }}</td>
                                    <td>{{ $row['sisa'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <a href="{{ route('laporan.cetakPdf', request()->all()) }}" class="btn btn-danger">Cetak PDF</a>
                @endisset
            </div>
        </div>
    </div>
@endsection
