<!-- resources/views/wilayah/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Wilayah: {{ $wilayah->nama }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Informasi lengkap wilayah administrasi</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Information -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">
                                        Informasi Dasar
                                    </h3>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Kode Wilayah</label>
                                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $wilayah->kode }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Nama Wilayah</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $wilayah->nama }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Level</label>
                                        <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $wilayah->level == 'provinsi' ? 'bg-purple-100 text-purple-800' : 
                                               ($wilayah->level == 'kabupaten' ? 'bg-blue-100 text-blue-800' : 
                                               ($wilayah->level == 'kecamatan' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($wilayah->level) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Hierarchy & Status -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">
                                        Hierarki & Status
                                    </h3>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Wilayah Induk</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            {{ $wilayah->parent->nama ?? 'Tidak Ada (Provinsi)' }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Status</label>
                                        <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $wilayah->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $wilayah->aktif ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Koordinat</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            @if($wilayah->latitude && $wilayah->longitude)
                                                {{ $wilayah->latitude }}, {{ $wilayah->longitude }}
                                            @else
                                                <span class="text-gray-400">Tidak tersedia</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Timestamps -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                                    <div>
                                        <label class="font-medium">Dibuat pada:</label>
                                        <p>{{ $wilayah->created_at->translatedFormat('d F Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <label class="font-medium">Diupdate pada:</label>
                                        <p>{{ $wilayah->updated_at->translatedFormat('d F Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions & Related Info -->
                <div class="space-y-6">
                    <!-- Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi</h3>
                            <div class="space-y-3">
                                <a href="{{ route('wilayah.edit', $wilayah) }}" 
                                   class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Wilayah
                                </a>
                                
                                <form action="{{ route('wilayah.destroy', $wilayah) }}" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus wilayah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus Wilayah
                                    </button>
                                </form>
                                
                                <a href="{{ route('wilayah.index') }}" 
                                   class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                                    Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Sub Wilayah</span>
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ $wilayah->children->count() }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Poktan</span>
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ $wilayah->poktan->count() }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Demplot</span>
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ $wilayah->demplot->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>