@extends('layouts.main')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Perjalanan</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Data Detail Perjalanan</li>
                    </ol>
                </dcdiv><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('details.create') }}" class="btn btn-primary">Tambah Data Detail Perjalanan</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Minyak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($details as $detail)
                            <tr>
                                <td>{{ $detail->lat }}</td>
                                <td>{{ $detail->lng }}</td>
                                <td>{{ $detail->minyak }}</td>
                                <td>
                                    <a href="{{ route('details.show', $detail->id) }}" class="btn btn-info">Detail</a>
                                    <a href="{{ route('details.edit', $detail->id) }}" class="btn btn-warning">Edit</a>
                                    <form action="{{ route('details.destroy', $detail->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus detail perjalanan ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                     <!-- Pagination Links -->
                     <div class="d-flex justify-content-center mt-3">
                        {{ $details->links('pagination::bootstrap-4') }}
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
    <!-- /.row (main row) -->
</div><!-- /.container-fluid -->
@endsection
