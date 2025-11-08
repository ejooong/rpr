<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Demplot - {{ $demplot->nama_lahan }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Detail informasi demplot pertanian</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Header dengan Actions -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $demplot->nama_lahan }}</h3>
                            @php
                                $statusColors = [
                                    'aktif' => 'bg-green-100 text-green-800',
                                    'selesai' => 'bg-blue-100 text-blue-800', 
                                    'rencana' => 'bg-yellow-100 text-yellow-800'
                                ];
                                $color = $statusColors[$demplot->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }} mt-1">
                                {{ ucfirst($demplot->status) }}
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('demplot.edit', $demplot->id) }}" 
                               class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition flex items-center text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                            <a href="{{ route('demplot.index') }}" 
                               class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition flex items-center text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali
                            </a>
                        </div>
                    </div>

                    <!-- Informasi Demplot -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Petani</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $demplot->petani->nama ?? '-' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Komoditas</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $demplot->komoditas->nama ?? '-' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Luas Lahan</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ number_format($demplot->luas_lahan, 2) }} Ha</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Tahun</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $demplot->tahun }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Alamat</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $demplot->alamat }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Koordinat</h4>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $demplot->latitude }}, {{ $demplot->longitude }}
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Wilayah</h4>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($demplot->desa)
                                        {{ $demplot->desa->nama }}, {{ $demplot->kecamatan->nama }}, 
                                        {{ $demplot->kabupaten->nama }}, {{ $demplot->provinsi->nama }}
                                    @else
                                        {{ $demplot->alamat }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Tanggal Dibuat</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $demplot->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Foto Lahan -->
                    @if($demplot->foto_lahan)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Foto Lahan</h4>
                        <img src="{{ Storage::url($demplot->foto_lahan) }}" 
                             alt="Foto Lahan {{ $demplot->nama_lahan }}" 
                             class="max-w-md rounded-lg shadow-md">
                    </div>
                    @endif

                    <!-- Keterangan -->
                    @if($demplot->keterangan)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Keterangan</h4>
                        <p class="text-sm text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $demplot->keterangan }}</p>
                    </div>
                    @endif

                    <!-- Informasi Petani -->
                    <div class="border-t pt-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-4">Informasi Petani</h4>
                        @if($demplot->petani)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Nama:</span>
                                <span class="text-gray-900 ml-2">{{ $demplot->petani->nama }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">NIK:</span>
                                <span class="text-gray-900 ml-2">{{ $demplot->petani->nik }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">No HP:</span>
                                <span class="text-gray-900 ml-2">{{ $demplot->petani->no_hp ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Alamat:</span>
                                <span class="text-gray-900 ml-2">{{ $demplot->petani->alamat }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Poktan:</span>
                                <span class="text-gray-900 ml-2">{{ $demplot->petani->poktan->nama ?? '-' }}</span>
                            </div>
                        </div>
                        @else
                        <p class="text-sm text-gray-500">Data petani tidak tersedia</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>