@extends('layouts.print')

@section('content')
    <div class="print-container">
        <h1 style="text-align: center;">Laporan Detail Perjalanan</h1>

        <div class="detail-section">
            <h3>Perjalanan #{{ $detail->perjalanan->id }}</h3>
            <p><strong>Nama Supir:</strong> {{ $detail->perjalanan->supir->nama }}</p>
            <p><strong>Plat Nomor Truk:</strong> {{ $detail->perjalanan->truk->plat_no }}</p>
            <p><strong>Gudang Tujuan:</strong> {{ $detail->perjalanan->gudang->nama_gudang }}</p>
            <p><strong>Titik Berangkat:</strong> Lat: {{ $detail->perjalanan->lat_berangkat }}, 
                Lng: {{ $detail->perjalanan->lng_berangkat }}</p>
            <p><strong>Titik Tujuan:</strong> Lat: {{ $detail->perjalanan->gudang->lat }}, 
                Lng: {{ $detail->perjalanan->gudang->lng }}</p>
            <p><strong>Kondisi Minyak:</strong> 
                Minyak Awal: {{ $detail->perjalanan->bensin_awal }}%, 
                Minyak Akhir: {{ $detail->perjalanan->bensin_akhir }}%</p>
        </div>

        <div class="print-button-container" style="text-align: center;">
            <button onclick="window.print();" class="btn btn-primary">Cetak</button>
        </div>
    </div>
@endsection

<style>
    body {
        font-family: Arial, sans-serif;
    }

    .print-container {
        width: 100%;
        padding: 20px;
        margin: auto;
    }

    h1, h3 {
        margin-bottom: 20px;
    }

    .detail-section p {
        font-size: 16px;
        margin-bottom: 10px;
    }

    .print-button-container {
        margin-top: 30px;
    }

    @media print {
        .print-button-container {
            display: none;
        }
    }
</style>
