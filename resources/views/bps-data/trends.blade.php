{{-- resources/views/bps-data/trends.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tren Data Pertanian - RPR NasDem
        </h2>
        <p class="text-sm text-gray-600 mt-1">Analisis perkembangan data pertanian dari tahun ke tahun</p>
    </x-slot>

    <div class="py-6">
        <!-- Filter Section -->
        <div class="mb-6 bg-white p-6 rounded-lg shadow">
            <form id="trendFilterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <!-- Tahun Range -->
                <div class="md:col-span-2 grid grid-cols-2 gap-4">
                    <div>
                        <label for="tahun_awal" class="block text-sm font-medium text-gray-700">Tahun Awal</label>
                        <input type="number" id="tahun_awal" name="tahun_awal" 
                               value="{{ $tahunAwal }}" min="2000" max="{{ date('Y') }}"
                               class="mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                    </div>
                    <div>
                        <label for="tahun_akhir" class="block text-sm font-medium text-gray-700">Tahun Akhir</label>
                        <input type="number" id="tahun_akhir" name="tahun_akhir" 
                               value="{{ $tahunAkhir }}" min="2000" max="{{ date('Y') }}"
                               class="mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                    </div>
                </div>

                <!-- Metric Selection -->
                <div>
                    <label for="metric" class="block text-sm font-medium text-gray-700">Metrik</label>
                    <select name="metric" id="metric" class="mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="produksi">Total Produksi</option>
                        <option value="luas_lahan">Luas Lahan</option>
                        <option value="produktivitas">Produktivitas</option>
                        <option value="jumlah_komoditas">Jumlah Komoditas</option>
                        <option value="jumlah_kecamatan">Jumlah Kecamatan</option>
                    </select>
                </div>

                <!-- Sektor -->
                <div>
                    <label for="sektor_id" class="block text-sm font-medium text-gray-700">Sektor</label>
                    <select name="sektor_id" id="sektor_id" class="mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="">Semua Sektor</option>
                        @foreach($sektors as $sektor)
                            <option value="{{ $sektor->id }}">{{ $sektor->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Komoditas -->
                <div>
                    <label for="komoditas_id" class="block text-sm font-medium text-gray-700">Komoditas</label>
                    <select name="komoditas_id" id="komoditas_id" class="mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="">Semua Komoditas</option>
                        @foreach($komoditasList as $komoditas)
                            <option value="{{ $komoditas->id }}">{{ $komoditas->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Provinsi -->
                <div>
                    <label for="provinsi_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <select name="provinsi_id" id="provinsi_id" class="provinsi-select mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinsis as $provinsi)
                            <option value="{{ $provinsi->id }}">{{ $provinsi->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="kabupaten_id" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                    <select name="kabupaten_id" id="kabupaten_id" class="kabupaten-select mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" disabled>
                        <option value="">Semua Kabupaten</option>
                    </select>
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="kecamatan_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                    <select name="kecamatan_id" id="kecamatan_id" class="kecamatan-select mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" disabled>
                        <option value="">Semua Kecamatan</option>
                    </select>
                </div>

                <div class="md:col-span-4 flex justify-end space-x-2 pt-4">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium rounded-md border border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Analisis Tren
                    </button>
                    <button type="button" id="resetFilter" class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        Reset Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="hidden text-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-2 text-gray-600">Memuat data tren...</p>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span id="errorText" class="text-red-700"></span>
            </div>
        </div>

        <!-- Summary Cards -->
        <div id="summaryCards" class="hidden grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Tahun -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">Rentang Tahun</p>
                        <p id="totalTahun" class="text-2xl font-bold">-</p>
                        <p class="text-blue-200 text-xs mt-1">Tahun analisis</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Nilai Terakhir -->
            <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p id="metricLabel" class="text-green-200 text-sm">Produksi Terakhir</p>
                        <p id="lastValue" class="text-2xl font-bold">-</p>
                        <p id="lastYear" class="text-green-200 text-xs mt-1">Tahun -</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pertumbuhan -->
            <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-200 text-sm">Pertumbuhan Terakhir</p>
                        <p id="lastGrowth" class="text-2xl font-bold">-</p>
                        <p class="text-purple-200 text-xs mt-1">Dari tahun sebelumnya</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Rata-rata -->
            <div class="bg-gradient-to-r from-orange-600 to-orange-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-200 text-sm">Rata-rata Tahunan</p>
                        <p id="averageValue" class="text-2xl font-bold">-</p>
                        <p class="text-orange-200 text-xs mt-1">Selama periode</p>
                    </div>
                    <div class="p-3 rounded-full bg-orange-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chart Section -->
        <div id="chartSection" class="hidden bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800" id="chartTitle">Tren Data Pertanian</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500" id="dataCount"></span>
                    <button id="exportChart" class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                </div>
            </div>
            <div class="h-96">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Data Table -->
        <div id="tableSection" class="hidden bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Data Tren per Tahun</h3>
                <span class="text-sm text-gray-500" id="tableInfo"></span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produksi (Ton)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Luas Lahan (Ha)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produktivitas (Ton/Ha)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jml Komoditas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertumbuhan</th>
                        </tr>
                    </thead>
                    <tbody id="trendTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Komoditas Trend Section - IMPROVED VERSION -->
        <div id="komoditasSection" class="hidden bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Tren Produksi Komoditas Teratas</h3>
                    <p class="text-sm text-gray-500 mt-1">Analisis perkembangan 10 komoditas dengan produksi tertinggi</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500" id="komoditasCount"></span>
                    <button id="toggleKomoditasLegend" class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Tampilkan Legenda
                    </button>
                    <button id="exportKomoditasChart" class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                </div>
            </div>
            
            <!-- Chart Container -->
            <div class="chart-container">
                <canvas id="komoditasTrendChart"></canvas>
            </div>
            
            <!-- Custom Legend -->
            <div id="customLegend" class="chart-legend-custom mt-4 hidden"></div>
            
            <!-- Statistik Ringkasan -->
            <div id="komoditasStats" class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 hidden">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-blue-800">Komoditas dengan Pertumbuhan Tercepat</span>
                    </div>
                    <p id="fastestGrowing" class="text-lg font-semibold text-blue-900 mt-1">-</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-green-800">Produksi Tertinggi</span>
                    </div>
                    <p id="highestProduction" class="text-lg font-semibold text-green-900 mt-1">-</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-purple-800">Komoditas Paling Stabil</span>
                    </div>
                    <p id="mostStable" class="text-lg font-semibold text-purple-900 mt-1">-</p>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-12">
            <svg class="w-24 h-24 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada data tren</h3>
            <p class="mt-2 text-sm text-gray-500">Pilih filter dan klik "Analisis Tren" untuk melihat perkembangan data</p>
        </div>
    </div>

    @push('styles')
    <style>
        /* Custom styles untuk chart yang lebih baik */
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }

        .trend-indicator {
            display: inline-flex;
            align-items: center;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 4px;
        }

        .trend-up {
            background-color: #dcfce7;
            color: #166534;
        }

        .trend-down {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .trend-stable {
            background-color: #fef3c7;
            color: #d97706;
        }

        .chart-legend-custom {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 16px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .legend-item:hover {
            background: #f1f5f9;
            transform: translateY(-1px);
        }

        .legend-item.hidden {
            opacity: 0.5;
            text-decoration: line-through;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .legend-text {
            font-size: 0.75rem;
            font-weight: 500;
            color: #374151;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Global variables
        let trendChart = null;
        let komoditasTrendChart = null;

        document.addEventListener('DOMContentLoaded', function() {
            initFilterLogic();
            initEventListeners();
        });

        function initFilterLogic() {
            const provinsiSelect = document.getElementById('provinsi_id');
            const kabupatenSelect = document.getElementById('kabupaten_id');
            const kecamatanSelect = document.getElementById('kecamatan_id');

            // Function untuk load kabupaten berdasarkan provinsi
            function loadKabupaten(provinsiId) {
                if (!provinsiId) {
                    kabupatenSelect.innerHTML = '<option value="">Semua Kabupaten</option>';
                    kabupatenSelect.disabled = true;
                    resetKecamatan();
                    return;
                }

                kabupatenSelect.innerHTML = '<option value="">Loading...</option>';
                kabupatenSelect.disabled = true;

                fetch(`/api/kabupaten-by-provinsi/${provinsiId}`)
                    .then(response => response.json())
                    .then(data => {
                        kabupatenSelect.innerHTML = '<option value="">Semua Kabupaten</option>';
                        data.forEach(kabupaten => {
                            const option = document.createElement('option');
                            option.value = kabupaten.id;
                            option.textContent = kabupaten.nama;
                            kabupatenSelect.appendChild(option);
                        });
                        kabupatenSelect.disabled = false;
                        resetKecamatan();
                    })
                    .catch(error => {
                        console.error('Error loading kabupaten:', error);
                        kabupatenSelect.innerHTML = '<option value="">Error loading data</option>';
                    });
            }

            // Function untuk load kecamatan berdasarkan kabupaten
            function loadKecamatan(kabupatenId) {
                if (!kabupatenId) {
                    resetKecamatan();
                    return;
                }

                kecamatanSelect.innerHTML = '<option value="">Loading...</option>';
                kecamatanSelect.disabled = true;

                fetch(`/api/kecamatan-by-kabupaten/${kabupatenId}`)
                    .then(response => response.json())
                    .then(data => {
                        kecamatanSelect.innerHTML = '<option value="">Semua Kecamatan</option>';
                        data.forEach(kecamatan => {
                            const option = document.createElement('option');
                            option.value = kecamatan.id;
                            option.textContent = kecamatan.nama;
                            kecamatanSelect.appendChild(option);
                        });
                        kecamatanSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading kecamatan:', error);
                        kecamatanSelect.innerHTML = '<option value="">Error loading data</option>';
                    });
            }

            function resetKecamatan() {
                kecamatanSelect.innerHTML = '<option value="">Semua Kecamatan</option>';
                kecamatanSelect.disabled = true;
            }

            // Event listeners
            provinsiSelect.addEventListener('change', function() {
                loadKabupaten(this.value);
            });

            kabupatenSelect.addEventListener('change', function() {
                loadKecamatan(this.value);
            });
        }

        function initEventListeners() {
            // Form submission
            document.getElementById('trendFilterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                loadTrendData();
            });

            // Reset filter
            document.getElementById('resetFilter').addEventListener('click', function() {
                document.getElementById('trendFilterForm').reset();
                document.getElementById('kabupaten_id').innerHTML = '<option value="">Semua Kabupaten</option>';
                document.getElementById('kabupaten_id').disabled = true;
                document.getElementById('kecamatan_id').innerHTML = '<option value="">Semua Kecamatan</option>';
                document.getElementById('kecamatan_id').disabled = true;
                
                // Reset UI
                hideAllSections();
                document.getElementById('emptyState').classList.remove('hidden');
            });

            // Export chart
            document.getElementById('exportChart').addEventListener('click', function() {
                if (trendChart) {
                    const link = document.createElement('a');
                    link.download = 'tren-data-pertanian.png';
                    link.href = trendChart.toBase64Image();
                    link.click();
                }
            });

            // Export komoditas chart
            document.getElementById('exportKomoditasChart').addEventListener('click', function() {
                if (komoditasTrendChart) {
                    const link = document.createElement('a');
                    link.download = 'tren-komoditas-teratas.png';
                    link.href = komoditasTrendChart.toBase64Image();
                    link.click();
                }
            });

            // Toggle legend
            document.getElementById('toggleKomoditasLegend').addEventListener('click', function() {
                const legend = document.getElementById('customLegend');
                legend.classList.toggle('hidden');
            });
        }

        function loadTrendData() {
            const formData = new FormData(document.getElementById('trendFilterForm'));
            const params = new URLSearchParams(formData);

            showLoading();
            hideError();

            fetch(`/api/bps-data/trend-data?${params}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        renderTrendData(data);
                    } else {
                        throw new Error(data.error || 'Unknown error occurred');
                    }
                })
                .catch(error => {
                    showError('Gagal memuat data tren: ' + error.message);
                })
                .finally(() => {
                    hideLoading();
                });
        }

        function renderTrendData(data) {
            // Update summary cards
            updateSummaryCards(data);
            
            // Render main trend chart
            renderMainChart(data.trend_data, data.filter.metric);
            
            // Render data table
            renderDataTable(data.trend_data);
            
            // Render komoditas trend jika ada
            if (data.komoditas_trend && data.komoditas_trend.length > 0) {
                renderKomoditasTrend(data.komoditas_trend);
                updateKomoditasStats(data.komoditas_trend);
            }
            
            // Show all sections
            showAllSections();
        }

        function updateSummaryCards(data) {
            const summary = data.summary;
            const trendData = data.trend_data;
            const metric = data.filter.metric;

            // Metric labels
            const metricLabels = {
                'produksi': 'Produksi',
                'luas_lahan': 'Luas Lahan', 
                'produktivitas': 'Produktivitas',
                'jumlah_komoditas': 'Jumlah Komoditas',
                'jumlah_kecamatan': 'Jumlah Kecamatan'
            };

            // Total tahun
            document.getElementById('totalTahun').textContent = summary.total_tahun + ' Tahun';

            // Nilai terakhir
            document.getElementById('metricLabel').textContent = metricLabels[metric] + ' Terakhir';
            document.getElementById('lastValue').textContent = formatValue(summary.produksi_terakhir, metric);
            document.getElementById('lastYear').textContent = 'Tahun ' + (summary.tahun_terakhir || '-');

            // Pertumbuhan
            const growth = summary.pertumbuhan_terakhir;
            if (growth !== null) {
                document.getElementById('lastGrowth').innerHTML = 
                    `${growth >= 0 ? '+' : ''}${growth.toFixed(1)}%`;
                document.getElementById('lastGrowth').className = 
                    `text-2xl font-bold ${growth >= 0 ? 'text-green-300' : 'text-red-300'}`;
            } else {
                document.getElementById('lastGrowth').textContent = '-';
            }

            // Rata-rata
            if (trendData.length > 0) {
                const avg = trendData.reduce((sum, item) => sum + item.current_value, 0) / trendData.length;
                document.getElementById('averageValue').textContent = formatValue(avg, metric);
            }
        }

        function renderMainChart(trendData, metric) {
            const ctx = document.getElementById('trendChart').getContext('2d');
            
            // Destroy previous chart
            if (trendChart) {
                trendChart.destroy();
            }

            const metricLabels = {
                'produksi': 'Produksi (Ton)',
                'luas_lahan': 'Luas Lahan (Ha)',
                'produktivitas': 'Produktivitas (Ton/Ha)',
                'jumlah_komoditas': 'Jumlah Komoditas',
                'jumlah_kecamatan': 'Jumlah Kecamatan'
            };

            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trendData.map(item => item.tahun),
                    datasets: [{
                        label: metricLabels[metric],
                        data: trendData.map(item => item.current_value),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: metricLabels[metric]
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tahun'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: `Tren ${metricLabels[metric]} (${trendData[0]?.tahun} - ${trendData[trendData.length-1]?.tahun})`
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const dataIndex = context.dataIndex;
                                    const growth = trendData[dataIndex].pertumbuhan;
                                    
                                    let label = `${metricLabels[metric]}: ${formatValue(value, metric)}`;
                                    
                                    if (growth !== null) {
                                        label += ` (${growth >= 0 ? '+' : ''}${growth.toFixed(1)}%)`;
                                    }
                                    
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Update chart title and info
            document.getElementById('chartTitle').textContent = `Tren ${metricLabels[metric]}`;
            document.getElementById('dataCount').textContent = `${trendData.length} tahun data`;
        }

        function renderDataTable(trendData) {
            const tbody = document.getElementById('trendTableBody');
            tbody.innerHTML = '';

            trendData.forEach(item => {
                const row = document.createElement('tr');
                
                // Growth indicator
                let growthHtml = '-';
                if (item.pertumbuhan !== null) {
                    const growthClass = item.pertumbuhan >= 0 ? 'text-green-600' : 'text-red-600';
                    const growthIcon = item.pertumbuhan >= 0 ? '↗' : '↘';
                    growthHtml = `<span class="${growthClass} font-medium">${growthIcon} ${Math.abs(item.pertumbuhan).toFixed(1)}%</span>`;
                }

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.tahun}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatNumber(item.produksi)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatNumber(item.luas_lahan)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.produktivitas.toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.jumlah_komoditas}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">${growthHtml}</td>
                `;
                
                tbody.appendChild(row);
            });

            document.getElementById('tableInfo').textContent = `Menampilkan ${trendData.length} tahun data`;
        }

        function renderKomoditasTrend(komoditasTrend) {
            const ctx = document.getElementById('komoditasTrendChart').getContext('2d');
            
            // Destroy previous chart
            if (komoditasTrendChart) {
                komoditasTrendChart.destroy();
            }

            // Get unique years from all data
            const years = [...new Set(komoditasTrend.flatMap(k => k.data.map(d => d.tahun)))].sort();

            // Warna yang lebih variatif dan accessible
            const colorPalette = [
                '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
                '#06b6d4', '#84cc16', '#f97316', '#6366f1', '#ec4899',
                '#14b8a6', '#f43f5e', '#8b5cf6', '#06b6d4', '#84cc16'
            ];

            const datasets = komoditasTrend.map((komoditas, index) => {
                const color = colorPalette[index % colorPalette.length];
                const dataForYears = years.map(year => {
                    const yearData = komoditas.data.find(d => d.tahun === year);
                    return yearData ? yearData.produksi : null;
                });

                // Hitung statistik untuk tooltip
                const validData = dataForYears.filter(d => d !== null);
                const maxValue = Math.max(...validData);
                const minValue = Math.min(...validData);
                const avgValue = validData.reduce((a, b) => a + b, 0) / validData.length;

                return {
                    label: komoditas.nama,
                    data: dataForYears,
                    borderColor: color,
                    backgroundColor: color + '20',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: false,
                    pointBackgroundColor: color,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHitRadius: 10,
                    // Custom metadata untuk tooltip
                    metadata: {
                        maxValue: maxValue,
                        minValue: minValue,
                        avgValue: avgValue,
                        trend: calculateTrend(validData)
                    }
                };
            });

            komoditasTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: years,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        y: {
                            type: 'logarithmic',
                            title: {
                                display: true,
                                text: 'Produksi (Ton) - Skala Logaritmik',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return (value / 1000000).toFixed(1) + 'M';
                                    }
                                    if (value >= 1000) {
                                        return (value / 1000).toFixed(1) + 'K';
                                    }
                                    return value;
                                },
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tahun',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tren Produksi 10 Komoditas Teratas',
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            padding: {
                                bottom: 20
                            }
                        },
                        legend: {
                            display: false // We'll use custom legend
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12,
                            usePointStyle: true,
                            callbacks: {
                                title: function(tooltipItems) {
                                    return `Tahun ${tooltipItems[0].label}`;
                                },
                                label: function(context) {
                                    const dataset = context.dataset;
                                    const value = context.parsed.y;
                                    const metadata = dataset.metadata;
                                    
                                    let label = `${dataset.label}: ${formatNumber(value)} Ton`;
                                    
                                    // Tambahkan informasi tren jika tersedia
                                    if (metadata && metadata.trend) {
                                        const trendIcon = metadata.trend.direction === 'up' ? '↗' : 
                                                       metadata.trend.direction === 'down' ? '↘' : '→';
                                        label += ` ${trendIcon} ${Math.abs(metadata.trend.percentage).toFixed(1)}%`;
                                    }
                                    
                                    return label;
                                },
                                afterLabel: function(context) {
                                    const dataset = context.dataset;
                                    const metadata = dataset.metadata;
                                    const currentIndex = context.dataIndex;
                                    const currentValue = context.parsed.y;
                                    
                                    if (!metadata || currentIndex === 0) return null;
                                    
                                    const previousValue = dataset.data[currentIndex - 1];
                                    if (previousValue === null || previousValue === undefined) return null;
                                    
                                    const change = ((currentValue - previousValue) / previousValue) * 100;
                                    
                                    return `Perubahan: ${change >= 0 ? '+' : ''}${change.toFixed(1)}% dari tahun sebelumnya`;
                                },
                                footer: function(tooltipItems) {
                                    const dataset = tooltipItems[0].dataset;
                                    const metadata = dataset.metadata;
                                    
                                    if (!metadata) return null;
                                    
                                    return [
                                        `Rata-rata: ${formatNumber(metadata.avgValue)} Ton`,
                                        `Maksimum: ${formatNumber(metadata.maxValue)} Ton`,
                                        `Minimum: ${formatNumber(metadata.minValue)} Ton`
                                    ];
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    elements: {
                        line: {
                            cubicInterpolationMode: 'monotone'
                        }
                    }
                }
            });

            // Create custom legend
            createCustomLegend(komoditasTrend, datasets);
            
            // Update komoditas count
            document.getElementById('komoditasCount').textContent = `${komoditasTrend.length} komoditas`;
        }

        function createCustomLegend(komoditasTrend, datasets) {
            const legendContainer = document.getElementById('customLegend');
            legendContainer.innerHTML = '';

            datasets.forEach((dataset, index) => {
                const legendItem = document.createElement('div');
                legendItem.className = 'legend-item';
                legendItem.style.borderLeftColor = dataset.borderColor;
                legendItem.style.borderLeftWidth = '3px';

                const lastValue = dataset.data[dataset.data.length - 1];
                const trend = dataset.metadata?.trend;
                
                let trendHtml = '';
                if (trend) {
                    const trendClass = trend.direction === 'up' ? 'trend-up' : 
                                     trend.direction === 'down' ? 'trend-down' : 'trend-stable';
                    const trendIcon = trend.direction === 'up' ? '↗' : 
                                    trend.direction === 'down' ? '↘' : '→';
                    trendHtml = `<span class="trend-indicator ${trendClass}">${trendIcon} ${Math.abs(trend.percentage).toFixed(1)}%</span>`;
                }

                legendItem.innerHTML = `
                    <div class="legend-color" style="background-color: ${dataset.borderColor}"></div>
                    <span class="legend-text">${dataset.label}</span>
                    ${trendHtml}
                    <span class="ml-2 text-xs text-gray-500">${lastValue ? formatNumber(lastValue) + ' Ton' : 'N/A'}</span>
                `;

                // Add click event to toggle dataset visibility
                legendItem.addEventListener('click', function() {
                    const meta = komoditasTrendChart.getDatasetMeta(index);
                    meta.hidden = meta.hidden === null ? !dataset.hidden : null;
                    komoditasTrendChart.update();
                    legendItem.classList.toggle('hidden');
                });

                legendContainer.appendChild(legendItem);
            });
        }

        function updateKomoditasStats(komoditasTrend) {
            const statsElement = document.getElementById('komoditasStats');
            const fastestElement = document.getElementById('fastestGrowing');
            const highestElement = document.getElementById('highestProduction');
            const stableElement = document.getElementById('mostStable');
            
            if (komoditasTrend.length === 0) {
                statsElement.classList.add('hidden');
                return;
            }
            
            // Hitung statistik
            let fastestGrowth = { name: '-', growth: 0 };
            let highestProduction = { name: '-', production: 0 };
            let mostStable = { name: '-', stability: Infinity };
            
            komoditasTrend.forEach(komoditas => {
                const data = komoditas.data;
                if (data.length < 2) return;
                
                // Hitung pertumbuhan
                const firstProd = data[0].produksi;
                const lastProd = data[data.length - 1].produksi;
                const growth = ((lastProd - firstProd) / firstProd) * 100;
                
                // Hitung stabilitas (standard deviation relative to mean)
                const productions = data.map(d => d.produksi);
                const mean = productions.reduce((a, b) => a + b, 0) / productions.length;
                const variance = productions.reduce((acc, val) => acc + Math.pow(val - mean, 2), 0) / productions.length;
                const stability = Math.sqrt(variance) / mean; // Coefficient of variation
                
                // Update fastest growth
                if (Math.abs(growth) > Math.abs(fastestGrowth.growth)) {
                    fastestGrowth = { 
                        name: komoditas.nama, 
                        growth: growth,
                        icon: growth >= 0 ? '↗' : '↘'
                    };
                }
                
                // Update highest production
                const maxProduction = Math.max(...productions);
                if (maxProduction > highestProduction.production) {
                    highestProduction = { 
                        name: komoditas.nama, 
                        production: maxProduction 
                    };
                }
                
                // Update most stable
                if (stability < mostStable.stability) {
                    mostStable = { 
                        name: komoditas.nama, 
                        stability: stability 
                    };
                }
            });
            
            // Update UI
            fastestElement.textContent = `${fastestGrowth.name} ${fastestGrowth.icon} ${Math.abs(fastestGrowth.growth).toFixed(1)}%`;
            highestElement.textContent = `${highestProduction.name} (${formatNumber(highestProduction.production)} Ton)`;
            stableElement.textContent = mostStable.name;
            
            statsElement.classList.remove('hidden');
        }

        // Fungsi helper untuk menghitung tren
        function calculateTrend(data) {
            if (data.length < 2) return null;
            
            const firstValue = data[0];
            const lastValue = data[data.length - 1];
            const percentage = ((lastValue - firstValue) / firstValue) * 100;
            
            return {
                direction: percentage > 0 ? 'up' : percentage < 0 ? 'down' : 'stable',
                percentage: percentage,
                magnitude: Math.abs(percentage)
            };
        }

        // Helper functions
        function formatValue(value, metric) {
            if (metric === 'produktivitas') {
                return value.toFixed(2) + ' Ton/Ha';
            } else if (metric === 'jumlah_komoditas' || metric === 'jumlah_kecamatan') {
                return Math.round(value).toString();
            } else {
                return formatNumber(value);
            }
        }

        function formatNumber(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + ' JT';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + ' RB';
            }
            return Math.round(num).toLocaleString();
        }

        function showLoading() {
            document.getElementById('loadingIndicator').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingIndicator').classList.add('hidden');
        }

        function showError(message) {
            document.getElementById('errorText').textContent = message;
            document.getElementById('errorMessage').classList.remove('hidden');
        }

        function hideError() {
            document.getElementById('errorMessage').classList.add('hidden');
        }

        function showAllSections() {
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('summaryCards').classList.remove('hidden');
            document.getElementById('chartSection').classList.remove('hidden');
            document.getElementById('tableSection').classList.remove('hidden');
            
            // Only show komoditas section if there's data and no specific komoditas filter
            const komoditasId = document.getElementById('komoditas_id').value;
            if (!komoditasId) {
                document.getElementById('komoditasSection').classList.remove('hidden');
            }
        }

        function hideAllSections() {
            document.getElementById('summaryCards').classList.add('hidden');
            document.getElementById('chartSection').classList.add('hidden');
            document.getElementById('tableSection').classList.add('hidden');
            document.getElementById('komoditasSection').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>