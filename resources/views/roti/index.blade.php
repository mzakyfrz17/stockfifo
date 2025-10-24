@extends('layouts.template')

@section('content')
    <div class="container">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{-- <h4>Roti</h4> --}}
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addRotiModal">+ Tambah
                        Roti</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="basic-datatables" class="basic-datatables display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Roti</th>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Stok Minimal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Kode Roti</th>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Stok Minimal</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach ($roti as $row)
                                    <tr>
                                        <td>{{ $row->kd_roti }}</td>
                                        <td>{{ $row->nama }}</td>
                                        <td>{{ $row->satuan }}</td>
                                        <td>{{ $row->stok_minimal }}</td>
                                        <td>
                                            <!-- Tombol Edit -->
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editRotiModal{{ $row->id }}">
                                                Edit
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('roti.destroy', $row->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Hapus data ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="editRotiModal{{ $row->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('roti.update', $row->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Roti</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label>Kode Roti</label>
                                                            <input type="text" name="kd_roti" class="form-control"
                                                                value="{{ $row->kd_roti }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Nama</label>
                                                            <input type="text" name="nama" class="form-control"
                                                                value="{{ $row->nama }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Satuan</label>
                                                            <input type="text" name="satuan" class="form-control"
                                                                value="{{ $row->satuan }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Stok Minimal</label>
                                                            <input type="number" name="stok_minimal" class="form-control"
                                                                value="{{ $row->stok_minimal }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success">Simpan</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="addRotiModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('roti.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Roti</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Kode Roti</label>
                            <input type="text" name="kd_roti" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Satuan</label>
                            <input type="text" name="satuan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Stok Minimal</label>
                            <input type="number" name="stok_minimal" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
