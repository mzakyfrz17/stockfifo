@extends('layouts.template')

@section('content')
    <div class="container">
        <!-- Dashboard -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <h5 class="mb-0">Dashboard</h5>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success mb-3" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <p class="mb-0">Selamat datang, <strong>{{ Auth::user()->name }}</strong>. Anda berhasil login!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cek Stok Minimal -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <h5 class="mb-0 text-dark">
                            <i class="bi bi-box-seam"></i> Cek Stok Minimal - {{ ucfirst($kategori) }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <!-- Filter hanya untuk Manager -->
                            @if ($role === 'manager')
                                <form method="GET" action="{{ route('home') }}" class="d-flex">
                                    <select name="kategori" class="form-select me-2" onchange="this.form.submit()">
                                        <option value="bar" {{ $kategori == 'bar' ? 'selected' : '' }}>Bar</option>
                                        <option value="kitchen" {{ $kategori == 'kitchen' ? 'selected' : '' }}>Kitchen
                                        </option>
                                        <option value="roti" {{ $kategori == 'roti' ? 'selected' : '' }}>Roti</option>
                                    </select>
                                </form>
                            @endif

                            <!-- Tombol Cetak PDF -->
                            <a href="{{ route('home.cetakPdf', ['kategori' => $kategori]) }}" target="_blank"
                                class="btn btn-sm btn-danger">
                                <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                            </a>
                        </div>

                        <!-- Tabel Stok -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle basic-datatables">
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
                                    @forelse ($hasil as $row)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ $row['kd_barang'] }}</span></td>
                                            <td>{{ $row['nama'] }}</td>
                                            <td>{{ $row['satuan'] }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $row['stok_sisa'] <= $row['stok_minimal'] ? 'bg-danger' : 'bg-warning text-dark' }}">
                                                    {{ $row['stok_sisa'] }}
                                                </span>
                                            </td>
                                            <td>{{ $row['stok_minimal'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">
                                                <i class="bi bi-check-circle"></i> Tidak ada barang yang mendekati stok
                                                minimal
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
