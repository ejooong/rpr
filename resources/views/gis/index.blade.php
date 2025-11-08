<!-- resources/views/gis/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Peta GIS Demplot - RPR NasDem
        </h2>
        <p class="text-sm text-gray-600 mt-1">Visualisasi sebaran demplot seluruh Indonesia</p>
    </x-slot>

    <div class="py-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Demplot</p>
                        <p class="text-xl font-bold text-gray-800">{{ $stats['total_demplot'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Demplot Aktif</p>
                        <p class="text-xl font-bold text-gray-800">{{ $stats['demplot_aktif'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Komoditas</p>
                        <p class="text-xl font-bold text-gray-800">{{ $stats['total_komoditas'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-orange-100 text-orange-600 mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Provinsi</p>
                        <p class="text-xl font-bold text-gray-800">{{ $stats['provinsi_tercover'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Demplot</h3>
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sektor</label>
                    <select name="sektor_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Sektor</option>
                        @foreach($sektor as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Komoditas</label>
                    <select name="komoditas_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Komoditas</option>
                        @foreach($komoditas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                    <select name="provinsi_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinsi as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="rencana">Rencana</option>
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </form>
            
            <div class="mt-4 flex space-x-3">
                <button type="button" onclick="applyFilters()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                    Terapkan Filter
                </button>
                <button type="button" onclick="resetFilters()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-200">
                    Reset Filter
                </button>
            </div>
        </div>

        <!-- Map Container -->
        <div class="bg-white rounded-lg shadow p-6">
            <div id="map" class="w-full h-[900px] rounded-lg border border-gray-300"></div>
            <div id="mapInfo" class="mt-4 text-sm text-gray-600">
                Memuat data demplot...
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        let map;
        let markers = [];
        let demplotData = [];

        // Initialize map
        function initMap() {
            map = L.map('map').setView([-2.5489, 118.0149], 5); // Center Indonesia
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            loadDemplotData();
        }

        // Load demplot data
        function loadDemplotData(filters = {}) {
            const url = new URL('{{ route("gis.api.demplot") }}');
            Object.keys(filters).forEach(key => {
                if (filters[key]) url.searchParams.append(key, filters[key]);
            });

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        demplotData = data.data;
                        updateMapMarkers();
                        updateMapInfo(data.total, data.debug);
                    } else {
                        console.error('Error loading data:', data.message);
                        document.getElementById('mapInfo').innerHTML = 
                            '<div class="text-red-600">Error memuat data: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('mapInfo').innerHTML = 
                        '<div class="text-red-600">Error memuat data demplot</div>';
                });
        }

        // Update map markers
        function updateMapMarkers() {
            // Clear existing markers
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            // Add new markers
            demplotData.forEach(demplot => {
                const marker = L.marker([demplot.latitude, demplot.longitude])
                    .bindPopup(demplot.popup_content)
                    .addTo(map);
                
                markers.push(marker);
            });

            // Fit map to show all markers
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds());
            }
        }

        // Update map info
        function updateMapInfo(total, debug) {
            const info = `Menampilkan ${total} demplot`;
            document.getElementById('mapInfo').innerHTML = info;
        }

        // Apply filters
        function applyFilters() {
            const formData = new FormData(document.getElementById('filterForm'));
            const filters = {};
            
            for (let [key, value] of formData.entries()) {
                if (value) filters[key] = value;
            }
            
            loadDemplotData(filters);
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('filterForm').reset();
            loadDemplotData();
        }

        // Focus on marker
        function focusOnMarker(demplotId) {
            const demplot = demplotData.find(d => d.id === demplotId);
            if (demplot) {
                map.setView([demplot.latitude, demplot.longitude], 15);
                const marker = markers.find(m => 
                    m.getLatLng().lat === demplot.latitude && 
                    m.getLatLng().lng === demplot.longitude
                );
                if (marker) marker.openPopup();
            }
        }

        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', initMap);
    </script>
    @endpush
</x-app-layout>