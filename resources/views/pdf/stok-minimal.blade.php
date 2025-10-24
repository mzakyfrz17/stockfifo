<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <title>Laporan Stok Minimal</title>
        <style>
            body {
                font-family: sans-serif;
                font-size: 12px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 6px;
                text-align: center;
            }

            th {
                background-color: #eee;
            }
        </style>
    </head>

    <body>
        <h3>Laporan Stok Minimal - {{ ucfirst($kategori) }}</h3>
        <p>Tanggal Cetak: {{ $tanggalCetak }}</p>
        <table>
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama</th>
                    <th>Satuan</th>
                    <th>Stok Sisa</th>
                    <th>Stok Minimal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hasil as $row)
                    <tr>
                        <td>{{ $row['kd_barang'] }}</td>
                        <td>{{ $row['nama'] }}</td>
                        <td>{{ $row['satuan'] }}</td>
                        <td>{{ $row['stok_sisa'] }}</td>
                        <td>{{ $row['stok_minimal'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>

</html>
