<!-- resources/views/desa/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Desa/Kelurahan
        </h2>
        <p class="text-sm text-gray-600 mt-1">Informasi lengkap data desa dan kelurahan</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Card Detail -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $desa->nama }}</h3>
                            <p class="text-sm text-gray-500">{{ $desa->kode }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            {{ $desa->tipe == 'kelurahan' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($desa->tipe) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informasi Utama -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Informasi Umum</h4>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Kode</label>
                                <p class="text-sm text-gray-900 font-mono">{{ $desa->kode }}</p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Nama</label>
                                <p class="text-sm text-gray-900">{{ $desa->nama }}</p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Tipe</label>
                                <p class="text-sm text-gray-900 capitalize">{{ $desa->tipe }}</p>
                            </div>
                        </div>

                        <!-- Informasi Wilayah -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Wilayah Administratif</h4>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Kecamatan</label>
                                <p class="text-sm text-gray-900">{{ $desa->kecamatan->nama }}</p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Kabupaten/Kota</label>
                                <p class="text-sm text-gray-900">{{ $desa->kecamatan->kabupaten->nama }}</p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Provinsi</label>
                                <p class="text-sm text-gray-900">{{ $desa->kecamatan->kabupaten->provinsi->nama }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Tambahan -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Informasi Tambahan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs font-medium text-gray-500">Koordinat</label>
                                    <p class="text-sm text-gray-900">
                                        @if($desa->latitude && $desa->longitude)
                                            {{ number_format($desa->latitude, 6) }}, {{ number_format($desa->longitude, 6) }}
                                        @else
                                            <span class="text-gray-400">Tidak tersedia</span>
                                        @endif
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="text-xs font-medium text-gray-500">Jumlah Petani</label>
                                    <p class="text-sm text-gray-900">{{ $desa->petani()->count() }}</p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs font-medium text-gray-500">Jumlah Kelompok Tani</label>
                                    <p class="text-sm text-gray-900">{{ $desa->poktan()->count() }}</p>
                                </div>
                                
                                <div>
                                    <label class="text-xs font-medium text-gray-500">Jumlah Demplot</label>
                                    <p class="text-sm text-gray-900">{{ $desa->demplot()->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-gray-500">Dibuat Pada</label>
                                <p class="text-sm text-gray-900">{{ $desa->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Diperbarui Pada</label>
                                <p class="text-sm text-gray-900">{{ $desa->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
                        <a href="{{ route('desa.index') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                            Kembali
                        </a>
                        <a href="{{ route('desa.edit', $desa) }}" 
                           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>