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
                                    <button id="scroll-start" onclick="startJourney()">Start Journey</button>
                                    <button id="scroll-stop" onclick="stopJourney()">Stop Journey</button>

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
            <script>
                let map;
                let journeyInterval;
                let currentMarker;
                let routeCoordinates = [];
                let routePolyline;
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
                    lat: {{ $detail->perjalanan->lat_tujuan ?? 'null' }},
                    lng: {{ $detail->perjalanan->lng_tujuan ?? 'null' }}
                };

                function initMap() {
                    map = L.map('map').setView([currentLocation.lat, currentLocation.lng], 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    L.marker([departurePoint.lat, departurePoint.lng], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: '<i class="fas fa-map-marker-alt" style="font-size: 38px; color: blue;"></i>',
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        })
                    }).addTo(map);

                    L.marker([destinationPoint.lat, destinationPoint.lng], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: '<i class="fas fa-flag-checkered" style="font-size: 30px; color: red;"></i>',
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        })
                    }).addTo(map);

                    // Initialize gauge chart
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

                        // Update the gauge and color periodically without API
                        setInterval(() => {
                            const fuelLevel = {{ $detail->minyak ?? 0 }};
                            const fuelLevelElement = document.getElementById('fuel-level');

                            fuelLevelElement.textContent = fuelLevel;

                            // Update color based on fuel level
                            if (fuelLevel <= 10) {
                                fuelLevelElement.style.color = 'red';
                            } else if (fuelLevel <= 30) {
                                fuelLevelElement.style.color = 'yellow';
                            } else {
                                fuelLevelElement.style.color = 'green';
                            }

                            data.setValue(0, 1, fuelLevel);
                            chart.draw(data, options);
                        }, 5000); // Update every 5 seconds
                    }
                }

                function startJourney() {
                    if (isJourneyActive) return;

                    isJourneyActive = true;

                    currentMarker = L.marker([currentLocation.lat, currentLocation.lng], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: '<i class="fas fa-truck" style="font-size: 38px; color: blue;"></i>',
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        })
                    }).addTo(map);

                    routePolyline = L.polyline(routeCoordinates, {
                        color: 'blue',
                        weight: 3,
                        opacity: 0.7,
                        dashArray: '5, 10'
                    }).addTo(map);

                    map.setView([currentLocation.lat, currentLocation.lng], 15);

                    journeyInterval = setInterval(() => {
                        const nextLat = currentLocation.lat + 0.0001; // Simulate movement
                        const nextLng = currentLocation.lng + 0.0001; // Simulate movement

                        currentLocation.lat = nextLat;
                        currentLocation.lng = nextLng;

                        routeCoordinates.push([nextLat, nextLng]);

                        currentMarker.setLatLng([nextLat, nextLng]);
                        routePolyline.setLatLngs(routeCoordinates);

                        map.setView([nextLat, nextLng], 15);

                        console.log('Current route coordinates:', routeCoordinates);
                    }, 5000); // Update every 5 seconds
                }

                function stopJourney() {
                    if (!isJourneyActive) return;

                    clearInterval(journeyInterval);
                    isJourneyActive = false;

                    if (currentMarker) {
                        currentMarker.remove();
                        currentMarker = null;
                    }

                    if (routePolyline) {
                        routePolyline.remove();
                        routePolyline = null;
                    }

                    // Add final route from start to end
                    if (departurePoint.lat && destinationPoint.lat) {
                        L.Routing.control({
                            waypoints: [
                                L.latLng(departurePoint.lat, departurePoint.lng),
                                L.latLng(destinationPoint.lat, destinationPoint.lng)
                            ],
                            routeWhileDragging: true,
                            lineOptions: {
                                styles: [{ color: 'blue', weight: 4 }]
                            }
                        }).addTo(map);
                    }
                }

                document.addEventListener('DOMContentLoaded', initMap);
            </script>
        </div>
    </div>
@endsection
