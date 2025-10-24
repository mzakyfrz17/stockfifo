@extends('layouts.template')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Edit User</div>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name">Nama</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    value="{{ $user->name }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Alamat Email</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    value="{{ $user->email }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="role">Role</label>
                                <select name="role" id="role" class="form-select">
                                    <option value="manager" {{ $user->role == 'manager' ? 'selected' : '' }}>Manager
                                    </option>
                                    <option value="kitchen" {{ $user->role == 'kitchen' ? 'selected' : '' }}>Kitchen
                                    </option>
                                    <option value="roti" {{ $user->role == 'roti' ? 'selected' : '' }}>Roti</option>
                                    <option value="bar" {{ $user->role == 'bar' ? 'selected' : '' }}>Barista</option>
                                </select>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password">Password (kosongkan jika tidak diubah)</label>
                                <input type="password" name="password" class="form-control" id="password">
                            </div>
                            <div class="form-group mb-3">
                                <label for="password_confirmation">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    id="password_confirmation">
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
