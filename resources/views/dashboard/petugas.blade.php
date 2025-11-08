<!-- resources/views/dashboard/petugas.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Petugas - {{ auth()->user()->wilayah->nama ?? 'Wilayah' }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Manajemen data produksi di wilayah kerja Anda</p>
    </x-slot>

    <div class="py-6">
        <!-- Filter Tahun -->
        <div class="mb-6 bg-white p-4 rounded-lg shadow">
            <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div>
                    <label for="tahun" class="text-sm font-medium text-gray-700">Filter Tahun:</label>
                    <select name="tahun" id="tahun" onchange="this.form.submit()" 
                        class="ml-2 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                
                <!-- Filter Status Demplot -->
                <div>
                    <label for="status_filter" class="text-sm font-medium text-gray-700">Status Demplot:</label>
                    <select name="status_filter" id="status_filter" onchange="filterMap()"
                        class="ml-2 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="rencana">Rencana</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <!-- Filter Komoditas -->
                <div>
                    <label for="komoditas_filter" class="text-sm font-medium text-gray-700">Komoditas:</label>
                    <select name="komoditas_filter" id="komoditas_filter" onchange="filterMap()"
                        class="ml-2 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Komoditas</option>
                        @foreach($komoditasList as $komoditas)
                            <option value="{{ $komoditas->id }}">{{ $komoditas->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Produksi Wilayah -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Produksi</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_produksi'], 0, ',', '.') }} Ton</p>
                    </div>
                </div>
            </div>

            <!-- Total Petani -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Petani</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_petani'], 0, ',', '.') }} Orang</p>
                    </div>
                </div>
            </div>

            <!-- Total Demplot -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Demplot</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_demplot'], 0, ',', '.') }} Lahan</p>
                    </div>
                </div>
            </div>

            <!-- Demplot Aktif -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-500 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Demplot Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['demplot_aktif'], 0, ',', '.') }} Lahan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peta GIS Demplot -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Peta Sebaran Demplot</h3>
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-1"></div>
                        <span>Aktif</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-1"></div>
                        <span>Rencana</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-1"></div>
                        <span>Selesai</span>
                    </div>
                </div>
            </div>
            <div id="map" class="w-full h-96 rounded-lg border border-gray-300"></div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                <div>Total ditampilkan: <span id="markerCount" class="font-semibold">0</span> demplot</div>
                <div>Klik marker untuk detail</div>
                <div class="text-right">
                    <a href="{{ route('gis.demplot') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Lihat Peta Lengkap →
                    </a>
                </div>
            </div>
        </div>

        <!-- Komoditas Unggulan & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Komoditas Unggulan -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Komoditas Unggulan Wilayah ({{ $tahun }})</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Produksi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($komoditasUnggulan as $komoditas)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $komoditas->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($komoditas->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Ton</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data komoditas unggulan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-6">
                <a href="{{ route('petani.create') }}" class="block bg-white rounded-lg shadow p-6 border border-gray-200 hover:border-blue-500 transition duration-200 group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4 group-hover:bg-blue-500 group-hover:text-white transition duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">Tambah Petani</h3>
                            <p class="text-sm text-gray-600">Input data petani baru</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('demplot.create') }}" class="block bg-white rounded-lg shadow p-6 border border-gray-200 hover:border-green-500 transition duration-200 group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4 group-hover:bg-green-500 group-hover:text-white transition duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">Tambah Demplot</h3>
                            <p class="text-sm text-gray-600">Input lahan demplot baru</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('produksi.create') }}" class="block bg-white rounded-lg shadow p-6 border border-gray-200 hover:border-yellow-500 transition duration-200 group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4 group-hover:bg-yellow-500 group-hover:text-white transition duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">Input Produksi</h3>
                            <p class="text-sm text-gray-600">Catat hasil produksi</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <style>
        #map { 
            height: 400px; 
            z-index: 1;
        }
        .leaflet-container {
            font-family: inherit;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script>
        let map, markersCluster;
        let allDemplots = @json($demplotsMap);

        function initMap() {
            // Inisialisasi peta - center ke lokasi pertama atau default
            const firstDemplot = allDemplots.length > 0 ? allDemplots[0] : null;
            const center = firstDemplot ? 
                [firstDemplot.latitude, firstDemplot.longitude] : 
                [-6.2088, 106.8456];
            
            map = L.map('map').setView(center, 10);

            // Tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Inisialisasi marker cluster
            markersCluster = L.markerClusterGroup({
                chunkedLoading: true,
                maxClusterRadius: 50
            });

            map.addLayer(markersCluster);

            // Load demplot data
            loadDemplotsToMap();
        }

        function getStatusColor(status) {
            switch(status) {
                case 'aktif': return '#10b981';
                case 'rencana': return '#f59e0b';
                case 'selesai': return '#ef4444';
                default: return '#6b7280';
            }
        }

        function getStatusIcon(status) {
            const color = getStatusColor(status);
            return L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${color}; width: 12px; height: 12px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
                iconSize: [18, 18],
                iconAnchor: [9, 9]
            });
        }

        function loadDemplotsToMap() {
            // Clear existing markers
            markersCluster.clearLayers();

            const statusFilter = document.getElementById('status_filter').value;
            const komoditasFilter = document.getElementById('komoditas_filter').value;

            let filteredDemplots = allDemplots.filter(demplot => {
                if (statusFilter && demplot.status !== statusFilter) return false;
                if (komoditasFilter && demplot.komoditas_id != komoditasFilter) return false;
                return demplot.latitude && demplot.longitude;
            });

            // Add markers to cluster
            filteredDemplots.forEach(demplot => {
                const marker = L.marker([demplot.latitude, demplot.longitude], {
                    icon: getStatusIcon(demplot.status)
                });

                const popupContent = `
                    <div class="p-2 min-w-48">
                        <h4 class="font-semibold text-gray-800">${demplot.nama_lahan}</h4>
                        <div class="mt-2 space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Petani:</span>
                                <span class="font-medium">${demplot.petani.nama}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Komoditas:</span>
                                <span class="font-medium">${demplot.komoditas.nama}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Luas:</span>
                                <span class="font-medium">${demplot.luas_lahan} Ha</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-medium capitalize" style="color: ${getStatusColor(demplot.status)}">${demplot.status}</span>
                            </div>
                        </div>
                        <div class="mt-3 flex space-x-2">
                            <a href="/demplot/${demplot.id}" class="flex-1 bg-blue-600 text-white text-center py-1 px-2 rounded text-xs hover:bg-blue-700 transition">
                                Detail
                            </a>
                            <a href="/demplot/${demplot.id}/edit" class="flex-1 bg-green-600 text-white text-center py-1 px-2 rounded text-xs hover:bg-green-700 transition">
                                Edit
                            </a>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);
                markersCluster.addLayer(marker);
            });

            // Update marker count
            document.getElementById('markerCount').textContent = filteredDemplots.length;

            // Fit map bounds to show all markers
            if (filteredDemplots.length > 0) {
                const group = new L.featureGroup(markersCluster.getLayers());
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        function filterMap() {
            loadDemplotsToMap();
        }

        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });
    </script>
    @endpush
</x-app-layout>