@extends('layouts.main')

@section('content')
    <div class="container mt-5 pt-5"> <!-- Tambahkan padding-top untuk memberikan jarak dari navbar -->
        <div class="blur-container border border-primary rounded p-4">
            <!-- Menambahkan border dan padding untuk tampilan lebih baik -->
            <h2>Add Data Supir</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('supir.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="no_karyawan" class="form-label">No Karyawan</label>
                    <input type="text" class="form-control" id="no_karyawan" name="no_karyawan" required>
                </div>
                <div class="mb-3">
                    <label for="noHP" class="form-label">No HP</label>
                    <input type="text" class="form-control" id="noHP" name="noHP" required>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('supir.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    @endsection
