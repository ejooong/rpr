<!-- resources/views/produksi/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Data Produksi
        </h2>
        <p class="text-sm text-gray-600 mt-1">Informasi lengkap data produksi</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Header dengan Actions -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Produksi {{ $produksi->komoditas->nama }} - {{ $produksi->tahun }}
                        </h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('produksi.edit', $produksi->id) }}" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">
                                Edit
                            </a>
                            <a href="{{ route('produksi.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">
                                Kembali
                            </a>
                        </div>
                    </div>

                    <!-- Data Produksi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informasi Demplot -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-3">Informasi Demplot</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nama Lahan</dt>
                                    <dd class="text-sm text-gray-900">{{ $produksi->demplot->nama_lahan }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Petani</dt>
                                    <dd class="text-sm text-gray-900">{{ $produksi->demplot->petani->nama }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Poktan</dt>
                                    <dd class="text-sm text-gray-900">{{ $produksi->demplot->petani->poktan->nama ?? 'Tidak ada' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $produksi->demplot->desa->nama ?? '' }}, 
                                        {{ $produksi->demplot->kecamatan->nama ?? '' }}, 
                                        {{ $produksi->demplot->kabupaten->nama ?? '' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Data Produksi -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-3">Data Produksi</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Komoditas</dt>
                                    <dd class="text-sm text-gray-900">{{ $produksi->komoditas->nama }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Periode</dt>
                                    <dd class="text-sm text-gray-900">{{ $produksi->nama_bulan }} {{ $produksi->tahun }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Luas Panen</dt>
                                    <dd class="text-sm text-gray-900">{{ number_format($produksi->luas_panen, 2) }} Ha</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Produksi</dt>
                                    <dd class="text-sm text-gray-900">{{ number_format($produksi->total_produksi, 2) }} Ton</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Produktivitas</dt>
                                    <dd class="text-sm text-gray-900">{{ number_format($produksi->produktivitas, 2) }} Ton/Ha</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Informasi Input -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-3">Informasi Input</h4>
                            <dl class="space-y-2">
<div>
    <dt class="text-sm font-medium text-gray-500">Petugas Input</dt>
    <dd class="text-sm text-gray-900">
        @if($produksi->petugas)
            @php
                $email = $produksi->petugas->email;
                $username = explode('@', $email)[0];
                $displayName = $produksi->petugas->nama ?: 'Petugas ' . ucfirst($username);
            @endphp
            {{ $displayName }}
            <br>
            <span class="text-gray-500 text-xs">{{ $produksi->petugas->email }}</span>
        @else
            <span class="text-yellow-600">Tidak diketahui</span>
        @endif
    </dd>
</div>                         <div>
                                    <dt class="text-sm font-medium text-gray-500">Sumber Data</dt>
                                    <dd class="text-sm text-gray-900">{{ $produksi->sumber_data ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ditambahkan</dt>
                                    <dd class="text-sm text-gray-900">{{ $produksi->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Terakhir Update</dt>
                                    <dd class="text-sm text-gray-900">{{ $produksi->updated_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Statistik -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-3">Statistik</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status Produktivitas</dt>
                                    <dd class="text-sm">
                                        @php
                                            $avgProduktivitasPadi = 6.0; // Rata-rata nasional padi
                                        @endphp
                                        @if($produksi->produktivitas >= $avgProduktivitasPadi)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                                ⬆️ Di Atas Rata-rata
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                                ⬇️ Di Bawah Rata-rata
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                    <dd class="text-sm">
                                        @if($produksi->produktivitas >= 8)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                                                Sangat Produktif
                                            </span>
                                        @elseif($produksi->produktivitas >= 6)
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                                Produktif
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">
                                                Perlu Perbaikan
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>