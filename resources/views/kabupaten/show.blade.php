<!-- resources/views/kabupaten/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Kabupaten/Kota
        </h2>
        <p class="text-sm text-gray-600 mt-1">Informasi lengkap data kabupaten dan kota</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Card Detail -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $kabupaten->nama }}</h3>
                            <p class="text-sm text-gray-500">{{ $kabupaten->kode }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            {{ $kabupaten->tipe == 'kota' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($kabupaten->tipe) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informasi Utama -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Informasi Umum</h4>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Kode</label>
                                <p class="text-sm text-gray-900 font-mono">{{ $kabupaten->kode }}</p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Nama</label>
                                <p class="text-sm text-gray-900">{{ $kabupaten->nama }}</p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Provinsi</label>
                                <p class="text-sm text-gray-900">{{ $kabupaten->provinsi->nama }}</p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Tipe</label>
                                <p class="text-sm text-gray-900 capitalize">{{ $kabupaten->tipe }}</p>
                            </div>
                        </div>

                        <!-- Informasi Tambahan -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Informasi Tambahan</h4>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Koordinat</label>
                                <p class="text-sm text-gray-900">
                                    @if($kabupaten->latitude && $kabupaten->longitude)
                                        {{ number_format($kabupaten->latitude, 6) }}, {{ number_format($kabupaten->longitude, 6) }}
                                    @else
                                        <span class="text-gray-400">Tidak tersedia</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Jumlah Kecamatan</label>
                                <p class="text-sm text-gray-900">{{ $kabupaten->kecamatans->count() }}</p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Dibuat Pada</label>
                                <p class="text-sm text-gray-900">{{ $kabupaten->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            
                            <div>
                                <label class="text-xs font-medium text-gray-500">Diperbarui Pada</label>
                                <p class="text-sm text-gray-900">{{ $kabupaten->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('kabupaten.index') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                            Kembali
                        </a>
                        <a href="{{ route('kabupaten.edit', $kabupaten) }}" 
                           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Edit Data
                        </a>
                    </div>
                </div>
            </div>

            <!-- Data Kecamatan -->
            @if($kabupaten->kecamatans->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Data Kecamatan</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kode
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Kecamatan
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jumlah Desa
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($kabupaten->kecamatans as $kecamatan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-gray-900">
                                        {{ $kecamatan->kode }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $kecamatan->nama }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $kecamatan->desas_count ?? $kecamatan->desas->count() }}
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
    </div>
</x-app-layout>