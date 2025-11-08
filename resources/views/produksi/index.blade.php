<!-- resources/views/produksi/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Data Produksi
        </h2>
        <p class="text-sm text-gray-600 mt-1">Manajemen data produksi pertanian</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Filter dan Actions -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
                        <div class="flex flex-wrap gap-4">
                            <!-- Filter Tahun -->
                            <select id="filterTahun" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunList as $tahun)
                                    <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Filter Bulan -->
                            <select id="filterBulan" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">Semua Bulan</option>
                                @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $key => $bulan)
                                    <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                                        {{ $bulan }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Filter Komoditas -->
                            <select id="filterKomoditas" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">Semua Komoditas</option>
                                @foreach($komoditas as $k)
                                    <option value="{{ $k->id }}" {{ request('komoditas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama }}
                                    </option>
                                @endforeach
                            </select>

                            <button onclick="applyFilters()" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                                Terapkan Filter
                            </button>
                        </div>

                        <a href="{{ route('produksi.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Produksi
                        </a>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun/Bulan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demplot</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Luas Panen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Produksi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produktivitas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($produksi as $p)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ $p->tahun }}</div>
                                        <div class="text-gray-500">{{ $p->nama_bulan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $p->demplot->nama_lahan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $p->komoditas->nama }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($p->luas_panen, 2) }} Ha
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($p->total_produksi, 2) }} Ton
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($p->produktivitas, 2) }} Ton/Ha
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('produksi.show', $p->id) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                                        <a href="{{ route('produksi.edit', $p->id) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                        <form action="{{ route('produksi.destroy', $p->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Hapus data produksi?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Belum ada data produksi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $produksi->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function applyFilters() {
            const tahun = document.getElementById('filterTahun').value;
            const bulan = document.getElementById('filterBulan').value;
            const komoditas = document.getElementById('filterKomoditas').value;
            
            let url = new URL(window.location.href);
            let params = new URLSearchParams();
            
            if (tahun) params.append('tahun', tahun);
            if (bulan) params.append('bulan', bulan);
            if (komoditas) params.append('komoditas_id', komoditas);
            
            window.location.href = url.pathname + '?' + params.toString();
        }
    </script>
    @endpush
</x-app-layout>