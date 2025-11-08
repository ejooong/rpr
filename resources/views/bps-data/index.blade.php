{{-- resources/views/bps-data/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kelola Data BPS
        </h2>
        <p class="text-sm text-gray-600 mt-1">Manajemen data pertanian dari Badan Pusat Statistik</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            

            <!-- Filter Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('bps-data.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4" id="filterForm">
                        <!-- Tahun Filter -->
                        <div>
                            <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                            <select name="tahun" id="tahun" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunList as $tahunItem)
                                    <option value="{{ $tahunItem }}" {{ request('tahun') == $tahunItem ? 'selected' : '' }}>
                                        {{ $tahunItem }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Provinsi Filter -->
                        <div>
                            <label for="provinsi_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
                            <select name="provinsi_id" id="provinsi_id" class="provinsi-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Provinsi</option>
                                @foreach($provinsis as $provinsi)
                                    <option value="{{ $provinsi->id }}" {{ request('provinsi_id') == $provinsi->id ? 'selected' : '' }}>
                                        {{ $provinsi->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kabupaten Filter -->
                        <div>
                            <label for="kabupaten_id" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                            <select name="kabupaten_id" id="kabupaten_id" 
                                class="kabupaten-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                {{ !request('provinsi_id') ? 'disabled' : '' }}>
                                <option value="">Semua Kabupaten</option>
                                @if(request('provinsi_id') && $kabupatens->count() > 0)
                                    @foreach($kabupatens as $kabupaten)
                                        <option value="{{ $kabupaten->id }}" {{ request('kabupaten_id') == $kabupaten->id ? 'selected' : '' }}>
                                            {{ $kabupaten->nama }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if(request('provinsi_id') && $kabupatens->count() == 0)
                                <p class="text-xs text-gray-500 mt-1">Tidak ada kabupaten untuk provinsi ini</p>
                            @endif
                        </div>

                        <!-- Kecamatan Filter -->
                        <div>
                            <label for="kecamatan_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                            <select name="kecamatan_id" id="kecamatan_id" 
                                class="kecamatan-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                {{ !request('kabupaten_id') ? 'disabled' : '' }}>
                                <option value="">Semua Kecamatan</option>
                                @if(request('kabupaten_id') && $kecamatans->count() > 0)
                                    @foreach($kecamatans as $kecamatan)
                                        <option value="{{ $kecamatan->id }}" {{ request('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>
                                            {{ $kecamatan->nama }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if(request('kabupaten_id') && $kecamatans->count() == 0)
                                <p class="text-xs text-gray-500 mt-1">Tidak ada kecamatan untuk kabupaten ini</p>
                            @endif
                        </div>

                        <!-- Sektor Filter -->
                        <div>
                            <label for="sektor_id" class="block text-sm font-medium text-gray-700">Sektor</label>
                            <select name="sektor_id" id="sektor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Sektor</option>
                                @foreach($sektors as $sektor)
                                    <option value="{{ $sektor->id }}" {{ request('sektor_id') == $sektor->id ? 'selected' : '' }}>
                                        {{ $sektor->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-end space-y-3 md:space-y-0 md:col-span-4">
                            <!-- Bagian Kiri - Filter Controls -->
                            <div class="flex items-end space-x-2">
                                <button type="submit" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    Filter Data
                                </button>
                                <a href="{{ route('bps-data.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    Reset
                                </a>
                            </div>
                            
                            <!-- Bagian Kanan - Data Actions -->
                            <div class="flex items-end space-x-2">
                                <a href="{{ route('bps-data.bulk-delete') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    Hapus Massal
                                </a>
                                <a href="{{ route('bps-data.import') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    Import Excel
                                </a>
                                <a href="{{ route('bps-data.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    + Tambah Data
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

<!-- Search Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Cari Data</h3>
                    </div>
                    
                    <!-- Search Form -->
                    <form action="{{ route('bps-data.index') }}" method="GET" class="flex items-center space-x-2 mt-4">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari berdasarkan provinsi, kabupaten, kecamatan, komoditas, sektor, atau tahun..." 
                               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                            Cari
                        </button>
                        @if(request('search'))
                        <a href="{{ route('bps-data.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                            Reset
                        </a>
                        @endif
                    </form>

                    @if(request('search'))
                    <div class="mt-3">
                        <p class="text-sm text-gray-600">
                            Menampilkan hasil pencarian untuk: <span class="font-medium">"{{ request('search') }}"</span>
                            @if($data->total() > 0)
                                - Ditemukan {{ $data->total() }} data
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($data->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <!-- Tahun Column dengan Sorting -->
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            onclick="sortTable('tahun')">
                                            <div class="flex items-center space-x-1">
                                                <span>Tahun</span>
                                                @if($sortField == 'tahun')
                                                    @if($sortDirection == 'asc')
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    @endif
                                                @endif
                                            </div>
                                        </th>
                                        
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Wilayah
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Level
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Komoditas
                                        </th>
                                        
                                        <!-- Produksi Column dengan Sorting -->
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            onclick="sortTable('produksi')">
                                            <div class="flex items-center space-x-1">
                                                <span>Produksi</span>
                                                @if($sortField == 'produksi')
                                                    @if($sortDirection == 'asc')
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    @endif
                                                @endif
                                            </div>
                                        </th>
                                        
                                        <!-- Luas Lahan Column dengan Sorting -->
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            onclick="sortTable('luas_lahan')">
                                            <div class="flex items-center space-x-1">
                                                <span>Luas Lahan</span>
                                                @if($sortField == 'luas_lahan')
                                                    @if($sortDirection == 'asc')
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    @endif
                                                @endif
                                            </div>
                                        </th>
                                        
                                        <!-- Produktivitas Column dengan Sorting -->
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            onclick="sortTable('produktivitas')">
                                            <div class="flex items-center space-x-1">
                                                <span>Produktivitas</span>
                                                @if($sortField == 'produktivitas')
                                                    @if($sortDirection == 'asc')
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    @endif
                                                @endif
                                            </div>
                                        </th>
                                        
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($data as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $item->tahun }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-medium">{{ $item->provinsi->nama }}</div>
                                            @if($item->kabupaten)
                                                <div class="text-xs text-gray-500">{{ $item->kabupaten->nama }}</div>
                                            @endif
                                            @if($item->kecamatan)
                                                <div class="text-xs text-gray-400">{{ $item->kecamatan->nama }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($item->kecamatan)
                                                <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">Kecamatan</span>
                                            @elseif($item->kabupaten)
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Kabupaten</span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Provinsi</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $item->komoditas->warna_chart ?? '#666666' }}"></div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $item->komoditas->nama }}</div>
                                                    <div class="text-xs text-gray-500">{{ $item->sektor->nama }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($item->produksi, 0, ',', '.') }} {{ $item->komoditas->satuan }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($item->luas_lahan)
                                                {{ number_format($item->luas_lahan, 0, ',', '.') }} Ha
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($item->produktivitas)
                                                {{ number_format($item->produktivitas, 2, ',', '.') }} Ton/Ha
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('bps-data.edit', $item) }}" 
                                                   class="text-blue-600 hover:text-blue-900 transition duration-200"
                                                   title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <button type="button" 
                                                    onclick="confirmDelete({{ $item->id }}, 'Data BPS', `
                                                        <div class='space-y-1'>
                                                            <div class='flex'><span class='w-20 font-medium'>Komoditas:</span><span>{{ $item->komoditas->nama }}</span></div>
                                                            <div class='flex'><span class='w-20 font-medium'>Tahun:</span><span>{{ $item->tahun }}</span></div>
                                                            <div class='flex'><span class='w-20 font-medium'>Provinsi:</span><span>{{ $item->provinsi->nama }}</span></div>
                                                            <div class='flex'><span class='w-20 font-medium'>Produksi:</span><span>{{ number_format($item->produksi, 0, ',', '.') }} {{ $item->komoditas->satuan }}</span></div>
                                                        </div>
                                                    `)"
                                                    class="text-red-600 hover:text-red-900 transition duration-200"
                                                    title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>

                                                <!-- Form delete yang tersembunyi -->
                                                <form id="delete-form-{{ $item->id }}" action="{{ route('bps-data.destroy', $item) }}" method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $data->appends(request()->except('page'))->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->anyFilled(['search', 'tahun', 'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'sektor_id']))
                                    Tidak ada data yang sesuai dengan kriteria pencarian/filter Anda.
                                @else
                                    Mulai dengan menambahkan data BPS pertama Anda.
                                @endif
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('bps-data.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    + Tambah Data BPS
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinsiSelect = document.getElementById('provinsi_id');
            const kabupatenSelect = document.getElementById('kabupaten_id');
            const kecamatanSelect = document.getElementById('kecamatan_id');

            // Load kabupaten berdasarkan provinsi
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
                            
                            // Set selected jika sesuai dengan URL parameter
                            const currentKabupatenId = '{{ request('kabupaten_id') }}';
                            if (currentKabupatenId && kabupaten.id == currentKabupatenId) {
                                option.selected = true;
                            }
                            
                            kabupatenSelect.appendChild(option);
                        });
                        kabupatenSelect.disabled = false;
                        
                        // Reset kecamatan
                        resetKecamatan();
                        
                        // Jika ada kabupatenId yang sudah dipilih sebelumnya, load kecamatan
                        const currentKabupatenId = '{{ request('kabupaten_id') }}';
                        if (currentKabupatenId && data.some(kab => kab.id == currentKabupatenId)) {
                            loadKecamatan(currentKabupatenId);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading kabupaten:', error);
                        kabupatenSelect.innerHTML = '<option value="">Error loading data</option>';
                    });
            }

            // Load kecamatan berdasarkan kabupaten
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
                            
                            // Set selected jika sesuai dengan URL parameter
                            const currentKecamatanId = '{{ request('kecamatan_id') }}';
                            if (currentKecamatanId && kecamatan.id == currentKecamatanId) {
                                option.selected = true;
                            }
                            
                            kecamatanSelect.appendChild(option);
                        });
                        kecamatanSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading kecamatan:', error);
                        kecamatanSelect.innerHTML = '<option value="">Error loading data</option>';
                    });
            }

            // Reset kecamatan
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

            // Initialize form based on existing values
            const currentProvinsiId = '{{ request('provinsi_id') }}';
            if (currentProvinsiId) {
                loadKabupaten(currentProvinsiId);
            } else if (kabupatenSelect.querySelector('option[value]')) {
                // Jika ada data kabupaten langsung dari server (saat pertama load)
                kabupatenSelect.disabled = false;
            }

            const currentKabupatenId = '{{ request('kabupaten_id') }}';
            if (currentKabupatenId && !currentProvinsiId) {
                loadKecamatan(currentKabupatenId);
            } else if (kecamatanSelect.querySelector('option[value]')) {
                // Jika ada data kecamatan langsung dari server (saat pertama load)
                kecamatanSelect.disabled = false;
            }
        });

        // Function untuk sorting table
        function sortTable(field) {
            const url = new URL(window.location.href);
            const currentSort = url.searchParams.get('sort');
            const currentDirection = url.searchParams.get('direction');
            
            let newDirection = 'desc';
            
            // Jika field yang sama diklik, toggle direction
            if (currentSort === field) {
                newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            }
            
            url.searchParams.set('sort', field);
            url.searchParams.set('direction', newDirection);
            
            window.location.href = url.toString();
        }
    </script>
    @endpush
</x-app-layout>