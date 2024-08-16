@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
    <div class="container mt-5 pt-5"> <!-- Tambahkan padding-top untuk memberikan jarak dari navbar -->
        <div class="blur-container border border-primary rounded p-4">
            <!-- Menambahkan border dan padding untuk tampilan lebih baik -->
            <h2>Edit Data Perjalanan</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('details.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="perjalanan_id">Perjalanan</label>
                        <select name="perjalanan_id" id="perjalanan_id" class="form-control">
                            <option value="">-- Pilih Perjalanan --</option>
                            @foreach ($perjalanans as $p)
                                <option value="{{ $p->id }}">{{ $p->id }} - {{ $p->nama_perjalanan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="lat">Latitude</label>
                        <input type="text" class="form-control" name="lat" id="lat"
                            placeholder="Masukkan Latitude">
                    </div>
                    <div class="form-group">
                        <label for="lng">Longitude</label>
                        <input type="text" class="form-control" name="lng" id="lng"
                            placeholder="Masukkan Longitude">
                    </div>
                    <div class="form-group">
                        <label for="minyak">Minyak</label>
                        <input type="text" class="form-control" name="minyak" id="minyak"
                            placeholder="Masukkan Jumlah Minyak">
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('details.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection
