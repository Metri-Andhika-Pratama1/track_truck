@extends('layouts.main')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Perjalanan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('perjalanan.index') }}">Data Perjalanan</a></li>
                        <li class="breadcrumb-item active">Perjalanan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Perjalanan #{{ $perjalanan->id }}</h3>
                        </div>
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
                                    <h5>Titik Berangkat:</h5>
                                    <p>Lat: {{ $perjalanan->lat_berangkat }}, Lng: {{ $perjalanan->lng_berangkat }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5>Titik Tujuan:</h5>
                                    <p>Lat: {{ $perjalanan->gudang->lat }}, Lng: {{ $perjalanan->gudang->lng }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5>Kondisi Bensin:</h5>
                                    <p>Bensin Awal: {{ $perjalanan->bensin_awal }}%</p>
                                    <p>Bensin Akhir: {{ $perjalanan->bensin_akhir }}%</p>
                                </div>
                            </div>

                            <a href="{{ route('perjalanan.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peta -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Peta Perjalanan</h3>
                        </div>
                        <div class="card-body">
                            <div id="map" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>

              <!-- Buttons for Start Journey and Stop Journey -->
              <div class="row mb-3">
                <div class="col-md-12">
                    <button id="startJourney" class="btn btn-success">Start Journey</button>
                    <button id="stopJourney" class="btn btn-danger">Stop Journey</button>
                </div>
            </div>
            
            <!-- Grafik -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Grafik Minyak</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="bensinChart" style="height: 400px; width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      // Initialize Map
      var map = L.map('map').setView([{{ $perjalanan->lat_berangkat }}, {{ $perjalanan->lng_berangkat }}], 13);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '© OpenStreetMap'
      }).addTo(map);

      var startMarker = L.marker([{{ $perjalanan->lat_berangkat }}, {{ $perjalanan->lng_berangkat }}]).addTo(map)
          .bindPopup('Titik Berangkat')
          .openPopup();

      var endMarker = L.marker([{{ $perjalanan->gudang->lat }}, {{ $perjalanan->gudang->lng }}]).addTo(map)
          .bindPopup('Titik Tujuan')
          .openPopup();

      // Polyline untuk perjalanan
      var polyline = L.polyline([], {color: 'blue'}).addTo(map);

      // Initialize Chart
      var ctx = document.getElementById('bensinChart').getContext('2d');
      var bensinChart = new Chart(ctx, {
          type: 'line',
          data: {
              labels: @json($labels),
              datasets: [{
                  label: 'Minyak Awal',
                  data: @json($bensinAwal),
                  backgroundColor: 'rgba(54, 162, 235, 0.2)',
                  borderColor: 'rgba(54, 162, 235, 1)',
                  borderWidth: 2,
                  fill: false
              }, {
                  label: 'Minyak Akhir',
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
                          text: 'Jumlah Minyak'
                      },
                      beginAtZero: true
                  }
              }
          }
      });

      // Function to update the polyline with new position
      function updatePolyline(lat, lng) {
          var latLng = new L.LatLng(lat, lng);
          polyline.addLatLng(latLng);
          map.setView(latLng);
      }

      // Real-time update simulation (replace this with real data fetching)
      setInterval(function() {
          // Fetch new data here (e.g., via AJAX)
          // Assume you get newLat and newLng from the server
          var newLat = {{ $perjalanan->gudang->lat }};
          var newLng = {{ $perjalanan->gudang->lng }};

          // Update the polyline with the new position
          updatePolyline(newLat, newLng);
      }, 5000);

      // Button Event Listeners
      document.getElementById('startJourney').addEventListener('click', function() {
          alert('Journey Started!');
          // Tambahkan logika untuk memulai perjalanan di sini
      });

      document.getElementById('stopJourney').addEventListener('click', function() {
          alert('Journey Stopped!');
          // Tambahkan logika untuk menghentikan perjalanan di sini
      });
  });
</script>
@endsection
