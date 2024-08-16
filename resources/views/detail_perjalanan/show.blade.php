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
                                <!-- Data GPS -->
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

        // Titik Sekarang
        let currentLocation = {
            lat: {{ $detail->lat ?? 'null' }},
            lng: {{ $detail->lng ?? 'null' }}
        };

        // Titik Berangkat
        let departurePoint = {
            lat: {{ $detail->perjalanan->lat_berangkat ?? 'null' }},
            lng: {{ $detail->perjalanan->lng_berangkat ?? 'null' }}
        };

        // Titik Tujuan
        let destinationPoint = {
            lat: {{ $detail->perjalanan->lat_tujuan ?? $gudang->lat }},
            lng: {{ $detail->perjalanan->lng_tujuan ?? $gudang->lng }}
        };

        function initMap() {
            map = L.map('map').setView([currentLocation.lat, currentLocation.lng], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Menambahkan marker untuk titik berangkat
            markers.departure = L.marker([departurePoint.lat, departurePoint.lng], {
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: '<i class="fas fa-map-marker-alt" style="font-size: 38px; color: blue;"></i>',
                    iconSize: [30, 42],
                    iconAnchor: [15, 42]
                })
            }).addTo(map);

            // Menambahkan marker untuk titik tujuan
            markers.destination = L.marker([destinationPoint.lat, destinationPoint.lng], {
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: '<i class="fas fa-flag-checkered" style="font-size: 30px; color: red;"></i>',
                    iconSize: [30, 42],
                    iconAnchor: [15, 42]
                })
            }).addTo(map);

            // Menambahkan polygon untuk titik berangkat
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

            // Menambahkan polygon untuk titik tujuan
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

            // Inisialisasi polyline kosong
            polyline = L.polyline([], {
                color: 'green',
                weight: 4,
                opacity: 0.7
            }).addTo(map);
        }

        function stopJourney() {
            if (journeyInterval) {
                clearInterval(journeyInterval);
                journeyInterval = null;
            }

            // Reset polyline dengan koordinat kosong
            polyline.setLatLngs([]);

            // Hapus routingControl jika ada
            if (routingControl) {
                // Keep routingControl on the map but stop route updates
                routingControl.setWaypoints([L.latLng(departurePoint.lat, departurePoint.lng), L.latLng(destinationPoint.lat, destinationPoint.lng)]);
            }

            // Hapus koordinat rute
            routeCoordinates = [];
        }

        function startJourney() {
            if (journeyInterval) return;

            // Reset polyline sebelum memulai perjalanan baru
            polyline.setLatLngs([]);

            // Buat atau refresh routing control
            if (routingControl) {
                routingControl.setWaypoints([L.latLng(departurePoint.lat, departurePoint.lng), L.latLng(destinationPoint.lat, destinationPoint.lng)]);
            } else {
                routingControl = L.Routing.control({
                    waypoints: [L.latLng(departurePoint.lat, departurePoint.lng), L.latLng(destinationPoint.lat, destinationPoint.lng)],
                    routeWhileDragging: true,
                    createMarker: () => null, // Menghilangkan marker waypoint
                    lineOptions: {
                        styles: [{
                            color: 'green',
                            weight: 4,
                            opacity: 0.7
                        }]
                    }
                }).addTo(map);
            }

            routingControl.on('routesfound', function(e) {
                if (e.routes.length > 0) {
                    const route = e.routes[0];
                    const routeCoords = route.getWaypoints().map(w => [w.latLng.lat, w.latLng.lng]);
                    routeCoordinates = routeCoords;
                    polyline.setLatLngs(routeCoords);
                }
            });

            // Mulai interval untuk memperbarui posisi saat ini
            journeyInterval = setInterval(() => {
                // Perbarui posisi saat ini (contoh koordinat baru, bisa diambil dari API atau data aktual)
                // Misalnya, ambil data dari API untuk mengupdate currentLocation.lat dan currentLocation.lng

                // Perbarui posisi marker
                if (currentMarker) {
                    currentMarker.setLatLng([currentLocation.lat, currentLocation.lng]);
                } else {
                    currentMarker = L.marker([currentLocation.lat, currentLocation.lng], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: '<i class="fas fa-truck-moving" style="font-size: 38px; color: green;"></i>',
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        })
                    }).addTo(map);
                }

                // Perbarui informasi di halaman
                document.getElementById('lokasi-berangkat').textContent =
                    `${currentLocation.lat.toFixed(6)}, ${currentLocation.lng.toFixed(6)}`;
            }, 5000); // Update setiap 5 detik
        }

        function drawFuelGauge(level) {
            google.charts.load('current', { 'packages': ['gauge'] });
            google.charts.setOnLoadCallback(function() {
                var data = google.visualization.arrayToDataTable([
                    ['Label', 'Value'],
                    ['Fuel Level', level]
                ]);

                var options = {
                    width: 400,
                    height: 120,
                    redFrom: 0,
                    redTo: 10,
                    yellowFrom: 10,
                    yellowTo: 50,
                    greenFrom: 50,
                    greenTo: 100,
                    minorTicks: 5
                };

                var chart = new google.visualization.Gauge(document.getElementById('gauge_chart'));
                chart.draw(data, options);

                // Update text color based on fuel level
                updateFuelTextColor(level);
            });
        }

        function updateFuelTextColor(level) {
            const fuelLevelElement = document.getElementById('fuel-level');
            
            if (level <= 10) {
                fuelLevelElement.style.color = 'red';
            } else if (level <= 50) {
                fuelLevelElement.style.color = 'yellow';
            } else {
                fuelLevelElement.style.color = 'green';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            const fuelLevel = parseFloat(document.getElementById('fuel-level').textContent);
            drawFuelGauge(fuelLevel);

            // Mulai perjalanan secara default jika perlu
            // startJourney();
        });
    </script>
@endsection
