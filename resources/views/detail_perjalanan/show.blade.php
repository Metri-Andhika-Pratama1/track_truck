@extends('layouts.main')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Perjalanan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item">Data Detail Perjalanan</li>
                        </ol>
                    </div>
                </div>
                <!-- Tombol Cetak -->
                <button onclick="window.print();" class="btn btn-success">Cetak</button>
            </div>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <!-- Detail Perjalanan -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Perjalanan #{{ $detail->perjalanan->id }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5>Nama Supir:</h5>
                                        <p>{{ $detail->perjalanan->supir->nama }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Plat Nomor Truk:</h5>
                                        <p>{{ $detail->perjalanan->truk->plat_no }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5>Gudang Tujuan:</h5>
                                        <p>{{ $detail->perjalanan->gudang->nama_gudang }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Titik Berangkat:</h5>
                                        <p>Lat: {{ $detail->perjalanan->lat_berangkat }}, Lng:
                                            {{ $detail->perjalanan->lng_berangkat }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5>Titik Tujuan:</h5>
                                        <p>Lat: {{ $detail->perjalanan->gudang->lat }}, Lng:
                                            {{ $detail->perjalanan->gudang->lng }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5>Kondisi Bensin:</h5>
                                        <p>Bensin Awal: {{ $detail->perjalanan->bensin_awal }}%</p>
                                        <p>Bensin Akhir: {{ $detail->minyak }}%</p>
                                    </div>
                                </div>
                                <div class="scroll-controls">
                                    <a href="{{ route('details.index') }}" class="btn btn-secondary">Kembali</a>
                                    <button id="scroll-start" onclick="startJourney()" class="btn btn-primary">Start Journey</button>
                                    <button id="scroll-stop" onclick="stopJourney()" class="btn btn-danger">Stop Journey</button>
                                </div>
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
                                <div id="map" style="height: 900px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bahan Bakar -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 style="text-align: center">Bahan Bakar</h3>
                                <div class="gauge-container">
                                    <div id="gauge_chart"></div>
                                </div>
                                <div id="fuel-level-data" class="fuel-content">
                                    <div class="text-section" style="text-align: center">
                                        <h5>Persentase: <span id="fuel-level">{{ $detail->minyak ?? '0' }}</span>%</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://www.gstatic.com/charts/loader.js"></script>
            <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
            <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
            <script>
                let map;
                let journeyInterval;
                let currentMarker;
                let routeCoordinates = [];
                let routePolyline;
                let routingControl;
                let isJourneyActive = false;
            
                const currentLocation = {
                    lat: {{ $detail->lat ?? 'null' }},
                    lng: {{ $detail->lng ?? 'null' }}
                };
            
                const departurePoint = {
                    lat: {{ $detail->perjalanan->lat_berangkat ?? 'null' }},
                    lng: {{ $detail->perjalanan->lng_berangkat ?? 'null' }}
                };
            
                const destinationPoint = {
                    lat: {{ $detail->perjalanan->gudang->lat ?? 'null' }},
                    lng: {{ $detail->perjalanan->gudang->lng ?? 'null' }}
                };
            
                function initMap() {
                    // Inisialisasi peta pada lokasi detail
                    map = L.map('map').setView([currentLocation.lat, currentLocation.lng], 12);
            
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
            
                    // Inisialisasi marker truk di lokasi detail
                    currentMarker = L.marker([currentLocation.lat, currentLocation.lng], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: '<i class="fas fa-truck" style="font-size: 38px; color: green;"></i>',
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        })
                    }).addTo(map);

                    // Menambahkan marker untuk titik keberangkatan
                    L.marker([departurePoint.lat, departurePoint.lng], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: '<i class="fas fa-map-marker-alt" style="font-size: 38px; color: blue;"></i>',
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        })
                    }).addTo(map);
            
                    // Menambahkan marker untuk titik tujuan
                    L.marker([destinationPoint.lat, destinationPoint.lng], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: '<i class="fas fa-flag-checkered" style="font-size: 30px; color: red;"></i>',
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        })
                    }).addTo(map);
            
                    // Inisialisasi gauge chart
                    google.charts.load('current', {
                        'packages': ['gauge']
                    });
                    google.charts.setOnLoadCallback(drawGauge);
            
                    function drawGauge() {
                        const data = google.visualization.arrayToDataTable([
                            ['Label', 'Value'],
                            ['Fuel Level', {{ $detail->minyak ?? 0 }}]
                        ]);
            
                        const options = {
                            width: 400,
                            height: 120,
                            redFrom: 0,
                            redTo: 10,
                            yellowFrom: 10,
                            yellowTo: 30,
                            greenFrom: 30,
                            greenTo: 100,
                            minorTicks: 5
                        };
            
                        const chart = new google.visualization.Gauge(document.getElementById('gauge_chart'));
                        chart.draw(data, options);
            
                        // Update gauge setiap 5 detik
                        setInterval(() => {
                            fetch('/sensor-data', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                const fuelLevel = data.fuelLevel; // Sesuaikan dengan data yang diterima
                                const fuelLevelElement = document.getElementById('fuel-level');
            
                                fuelLevelElement.textContent = fuelLevel;
            
                                // Update warna berdasarkan level bensin
                                data.setValue(0, 1, fuelLevel);
                                chart.draw(data, options);
                            });
                        }, 5000);
                    }
                }

                // Start the journey
                function startJourney() {
                    if (isJourneyActive) return;
                    isJourneyActive = true;

                    let index = 0;
                    journeyInterval = setInterval(() => {
                        if (index >= routeCoordinates.length) {
                            stopJourney();
                            return;
                        }

                        const { lat, lng } = routeCoordinates[index];
                        currentMarker.setLatLng([lat, lng]);
                        map.setView([lat, lng], 12);

                        index++;
                    }, 1000);
                }

                // Stop the journey
                function stopJourney() {
                    if (!isJourneyActive) return;
                    clearInterval(journeyInterval);
                    isJourneyActive = false;
                }

                // Inisialisasi peta ketika dokumen siap
                document.addEventListener('DOMContentLoaded', function() {
                    initMap();
                });
            </script>
        </div>
    </div>
@endsection
