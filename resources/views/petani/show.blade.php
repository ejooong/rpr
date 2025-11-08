<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Petani
        </h2>
        <p class="text-sm text-gray-600 mt-1">Informasi lengkap data petani</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('petani.index') }}" class="text-blue-600 hover:text-blue-800">
                            Data Petani
                        </a>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ml-2 text-gray-500">Detail Petani</span>
                    </li>
                </ol>
            </nav>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $petani->nama }}</h1>
                    <p class="text-gray-600">NIK: {{ $petani->nik }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('petani.edit', $petani->id) }}" 
                       class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-2.5 rounded-lg font-medium transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('petani.index') }}" 
                       class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-gray-700 text-white px-6 py-2.5 rounded-lg font-medium transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Profile & Status -->
                <div class="lg:col-span-1">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <div class="text-center">
                            <!-- Foto Profil -->
                            <div class="w-32 h-32 mx-auto mb-4 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                @if($petani->foto)
                                    <img src="{{ asset('storage/' . $petani->foto) }}" 
                                         alt="{{ $petani->nama }}" 
                                         class="w-full h-full object-cover rounded-full">
                                @else
                                    <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                @endif
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900">{{ $petani->nama }}</h3>
                            <p class="text-gray-600 text-sm mt-1">NIK: {{ $petani->nik }}</p>
                            
                            <!-- Status -->
                            <div class="mt-4">
                                <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full shadow-md {{ $petani->aktif ? 'bg-gradient-to-r from-green-500 to-green-600 text-white' : 'bg-gradient-to-r from-red-500 to-red-600 text-white' }}">
                                    {{ $petani->aktif ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Kelompok Tani Info -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Kelompok Tani</h4>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Nama Poktan</p>
                                <p class="font-medium text-gray-900">{{ $petani->poktan->nama ?? '-' }}</p>
                            </div>
                            @if($petani->poktan && $petani->poktan->komoditasUtama)
                            <div>
                                <p class="text-sm text-gray-600">Komoditas Utama</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                                    {{ $petani->poktan->komoditasUtama->nama }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Detailed Information -->
                <div class="lg:col-span-2">
                    <!-- Personal Information -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pribadi</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Jenis Kelamin</p>
                                <p class="font-medium text-gray-900">
                                    {{ $petani->jenis_kelamin == 'L' ? 'Laki-laki' : ($petani->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Tanggal Lahir</p>
                                <p class="font-medium text-gray-900">
                                    {{ $petani->tanggal_lahir ? \Carbon\Carbon::parse($petani->tanggal_lahir)->format('d/m/Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Pendidikan</p>
                                <p class="font-medium text-gray-900">{{ $petani->pendidikan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">No. HP</p>
                                <p class="font-medium text-gray-900">{{ $petani->no_hp }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Lahan Information -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Lahan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Luas Lahan Garap</p>
                                <p class="font-medium text-gray-900">
                                    {{ $petani->luas_lahan_garap ? number_format($petani->luas_lahan_garap, 2) . ' Ha' : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status Lahan</p>
                                <p class="font-medium text-gray-900">{{ $petani->status_lahan ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Lokasi</h4>
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Alamat Lengkap</p>
                            <p class="font-medium text-gray-900 mt-1 leading-relaxed">{{ $petani->alamat }}</p>
                        </div>
                        
                        @if($petani->poktan)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Provinsi</p>
                                <p class="font-medium text-gray-900">{{ $petani->poktan->provinsi->nama ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Kabupaten</p>
                                <p class="font-medium text-gray-900">{{ $petani->poktan->kabupaten->nama ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Kecamatan</p>
                                <p class="font-medium text-gray-900">{{ $petani->poktan->kecamatan->nama ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Desa</p>
                                <p class="font-medium text-gray-900">{{ $petani->poktan->desa->nama ?? '-' }}</p>
                            </div>
                        </div>
                        @endif

                        @if($petani->latitude && $petani->longitude)
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Koordinat</p>
                            <p class="font-medium text-gray-900">
                                {{ $petani->latitude }}, {{ $petani->longitude }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bottom Action Buttons -->
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    Terakhir diupdate: {{ $petani->updated_at->format('d/m/Y H:i') }}
                </div>
                <div class="flex space-x-3">
                    <form action="{{ route('petani.destroy', $petani->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-2.5 rounded-lg font-medium transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus petani {{ $petani->nama }}?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>