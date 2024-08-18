@extends('layouts.main')

@section('content')
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

            <form action="{{ route('perjalanan.update', $perjalanan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="supir_id">Nama Supir:</label>
                            <select name="supir_id" id="supir_id" class="form-control">
                                @foreach($supirs as $supir)
                                    <option value="{{ $supir->id }}" {{ $supir->id == $perjalanan->supir_id ? 'selected' : '' }}>
                                        {{ $supir->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="truk_id">Plat Nomor Truk:</label>
                            <select name="truk_id" id="truk_id" class="form-control">
                                @foreach($truks as $truk)
                                    <option value="{{ $truk->id }}" {{ $truk->id == $perjalanan->truk_id ? 'selected' : '' }}>
                                        {{ $truk->plat_no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="lat_berangkat">Titik Berangkat (Lat):</label>
                            <input type="text" name="lat_berangkat" id="lat_berangkat" class="form-control" value="{{ $perjalanan->lat_berangkat }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lng_berangkat">Titik Berangkat (Lng):</label>
                            <input type="text" name="lng_berangkat" id="lng_berangkat" class="form-control" value="{{ $perjalanan->lng_berangkat }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="gudang_id">Gudang Tujuan:</label>
                            <select name="gudang_id" id="gudang_id" class="form-control" readonly>
                                <option value="{{ $perjalanan->gudang->id }}" selected>{{ $perjalanan->gudang->nama_gudang }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="lat_tujuan">Titik Tujuan (Lat):</label>
                            <input type="text" name="lat_tujuan" id="lat_tujuan" class="form-control" value="{{ $perjalanan->gudang->lat }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="lng_tujuan">Titik Tujuan (Lng):</label>
                            <input type="text" name="lng_tujuan" id="lng_tujuan" class="form-control" value="{{ $perjalanan->gudang->lng }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bensin_awal">Bensin Awal (%):</label>
                            <input type="number" name="bensin_awal" id="bensin_awal" class="form-control" value="{{ $perjalanan->bensin_awal }}" min="0" max="100" required>
                        </div>
                        <div class="col-md-6">
                            <label for="bensin_akhir">Bensin Akhir (%):</label>
                            <input type="number" name="bensin_akhir" id="bensin_akhir" class="form-control" value="{{ $perjalanan->bensin_akhir }}" min="0" max="100">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('perjalanan.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection
