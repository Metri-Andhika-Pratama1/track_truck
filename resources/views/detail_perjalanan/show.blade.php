@extends('layouts.main')

@section('content')
    <div class="dashboard-container">
        <div class="scroll-container">
            <div class="scroll-content">
                <div class="row row1">
                    <div class="col-md-12 gps-section">
                        <h3 style="text-align: center">Data GPS</h3>
                        <div class="gps-content-wrapper">
                            <div id="map" style="height: 500px; width: 100%;"></div>
                            <div id="gps-data">
                                <p>Nama Supir: <strong
                                        id="nama-pengemudi">{{ $detail->perjalanan->supir->nama ?? 'Tidak Ada' }}</strong>
                                </p>
                                <p>No Karyawan: <strong
                                        id="no-karyawan">{{ $detail->perjalanan->supir->no_karyawan ?? 'Tidak Ada' }}</strong>
                                </p>
                                <p>No HP: <strong
                                        id="no-hp">{{ $detail->perjalanan->supir->noHP ?? 'Tidak Ada' }}</strong></p>
                                <p>Alamat Supir: <strong
                                        id="alamat-pengemudi">{{ $detail->perjalanan->supir->alamat ?? 'Tidak Ada' }}</strong>
                                </p>
                                <p>Plat Nomor: <strong
                                        id="plat-nomor">{{ $detail->perjalanan->truk->plat_no ?? 'Tidak Ada' }}</strong></p>
                                <p>Nama Gudang: <strong
                                        id="nama-gudang">{{ $detail->perjalanan->gudang->nama_gudang ?? 'Tidak Ada' }}</strong>
                                </p>
                                <p>Lokasi Berangkat: <strong
                                        id="lokasi-berangkat">{{ $detail->perjalanan->lat_berangkat ?? 'Tidak Ada' }},
                                        {{ $detail->perjalanan->lng_berangkat ?? 'Tidak Ada' }}</strong></p>
                                <p>Lokasi Tujuan: <strong
                                        id="lokasi-tujuan">{{ $detail->perjalanan->lat_tujuan ?? 'Tidak Ada' }},
                                        {{ $detail->perjalanan->lng_tujuan ?? 'Tidak Ada' }}</strong></p>
                                <p>Koordinat Terbaru: <strong
                                        id="current-coordinates">{{ $detail->lat ?? 'Tidak Tersedia' }},
                                        {{ $detail->lng ?? 'Tidak Tersedia' }}</strong></p>
                                <p>Waktu Terbaru: <strong
                                        id="current-timestamp">{{ $detail->timestamp ?? 'Tidak Tersedia' }}</strong></p>
                            </div>

                            <div class="scroll-controls">
                                <button id="scroll-start" onclick="startJourney()">Start Journey</button>
                                <button id="scroll-stop" onclick="stopJourney()">Stop Journey</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row2">
                    <div class="col-md-12 fuel-container">
                        <h3 style="text-align: center">Bahan Bakar</h3>
                        <div class="gauge-container">
                            <div id="gauge_chart"></div>
                        </div>
                        <div id="fuel-level-data" class="fuel-content">
                            <div class="text-section">
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
        let routingControl;
        let currentMarker;
        let routeCoordinates = [];
        let markers = {};
        let polygons = {};

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

            markers.departure = L.marker([departurePoint.lat, departurePoint.lng], {
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: '<i class="fas fa-map-marker-alt" style="font-size: 38px; color: blue;"></i>',
                    iconSize: [30, 42],
                    iconAnchor: [15, 42]
                })
            }).addTo(map);

            markers.destination = L.marker([destinationPoint.lat, destinationPoint.lng], {
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: '<i class="fas fa-flag-checkered" style="font-size: 30px; color: red;"></i>',
                    iconSize: [30, 42],
                    iconAnchor: [15, 42]
                })
            }).addTo(map);

            polygons.departure = L.polygon([
                [departurePoint.lat - 0.01, departurePoint.lng - 0.01],
                [departurePoint.lat + 0.01, departurePoint.lng - 0.01],
                [departurePoint.lat + 0.01, departurePoint.lng + 0.01],
                [departurePoint.lat - 0.01, departurePoint.lng + 0.01]
            ], {
                color: 'blue',
                weight: 2,
                opacity: 0.5,
                fillOpacity: 0.2
            }).addTo(map);

            polygons.destination = L.polygon([
                [destinationPoint.lat - 0.01, destinationPoint.lng - 0.01],
                [destinationPoint.lat + 0.01, destinationPoint.lng - 0.01],
                [destinationPoint.lat + 0.01, destinationPoint.lng + 0.01],
                [destinationPoint.lat - 0.01, destinationPoint.lng + 0.01]
            ], {
                color: 'red',
                weight: 2,
                opacity: 0.5,
                fillOpacity: 0.2
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
                        fuelLevelElement.className = 'fuel-level low-fuel';
                    } else if (fuelLevel <= 30) {
                        fuelLevelElement.className = 'fuel-level medium-fuel';
                    } else {
                        fuelLevelElement.className = 'fuel-level high-fuel';
                    }

                    chart.draw(google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['Fuel Level', fuelLevel]
                    ]), options);
                }, 5000);
            }
        }

        function stopJourney() {
            if (journeyInterval) {
                clearInterval(journeyInterval);
                journeyInterval = null;
            }

            if (routingControl) {
                routingControl.remove();
                routingControl = null;
            }

            // Store the route coordinates for later use
            console.log('Final route coordinates:', routeCoordinates);
        }

        function startJourney() {
            if (journeyInterval) return;

            if (routingControl) {
                routingControl.remove();
                routingControl = null;
            }

            // Reset route coordinates
            routeCoordinates = [];

            currentMarker = L.marker([currentLocation.lat, currentLocation.lng], {
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: '<i class="fas fa-truck" style="font-size: 30px; color: green;"></i>',
                    iconSize: [30, 42],
                    iconAnchor: [15, 42]
                })
            }).addTo(map);


            map.setView([currentLocation.lat, currentLocation.lng], 15);

            // Initialize routing control without drawing polyline between waypoints
            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(departurePoint.lat, departurePoint.lng),
                    L.latLng(destinationPoint.lat, destinationPoint.lng)
                ],
                createMarker: function() {
                    return null; // Do not create default markers
                },
                routeWhileDragging: false,
                show: false
            }).addTo(map);

            journeyInterval = setInterval(() => {
                const nextLat = currentLocation.lat += 0.0001; // Example for next latitude
                const nextLng = currentLocation.lng += 0.0001; // Example for next longitude

                routeCoordinates.push([nextLat, nextLng]);

                currentMarker.setLatLng([nextLat, nextLng]);

                map.setView([nextLat, nextLng], 15);

                console.log('Current route coordinates:', routeCoordinates);
            }, 5000); // Example interval
        }

        initMap();
    </script>
@endsection
