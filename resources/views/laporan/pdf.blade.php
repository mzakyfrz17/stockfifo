<!DOCTYPE html>
<html>

<head>
    <title>Laporan {{ ucfirst($kategori) }} Bulan {{ $bulan }} Tahun {{ $tahun }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
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
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
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
                <h3>Laporan {{ ucfirst($kategori) }}</h3>
                <p>Bulan {{ $bulan }} Tahun {{ $tahun }}</p>
            </td>
        </tr>
    </table>

    <!-- TABEL DATA -->
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
