<!-- resources/views/laporan/komoditas-unggulan.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Komoditas Unggulan - RPR NasDem
        </h2>
        <p class="text-sm text-gray-600 mt-1">Analisis komoditas unggulan berdasarkan produksi dan produktivitas</p>
    </x-slot>

    <div class="py-6">
        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form action="{{ route('laporan.unggulan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow p-6 text-white">
                <div class="text-center">
                    <p class="text-blue-200 text-sm">Total Komoditas Unggulan</p>
                    <p class="text-3xl font-bold">{{ $unggulanData->count() }}</p>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg shadow p-6 text-white">
                <div class="text-center">
                    <p class="text-green-200 text-sm">Total Produksi</p>
                    <p class="text-3xl font-bold">{{ number_format($unggulanData->sum('total_produksi'), 0, ',', '.') }} Ton</p>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-600 to-yellow-800 rounded-lg shadow p-6 text-white">
                <div class="text-center">
                    <p class="text-yellow-200 text-sm">Rata-rata Produktivitas</p>
                    <p class="text-3xl font-bold">{{ number_format($unggulanData->avg('rata_produktivitas'), 2, ',', '.') }} Ton/Ha</p>
                </div>
            </div>

            <div class="bg-gradient-to-r from-red-600 to-red-800 rounded-lg shadow p-6 text-white">
                <div class="text-center">
                    <p class="text-red-200 text-sm">Total Luas Panen</p>
                    <p class="text-3xl font-bold">{{ number_format($unggulanData->sum('total_luas'), 0, ',', '.') }} Ha</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Production Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Produksi Komoditas Unggulan</h3>
                <div class="h-80">
                    <canvas id="distribusiProduksiChart"></canvas>
                </div>
            </div>

            <!-- Productivity Comparison -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Perbandingan Produktivitas</h3>
                <div class="h-80">
                    <canvas id="produktivitasChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Ranking Table -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ranking Komoditas Unggulan Tahun {{ $tahun }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peringkat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sektor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Produksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Luas Panen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produktivitas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $totalProduksi = $unggulanData->sum('total_produksi');
                            $rank = 1;
                        @endphp
                        @foreach($unggulanData as $data)
                        @php
                            $kontribusi = $totalProduksi > 0 ? ($data->total_produksi / $totalProduksi) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $rank++ }}</span>
                                    @if($rank <= 4)
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full 
                                        {{ $rank == 2 ? 'bg-yellow-100 text-yellow-800' : 
                                           ($rank == 3 ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $rank == 2 ? '🥈' : ($rank == 3 ? '🥉' : '🏆') }}
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $data->komoditas->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $data->komoditas->sektor->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($data->total_produksi, 0, ',', '.') }} Ton
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($data->total_luas, 2, ',', '.') }} Ha
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($data->rata_produktivitas, 2, ',', '.') }} Ton/Ha
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: {{ $kontribusi }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ number_format($kontribusi, 1) }}%</span>
                                </div>
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
        const unggulanData = {!! json_encode($unggulanData) !!};

        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeDistribusiProduksiChart();
            initializeProduktivitasChart();
        });

        function initializeDistribusiProduksiChart() {
            const ctx = document.getElementById('distribusiProduksiChart').getContext('2d');
            
            const labels = unggulanData.map(item => item.komoditas.nama);
            const data = unggulanData.map(item => item.total_produksi);
            const backgroundColors = generateColors(unggulanData.length);

            const chart = new Chart(ctx, {
                type: 'doughnut',
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
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 15
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
                    },
                    cutout: '50%'
                }
            });
        }

        function initializeProduktivitasChart() {
            const ctx = document.getElementById('produktivitasChart').getContext('2d');
            
            const labels = unggulanData.map(item => item.komoditas.nama);
            const produktivitasData = unggulanData.map(item => item.rata_produktivitas);
            const produksiData = unggulanData.map(item => item.total_produksi);

            const backgroundColors = generateColors(unggulanData.length);

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Produktivitas (Ton/Ha)',
                            data: produktivitasData,
                            backgroundColor: backgroundColors.map(color => color.replace('0.6', '0.8')),
                            borderColor: backgroundColors,
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Total Produksi (Ton)',
                            data: produksiData,
                            backgroundColor: 'rgba(220, 38, 38, 0.2)',
                            borderColor: 'rgb(220, 38, 38)',
                            borderWidth: 1,
                            type: 'line',
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Produktivitas (Ton/Ha)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Total Produksi (Ton)'
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

        function generateColors(count) {
            const colors = [
                'rgba(30, 58, 138, 0.6)',   // NasDem Blue
                'rgba(59, 130, 246, 0.6)',  // Light Blue
                'rgba(220, 38, 38, 0.6)',   // NasDem Red
                'rgba(16, 185, 129, 0.6)',  // Green
                'rgba(245, 158, 11, 0.6)',  // Yellow
                'rgba(139, 92, 246, 0.6)',  // Purple
                'rgba(236, 72, 153, 0.6)',  // Pink
                'rgba(6, 182, 212, 0.6)',   // Cyan
                'rgba(132, 204, 22, 0.6)',  // Lime
                'rgba(251, 191, 36, 0.6)'   // Amber
            ];
            
            // If we need more colors than available, repeat the palette
            const result = [];
            for (let i = 0; i < count; i++) {
                result.push(colors[i % colors.length]);
            }
            return result;
        }

        function resetFilter() {
            document.getElementById('tahun').value = '{{ date('Y') }}';
            document.getElementById('wilayah_id').value = '';
        }
    </script>
    @endpush
</x-app-layout>