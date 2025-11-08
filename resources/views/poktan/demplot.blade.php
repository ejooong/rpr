<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Data Demplot - {{ auth()->user()->nama }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Manajemen demplot percontohan anggota kelompok tani</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Total Demplot -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-blue-100 text-blue-500 mr-3">
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

                <!-- Total Luas Lahan -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-green-100 text-green-500 mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600">Total Luas Lahan</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_luas_lahan'], 2, ',', '.') }} Ha</p>
                        </div>
                    </div>
                </div>

                <!-- Demplot Aktif -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-yellow-100 text-yellow-500 mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600">Demplot Aktif</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($stats['demplot_aktif'], 0, ',', '.') }} Lahan</p>
                        </div>
                    </div>
                </div>

                <!-- Demplot Selesai -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-purple-100 text-purple-500 mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600">Demplot Selesai</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($stats['demplot_selesai'], 0, ',', '.') }} Lahan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Data</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="statusFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="rencana" {{ request('status') == 'rencana' ? 'selected' : '' }}>Rencana</option>
                            </select>
                        </div>

                        <!-- Sektor Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sektor</label>
                            <select id="sektorFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Sektor</option>
                                @foreach($sektor as $s)
                                    <option value="{{ $s->id }}" {{ request('sektor_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Komoditas Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Komoditas</label>
                            <select id="komoditasFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Komoditas</option>
                                @foreach($komoditas as $kom)
                                    <option value="{{ $kom->id }}" {{ request('komoditas_id') == $kom->id ? 'selected' : '' }}>
                                        {{ $kom->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="flex items-end">
                            <a href="{{ route('poktan.demplot') }}" class="w-full bg-gray-100 border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition duration-200 text-center">
                                <i class="fas fa-refresh mr-2"></i>Reset Filter
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Data Demplot</h3>
                        <div class="text-sm text-gray-600">
                            Total: {{ $demplots->total() }} data
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Demplot</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petani</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sektor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Luas Lahan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Tanam</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($demplots as $demplot)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $loop->iteration + ($demplots->currentPage() - 1) * $demplots->perPage() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $demplot->nama }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ $demplot->alamat }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $demplot->petani->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $demplot->komoditas->sektor->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $demplot->komoditas->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    {{ number_format($demplot->luas_lahan, 2) }} Ha
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="text-xs">
                                        {{ $demplot->desa->nama ?? '-' }}, {{ $demplot->kecamatan->nama ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $demplot->status_badge }}">
                                        {{ ucfirst($demplot->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($demplot->tanggal_tanam)
                                        {{ \Carbon\Carbon::parse($demplot->tanggal_tanam)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <p class="text-lg font-medium mb-1">Tidak ada data demplot</p>
                                        <p class="text-sm">Data demplot akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($demplots->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Menampilkan 
                            <span class="font-medium">{{ $demplots->firstItem() }}</span>
                            sampai 
                            <span class="font-medium">{{ $demplots->lastItem() }}</span>
                            dari 
                            <span class="font-medium">{{ $demplots->total() }}</span>
                            hasil
                        </div>
                        <div class="flex space-x-2">
                            {{ $demplots->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const sektorFilter = document.getElementById('sektorFilter');
            const komoditasFilter = document.getElementById('komoditasFilter');

            // Function untuk apply filters
            function applyFilters() {
                const status = statusFilter.value;
                const sektor = sektorFilter.value;
                const komoditas = komoditasFilter.value;
                
                const url = new URL(window.location.href);
                
                if (status) {
                    url.searchParams.set('status', status);
                } else {
                    url.searchParams.delete('status');
                }
                
                if (sektor) {
                    url.searchParams.set('sektor_id', sektor);
                } else {
                    url.searchParams.delete('sektor_id');
                }
                
                if (komoditas) {
                    url.searchParams.set('komoditas_id', komoditas);
                } else {
                    url.searchParams.delete('komoditas_id');
                }
                
                window.location.href = url.toString();
            }

            // Event listeners untuk filter
            statusFilter.addEventListener('change', applyFilters);
            sektorFilter.addEventListener('change', applyFilters);
            komoditasFilter.addEventListener('change', applyFilters);

            // Auto-update komoditas filter ketika sektor berubah
            sektorFilter.addEventListener('change', function() {
                const sektorId = this.value;
                if (sektorId) {
                    // Reload page dengan filter sektor untuk mendapatkan komoditas yang sesuai
                    applyFilters();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>