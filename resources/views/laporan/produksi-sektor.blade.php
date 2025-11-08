<!-- resources/views/laporan/produksi-sektor.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Produksi per Sektor - RPR NasDem
        </h2>
        <p class="text-sm text-gray-600 mt-1">Analisis kontribusi setiap sektor pertanian terhadap produksi nasional</p>
    </x-slot>

    <div class="py-6">
        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form action="{{ route('laporan.produksi-sektor') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Tahun -->
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select name="tahun" id="tahun" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
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
                <div class="flex items-end space-x-4">
                    <button type="submit" class="btn-nasdem">
                        Terapkan Filter
                    </button>
                    <button type="button" onclick="resetFilter()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                        Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow p-6 text-white">
                <div class="text-center">
                    <p class="text-blue-200 text-sm">Total Sektor</p>
                    <p class="text-3xl font-bold">{{ $sektorData->count() }}</p>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg shadow p-6 text-white">
                <div class="text-center">
                    <p class="text-green-200 text-sm">Total Produksi</p>
                    <p class="text-3xl font-bold">{{ number_format($sektorData->sum('total_produksi'), 0, ',', '.') }} Ton</p>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-600 to-yellow-800 rounded-lg shadow p-6 text-white">
                <div class="text-center">
                    <p class="text-yellow-200 text-sm">Total Demplot</p>
                    <p class="text-3xl font-bold">{{ number_format($sektorData->sum('jumlah_demplot'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Pie Chart - Distribusi Sektor -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Produksi per Sektor</h3>
                <div class="h-80">
                    <canvas id="distribusiSektorChart"></canvas>
                </div>
            </div>

            <!-- Bar Chart - Perbandingan Sektor -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Perbandingan Sektor</h3>
                <div class="h-80">
                    <canvas id="perbandinganSektorChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Sektor Performance -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Kinerja Sektor Pertanian Tahun {{ $tahun }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sektor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Produksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Demplot</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontribusi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata per Demplot</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $totalProduksi = $sektorData->sum('total_produksi');
                        @endphp
                        @foreach($sektorData as $data)
                        @php
                            $kontribusi = $totalProduksi > 0 ? ($data->total_produksi / $totalProduksi) * 100 : 0;
                            $rataDemplot = $data->jumlah_demplot > 0 ? $data->total_produksi / $data->jumlah_demplot : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $data->sektor }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($data->total_produksi, 0, ',', '.') }} Ton
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($data->jumlah_demplot, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: {{ $kontribusi }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ number_format($kontribusi, 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($rataDemplot, 1, ',', '.') }} Ton
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($kontribusi >= 30)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Unggulan
                                    </span>
                                @elseif($kontribusi >= 15)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Berkembang
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Potensial
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const sektorData = {!! json_encode($sektorData) !!};

        document.addEventListener('DOMContentLoaded', function() {
            initializeDistribusiSektorChart();
            initializePerbandinganSektorChart();
        });

        function initializeDistribusiSektorChart() {
            const ctx = document.getElementById('distribusiSektorChart').getContext('2d');
            
            const labels = sektorData.map(item => item.sektor);
            const data = sektorData.map(item => item.total_produksi);
            const backgroundColors = generateSektorColors(sektorData.length);

            const chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value.toLocaleString()} Ton (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function initializePerbandinganSektorChart() {
            const ctx = document.getElementById('perbandinganSektorChart').getContext('2d');
            
            const labels = sektorData.map(item => item.sektor);
            const produksiData = sektorData.map(item => item.total_produksi);
            const demplotData = sektorData.map(item => item.jumlah_demplot);

            const backgroundColors = generateSektorColors(sektorData.length);

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Total Produksi (Ton)',
                            data: produksiData,
                            backgroundColor: backgroundColors,
                            borderColor: backgroundColors.map(color => color.replace('0.7', '1')),
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Jumlah Demplot',
                            data: demplotData,
                            type: 'line',
                            borderColor: '#dc2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.1)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Produksi (Ton)'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Jumlah Demplot'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });
        }

        function generateSektorColors(count) {
            const colors = [
                'rgba(30, 58, 138, 0.7)',   // Tanaman Pangan
                'rgba(16, 185, 129, 0.7)',  // Hortikultura
                'rgba(245, 158, 11, 0.7)',  // Perkebunan
                'rgba(220, 38, 38, 0.7)',   // Peternakan
                'rgba(139, 92, 246, 0.7)'   // Perikanan
            ];
            
            return colors.slice(0, count);
        }

        function resetFilter() {
            document.getElementById('tahun').value = '{{ date('Y') }}';
            document.getElementById('wilayah_id').value = '';
        }
    </script>
    @endpush
</x-app-layout>