{{-- resources/views/bps-data/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Data Pertanian BPS - RPR NasDem
        </h2>
        <p class="text-sm text-gray-600 mt-1">Data produksi dan produktivitas pertanian dari Badan Pusat Statistik</p>
    </x-slot>

    <div class="py-6">
<!-- Filter Section -->
<div class="mb-6 bg-white p-4 rounded-lg shadow">
    <form action="{{ route('bps-data.dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
            <select name="tahun" id="tahun" class="mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                @foreach($tahunList as $tahunItem)
                    <option value="{{ $tahunItem }}" {{ $tahunItem == $tahun ? 'selected' : '' }}>{{ $tahunItem }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- PROVINSI -->
        <div>
            <label for="provinsi_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
            <select name="provinsi_id" id="provinsi_id" class="provinsi-select mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                <option value="">Semua Provinsi</option>
                @foreach($provinsis as $provinsi)
                    <option value="{{ $provinsi->id }}" {{ $provinsiId == $provinsi->id ? 'selected' : '' }}>{{ $provinsi->nama }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- KABUPATEN - AKAN DIUPDATE OTOMATIS -->
        <div>
            <label for="kabupaten_id" class="block text-sm font-medium text-gray-700">Kabupaten/Kota</label>
            <select name="kabupaten_id" id="kabupaten_id" class="kabupaten-select mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" 
                    {{ !$provinsiId ? 'disabled' : '' }}>
                <option value="">Semua Kabupaten</option>
                @if($provinsiId)
                    @foreach($kabupatens as $kabupaten)
                        <option value="{{ $kabupaten->id }}" {{ $kabupatenId == $kabupaten->id ? 'selected' : '' }}>{{ $kabupaten->nama }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        
        <!-- KECAMATAN - AKAN DIUPDATE OTOMATIS -->
        <div>
            <label for="kecamatan_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
            <select name="kecamatan_id" id="kecamatan_id" class="kecamatan-select mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                    {{ !$kabupatenId ? 'disabled' : '' }}>
                <option value="">Semua Kecamatan</option>
                @if($kabupatenId)
                    @foreach($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}" {{ $kecamatanId == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->nama }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- FILTER SEKTOR OPSIONAL -->
        <div>
            <label for="sektor_id" class="block text-sm font-medium text-gray-700">Sektor (Opsional)</label>
            <select name="sektor_id" id="sektor_id" class="mt-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                <option value="">Semua Sektor</option>
                @foreach($sektors as $sektor)
                    <option value="{{ $sektor->id }}" {{ request('sektor_id') == $sektor->id ? 'selected' : '' }}>{{ $sektor->nama }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="md:col-span-5 flex justify-end space-x-2">
            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                Filter Data
            </button>
            <a href="{{ route('bps-data.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                Reset
            </a>
        </div>
    </form>
</div>


        <!-- Tampilkan info filter aktif -->
        @if($provinsiId || $kabupatenId || $kecamatanId)
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-semibold text-blue-800 mb-2">Filter Aktif:</h4>
            <div class="flex flex-wrap gap-2">
                @if($provinsiId)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Provinsi: {{ $provinsis->where('id', $provinsiId)->first()->nama ?? 'Tidak Diketahui' }}
                </span>
                @endif
                @if($kabupatenId)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Kabupaten: {{ $kabupatens->where('id', $kabupatenId)->first()->nama ?? 'Tidak Diketahui' }}
                </span>
                @endif
                @if($kecamatanId)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Kecamatan: {{ $kecamatans->where('id', $kecamatanId)->first()->nama ?? 'Tidak Diketahui' }}
                </span>
                @endif
            </div>
        </div>
        @endif

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Produksi -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">Total Produksi</p>
                        <p class="text-2xl font-bold">{{ number_format($totalProduksi, 0, ',', '.') }} Ton</p>
                        <p class="text-blue-200 text-xs mt-1">{{ number_format($rataProduktivitas, 1) }} Ton/Ha (rata-rata)</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Komoditas -->
            <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-200 text-sm">Total Komoditas</p>
                        <p class="text-2xl font-bold">{{ $totalKomoditas }}</p>
                        <p class="text-green-200 text-xs mt-1">{{ $produkUnggulan->count() }} produk unggulan</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Produktivitas Tertinggi -->
            <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-200 text-sm">Prod. Tertinggi</p>
                        <p class="text-2xl font-bold">{{ number_format($produktivitasTertinggi, 1) }} Ton/Ha</p>
                        <p class="text-purple-200 text-xs mt-1">{{ $komoditasProduktivitasTertinggi }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Luas Lahan Total -->
            <div class="bg-gradient-to-r from-red-600 to-red-800 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-200 text-sm">Luas Lahan</p>
                        <p class="text-2xl font-bold">{{ number_format($totalLuasLahan, 0, ',', '.') }} Ha</p>
                        <p class="text-red-200 text-xs mt-1">Total area tanam</p>
                    </div>
                    <div class="p-3 rounded-full bg-red-500 bg-opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Chart Produk Unggulan -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Produk Unggulan Berdasarkan Produksi</h3>
                    <span class="text-sm text-gray-500">Top 5 Komoditas Unggulan</span>
                </div>
                <div class="h-80">
                    <canvas id="unggulanChart"></canvas>
                </div>
            </div>

            <!-- Chart Produktivitas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Produktivitas Komoditas</h3>
                    <span class="text-sm text-gray-500">Top 5 Semua Komoditas</span>
                </div>
                <div class="h-80">
                    <canvas id="produktivitasChart"></canvas>
                </div>
            </div>
        </div>

<!-- Rekomendasi Komoditas untuk Demplot - Tingkat Provinsi -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Rekomendasi Komoditas untuk Demplot - Tingkat Provinsi</h3>
            <p class="text-sm text-gray-600 mt-1">
                @if($provinsiId)
                    Rekomendasi untuk <span class="font-semibold">{{ $provinsis->where('id', $provinsiId)->first()->nama ?? 'Tidak Diketahui' }}</span>
                @else
                    Rekomendasi untuk <span class="font-semibold">Seluruh Indonesia</span>
                @endif
                - Tahun {{ $tahun }}
            </p>
        </div>
        <button id="refreshRekomendasiProvinsi" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md border border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refresh Provinsi
        </button>
    </div>

    <div id="rekomendasiProvinsiLoading" class="hidden text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Memuat rekomendasi provinsi...</p>
    </div>

    <div id="rekomendasiProvinsiContent">
        <!-- Default content sebelum di-load -->
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <p class="mt-2">Klik "Refresh Provinsi" untuk melihat rekomendasi tingkat provinsi</p>
        </div>
    </div>
</div>

<!-- Rekomendasi Komoditas untuk Demplot - Tingkat Kabupaten/Kota -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Rekomendasi Komoditas untuk Demplot - Tingkat Kabupaten/Kota</h3>
            <p class="text-sm text-gray-600 mt-1">
                @if($kabupatenId)
                    Rekomendasi untuk <span class="font-semibold">{{ $kabupatens->where('id', $kabupatenId)->first()->nama ?? 'Tidak Diketahui' }}</span>
                    @if($provinsiId)
                        , <span class="font-semibold">{{ $provinsis->where('id', $provinsiId)->first()->nama ?? 'Tidak Diketahui' }}</span>
                    @endif
                @else
                    <span class="text-yellow-600">Pilih kabupaten/kota terlebih dahulu untuk melihat rekomendasi</span>
                @endif
                - Tahun {{ $tahun }}
            </p>
        </div>
        <button id="refreshRekomendasiKabupaten" 
                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md border border-transparent text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out"
                {{ !$kabupatenId ? 'disabled' : '' }}>
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refresh Kabupaten
        </button>
    </div>

    <div id="rekomendasiKabupatenLoading" class="hidden text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Memuat rekomendasi kabupaten...</p>
    </div>

    <div id="rekomendasiKabupatenContent">
        @if(!$kabupatenId)
        <!-- Pesan jika kabupaten belum dipilih -->
        <div class="text-center py-8 text-yellow-600">
            <svg class="w-12 h-12 mx-auto text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <p class="mt-2">Silakan pilih kabupaten/kota terlebih dahulu untuk melihat rekomendasi</p>
            <p class="text-sm mt-1">Pilih provinsi kemudian kabupaten/kota pada filter di atas</p>
        </div>
        @else
        <!-- Default content sebelum di-load -->
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <p class="mt-2">Klik "Refresh Kabupaten" untuk melihat rekomendasi tingkat kabupaten/kota</p>
        </div>
        @endif
    </div>
</div>

    <div id="rekomendasiLoading" class="hidden text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Memuat rekomendasi...</p>
    </div>

    <div id="rekomendasiContent">
        <!-- Content akan diisi oleh JavaScript -->
    </div>
</div>

<!-- Data Produk Unggulan Detail dengan Wilayah -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Detail Produk Unggulan per Wilayah</h3>
        <span class="text-sm text-gray-500">Top 5 Produk Unggulan</span> <!-- UBAH TEKS INI -->
    </div>
    
    @if($produkUnggulan->count() > 0)
    <div class="space-y-6">
        @foreach($produkUnggulan as $produk)
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-md font-semibold text-gray-800 flex items-center">
                    <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $produk['warna'] }}"></div>
                    {{ $produk['nama'] }}
                    <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Unggulan</span>
                </h4>
                <div class="text-sm text-gray-600">
                    Total: {{ number_format($produk['total_produksi'], 0, ',', '.') }} Ton | 
                    {{ number_format($produk['produktivitas'], 1) }} Ton/Ha
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($produk['detail_wilayah']->take(5) as $wilayah) <!-- TAMBAHKAN ->take(5) -->
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium text-gray-700">{{ $wilayah['nama'] }}</span>
                        <span class="text-xs px-2 py-1 rounded-full 
                            @if($wilayah['level'] == 'kecamatan') bg-purple-100 text-purple-800
                            @elseif($wilayah['level'] == 'kabupaten') bg-green-100 text-green-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ $wilayah['level'] }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-600 space-y-1">
                        <div>Produksi: {{ number_format($wilayah['produksi'], 0, ',', '.') }} Ton</div>
                        <div>Luas: {{ number_format($wilayah['luas_lahan'], 0, ',', '.') }} Ha</div>
                        @php
                            $produktivitasWilayah = $wilayah['luas_lahan'] > 0 ? $wilayah['produksi'] / $wilayah['luas_lahan'] : 0;
                        @endphp
                        <div>Produktivitas: {{ number_format($produktivitasWilayah, 1) }} Ton/Ha</div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Tampilkan info jika ada lebih dari 5 wilayah -->
            @if($produk['detail_wilayah']->count() > 5)
            <div class="mt-3 text-center">
                <span class="text-xs text-gray-500">
                    + {{ $produk['detail_wilayah']->count() - 5 }} wilayah lainnya...
                </span>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-8 text-gray-500">
        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.927-6.04-2.444M3 15a9 9 0 1118 0 9 9 0 01-18 0z"></path>
        </svg>
        <p class="mt-2">Tidak ada data produk unggulan untuk filter yang dipilih</p>
    </div>
    @endif
</div>



<!-- Ranking per Kecamatan Section -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Ranking Komoditas per Kecamatan</h3>
        <span class="text-sm text-gray-500">Top 5 Berdasarkan Produktivitas</span> <!-- UBAH TEKS INI -->
    </div>

    @if($dataKecamatan && $dataKecamatan->count() > 0)
        <div class="space-y-6">
            @foreach($dataKecamatan as $kecamatanData)
            <div class="border border-gray-200 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h4 class="font-semibold text-gray-800">
                            {{ $kecamatanData['kecamatan'] }} - {{ $kecamatanData['kabupaten'] }}
                        </h4>
                        <span class="text-xs text-gray-500">
                            {{ $kecamatanData['ranking']->count() }} komoditas teratas
                        </span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Komoditas</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sektor</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Luas (Ha)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produksi (Ton)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produktivitas</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($kecamatanData['ranking'] as $index => $item)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                                <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                    {{ $index + 1 }}
                                    @if($index < 3)
                                    <span class="inline-block w-2 h-2 rounded-full 
                                        @if($index == 0) bg-yellow-400
                                        @elseif($index == 1) bg-gray-400
                                        @else bg-orange-400
                                        @endif ml-1"></span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    {{ $item['komoditas'] }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    {{ $item['sektor'] }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    {{ number_format($item['luas_tanam'], 2) }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    {{ number_format($item['produksi'], 2) }}
                                </td>
                                <td class="px-4 py-2 text-sm font-semibold text-blue-600">
                                    {{ number_format($item['produktivitas'], 2) }} Ton/Ha
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    {{ $item['kontribusi'] }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Tampilkan info jika ada lebih dari 5 ranking -->
                @if($kecamatanData['ranking']->count() > 5)
                <div class="bg-gray-50 px-4 py-2 border-t border-gray-200">
                    <p class="text-xs text-gray-500 text-center">
                        Menampilkan 5 komoditas teratas dari total data
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.927-6.04-2.444M3 15a9 9 0 1118 0 9 9 0 01-18 0z"></path>
            </svg>
            <p class="mt-2">Tidak ada data ranking kecamatan untuk filter yang dipilih</p>
            <p class="text-sm">Coba pilih provinsi/kabupaten tertentu untuk melihat ranking per kecamatan</p>
        </div>
    @endif
</div>


        <!-- Quick Stats Section -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Komoditas dengan Produktivitas Tertinggi -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-md font-semibold text-gray-800 mb-4">Top 10 Produktivitas</h4>
        <div class="space-y-3">
            @foreach($topProduktivitas as $produk)
            <div class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                <div class="flex items-center">
                    <span class="text-sm text-gray-600">{{ $produk['nama'] }}</span>
                    
                    <span class="ml-2 text-xs px-1 bg-yellow-100 text-yellow-800 rounded">Top</span>
                    
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-semibold text-gray-800">{{ number_format($produk['produktivitas'], 1) }} Ton/Ha</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Tren Produktivitas -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-md font-semibold text-gray-800 mb-4">Perkembangan Produktivitas</h4>
        <div class="space-y-3">
            @foreach($trenProduktivitas as $tren)
            <div class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                <span class="text-sm text-gray-600">{{ $tren->tahun }}</span>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-semibold text-gray-800">{{ number_format($tren->rata_produktivitas, 1) }} Ton/Ha</span>
                    <span class="text-xs 
                        @if($tren->pertumbuhan > 0) text-green-600
                        @elseif($tren->pertumbuhan < 0) text-red-600
                        @else text-gray-500
                        @endif">
                        @if($tren->pertumbuhan > 0)+@endif{{ number_format($tren->pertumbuhan, 1) }}%
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="text-md font-semibold text-gray-800 mb-4">Aksi Cepat</h4>
        <div class="space-y-3">
            @if(auth()->user()->isAdmin())
            <a href="{{ route('bps-data.index') }}" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition duration-200">
                <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="text-sm font-medium text-gray-800">Kelola Data BPS</span>
            </a>
            @endif
            
            <!-- Export Excel dengan parameter filter -->
            <a href="{{ route('export.bps-data.excel', [
                'tahun' => $tahun,
                'provinsi_id' => $provinsiId,
                'kabupaten_id' => $kabupatenId,
                'kecamatan_id' => $kecamatanId
            ]) }}" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition duration-200">
                <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-800">Export Data Excel</span>
            </a>
            
            <!-- Export PDF dengan parameter filter -->
            <a href="{{ route('export.bps-data.pdf', [
                'tahun' => $tahun,
                'provinsi_id' => $provinsiId,
                'kabupaten_id' => $kabupatenId,
                'kecamatan_id' => $kecamatanId
            ]) }}" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition duration-200">
                <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-800">Export Laporan PDF</span>
            </a>

        </div>
    </div>
</div>
    </div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize Charts
    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
        initFilterLogic();
        initRekomendasiLogic(); // Tambahkan inisialisasi rekomendasi
    });

    function initCharts() {
        // Ambil hanya 5 data teratas untuk chart
        const produkUnggulanTop5 = {!! json_encode($produkUnggulan->take(5)) !!};
        const topProduktivitasTop5 = {!! json_encode($topProduktivitas->take(5)) !!};

        // Warna dinamis yang lebih menarik
        const dynamicColors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
            '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
        ];

        // Chart Produk Unggulan
        const unggulanCtx = document.getElementById('unggulanChart').getContext('2d');
        const unggulanChart = new Chart(unggulanCtx, {
            type: 'bar',
            data: {
                labels: produkUnggulanTop5.map(item => item.nama),
                datasets: [{
                    label: 'Produksi (Ton)',
                    data: produkUnggulanTop5.map(item => item.total_produksi),
                    backgroundColor: dynamicColors.slice(0, produkUnggulanTop5.length),
                    borderColor: dynamicColors.slice(0, produkUnggulanTop5.length).map(color => {
                        // Buat border color lebih gelap
                        return color.replace(')', ', 0.8)').replace('rgb', 'rgba');
                    }),
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'logarithmic',
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Produksi (Ton) - Skala Logaritmik'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                // Format tick labels untuk skala logaritmik
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                }
                                if (value >= 1000) {
                                    return (value / 1000).toFixed(1) + 'k';
                                }
                                if (value >= 1) {
                                    return value.toFixed(1);
                                }
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                const produk = produkUnggulanTop5[context.dataIndex];
                                return [
                                    `Produksi: ${context.raw.toLocaleString()} Ton`,
                                    `Luas: ${produk.luas_lahan.toLocaleString()} Ha`,
                                    `Produktivitas: ${produk.produktivitas.toFixed(1)} Ton/Ha`
                                ];
                            }
                        }
                    }
                }
            }
        });

        // Chart Produktivitas dengan skala logaritmik
        const produktivitasCtx = document.getElementById('produktivitasChart').getContext('2d');
        const produktivitasChart = new Chart(produktivitasCtx, {
            type: 'bar',
            data: {
                labels: topProduktivitasTop5.map(item => item.nama),
                datasets: [{
                    label: 'Produktivitas (Ton/Ha)',
                    data: topProduktivitasTop5.map(item => item.produktivitas),
                    backgroundColor: [
                        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
                        '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
                    ].slice(0, topProduktivitasTop5.length),
                    borderColor: [
                        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
                        '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
                    ].slice(0, topProduktivitasTop5.length).map(color => {
                        return color.replace(')', ', 0.8)').replace('rgb', 'rgba');
                    }),
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'logarithmic', // SKALA LOGARITMIK UNTUK PRODUKTIVITAS
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Produktivitas (Ton/Ha) - Skala Logaritmik'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                // Format tick labels untuk skala logaritmik
                                if (value >= 1000) {
                                    return (value / 1000).toFixed(1) + 'k';
                                }
                                if (value >= 1) {
                                    return value.toFixed(1);
                                }
                                if (value >= 0.1) {
                                    return value.toFixed(2);
                                }
                                return value.toFixed(3);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                const produk = topProduktivitasTop5[context.dataIndex];
                                return [
                                    `Produktivitas: ${context.raw.toFixed(2)} Ton/Ha`,
                                    `Produksi: ${produk.total_produksi.toLocaleString()} Ton`,
                                    `Luas: ${produk.luas_lahan.toLocaleString()} Ha`
                                ];
                            }
                        }
                    }
                }
            }
        });
    }

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
                updateKabupatenButton(); // Update status button kabupaten
                return;
            }

            // Tampilkan loading
            kabupatenSelect.innerHTML = '<option value="">Loading...</option>';
            kabupatenSelect.disabled = true;

            fetch(`/api/kabupaten-by-provinsi/${provinsiId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    kabupatenSelect.innerHTML = '<option value="">Semua Kabupaten</option>';
                    data.forEach(kabupaten => {
                        const option = document.createElement('option');
                        option.value = kabupaten.id;
                        option.textContent = kabupaten.nama;
                        kabupatenSelect.appendChild(option);
                    });
                    kabupatenSelect.disabled = false;
                    
                    // Reset kecamatan
                    resetKecamatan();
                    
                    // Jika ada kabupatenId yang sudah dipilih sebelumnya, set nilai tersebut
                    const currentKabupatenId = '{{ $kabupatenId }}';
                    if (currentKabupatenId && data.some(kab => kab.id == currentKabupatenId)) {
                        kabupatenSelect.value = currentKabupatenId;
                        // Trigger change event untuk load kecamatan
                        kabupatenSelect.dispatchEvent(new Event('change'));
                    }
                    
                    updateKabupatenButton(); // Update status button kabupaten
                })
                .catch(error => {
                    console.error('Error loading kabupaten:', error);
                    kabupatenSelect.innerHTML = '<option value="">Error loading data</option>';
                    updateKabupatenButton(); // Update status button kabupaten
                });
        }

        // Function untuk load kecamatan berdasarkan kabupaten
        function loadKecamatan(kabupatenId) {
            if (!kabupatenId) {
                resetKecamatan();
                return;
            }

            // Tampilkan loading
            kecamatanSelect.innerHTML = '<option value="">Loading...</option>';
            kecamatanSelect.disabled = true;

            fetch(`/api/kecamatan-by-kabupaten/${kabupatenId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    kecamatanSelect.innerHTML = '<option value="">Semua Kecamatan</option>';
                    data.forEach(kecamatan => {
                        const option = document.createElement('option');
                        option.value = kecamatan.id;
                        option.textContent = kecamatan.nama;
                        kecamatanSelect.appendChild(option);
                    });
                    kecamatanSelect.disabled = false;
                    
                    // Jika ada kecamatanId yang sudah dipilih sebelumnya, set nilai tersebut
                    const currentKecamatanId = '{{ $kecamatanId }}';
                    if (currentKecamatanId && data.some(kec => kec.id == currentKecamatanId)) {
                        kecamatanSelect.value = currentKecamatanId;
                    }
                })
                .catch(error => {
                    console.error('Error loading kecamatan:', error);
                    kecamatanSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        }

        // Function reset kecamatan
        function resetKecamatan() {
            kecamatanSelect.innerHTML = '<option value="">Semua Kecamatan</option>';
            kecamatanSelect.disabled = true;
        }

        // Event listener untuk provinsi
        provinsiSelect.addEventListener('change', function() {
            loadKabupaten(this.value);
        });

        // Event listener untuk kabupaten
        kabupatenSelect.addEventListener('change', function() {
            loadKecamatan(this.value);
            updateKabupatenButton(); // Update status button kabupaten
        });

        // Jika halaman dimuat dengan provinsi sudah terpilih, load kabupaten
        const currentProvinsiId = '{{ $provinsiId }}';
        if (currentProvinsiId) {
            loadKabupaten(currentProvinsiId);
        }

        // Jika halaman dimuat dengan kabupaten sudah terpilih (tanpa provinsi), load kecamatan
        const currentKabupatenId = '{{ $kabupatenId }}';
        if (currentKabupatenId && !currentProvinsiId) {
            loadKecamatan(currentKabupatenId);
        }
        
        // Inisialisasi status button kabupaten
        updateKabupatenButton();
    }

    // FUNGSI REKOMENDASI BARU - DUA LEVEL
    function initRekomendasiLogic() {
        // Event listener untuk refresh button provinsi
        document.getElementById('refreshRekomendasiProvinsi').addEventListener('click', function() {
            loadRekomendasiKomoditas('provinsi');
        });

        // Event listener untuk refresh button kabupaten
        document.getElementById('refreshRekomendasiKabupaten').addEventListener('click', function() {
            loadRekomendasiKomoditas('kabupaten');
        });

        // Load rekomendasi provinsi pertama kali
        loadRekomendasiKomoditas('provinsi');
    }

    function updateKabupatenButton() {
        const kabupatenId = document.getElementById('kabupaten_id').value;
        const button = document.getElementById('refreshRekomendasiKabupaten');
        
        if (kabupatenId) {
            button.disabled = false;
            button.classList.remove('bg-gray-400', 'cursor-not-allowed');
            button.classList.add('bg-green-600', 'hover:bg-green-700', 'cursor-pointer');
        } else {
            button.disabled = true;
            button.classList.remove('bg-green-600', 'hover:bg-green-700', 'cursor-pointer');
            button.classList.add('bg-gray-400', 'cursor-not-allowed');
        }
    }

    function loadRekomendasiKomoditas(level) {
        const loadingElement = document.getElementById(`rekomendasi${level.charAt(0).toUpperCase() + level.slice(1)}Loading`);
        const contentElement = document.getElementById(`rekomendasi${level.charAt(0).toUpperCase() + level.slice(1)}Content`);
        
        // Show loading
        loadingElement.classList.remove('hidden');
        contentElement.innerHTML = '';
        
        // Get filter values
        const tahun = document.getElementById('tahun').value;
        const provinsiId = document.getElementById('provinsi_id').value;
        const kabupatenId = document.getElementById('kabupaten_id').value;
        const sektorId = document.getElementById('sektor_id').value;
        
        // Build query parameters berdasarkan level
        const params = new URLSearchParams({
            tahun: tahun,
            level: level, // Tambahkan parameter level
            ...(provinsiId && { provinsi_id: provinsiId }),
            ...(level === 'kabupaten' && kabupatenId && { kabupaten_id: kabupatenId }),
            ...(sektorId && { sektor_id: sektorId })
        });
        
        // Untuk level kabupaten, pastikan kabupatenId ada
        if (level === 'kabupaten' && !kabupatenId) {
            contentElement.innerHTML = `
                <div class="text-center py-8 text-yellow-600">
                    <svg class="w-12 h-12 mx-auto text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <p class="mt-2">Silakan pilih kabupaten/kota terlebih dahulu</p>
                </div>
            `;
            loadingElement.classList.add('hidden');
            return;
        }
        
        fetch(`/bps-data/rekomendasi-komoditas?${params}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                renderRekomendasiContent(data, level);
                loadingElement.classList.add('hidden');
            })
            .catch(error => {
                console.error(`Error loading rekomendasi ${level}:`, error);
                contentElement.innerHTML = `
                    <div class="text-center py-8 text-red-600">
                        <svg class="w-12 h-12 mx-auto text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="mt-2">Gagal memuat rekomendasi: ${error.message}</p>
                    </div>
                `;
                loadingElement.classList.add('hidden');
            });
    }

    // Function untuk render content rekomendasi dengan level
    function renderRekomendasiContent(data, level) {
        const contentElement = document.getElementById(`rekomendasi${level.charAt(0).toUpperCase() + level.slice(1)}Content`);
        const levelText = level === 'provinsi' ? 'Provinsi' : 'Kabupaten/Kota';
        
        if (!data.rekomendasi || data.rekomendasi.length === 0) {
            contentElement.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.927-6.04-2.444M3 15a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                    </svg>
                    <p class="mt-2">Tidak ada rekomendasi komoditas untuk ${levelText} ini</p>
                    <p class="text-sm mt-1">Coba ubah filter tahun atau sektor</p>
                </div>
            `;
            return;
        }
        
        // Dapatkan informasi wilayah
        const wilayahInfo = getWilayahInfo(data.filter, level);
        
        let html = `
            <div class="mb-4 p-3 ${level === 'provinsi' ? 'bg-blue-50 border-blue-200' : 'bg-green-50 border-green-200'} border rounded-lg">
                <div class="flex items-center">
                    <svg class="w-4 h-4 ${level === 'provinsi' ? 'text-blue-600' : 'text-green-600'} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm ${level === 'provinsi' ? 'text-blue-700' : 'text-green-700'}">
                        <span class="font-medium">Rekomendasi ${levelText}:</span>
                        <span class="ml-1">${wilayahInfo}</span>
                    </div>
                </div>
            </div>
            
            <div class="mb-4 text-sm text-gray-600">
                Menampilkan ${data.rekomendasi.length} dari ${data.total_komoditas} komoditas berdasarkan potensi tertinggi
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        `;
        
        data.rekomendasi.forEach((item, index) => {
            const badgeColor = getBadgeColor(item.rekomendasi_level);
            const rankClass = index < 3 ? 'ring-2 ring-yellow-400' : '';
            
            html += `
                <div class="border border-gray-200 rounded-lg p-4 ${rankClass} hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full ${level === 'provinsi' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'} font-bold text-sm mr-3">
                                ${index + 1}
                            </span>
                            <div>
                                <h4 class="text-md font-semibold text-gray-800">${item.nama}</h4>
                                <p class="text-sm text-gray-600">${item.sektor}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${badgeColor}">
                            ${item.rekomendasi_level}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Produktivitas:</span>
                                <span class="font-semibold">${item.produktivitas.toFixed(2)} Ton/Ha</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Produksi:</span>
                                <span class="font-semibold">${item.total_produksi.toLocaleString()} Ton</span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Luas Lahan:</span>
                                <span class="font-semibold">${item.total_luas_lahan.toLocaleString()} Ha</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jumlah Wilayah:</span>
                                <span class="font-semibold">${item.jumlah_wilayah}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500">Skor Potensi:</span>
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="${level === 'provinsi' ? 'bg-blue-600' : 'bg-green-600'} h-2 rounded-full" style="width: ${Math.min(item.skor_potensi * 10, 100)}%"></div>
                            </div>
                            <span class="font-medium">${item.skor_potensi.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `</div>`;
        contentElement.innerHTML = html;
    }

    // Helper function untuk mendapatkan informasi wilayah
    function getWilayahInfo(filter, level) {
        let info = '';
        
        // Dapatkan elemen select untuk mendapatkan nama wilayah
        const provinsiSelect = document.getElementById('provinsi_id');
        const kabupatenSelect = document.getElementById('kabupaten_id');
        const tahunSelect = document.getElementById('tahun');
        
        if (level === 'kabupaten' && filter.kabupaten_id && kabupatenSelect) {
            const selectedKabupaten = kabupatenSelect.options[kabupatenSelect.selectedIndex];
            info += `Kabupaten ${selectedKabupaten.text}`;
            
            if (filter.provinsi_id && provinsiSelect) {
                const selectedProvinsi = provinsiSelect.options[provinsiSelect.selectedIndex];
                info += `, ${selectedProvinsi.text}`;
            }
        } 
        else if (level === 'provinsi') {
            if (filter.provinsi_id && provinsiSelect) {
                const selectedProvinsi = provinsiSelect.options[provinsiSelect.selectedIndex];
                info += `Provinsi ${selectedProvinsi.text}`;
            } else {
                info += 'Seluruh Indonesia';
            }
        }
        
        info += ` - Tahun ${filter.tahun}`;
        
        return info;
    }

    // Helper function untuk warna badge
    function getBadgeColor(level) {
        switch(level) {
            case 'Sangat Direkomendasikan':
                return 'bg-green-100 text-green-800';
            case 'Direkomendasikan':
                return 'bg-blue-100 text-blue-800';
            case 'Cukup Direkomendasikan':
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    // FUNGSI LAMA - DIPERTAHANKAN UNTUK KOMPATIBILITAS
    function loadRekomendasiKomoditasOld() {
        const loadingElement = document.getElementById('rekomendasiLoading');
        const contentElement = document.getElementById('rekomendasiContent');
        
        // Show loading
        loadingElement.classList.remove('hidden');
        contentElement.innerHTML = '';
        
        // Get filter values
        const tahun = document.getElementById('tahun').value;
        const provinsiId = document.getElementById('provinsi_id').value;
        const kabupatenId = document.getElementById('kabupaten_id').value;
        const kecamatanId = document.getElementById('kecamatan_id').value;
        const sektorId = document.getElementById('sektor_id').value;
        
        // Build query parameters
        const params = new URLSearchParams({
            tahun: tahun,
            ...(provinsiId && { provinsi_id: provinsiId }),
            ...(kabupatenId && { kabupaten_id: kabupatenId }),
            ...(kecamatanId && { kecamatan_id: kecamatanId }),
            ...(sektorId && { sektor_id: sektorId })
        });
        
        // Gunakan route name yang benar
        fetch(`/bps-data/rekomendasi-komoditas?${params}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                renderRekomendasiContentOld(data);
                loadingElement.classList.add('hidden');
            })
            .catch(error => {
                console.error('Error loading rekomendasi:', error);
                contentElement.innerHTML = `
                    <div class="text-center py-8 text-red-600">
                        <svg class="w-12 h-12 mx-auto text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="mt-2">Gagal memuat rekomendasi: ${error.message}</p>
                        <p class="text-sm mt-1">Pastikan Anda sudah login dan memiliki akses</p>
                    </div>
                `;
                loadingElement.classList.add('hidden');
            });
    }

    // Function untuk render content rekomendasi (lama)
    function renderRekomendasiContentOld(data) {
        const contentElement = document.getElementById('rekomendasiContent');
        
        if (!data.rekomendasi || data.rekomendasi.length === 0) {
            contentElement.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.927-6.04-2.444M3 15a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                    </svg>
                    <p class="mt-2">Tidak ada rekomendasi komoditas untuk filter yang dipilih</p>
                </div>
            `;
            return;
        }
        
        let html = `
            <div class="mb-4 text-sm text-gray-600">
                Menampilkan ${data.rekomendasi.length} dari ${data.total_komoditas} komoditas berdasarkan potensi tertinggi
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        `;
        
        data.rekomendasi.forEach((item, index) => {
            const badgeColor = getBadgeColor(item.rekomendasi_level);
            const rankClass = index < 3 ? 'ring-2 ring-yellow-400' : '';
            
            html += `
                <div class="border border-gray-200 rounded-lg p-4 ${rankClass} hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-bold text-sm mr-3">
                                ${index + 1}
                            </span>
                            <div>
                                <h4 class="text-md font-semibold text-gray-800">${item.nama}</h4>
                                <p class="text-sm text-gray-600">${item.sektor}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${badgeColor}">
                            ${item.rekomendasi_level}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Produktivitas:</span>
                                <span class="font-semibold">${item.produktivitas.toFixed(2)} Ton/Ha</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Produksi:</span>
                                <span class="font-semibold">${item.total_produksi.toLocaleString()} Ton</span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Luas Lahan:</span>
                                <span class="font-semibold">${item.total_luas_lahan.toLocaleString()} Ha</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jumlah Wilayah:</span>
                                <span class="font-semibold">${item.jumlah_wilayah}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500">Skor Potensi:</span>
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: ${Math.min(item.skor_potensi * 10, 100)}%"></div>
                            </div>
                            <span class="font-medium">${item.skor_potensi.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `</div>`;
        contentElement.innerHTML = html;
    }
</script>
@endpush
</x-app-layout>