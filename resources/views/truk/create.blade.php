@extends('layouts.main')

@section('content')

    <div class="container mt-5 pt-5"> <!-- Tambahkan padding-top untuk memberikan jarak dari navbar -->
        <div class="blur-container border border-primary rounded p-4"> <!-- Menambahkan border dan padding untuk tampilan lebih baik -->
            <h2>Add Data Truk</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('truk.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="plat_no" class="form-label">Plat Nomor</label>
                    <input type="text" class="form-control" id="plat_no" name="plat_no" required>
                </div>
                <div class="mb-3">
                    <label for="manufaktur" class="form-label">Manufaktur</label>
                    <input type="text" class="form-control" id="manufaktur" name="manufaktur" required>
                </div>
                <div class="mb-3">
                    <label for="seri" class="form-label">Seri</label>
                    <input type="text" class="form-control" id="seri" name="seri" required>
                </div>
                <div class="mb-3">
                    <label for="tahun_pembuatan" class="form-label">Tahun Pembuatan</label>
                    <textarea class="form-control" id="tahun_pembuatan" name="tahun_pembuatan" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('truk.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection
