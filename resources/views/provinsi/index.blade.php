<!-- resources/views/provinsi/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Data Provinsi
        </h2>
        <p class="text-sm text-gray-600 mt-1">Kelola data provinsi</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Actions -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex space-x-2">
                    <a href="{{ route('wilayah.create') }}" class="btn-nasdem flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Wilayah
                    </a>
                </div>
                
                <!-- Search Form -->
                <form action="{{ route('provinsi.index') }}" method="GET" class="flex items-center space-x-2">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari provinsi..." 
                           class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="btn-nasdem-secondary">
                        Cari
                    </button>
                    @if(request('search'))
                    <a href="{{ route('provinsi.index') }}" class="btn-nasdem-outline">
                        Reset
                    </a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Provinsi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Koordinat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah Kabupaten
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($provinsis as $provinsi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                    {{ $provinsi->kode }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $provinsi->nama }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($provinsi->latitude && $provinsi->longitude)
                                        {{ number_format($provinsi->latitude, 4) }}, {{ number_format($provinsi->longitude, 4) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $provinsi->kabupatens_count ?? $provinsi->kabupatens->count() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('provinsi.show', $provinsi) }}" 
                                           class="text-green-600 hover:text-green-900 transition duration-150 ease-in-out"
                                           title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('provinsi.edit', $provinsi) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition duration-150 ease-in-out"
                                           title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        
                                        <!-- Delete Button menggunakan modal global -->
                                        <button type="button" 
                                                onclick="confirmDelete({{ $provinsi->id }}, 'Provinsi {{ $provinsi->nama }}', `
                                                    <div class='space-y-1 text-sm'>
                                                        <div class='flex'><span class='w-20 font-medium'>Kode:</span><span class='font-mono'>{{ $provinsi->kode }}</span></div>
                                                        <div class='flex'><span class='w-20 font-medium'>Nama:</span><span>{{ $provinsi->nama }}</span></div>
                                                        <div class='flex'><span class='w-20 font-medium'>Koordinat:</span><span>{{ $provinsi->latitude && $provinsi->longitude ? number_format($provinsi->latitude, 4) . ', ' . number_format($provinsi->longitude, 4) : '-' }}</span></div>
                                                        <div class='flex'><span class='w-20 font-medium'>Kabupaten:</span><span>{{ $provinsi->kabupatens_count ?? $provinsi->kabupatens->count() }} kabupaten</span></div>
                                                    </div>
                                                `)"
                                                class="text-red-600 hover:text-red-900 transition duration-200"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>

                                        <!-- Hidden Delete Form -->
                                        <form id="delete-form-{{ $provinsi->id }}" action="{{ route('provinsi.destroy', $provinsi) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Belum ada data provinsi
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($provinsis->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $provinsis->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>