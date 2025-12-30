@extends('layouts.template')

@section('content')
    <div class="container">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{-- <h4>bar</h4> --}}
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addbarModal">+ Tambah
                        Bar</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="basic-datatables" class="basic-datatables display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kode bar</th>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Stok Minimal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Kode bar</th>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Stok Minimal</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach ($bar as $row)
                                    <tr>
                                        <td>{{ $row->kd_bar }}</td>
                                        <td>{{ $row->nama }}</td>
                                        <td>{{ $row->satuan }}</td>
                                        <td>{{ $row->stok_minimal }}</td>
                                        <td>
                                            <div class="d-flex gap-3">
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editbarModal{{ $row->id }}">
                                                    Edit
                                                </button>

                                                <!-- Tombol Hapus -->
                                                <form action="{{ route('bar.destroy', $row->id) }}" method="POST"
                                                    class="form-hapus">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                            <!-- Tombol Edit -->

                                        </td>
                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="editbarModal{{ $row->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('bar.update', $row->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit bar</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label>Kode bar</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ $row->kd_bar }}" readonly>

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
                                                        <button type="button" class="btn btn-secondary"
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
    <div class="modal fade" id="addbarModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('bar.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah bar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- <div class="mb-3">
                            <label>Kode bar</label>
                            <input type="text" name="kd_bar" class="form-control" required>
                        </div> --}}
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
