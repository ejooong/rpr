<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Komoditas: {{ $komoditas->nama }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Informasi lengkap komoditas</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Informasi Komoditas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Komoditas</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nama Komoditas</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $komoditas->nama }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Sektor</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $komoditas->sektor->nama }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Satuan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $komoditas->satuan }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $komoditas->deskripsi ?: '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Lainnya</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ikon</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($komoditas->ikon)
                                            <i class="{{ $komoditas->ikon }} mr-2"></i>{{ $komoditas->ikon }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Warna Chart</dt>
                                    <dd class="mt-1 flex items-center">
                                        @if($komoditas->warna_chart)
                                            <div class="w-6 h-6 rounded border border-gray-300 mr-2" style="background-color: {{ $komoditas->warna_chart }}"></div>
                                            <span class="text-sm text-gray-900 font-mono">{{ $komoditas->warna_chart }}</span>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status Unggulan</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $komoditas->status_unggulan ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $komoditas->status_unggulan ? 'Unggulan' : 'Biasa' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $komoditas->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $komoditas->aktif ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Statistik -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <dt class="text-sm font-medium text-blue-800">Total Demplot</dt>
                                <dd class="mt-1 text-2xl font-semibold text-blue-900">{{ $komoditas->demplot->count() }}</dd>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <dt class="text-sm font-medium text-green-800">Total Produksi</dt>
                                <dd class="mt-1 text-2xl font-semibold text-green-900">{{ $komoditas->produksi->count() }}</dd>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <dt class="text-sm font-medium text-purple-800">Tanggal Dibuat</dt>
                                <dd class="mt-1 text-sm font-semibold text-purple-900">{{ $komoditas->created_at->format('d M Y') }}</dd>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('komoditas.index') }}" class="btn-nasdem-secondary">
                            Kembali ke Daftar
                        </a>
                        <a href="{{ route('komoditas.edit', $komoditas) }}" class="btn-nasdem">
                            Edit Komoditas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>