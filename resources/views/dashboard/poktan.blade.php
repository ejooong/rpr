<!-- resources/views/dashboard/poktan.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Poktan - {{ auth()->user()->nama }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Manajemen anggota dan produksi kelompok tani</p>
    </x-slot>

    <div class="py-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Total Anggota -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-500 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total Anggota</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_anggota'], 0, ',', '.') }} Orang</p>
                    </div>
                </div>
            </div>

            <!-- Total Demplot -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100 text-green-500 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total Demplot</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_demplot'], 0, ',', '.') }} Lahan</p>
                    </div>
                </div>
            </div>

            <!-- Luas Lahan Total -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-yellow-100 text-yellow-500 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600">Luas Lahan Total</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['luas_lahan_total'], 2, ',', '.') }} Ha</p>
                    </div>
                </div>
            </div>

            <!-- Total Produksi -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-purple-100 text-purple-500 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total Produksi</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_produksi'] ?? 0, 2, ',', '.') }} Ton</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produksi Poktan -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Produksi Kelompok Tahun {{ date('Y') }}</h3>
                @if($produksiPoktan->count() > 0)
                    <div class="h-64">
                        <canvas id="produksiPoktanChart"></canvas>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500">Belum ada data produksi untuk tahun ini</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('poktan.anggota') }}" class="bg-white rounded-lg shadow p-6 border border-gray-200 hover:border-blue-500 transition duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4 group-hover:bg-blue-500 group-hover:text-white transition duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Kelola Anggota</h3>
                        <p class="text-sm text-gray-600">Lihat dan kelola data anggota kelompok tani</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('poktan.produksi') }}" class="bg-white rounded-lg shadow p-6 border border-gray-200 hover:border-green-500 transition duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4 group-hover:bg-green-500 group-hover:text-white transition duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Data Produksi</h3>
                        <p class="text-sm text-gray-600">Lihat riwayat produksi anggota kelompok</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('poktan.demplot') }}" class="bg-white rounded-lg shadow p-6 border border-gray-200 hover:border-yellow-500 transition duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4 group-hover:bg-yellow-500 group-hover:text-white transition duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Data Demplot</h3>
                        <p class="text-sm text-gray-600">Kelola data demplot percontohan</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Recent Activity -->
        @if(isset($recentProduksi) && $recentProduksi->count() > 0)
        <div class="mt-6 bg-white rounded-lg shadow">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Produksi Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petani</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentProduksi as $produksi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($produksi->tanggal_input)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $produksi->komoditas->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $produksi->demplot->petani->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($produksi->total_produksi, 2) }} Ton
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if($produksiPoktan->count() > 0)
    @push('scripts')
    <script>
        // Produksi Poktan Chart
        const poktanCtx = document.getElementById('produksiPoktanChart').getContext('2d');
        const poktanChart = new Chart(poktanCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($produksiPoktan->pluck('nama')) !!},
                datasets: [{
                    label: 'Produksi (Ton)',
                    data: {!! json_encode($produksiPoktan->pluck('total')) !!},
                    backgroundColor: [
                        '#1e3a8a', '#1e40af', '#1d4ed8', '#2563eb', '#3b82f6',
                        '#60a5fa', '#93c5fd', '#bfdbfe'
                    ],
                    borderColor: '#1e3a8a',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Ton'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Komoditas'
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
                                return `Produksi: ${context.parsed.y} Ton`;
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endpush
    @endif
</x-app-layout>