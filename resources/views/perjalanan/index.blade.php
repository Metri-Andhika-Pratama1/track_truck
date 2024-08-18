@extends('layouts.main')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tabel Perjalanan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Data Perjalanan</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main row -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('perjalanan.create') }}" class="btn btn-primary">Tambah Data Perjalanan</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Supir</th>
                                    <th>Plat Nomor</th>
                                    <th>Nama Gudang</th>
                                    <th>Titik Berangkat</th>
                                    <th>Titik Tujuan</th>
                                    <th>Bensin Awal (%)</th>
                                    <th>Bensin Akhir (%)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($perjalanans as $perjalanan)
                                    <tr>
                                        <td>{{ $perjalanan->supir->nama }}</td>
                                        <td>{{ $perjalanan->truk->plat_no }}</td>
                                        <td>{{ $perjalanan->gudang->nama_gudang }}</td>
                                        <td>Lat: {{ $perjalanan->lat_berangkat }}, Lng: {{ $perjalanan->lng_berangkat }}</td>
                                        <td>Lat: {{ $perjalanan->gudang->lat }}, Lng: {{ $perjalanan->gudang->lng }}</td>
                                        <td>{{ $perjalanan->bensin_awal }}</td>
                                        <td>{{ $perjalanan->bensin_akhir }}</td>
                                        <td>
                                            <a href="{{ route('perjalanan.show', $perjalanan->id) }}" class="btn btn-info">Detail</a>
                                            <a href="{{ route('perjalanan.edit', $perjalanan->id) }}" class="btn btn-warning">Edit</a>
                                            <form action="{{ route('perjalanan.destroy', $perjalanan->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $perjalanans->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row (main row) -->
    </div>
@endsection
