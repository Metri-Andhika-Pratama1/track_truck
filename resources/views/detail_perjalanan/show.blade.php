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
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script>
        let map;
        let journeyInterval;
        let polyline;
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

                // Update the gauge and color periodically
                setInterval(() => {
                    fetch('/api/sensor-data')
                        .then(response => response.json())
                        .then(data => {
                            const fuelLevel = data.fuelLevel; // Adjust based on your API response
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
                        });
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

            if (polyline) {
                polyline.remove();
                polyline = null;
            }

            // Store the route coordinates for later use
            console.log('Final route coordinates:', routeCoordinates);
        }

        function startJourney() {
            if (journeyInterval) return;

            if (polyline) {
                polyline.remove();
                polyline = null;
            }

            if (routingControl) {
                routingControl.remove();
                routingControl = null;
            }

            journeyInterval = setInterval(() => {
                fetch('/api/sensor-data')
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            const {
                                lat,
                                lng,
                                timestamp,
                                fuelLevel
                            } = data;

                            document.getElementById('current-coordinates').textContent = `${lat}, ${lng}`;
                            document.getElementById('current-timestamp').textContent = timestamp;

                            if (currentMarker) {
                                currentMarker.setLatLng([lat, lng]);
                            } else {
                                currentMarker = L.marker([lat, lng]).addTo(map);
                            }

                            routeCoordinates.push([lat, lng]);

                            if (polyline) {
                                polyline.setLatLngs(routeCoordinates);
                            } else {
                                polyline = L.polyline(routeCoordinates, {
                                    color: 'blue'
                                }).addTo(map);
                            }

                            if (routingControl) {
                                routingControl.setWaypoints(routeCoordinates);
                            } else {
                                routingControl = L.Routing.control({
                                    waypoints: routeCoordinates.map(coord => L.latLng(coord[0], coord[
                                        1])),
                                    createMarker: () => null
                                }).addTo(map);
                            }

                            map.fitBounds(polyline.getBounds());
                        }
                    });
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            initMap();
        });
    </script>
@endsection
