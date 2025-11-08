<!-- resources/views/dashboard/dpd.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Ketua DPP Bidang Pertanian, Peternakan, dan Kemandirian Desa. - RPR NasDem
        </h2>
        <p class="text-sm text-gray-600 mt-1">Monitoring tren komoditas dan sebaran demplot nasional</p>
    </x-slot>

    <div class="py-6">
        <!-- Filter Tahun -->
        <div class="mb-6 bg-white p-4 rounded-lg shadow">
            <form action="{{ route('dashboard') }}" method="GET" class="flex items-center space-x-4">
                <label for="tahun" class="text-sm font-medium text-gray-700">Filter Tahun:</label>
                <select name="tahun" id="tahun" onchange="this.form.submit()" 
                    class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </form>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">Total Demplot Aktif</p>
                        <p class="text-2xl font-bold">{{ $sebaranDemplot->where('status', 'aktif')->count() }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-red-600 to-red-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-200 text-sm">Komoditas Unggulan</p>
                        <p class="text-2xl font-bold">{{ $trendKomoditas->count() }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-red-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-200 text-sm">Wilayah Tercover</p>
                        <p class="text-2xl font-bold">{{ $sebaranDemplot->unique('wilayah_id')->count() }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-200 text-sm">Total Luas Lahan</p>
                        <p class="text-2xl font-bold">{{ number_format($sebaranDemplot->sum('luas_lahan'), 1) }} Ha</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"></path>
                        </svg>
                    </div>
                </div>
            </div>


            
        </div>

        <!-- Bar Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Bar Chart - Top Komoditas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Top 10 Komoditas Unggulan</h3>
                    <span class="text-sm text-gray-500">Berdasarkan Jumlah Demplot</span>
                </div>
                <div class="h-80">
                    <canvas id="komoditasBarChart"></canvas>
                </div>
            </div>

            <!-- Bar Chart - Status Demplot -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Distribusi Status Demplot</h3>
                    <span class="text-sm text-gray-500">Total: {{ $sebaranDemplot->count() }} Demplot</span>
                </div>
                <div class="h-80">
                    <canvas id="statusBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Statistik Komoditas -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-md font-semibold text-gray-800 mb-4">Statistik Komoditas</h4>
                <div class="space-y-3">
                    @foreach($trendKomoditas->take(5) as $komoditas)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ $komoditas->nama }}</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-800">{{ $komoditas->total_demplot }} demplot</span>
                            <span class="text-xs text-gray-500">({{ number_format($komoditas->total_luas, 1) }} Ha)</span>
                        </div>
                    </div>
                    @endforeach
                    @if($trendKomoditas->count() > 5)
                    <div class="text-center pt-2 border-t">
                        <span class="text-sm text-blue-600">+{{ $trendKomoditas->count() - 5 }} komoditas lainnya</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistik Wilayah -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-md font-semibold text-gray-800 mb-4">Wilayah Terbanyak</h4>
                <div class="space-y-3">
                    @php
                        $wilayahStats = $sebaranDemplot->groupBy('provinsi.nama')->map->count()->sortDesc()->take(5);
                    @endphp
                    @foreach($wilayahStats as $wilayah => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ $wilayah ?: 'Tidak diketahui' }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $count }} demplot</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-md font-semibold text-gray-800 mb-4">Aksi Cepat</h4>
                <div class="space-y-3">
                    <a href="{{ route('laporan.tren') }}" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition duration-200">
                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-800">Lihat Tren Detail</span>
                    </a>
                    <a href="{{ route('gis.demplot') }}" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition duration-200">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-800">Buka Peta Lengkap</span>
                    </a>
                    <a href="{{ route('laporan.demplot') }}" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition duration-200">
                        <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-800">Export Laporan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Peta GIS Mini -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Peta Sebaran Demplot Nasional</h3>
                <a href="{{ route('gis.demplot') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Lihat Peta Lengkap →
                </a>
            </div>
            <div id="miniMap" class="w-full h-96 rounded-lg border border-gray-300"></div>
            <div class="mt-3 flex flex-wrap gap-4 text-sm text-gray-600">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-600 rounded-full mr-2"></div>
                    <span>Tanaman Pangan</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-600 rounded-full mr-2"></div>
                    <span>Hortikultura</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-orange-600 rounded-full mr-2"></div>
                    <span>Perkebunan</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-red-600 rounded-full mr-2"></div>
                    <span>Peternakan</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-purple-600 rounded-full mr-2"></div>
                    <span>Perikanan</span>
                </div>
            </div>
        </div>

        <!-- Recent Demplot Activity -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Demplot Terbaru</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lahan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Luas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sebaranDemplot->take(5) as $demplot)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $demplot->nama_lahan ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $demplot->wilayah->nama ?? 'Lokasi tidak tersedia' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $demplot->komoditas->nama ?? 'Komoditas tidak tersedia' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($demplot->status == 'aktif')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @elseif($demplot->status == 'rencana')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Rencana</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Selesai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $demplot->luas_lahan ?? 0 }} Ha</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data demplot</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
            initMiniMap();
        });

        // Bar Charts
        function initCharts() {
            // Top Komoditas Bar Chart
            const komoditasBarCtx = document.getElementById('komoditasBarChart').getContext('2d');
            
            // Ambil top 10 komoditas
            const topKomoditas = {!! json_encode($trendKomoditas->take(10)->values()) !!};
            
            const komoditasBarChart = new Chart(komoditasBarCtx, {
                type: 'bar',
                data: {
                    labels: topKomoditas.map(k => k.nama),
                    datasets: [{
                        label: 'Jumlah Demplot',
                        data: topKomoditas.map(k => k.total_demplot),
                        backgroundColor: '#3B82F6',
                        borderColor: '#1D4ED8',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Demplot'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Jumlah Demplot: ${context.raw}`;
                                }
                            }
                        }
                    }
                }
            });

            // Status Demplot Bar Chart
            const statusBarCtx = document.getElementById('statusBarChart').getContext('2d');
            const statusData = {
                aktif: {{ $sebaranDemplot->where('status', 'aktif')->count() }},
                rencana: {{ $sebaranDemplot->where('status', 'rencana')->count() }},
                selesai: {{ $sebaranDemplot->where('status', 'selesai')->count() }},
                nonaktif: {{ $sebaranDemplot->where('status', 'nonaktif')->count() }}
            };

            const statusBarChart = new Chart(statusBarCtx, {
                type: 'bar',
                data: {
                    labels: ['Aktif', 'Rencana', 'Selesai', 'Nonaktif'],
                    datasets: [{
                        label: 'Jumlah Demplot',
                        data: [
                            statusData.aktif,
                            statusData.rencana,
                            statusData.selesai,
                            statusData.nonaktif
                        ],
                        backgroundColor: [
                            '#10B981', // green
                            '#F59E0B', // yellow
                            '#3B82F6', // blue
                            '#6B7280'  // gray
                        ],
                        borderColor: [
                            '#047857',
                            '#D97706',
                            '#1D4ED8',
                            '#4B5563'
                        ],
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Jumlah Demplot: ${context.raw}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Mini Map for Demplot Distribution
        function initMiniMap() {
            const map = L.map('miniMap').setView([-2.5489, 118.0149], 5);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Load demplot data for mini map
            fetch('{{ route("gis.api.demplot") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.data.forEach(demplot => {
                            const icon = L.divIcon({
                                className: 'custom-marker',
                                html: `<div style="background-color: ${demplot.warna}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                                iconSize: [16, 16],
                                iconAnchor: [8, 8]
                            });

                            L.marker([demplot.latitude, demplot.longitude], { icon: icon })
                                .bindPopup(`
                                    <div class="text-sm">
                                        <strong>${demplot.nama_lahan}</strong><br>
                                        Komoditas: ${demplot.komoditas}<br>
                                        Status: ${demplot.status}<br>
                                        Luas: ${demplot.luas_lahan} Ha
                                    </div>
                                `)
                                .addTo(map);
                        });

                        if (data.data.length > 0) {
                            const group = new L.featureGroup();
                            data.data.forEach(demplot => {
                                group.addLayer(L.marker([demplot.latitude, demplot.longitude]));
                            });
                            map.fitBounds(group.getBounds());
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading mini map data:', error);
                });
        }
    </script>

    <style>
        .custom-marker {
            background: transparent !important;
            border: none !important;
        }
    </style>
    @endpush
</x-app-layout>