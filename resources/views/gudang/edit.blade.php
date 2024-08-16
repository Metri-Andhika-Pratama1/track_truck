@extends('layouts.main')

@section('content')
    <div class="container mt-5 pt-5"> <!-- Tambahkan padding-top untuk memberikan jarak dari navbar -->
        <div class="blur-container border border-primary rounded p-4">
            <!-- Menambahkan border dan padding untuk tampilan lebih baik -->
            <h2>Edit Data Gudang</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('gudang.update', $gudang->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="nama_gudang" class="form-label">Nama Gudang</label>
                    <input type="text" class="form-control" id="nama_gudang" name="nama_gudang"
                        value="{{ old('nama_gudang', $gudang->nama_gudang) }}" required>
                </div>
                <div class="mb-3">
                    <label for="lat" class="form-label">Latitude</label>
                    <input type="text" class="form-control" id="lat" name="lat"
                        value="{{ old('lat', $gudang->lat) }}" required>
                </div>
                <div class="mb-3">
                    <label for="lng" class="form-label">Longitude</label>
                    <input type="text" class="form-control" id="lng" name="lng"
                        value="{{ old('lng', $gudang->lng) }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('gudang.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    @endsection
