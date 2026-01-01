@extends('layouts.template')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Tambah User</div>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name">Nama</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    placeholder="Masukkan Nama" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Alamat Email</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    placeholder="Masukkan Email" required>
                                <small class="form-text text-muted">Email harus unik dan valid</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="role">Role</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="" disabled selected>Pilih Role</option>
                                    <option value="manager">Manager</option>
                                    <option value="bar">Bar</option>
                                    <option value="kitchen">Kitchen</option>
                                    <option value="roti">Roti</option>
                                </select>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Password" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password_confirmation">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    id="password_confirmation" placeholder="Ulangi Password" required>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
