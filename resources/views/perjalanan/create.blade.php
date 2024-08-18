@extends('layouts.main')

@section('content')
    <div class="container mt-5 pt-5"> <!-- Tambahkan padding-top untuk memberikan jarak dari navbar -->
        <div class="blur-container border border-primary rounded p-4">
            <!-- Menambahkan border dan padding untuk tampilan lebih baik -->
            <h2>Tambah Data Perjalanan</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="createPerjalananForm" action="{{ route('perjalanan.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="supir_id">Nama Supir:</label>
                        <select name="supir_id" id="supir_id" class="form-control">
                            @foreach($supirs as $supir)
                                <option value="{{ $supir->id }}">
                                    {{ $supir->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="truk_id">Plat Nomor Truk:</label>
                        <select name="truk_id" id="truk_id" class="form-control">
                            @foreach($truks as $truk)
                                <option value="{{ $truk->id }}">
                                    {{ $truk->plat_no }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="lat_berangkat">Titik Berangkat (Lat):</label>
                        <input type="text" name="lat_berangkat" id="lat_berangkat" class="form-control" value="{{ old('lat_berangkat') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lng_berangkat">Titik Berangkat (Lng):</label>
                        <input type="text" name="lng_berangkat" id="lng_berangkat" class="form-control" value="{{ old('lng_berangkat') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="gudang_id">Gudang Tujuan:</label>
                        <select name="gudang_id" id="gudang_id" class="form-control" required>
                            <option value="" disabled selected>Pilih Gudang Tujuan</option>
                            @foreach($gudangs as $gudang)
                                <option value="{{ $gudang->id }}" data-lat="{{ $gudang->lat }}" data-lng="{{ $gudang->lng }}">
                                    {{ $gudang->nama_gudang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="lat_tujuan">Titik Tujuan (Lat):</label>
                        <input type="text" name="lat_tujuan" id="lat_tujuan" class="form-control" value="{{ old('lat_tujuan') }}" readonly required>
                    </div>
                    <div class="col-md-6">
                        <label for="lng_tujuan">Titik Tujuan (Lng):</label>
                        <input type="text" name="lng_tujuan" id="lng_tujuan" class="form-control" value="{{ old('lng_tujuan') }}" readonly required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="bensin_awal">Bensin Awal (%):</label>
                        <input type="number" name="bensin_awal" id="bensin_awal" class="form-control" value="{{ old('bensin_awal') }}" min="0" max="100" required>
                    </div>
                    <div class="col-md-6">
                        <label for="bensin_akhir">Bensin Akhir (%):</label>
                        <input type="number" name="bensin_akhir" id="bensin_akhir" class="form-control" value="{{ old('bensin_akhir') }}" min="0" max="100">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perjalanan</button>
                <a href="{{ route('perjalanan.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('gudang_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('lat_tujuan').value = selectedOption.getAttribute('data-lat');
            document.getElementById('lng_tujuan').value = selectedOption.getAttribute('data-lng');
        });

        document.getElementById('createPerjalananForm').addEventListener('submit', function(event) {
            const latTujuan = document.getElementById('lat_tujuan').value;
            const lngTujuan = document.getElementById('lng_tujuan').value;
            if (!latTujuan || !lngTujuan) {
                alert('Silakan pilih Gudang Tujuan untuk mengisi Titik Tujuan (Lat) dan (Lng).');
                event.preventDefault(); // Mencegah form submit jika lat/lng tujuan kosong
            }
        });
    </script>
@endsection
