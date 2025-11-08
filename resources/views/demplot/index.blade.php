<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Data Demplot
        </h2>
        <p class="text-sm text-gray-600 mt-1">Manajemen data demplot pertanian</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header dengan Tombol Tambah -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Daftar Demplot</h3>
                    <p class="text-sm text-gray-600">Total: {{ $demplots->total() }} demplot</p>
                </div>
                <a href="{{ route('demplot.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Demplot
                </a>
            </div>

            <!-- Filter Section -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <form action="{{ route('demplot.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 flex-1">
                            <!-- Filter Status -->
                            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="rencana" {{ request('status') == 'rencana' ? 'selected' : '' }}>Rencana</option>
                            </select>

                            <!-- Filter Komoditas -->
                            <select name="komoditas_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Komoditas</option>
                                @foreach($komoditas as $k)
                                    <option value="{{ $k->id }}" {{ request('komoditas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                    Terapkan
                                </button>
                                <a href="{{ route('demplot.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lahan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petani</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Luas Lahan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($demplots as $demplot)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $demplot->nama_lahan }}</div>
                                    <div class="text-sm text-gray-500">
                                        @if($demplot->desa)
                                            {{ $demplot->desa->nama }}, {{ $demplot->kecamatan->nama }}
                                        @else
                                            {{ $demplot->alamat }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $demplot->petani->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($demplot->komoditas)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $demplot->komoditas->nama }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($demplot->luas_lahan, 2) }} Ha
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'aktif' => 'bg-green-100 text-green-800',
                                            'selesai' => 'bg-blue-100 text-blue-800', 
                                            'rencana' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                        $color = $statusColors[$demplot->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ ucfirst($demplot->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $demplot->tahun }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('demplot.show', $demplot->id) }}" 
                                           class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                                           title="Lihat">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('demplot.edit', $demplot->id) }}" 
                                           class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50"
                                           title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
<button type="button" 
        onclick="confirmDelete({{ $demplot->id }}, 'Demplot {{ $demplot->nama_lahan }}', `
            <div class='space-y-1'>
                <div class='flex'><span class='w-20 font-medium'>Lahan:</span><span>{{ $demplot->nama_lahan }}</span></div>
                <div class='flex'><span class='w-20 font-medium'>Komoditas:</span><span>{{ $demplot->komoditas->nama }}</span></div>
                <div class='flex'><span class='w-20 font-medium'>Lokasi:</span><span>{{ $demplot->provinsi->nama }}{{ $demplot->kabupaten ? ', ' . $demplot->kabupaten->nama : '' }}</span></div>
                <div class='flex'><span class='w-16 font-medium'>Luas:</span><span>{{ $demplot->luas_lahan }} Ha</span></div>
            </div>
        `)"
        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
        title="Hapus">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
    </svg>
</button>

<form id="delete-form-{{ $demplot->id }}" action="{{ route('demplot.destroy', $demplot->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                    </svg>
                                    <p class="text-gray-500 text-sm mb-2">Belum ada data demplot</p>
                                    <a href="{{ route('demplot.create') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Tambah demplot pertama
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($demplots->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $demplots->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>