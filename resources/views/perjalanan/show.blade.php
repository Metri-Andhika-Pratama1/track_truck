@extends('layouts.main')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Perjalanan</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('perjalanan.index') }}">Data Perjalanan</a></li>
                        <li class="breadcrumb-item active">Perjalanan</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Perjalanan #{{ $perjalanan->id }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5>Nama Supir:</h5>
                                    <p>{{ $perjalanan->supir->nama }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Plat Nomor Truk:</h5>
                                    <p>{{ $perjalanan->truk->plat_no }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5>Gudang Tujuan:</h5>
                                    <p>{{ $perjalanan->gudang->nama_gudang }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Lokasi Berangkat:</h5>
                                    <p>Lat: {{ $perjalanan->lat_berangkat }}, Lng: {{ $perjalanan->lng_berangkat }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5>Lokasi Tujuan:</h5>
                                    <p>Lat: {{ $perjalanan->lat_tujuan }}, Lng: {{ $perjalanan->lng_tujuan }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5>Minyak:</h5>
                                    <p>Bensin Awal: {{ $perjalanan->bensin_awal }}%</p>
                                    <p>Bensin Akhir: {{ $perjalanan->bensin_akhir }}%</p>
                                </div>
                            </div>

                            <a href="{{ route('perjalanan.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->

            <!-- Grafik -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Grafik Bensin</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <canvas id="bensinChart" style="height: 400px; width: 100%;"></canvas>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row (grafik) -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      var ctx = document.getElementById('bensinChart').getContext('2d');
      var bensinChart = new Chart(ctx, {
          type: 'line',
          data: {
              labels: @json($labels),
              datasets: [{
                  label: 'Bensin Awal',
                  data: @json($bensinAwal),
                  backgroundColor: 'rgba(54, 162, 235, 0.2)',
                  borderColor: 'rgba(54, 162, 235, 1)',
                  borderWidth: 2,
                  fill: false
              }, {
                  label: 'Bensin Akhir',
                  data: @json($bensinAkhir),
                  backgroundColor: 'rgba(255, 99, 132, 0.2)',
                  borderColor: 'rgba(255, 99, 132, 1)',
                  borderWidth: 2,
                  fill: false
              }]
          },
          options: {
              scales: {
                  x: {
                      title: {
                          display: true,
                          text: 'ID Perjalanan'
                      }
                  },
                  y: {
                      title: {
                          display: true,
                          text: 'Jumlah Bensin'
                      },
                      beginAtZero: true
                  }
              }
          }
      });
  });
</script>
@endsection
