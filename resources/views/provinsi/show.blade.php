<!-- resources/views/provinsi/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Provinsi: {{ $provinsi->nama }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Informasi lengkap data provinsi</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Detail Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Provinsi</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Kode Provinsi</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $provinsi->kode }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nama Provinsi</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $provinsi->nama }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Koordinat</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($provinsi->latitude && $provinsi->longitude)
                                            {{ number_format($provinsi->latitude, 4) }}, {{ number_format($provinsi->longitude, 4) }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Jumlah Kabupaten/Kota</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $kabupatensCount ?? $provinsi->kabupatens_count ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Jumlah Kecamatan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $kecamatansCount ?? $provinsi->kecamatans_count ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Jumlah Desa/Kelurahan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $desasCount ?? $provinsi->desas_count ?? 0 }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('provinsi.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                            Kembali
                        </a>
                        <a href="{{ route('provinsi.edit', $provinsi) }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                            Edit Provinsi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>