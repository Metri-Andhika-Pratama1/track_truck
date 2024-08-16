@extends('layouts.main')

@section('content')
    <div class="container mt-5 pt-5"> <!-- Tambahkan padding-top untuk memberikan jarak dari navbar -->
        <div class="blur-container border border-primary rounded p-4">
            <!-- Menambahkan border dan padding untuk tampilan lebih baik -->
            <h2>Edit Data Detail Perjalanan</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('details.update', $detail->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="perjalanan_id">Perjalanan</label>
                        <select name="perjalanan_id" id="perjalanan_id"
                            class="form-control @error('perjalanan_id') is-invalid @enderror">
                            <option value="">-- Pilih Perjalanan --</option>
                            @foreach ($perjalanans as $p)
                                <option value="{{ $p->id }}"
                                    {{ $p->id == old('perjalanan_id', $detail->perjalanan_id) ? 'selected' : '' }}>
                                    {{ $p->id }} - {{ $p->supir->nama ?? 'Tidak Ada Supir' }} -
                                    {{ $p->truk->plat_no ?? 'Tidak Ada Plat Nomor' }}
                                </option>
                            @endforeach
                        </select>
                        @error('perjalanan_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="lat">Latitude</label>
                        <input type="text" class="form-control @error('lat') is-invalid @enderror" name="lat"
                            id="lat" value="{{ old('lat', $detail->lat) }}" placeholder="Masukkan Latitude">
                        @error('lat')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="lng">Longitude</label>
                        <input type="text" class="form-control @error('lng') is-invalid @enderror" name="lng"
                            id="lng" value="{{ old('lng', $detail->lng) }}" placeholder="Masukkan Longitude">
                        @error('lng')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="minyak">Minyak</label>
                        <input type="text" class="form-control @error('minyak') is-invalid @enderror" name="minyak"
                            id="minyak" value="{{ old('minyak', $detail->minyak) }}"
                            placeholder="Masukkan Jumlah Minyak">
                        @error('minyak')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('details.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection
