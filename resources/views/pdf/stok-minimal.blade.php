<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Stok Minimal</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
        }

        .logo {
            width: 90px;
        }

        .logo img {
            height: 65px;
        }

        .title {
            text-align: left;
            padding-left: 10px;
        }

        .title h3 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .title p {
            margin: 3px 0 0;
            font-size: 11px;
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
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td class="logo">
                <img src="{{ public_path('assets/img/ktb1.png') }}" alt="Logo">
            </td>
            <td class="title">
                <h3>Laporan Stok Minimal</h3>
                <p>Kategori: {{ ucfirst($kategori) }}</p>
                <p>Tanggal Cetak: {{ $tanggalCetak }}</p>
            </td>
        </tr>
    </table>

    <!-- TABEL DATA -->
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
