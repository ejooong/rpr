<!-- resources/views/laporan/tren-komoditas.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tren Komoditas Unggulan - RPR NasDem
        </h2>
        <p class="text-sm text-gray-600 mt-1">Analisis perkembangan komoditas unggulan dari waktu ke waktu</p>
    </x-slot>

    <div class="py-6">
        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form action="{{ route('laporan.tren') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Tahun Awal -->
                <div>
                    <label for="tahun_awal" class="block text-sm font-medium text-gray-700 mb-1">Tahun Awal</label>
                    <select name="tahun_awal" id="tahun_awal" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for($i = date('Y'); $i >= 2010; $i--)
                            <option value="{{ $i }}" {{ $tahunAwal == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Tahun Akhir -->
                <div>
                    <label for="tahun_akhir" class="block text-sm font-medium text-gray-700 mb-1">Tahun Akhir</label>
                    <select name="tahun_akhir" id="tahun_akhir" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for($i = date('Y'); $i >= 2010; $i--)
                            <option value="{{ $i }}" {{ $tahunAkhir == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Komoditas -->
                <div>
                    <label for="komoditas_id" class="block text-sm font-medium text-gray-700 mb-1">Komoditas</label>
                    <select name="komoditas_id" id="komoditas_id" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Komoditas</option>
                        @foreach($komoditas as $k)
                            <option value="{{ $k->id }}" {{ request('komoditas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Wilayah -->
                <div>
                    <label for="wilayah_id" class="block text-sm font-medium text-gray-700 mb-1">Wilayah</label>
                    <select name="wilayah_id" id="wilayah_id" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Nasional</option>
                        @foreach($wilayah as $w)
                            <option value="{{ $w->id }}" {{ request('wilayah_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="md:col-span-4 flex space-x-4">
                    <button type="submit" class="btn-nasdem flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Terapkan Filter
                    </button>
                    <button type="button" onclick="resetFilter()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                        Reset
                    </button>
                    <button type="button" onclick="showExportModal()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                </div>
            </form>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Line Chart - Tren Produksi -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Produksi Komoditas ({{ $tahunAwal }} - {{ $tahunAkhir }})</h3>
                <div class="h-80">
                    <canvas id="trenProduksiChart"></canvas>
                </div>
            </div>

            <!-- Bar Chart - Perbandingan Komoditas -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Perbandingan Produksi per Tahun</h3>
                <div class="h-80">
                    <canvas id="perbandinganChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Productivity Chart -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Produktivitas (Ton/Ha)</h3>
            <div class="h-80">
                <canvas id="produktivitasChart"></canvas>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Detail Tren Komoditas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Produksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produktivitas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertumbuhan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="dataTableBody">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        // Data from backend
        const trenData = {!! json_encode($trenData) !!};
        const tahunAwal = {{ $tahunAwal }};
        const tahunAkhir = {{ $tahunAkhir }};

        // Process data for charts
        const processedData = processTrenData(trenData);
        
        // Initialize charts
        initializeTrenProduksiChart(processedData);
        initializePerbandinganChart(processedData);
        initializeProduktivitasChart(processedData);
        populateDataTable(processedData);

        function processTrenData(rawData) {
            const years = [];
            for (let year = tahunAwal; year <= tahunAkhir; year++) {
                years.push(year);
            }

            const komoditasMap = {};
            const productivityMap = {};
            const yearlyData = {};

            // Initialize structure
            years.forEach(year => {
                yearlyData[year] = {};
            });

            // Process raw data
            Object.keys(rawData).forEach(year => {
                rawData[year].forEach(item => {
                    const komoditasName = item.komoditas.nama;
                    
                    if (!komoditasMap[komoditasName]) {
                        komoditasMap[komoditasName] = {
                            label: komoditasName,
                            data: years.map(y => 0),
                            borderColor: getRandomColor(),
                            backgroundColor: getRandomColor(0.1),
                            tension: 0.4,
                            fill: false
                        };
                    }

                    if (!productivityMap[komoditasName]) {
                        productivityMap[komoditasName] = {
                            label: komoditasName,
                            data: years.map(y => 0),
                            borderColor: getRandomColor(),
                            backgroundColor: getRandomColor(0.1),
                            tension: 0.4,
                            fill: false
                        };
                    }

                    const yearIndex = years.indexOf(parseInt(year));
                    if (yearIndex !== -1) {
                        komoditasMap[komoditasName].data[yearIndex] = parseFloat(item.total_produksi) || 0;
                        productivityMap[komoditasName].data[yearIndex] = parseFloat(item.rata_produktivitas) || 0;
                        yearlyData[year][komoditasName] = {
                            produksi: parseFloat(item.total_produksi) || 0,
                            produktivitas: parseFloat(item.rata_produktivitas) || 0
                        };
                    }
                });
            });

            return {
                years,
                komoditas: Object.values(komoditasMap),
                productivity: Object.values(productivityMap),
                yearlyData
            };
        }

        function initializeTrenProduksiChart(data) {
            const ctx = document.getElementById('trenProduksiChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.years,
                    datasets: data.komoditas
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Produksi (Ton)'
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
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y.toLocaleString()} Ton`;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }

        function initializePerbandinganChart(data) {
            const ctx = document.getElementById('perbandinganChart').getContext('2d');
            
            // Prepare data for stacked bar chart
            const datasets = data.komoditas.map(komoditas => ({
                label: komoditas.label,
                data: komoditas.data,
                backgroundColor: komoditas.backgroundColor,
                borderColor: komoditas.borderColor,
                borderWidth: 1
            }));

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.years,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'Tahun'
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Produksi (Ton)'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y.toLocaleString()} Ton`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function initializeProduktivitasChart(data) {
            const ctx = document.getElementById('produktivitasChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.years,
                    datasets: data.productivity
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Produktivitas (Ton/Ha)'
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
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y.toFixed(2)} Ton/Ha`;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }

        function populateDataTable(data) {
            const tbody = document.getElementById('dataTableBody');
            tbody.innerHTML = '';

            let allRows = [];

            data.years.forEach(year => {
                Object.keys(data.yearlyData[year]).forEach(komoditas => {
                    const item = data.yearlyData[year][komoditas];
                    const prevYear = data.yearlyData[year - 1];
                    const prevProduksi = prevYear && prevYear[komoditas] ? prevYear[komoditas].produksi : 0;
                    
                    let pertumbuhan = 0;
                    if (prevProduksi > 0) {
                        pertumbuhan = ((item.produksi - prevProduksi) / prevProduksi) * 100;
                    }

                    allRows.push({
                        year,
                        komoditas,
                        produksi: item.produksi,
                        produktivitas: item.produktivitas,
                        pertumbuhan
                    });
                });
            });

            // Sort by year and komoditas
            allRows.sort((a, b) => {
                if (a.year !== b.year) return b.year - a.year;
                return a.komoditas.localeCompare(b.komoditas);
            });

            // Populate table
            allRows.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${row.year}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${row.komoditas}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${row.produksi.toLocaleString()} Ton</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${row.produktivitas.toFixed(2)} Ton/Ha</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${row.pertumbuhan >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${row.pertumbuhan >= 0 ? '+' : ''}${row.pertumbuhan.toFixed(1)}%
                        </span>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function getRandomColor(alpha = 1) {
            const colors = [
                '#1e3a8a', '#3b82f6', '#dc2626', '#16a34a', '#d97706',
                '#7c3aed', '#ec4899', '#06b6d4', '#84cc16', '#f59e0b'
            ];
            return colors[Math.floor(Math.random() * colors.length)];
        }

        function resetFilter() {
            document.getElementById('tahun_awal').value = '{{ date("Y") - 5 }}';
            document.getElementById('tahun_akhir').value = '{{ date("Y") }}';
            document.getElementById('komoditas_id').value = '';
            document.getElementById('wilayah_id').value = '';
        }

        function showExportModal() {
            // Buat modal sederhana
            const modalHtml = `
                <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3 text-center">
                            <h3 class="text-lg font-medium text-gray-900">Export Data Tren Komoditas</h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">Pilih format export:</p>
                            </div>
                            <div class="flex justify-center space-x-4 mt-6">
                                <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-200 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Excel
                                </button>
                                <button onclick="exportToPDF()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-200 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    PDF
                                </button>
                            </div>
                            <div class="flex justify-center mt-4">
                                <button onclick="closeExportModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Tambahkan modal ke body
            const modalDiv = document.createElement('div');
            modalDiv.id = 'exportModal';
            modalDiv.innerHTML = modalHtml;
            document.body.appendChild(modalDiv);
        }

        function closeExportModal() {
            const modal = document.getElementById('exportModal');
            if (modal) {
                modal.remove();
            }
        }

        function exportToExcel() {
            const tahunAwal = document.getElementById('tahun_awal').value;
            const tahunAkhir = document.getElementById('tahun_akhir').value;
            const komoditasId = document.getElementById('komoditas_id').value;
            const wilayahId = document.getElementById('wilayah_id').value;
            
            const baseUrl = '{{ route("export.tren.excel") }}';
            const params = new URLSearchParams({
                tahun_awal: tahunAwal,
                tahun_akhir: tahunAkhir,
                komoditas_id: komoditasId,
                wilayah_id: wilayahId
            });
            
            window.location.href = `${baseUrl}?${params.toString()}`;
            closeExportModal();
        }

        function exportToPDF() {
            const tahunAwal = document.getElementById('tahun_awal').value;
            const tahunAkhir = document.getElementById('tahun_akhir').value;
            const komoditasId = document.getElementById('komoditas_id').value;
            const wilayahId = document.getElementById('wilayah_id').value;
            
            const baseUrl = '{{ route("export.tren.pdf") }}';
            const params = new URLSearchParams({
                tahun_awal: tahunAwal,
                tahun_akhir: tahunAkhir,
                komoditas_id: komoditasId,
                wilayah_id: wilayahId
            });
            
            window.location.href = `${baseUrl}?${params.toString()}`;
            closeExportModal();
        }

        // Close modal ketika klik di luar
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('exportModal');
            if (modal && event.target === modal) {
                closeExportModal();
            }
        });

        // Real-time updates
        setInterval(() => {
            // This would typically make an AJAX call to update data
            console.log('Checking for data updates...');
        }, 30000); // Check every 30 seconds
    </script>
    @endpush
</x-app-layout>