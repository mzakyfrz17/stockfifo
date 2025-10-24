@extends('layouts.template')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Profil Saya</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Nama</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label>Email</label>
                            <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Role</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label>Tanggal Dibuat</label>
                            <input type="text" class="form-control" value="{{ $user->created_at->format('d M Y H:i') }}"
                                readonly>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">Edit Profil</a>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
@endsection
