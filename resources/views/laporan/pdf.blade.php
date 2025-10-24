<!DOCTYPE html>
<html>

    <head>
        <title>Laporan {{ ucfirst($kategori) }} Bulan {{ $bulan }} Tahun {{ $tahun }}</title>
        <style>
            body {
                font-family: sans-serif;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid black;
                padding: 6px;
                text-align: center;
            }
        </style>
    </head>

    <body>
        <h3>Laporan {{ ucfirst($kategori) }} Bulan {{ $bulan }} Tahun {{ $tahun }}</h3>
        <table>
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
    </body>

</html>
