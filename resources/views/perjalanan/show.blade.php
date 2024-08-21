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
                            <li class="breadcrumb-item">Data Perjalanan</li>
                        </ol>
                    </div>
                </div>
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
                                <h3 class="card-title">Perjalanan #{{ $perjalanan->id }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5>Nama Supir:</h5>
                                        <p>{{ $perjalanan->supir->nama ?? 'Tidak Ada' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Plat Nomor Truk:</h5>
                                        <p>{{ $perjalanan->truk->plat_no ?? 'Tidak Ada' }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5>Gudang Tujuan:</h5>
                                        <p>{{ $perjalanan->gudang->nama_gudang ?? 'Tidak Ada' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Titik Berangkat:</h5>
                                        <p>Lat: {{ $lat_awal ?? $perjalanan->lat_berangkat }}, Lng: {{ $lng_awal ?? $perjalanan->lng_berangkat }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5>Titik Tujuan:</h5>
                                        <p>Lat: {{ $perjalanan->lat_tujuan }}, Lng: {{ $perjalanan->lng_tujuan }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5>Kondisi Bensin:</h5>
                                        <p>Bensin Awal: {{ $perjalanan->bensin_awal ?? 'Tidak Ada' }}%</p>
                                        <p>Bensin Akhir: <span id="fuel-level">{{ $bensin_akhir ?? 'Tidak Ada' }}</span>%</p>
                                    </div>
                                </div>
                                <div class="scroll-controls">
                                    <a href="{{ route('perjalanan.index') }}" class="btn btn-secondary">Kembali</a>
                                    <button id="scroll-start" onclick="startJourney()" class="btn btn-primary">Start Journey</button>
                                    <button id="scroll-stop" onclick="stopJourney()" class="btn btn-danger">Stop Journey</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Peta Perjalanan</h3>
                            </div>
                            <div class="card-body">
                                <div id="map" style="height: 500px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fuel Gauge -->
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
                                        <h5>Persentase: <span id="fuel-level">{{ $bensin_akhir ?? 'Tidak Ada' }}</span>%</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- External Resources -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://www.gstatic.com/charts/loader.js"></script>

        <!-- JavaScript -->
        <script>
            let map;
            let currentMarker;
            let routePolyline;
            let isJourneyActive = false;
            const routePoints = [];
            const pollingInterval = 5000; // 5 detik untuk polling

            document.addEventListener('DOMContentLoaded', () => {
                initMap();
                updateFuelGauge(); // Start updating the fuel gauge on load
            });

            function initMap() {
                const latAwal = {{ $lat_awal ?? $perjalanan->lat_berangkat }};
                const lngAwal = {{ $lng_awal ?? $perjalanan->lng_berangkat }};
                const latTujuan = {{ $perjalanan->lat_tujuan }};
                const lngTujuan = {{ $perjalanan->lng_tujuan }};

                map = L.map('map').setView([latAwal, lngAwal], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                currentMarker = L.marker([latAwal, lngAwal], {
                    icon: L.divIcon({
                        className: 'custom-div-icon',
                        html: '<i class="fas fa-truck" style="font-size: 38px; color: green;"></i>',
                        iconSize: [30, 42],
                        iconAnchor: [15, 42]
                    })
                }).addTo(map);

                L.marker([latAwal, lngAwal], {
                    icon: L.divIcon({
                        className: 'custom-div-icon',
                        html: '<i class="fas fa-map-marker-alt" style="font-size: 38px; color: blue;"></i>',
                        iconSize: [30, 42],
                        iconAnchor: [15, 42]
                    })
                }).addTo(map);

                L.marker([latTujuan, lngTujuan], {
                    icon: L.divIcon({
                        className: 'custom-div-icon',
                        html: '<i class="fas fa-flag-checkered" style="font-size: 30px; color: red;"></i>',
                        iconSize: [30, 42],
                        iconAnchor: [15, 42]
                    })
                }).addTo(map);

                routePolyline = L.polyline([], { color: 'blue' }).addTo(map);

                google.charts.load('current', { packages: ['gauge'] });
                google.charts.setOnLoadCallback(drawGauge);
            }

            function drawGauge() {
                const fuelLevel = parseFloat(document.getElementById('fuel-level').textContent) || 0;

                const data = google.visualization.arrayToDataTable([
                    ['Label', 'Value'],
                    ['Fuel Level', fuelLevel]
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

                setInterval(() => {
                    fetch('/api/fuel-level')
                        .then(response => response.json())
                        .then(data => {
                            const newFuelLevel = data.fuelLevel;
                            document.getElementById('fuel-level').textContent = newFuelLevel;
                            data.setValue(0, 1, newFuelLevel);
                            chart.draw(data, options);
                        });
                }, pollingInterval);
            }

            function updateFuelGauge() {
                fetch(`/api/real-time-data/{{ $perjalanan->id }}`)
                    .then(response => response.json())
                    .then(data => {
                        const { fuelLevel } = data;
                        document.getElementById('fuel-level').textContent = fuelLevel || 0;
                    });
            }

            let intervalId;

            function startJourney() {
                if (isJourneyActive) return; 
                isJourneyActive = true;

                intervalId = setInterval(async () => {
                    const response = await fetch(`/api/real-time-data/{{ $perjalanan->id }}`);
                    const data = await response.json();

                    const { latitude, longitude } = data;
                    const newLatLng = new L.LatLng(latitude, longitude);

                    currentMarker.setLatLng(newLatLng);
                    map.setView(newLatLng, 15);

                    routePoints.push(newLatLng);
                    routePolyline.setLatLngs(routePoints);
                }, pollingInterval);
            }

            function stopJourney() {
                if (!isJourneyActive) return; 
                isJourneyActive = false;

                clearInterval(intervalId);
                routePolyline.setLatLngs(routePoints); 
            }
        </script>
    </div>
@endsection
