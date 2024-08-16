@extends('layouts.main')

@section('content')
    <div class="container mt-5 pt-5">
        <div class="blur-container border border-primary rounded p-4"> <!-- Menambahkan border dan padding -->
            <h2>Edit Data Truk</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('truk.update', $truk->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="plat_no" class="form-label">Plat Nomor</label>
                    <input type="text" class="form-control border border-secondary" id="plat_no" name="plat_no" value="{{ $truk->plat_no }}" required>
                </div>
                <div class="mb-3">
                    <label for="manufaktur" class="form-label">Manufaktur</label>
                    <input type="text" class="form-control border border-secondary" id="manufaktur" name="manufaktur" value="{{ $truk->manufaktur }}" required>
                </div>
                <div class="mb-3">
                    <label for="seri" class="form-label">Seri</label>
                    <input type="text" class="form-control border border-secondary" id="seri" name="seri" value="{{ $truk->seri }}" required>
                </div>
                <div class="mb-3">
                    <label for="tahun_pembuatan" class="form-label">Tahun Pembuatan</label>
                    <textarea class="form-control border border-secondary" id="tahun_pembuatan" name="tahun_pembuatan" rows="3" required>{{ $truk->tahun_pembuatan }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('truk.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection
