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

            <form action="{{ route('perjalanan.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="supir_id">Nama Supir</label>
                    <select name="supir_id" id="supir_id" class="form-control form-control-lg" required>
                        <option value="">Pilih Supir</option>
                        @foreach ($supirs as $supir)
                            <option value="{{ $supir->id }}">{{ $supir->nama }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('supir_id'))
                        <span class="text-danger">{{ $errors->first('supir_id') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="truk_id">Plat Nomor Truk</label>
                    <select name="truk_id" id="truk_id" class="form-control form-control-lg" required>
                        <option value="">Pilih Truk</option>
                        @foreach ($truks as $truk)
                            <option value="{{ $truk->id }}">{{ $truk->plat_no }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('truk_id'))
                        <span class="text-danger">{{ $errors->first('truk_id') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="gudang_id">Gudang Tujuan</label>
                    <select name="gudang_id" id="gudang_id" class="form-control form-control-lg" required>
                        <option value="">Pilih Gudang</option>
                        @foreach ($gudangs as $gudang)
                            <option value="{{ $gudang->id }}">{{ $gudang->nama_gudang }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('gudang_id'))
                        <span class="text-danger">{{ $errors->first('gudang_id') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="lat_berangkat">Latitude Berangkat</label>
                    <input type="text" name="lat_berangkat" id="lat_berangkat" class="form-control form-control-lg"
                        placeholder="Masukkan Latitude Berangkat" required>
                    @if ($errors->has('lat_berangkat'))
                        <span class="text-danger">{{ $errors->first('lat_berangkat') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="lng_berangkat">Longitude Berangkat</label>
                    <input type="text" name="lng_berangkat" id="lng_berangkat" class="form-control form-control-lg"
                        placeholder="Masukkan Longitude Berangkat" required>
                    @if ($errors->has('lng_berangkat'))
                        <span class="text-danger">{{ $errors->first('lng_berangkat') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="lat_tujuan">Latitude Tujuan</label>
                    <input type="text" name="lat_tujuan" id="lat_tujuan" class="form-control form-control-lg"
                        placeholder="Masukkan Latitude Tujuan" required>
                    @if ($errors->has('lat_tujuan'))
                        <span class="text-danger">{{ $errors->first('lat_tujuan') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="lng_tujuan">Longitude Tujuan</label>
                    <input type="text" name="lng_tujuan" id="lng_tujuan" class="form-control form-control-lg"
                        placeholder="Masukkan Longitude Tujuan" required>
                    @if ($errors->has('lng_tujuan'))
                        <span class="text-danger">{{ $errors->first('lng_tujuan') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="bensin_awal">Bensin Awal (%)</label>
                    <input type="text" name="bensin_awal" id="bensin_awal" class="form-control form-control-lg"
                        placeholder="Masukkan Bensin Awal" required>
                    @if ($errors->has('bensin_awal'))
                        <span class="text-danger">{{ $errors->first('bensin_awal') }}</span>
                    @endif
                </div>

                {{-- <div class="form-group">
                    <label for="bensin_akhir">Bensin Akhir (%)</label>
                    <input type="text" name="bensin_akhir" id="bensin_akhir" class="form-control form-control-lg"
                        placeholder="Masukkan Bensin Akhir" required>
                    @if ($errors->has('bensin_akhir'))
                        <span class="text-danger">{{ $errors->first('bensin_akhir') }}</span>
                    @endif
                </div> --}}

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('perjalanan.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection
