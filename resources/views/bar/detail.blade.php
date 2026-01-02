@extends('layouts.template')

@section('content')
    <div class="container">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Laporan Detail Stok Bar</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="basic-datatables" class="basic-datatables display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Barang (Kode)</th>
                                    <th>Tanggal</th>
                                    <th>Stok Awal</th>
                                    <th>Barang Masuk</th>
                                    <th>Barang Keluar</th>
                                    <th>Stok Akhir</th>
                                    <th>Stok Minimal</th>
                                    {{-- <th>Satuan</th> --}}
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($laporan as $item)
                                    @foreach ($item['detail'] as $detail)
                                        <tr @if ($detail['stok_akhir'] < $detail['stok_minimal']) style="background:#ffdddd" @endif>
                                            <td>{{ $item['bar']->nama }} ({{ $item['bar']->kd_bar }})</td>
                                            <td>{{ \Carbon\Carbon::parse($detail['tanggal'])->format('d-m-Y') }}</td>
                                            <td>{{ $detail['stok_awal'] }}</td>
                                            <td>
                                                <span>
                                                    {{ $detail['barang_datang'] }} {{ $detail['satuan'] }}
                                                </span><br>

                                                <small class="text-muted">
                                                    {{ $detail['user_masuk'] }} <br>
                                                </small>
                                            </td>

                                            <td>
                                                <span>
                                                    {{ $detail['barang_terpakai'] }} {{ $detail['satuan'] }}
                                                </span><br>

                                                <small class="text-muted">
                                                    {{ $detail['user_keluar'] }}
                                                </small>
                                            </td>
                                            <td>{{ $detail['stok_akhir'] }}</td>
                                            <td>{{ $detail['stok_minimal'] }}</td>
                                            {{-- <td>{{ $detail['satuan'] }}</td> --}}
                                            <td>

                                                {{-- Barang Datang --}}
                                                @if ($detail['barang_datang'] > 0)
                                                    <button class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal"
                                                        data-bs-target="#modalDatang{{ $detail['id'] }}">
                                                        Edit Barang Datang
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-success mb-2" data-bs-toggle="modal"
                                                        data-bs-target="#modalDatang{{ $detail['id'] }}">
                                                        Input Barang Datang
                                                    </button>
                                                @endif

                                                {{-- Barang Terpakai --}}
                                                @if ($detail['barang_terpakai'] > 0)
                                                    <button class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal"
                                                        data-bs-target="#modalTerpakai{{ $detail['id'] }}">
                                                        Edit Barang Terpakai
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-warning mb-2" data-bs-toggle="modal"
                                                        data-bs-target="#modalTerpakai{{ $detail['id'] }}">
                                                        Input Barang Terpakai
                                                    </button>
                                                @endif

                                                {{-- FIFO --}}
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#modalFIFO{{ $detail['id'] }}">
                                                    Detail FIFO
                                                </button>

                                            </td>
                                        </tr>

                                        <!-- Modal Barang Datang -->
                                        <div class="modal fade" id="modalDatang{{ $detail['id'] }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('bar.masuk') }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                {{ $detail['barang_datang'] > 0 ? 'Edit' : 'Input' }}
                                                                Barang Datang
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><b>{{ $item['bar']->nama }}</b>
                                                                ({{ $item['bar']->kd_bar }})
                                                            </p>
                                                            <input type="hidden" name="bar_id"
                                                                value="{{ $item['bar']->id }}">
                                                            <input type="hidden" name="tanggal"
                                                                value="{{ $detail['tanggal'] }}">
                                                            <div class="form-group">
                                                                <label>Jumlah Barang Datang</label>
                                                                <input type="number" name="jumlah" class="form-control"
                                                                    value="{{ $detail['barang_datang'] > 0 ? $detail['barang_datang'] : '' }}"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Barang Terpakai -->
                                        <div class="modal fade" id="modalTerpakai{{ $detail['id'] }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('bar.keluar') }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                {{ $detail['barang_terpakai'] > 0 ? 'Edit' : 'Input' }}
                                                                Barang Terpakai
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><b>{{ $item['bar']->nama }}</b>
                                                                ({{ $item['bar']->kd_bar }})</p>
                                                            <input type="hidden" name="bar_id"
                                                                value="{{ $item['bar']->id }}">
                                                            <input type="hidden" name="tanggal"
                                                                value="{{ $detail['tanggal'] }}">
                                                            <div class="form-group">
                                                                <label>Jumlah Barang Terpakai</label>
                                                                <input type="number" name="jumlah" class="form-control"
                                                                    value="{{ $detail['barang_terpakai'] > 0 ? $detail['barang_terpakai'] : '' }}"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-warning">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Modal FIFO dipisah agar tidak error --}}
                        @foreach ($laporan as $item)
                            @foreach ($item['detail'] as $detail)
                                <div class="modal fade" id="modalFIFO{{ $detail['id'] }}" tabindex="-1">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail FIFO</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">

                                                {{-- HEADER --}}
                                                <div class="mb-3">
                                                    <h5 class="mb-1">
                                                        {{ $item['bar']->nama }}
                                                        ({{ $item['bar']->kd_bar }})
                                                    </h5>
                                                    <p class="mb-0">
                                                        <b>Rekomendasi Barang Dipakai (FIFO):</b>
                                                        <span class="badge bg-success">
                                                            Kode {{ $detail['rekomendasi'] }}
                                                        </span>
                                                    </p>
                                                </div>

                                                {{-- TABLE FIFO --}}
                                                <table class="table table-bordered table-striped basic-datatables">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th>Kode Barang</th>
                                                            <th>Tanggal</th>
                                                            <th>Barang Masuk</th>
                                                            <th>Barang Terpakai</th>
                                                            <th>Jumlah</th>
                                                            <th>Sisa</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($detail['fifo'] as $fifo)
                                                            <tr>
                                                                <td>{{ $fifo['kode_barang'] }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($fifo['tanggal'])->format('d-m-Y') }}
                                                                </td>
                                                                <td>{{ $fifo['barang_masuk'] }}</td>
                                                                <td>{{ $fifo['barang_terpakai'] }}</td>
                                                                <td>{{ $fifo['jumlah'] }}</td>
                                                                <td>{{ $fifo['sisa'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
