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
                                        <p>Lat: {{ $lat_awal ?? $perjalanan->lat_berangkat }}, Lng:
                                            {{ $lng_awal ?? $perjalanan->lng_berangkat }}</p>
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
                                        <p>Bensin Akhir: <span id="fuel-level">{{ $bensin_akhir ?? 'Tidak Ada' }}</span>%
                                        </p>
                                    </div>
                                </div>
                                <div class="scroll-controls">
                                    <a href="{{ route('perjalanan.index') }}" class="btn btn-secondary">Kembali</a>
                                    <button id="scroll-start" onclick="startJourney()" class="btn btn-primary">Start
                                        Journey</button>
                                    <button id="scroll-stop" onclick="stopJourney()" class="btn btn-danger">Stop
                                        Journey</button>
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
                                <div id="map" style="height: 500px;"></div>
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
                                        <h5>Persentase: <span id="fuel-level">{{ $bensin_akhir ?? 'Tidak Ada' }}</span>%
                                        </h5>
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
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://www.gstatic.com/charts/loader.js"></script>

        <!-- JavaScript -->
        <script>
            let map;
            let journeyInterval;
            let currentMarker;
            let polyline;
            let routeCoordinates = [];
            let isJourneyActive = false;
            let previousLat = null;
            let previousLng = null;
            const mapElement = document.getElementById('map');
            const fuelLevelElement = document.getElementById('fuel-level');
        
            function initMap() {
                map = L.map(mapElement).setView([{{ $lat_awal ?? $perjalanan->lat_berangkat }},
                    {{ $lng_awal ?? $perjalanan->lng_berangkat }}
                ], 13);
        
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
        
                currentMarker = L.marker([{{ $lat_awal ?? $perjalanan->lat_berangkat }},
                    {{ $lng_awal ?? $perjalanan->lng_berangkat }}
                ], {
                    icon: L.divIcon({
                        className: 'custom-div-icon',
                        html: '<i class="fas fa-truck" style="font-size: 38px; color: green;"></i>',
                        iconSize: [30, 42],
                        iconAnchor: [15, 42]
                    })
                }).addTo(map);
        
                // Add start and end markers
                L.marker([{{ $perjalanan->lat_berangkat }}, {{ $perjalanan->lng_berangkat }}], {
                    icon: L.divIcon({
                        className: 'custom-div-icon',
                        html: '<i class="fas fa-map-marker-alt" style="font-size: 38px; color: blue;"></i>',
                        iconSize: [30, 42],
                        iconAnchor: [15, 42]
                    })
                }).addTo(map);
        
                L.marker([{{ $perjalanan->lat_tujuan }}, {{ $perjalanan->lng_tujuan }}], {
                    icon: L.divIcon({
                        className: 'custom-div-icon',
                        html: '<i class="fas fa-flag-checkered" style="font-size: 30px; color: red;"></i>',
                        iconSize: [30, 42],
                        iconAnchor: [15, 42]
                    })
                }).addTo(map);
        
                // Initialize polyline
                polyline = L.polyline([], { color: 'blue' }).addTo(map);
        
                google.charts.load('current', {
                    'packages': ['gauge']
                });
                google.charts.setOnLoadCallback(drawGauge);
        
                function drawGauge() {
                    const data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['Fuel Level', parseFloat(fuelLevelElement.textContent) || 0]
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
        
                    // Fetch and update gauge every 5 seconds
                    setInterval(() => {
                        fetch(`/detail-perjalanan/{{ $perjalanan->id }}`)
                            .then(response => response.json())
                            .then(data => {
                                const fuelLevel = data.persentase_bahan_bakar;
                                fuelLevelElement.textContent = fuelLevel;
        
                                // Set color based on fuel level
                                setFuelLevelColor(fuelLevel);
        
                                // Update gauge
                                data.setValue(0, 1, fuelLevel);
                                chart.draw(data, options);
                            });
                    }, 5000);
                }
            }
        
            function startJourney() {
                if (!isJourneyActive) {
                    isJourneyActive = true;
                    journeyInterval = setInterval(updateJourneyData, 5000);
                }
            }
        
            function stopJourney() {
                if (isJourneyActive) {
                    clearInterval(journeyInterval);
                    journeyInterval = null;
                    isJourneyActive = false;
        
                    // Display route when journey stops
                    polyline.setLatLngs(routeCoordinates);
                    map.fitBounds(polyline.getBounds());
                }
            }
        
            async function updateJourneyData() {
                const response = await fetch(`/detail-perjalanan/{{ $perjalanan->id }}`);
                const data = await response.json();
                const { lat, lng, persentase_bahan_bakar } = data;
        
                // Calculate angle to rotate the marker
                if (previousLat && previousLng) {
                    const bearing = calculateBearing(previousLat, previousLng, lat, lng);
                    currentMarker.setRotationAngle(bearing);
                }
        
                // Update previous coordinates
                previousLat = lat;
                previousLng = lng;
        
                // Update marker position and polyline
                const newLatLng = L.latLng(lat, lng);
                currentMarker.setLatLng(newLatLng);
                routeCoordinates.push(newLatLng);
        
                // Update fuel level
                fuelLevelElement.textContent = persentase_bahan_bakar;
            }
        
            // Function to calculate bearing between two points
            function calculateBearing(lat1, lng1, lat2, lng2) {
                const radLat1 = lat1 * (Math.PI / 180);
                const radLat2 = lat2 * (Math.PI / 180);
                const deltaLng = (lng2 - lng1) * (Math.PI / 180);
        
                const y = Math.sin(deltaLng) * Math.cos(radLat2);
                const x = Math.cos(radLat1) * Math.sin(radLat2) - Math.sin(radLat1) * Math.cos(radLat2) * Math.cos(deltaLng);
        
                const bearing = Math.atan2(y, x) * (180 / Math.PI);
                return (bearing + 360) % 360;
            }
        
            initMap();
        </script>
    </div>
@endsection
