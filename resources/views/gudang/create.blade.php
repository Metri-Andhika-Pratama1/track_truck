@extends('layouts.main')

@section('content')
    <div class="container mt-5 pt-5"> <!-- Tambahkan padding-top untuk memberikan jarak dari navbar -->
        <div class="blur-container border border-primary rounded p-4">
            <!-- Menambahkan border dan padding untuk tampilan lebih baik -->
            <h2>Tambah Data Gudang</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('gudang.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama_gudang" class="form-label">Nama Gudang</label>
                    <input type="text" class="form-control" id="nama_gudang" name="nama_gudang" required>
                </div>
                <div class="mb-3">
                    <label for="lat" class="form-label">Latitude</label>
                    <input type="text" class="form-control" id="lat" name="lat" required>
                </div>
                <div class="mb-3">
                    <label for="lng" class="form-label">Longtitude</label>
                    <input type="text" class="form-control" id="lng" name="lng" required>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('gudang.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    @endsection
