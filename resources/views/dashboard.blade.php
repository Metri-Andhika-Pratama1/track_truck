@extends('layouts.main')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Perjalanan</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Data Detail Perjalanan</li> --}}
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Sopir</th>
                                <th>Plat Nomor</th>
                                <th>Latitude Awal</th>
                                <th>Longitude Awal</th>
                                <th>Latitude Akhir</th>
                                <th>Longitude Akhir</th>
                                <th>Bensin Awal</th>
                                <th>Bensin Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($details as $index => $detailGroup)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detailGroup['first']->perjalanan->supir->nama ?? 'Tidak Ada' }}</td>
                                <td>{{ $detailGroup['first']->perjalanan->truk->plat_no ?? 'Tidak Ada' }}</td>
                                <td>{{ $detailGroup['first']->lat }}</td>
                                <td>{{ $detailGroup['first']->lng }}</td>
                                <td>{{ $detailGroup['last']->lat }}</td>
                                <td>{{ $detailGroup['last']->lng }}</td>
                                <td>{{ $detailGroup['first']->perjalanan->bensin_awal}}</td>
                                <td>{{ $detailGroup['last']->minyak }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Grafik Bensin</h3>
                </div>
                <div class="card-body">
                    <canvas id="bensinChart" style="height: 400px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
