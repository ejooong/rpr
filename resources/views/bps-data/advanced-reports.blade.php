{{-- resources/views/bps-data/advanced-reports.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-chart-line mr-2"></i>Laporan Advanced Data BPS
        </h2>
        <p class="text-sm text-gray-600 mt-1">Analisis mendalam komoditas unggulan dan ranking wilayah berdasarkan data BPS</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-filter mr-2"></i>Filter Laporan Data BPS
                        </h3>
                    </div>
                    
                    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                            <select name="tahun" id="tahun" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach($tahunList as $tahunItem)
                                    <option value="{{ $tahunItem }}" {{ $tahunItem == date('Y') - 1 ? 'selected' : '' }}>
                                        {{ $tahunItem }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="kabupaten_id" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                            <select name="kabupaten_id" id="kabupaten_id" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Kabupaten</option>
                                @foreach($kabupatens as $kabupaten)
                                    <option value="{{ $kabupaten->id }}">
                                        {{ $kabupaten->nama }} - {{ $kabupaten->provinsi->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="kecamatan_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                            <select name="kecamatan_id" id="kecamatan_id" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Kecamatan</option>
                                @foreach($kecamatans as $kecamatan)
                                    <option value="{{ $kecamatan->id }}">
                                        {{ $kecamatan->nama }} - {{ $kecamatan->kabupaten->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="sektor_id" class="block text-sm font-medium text-gray-700">Sektor</label>
                            <select name="sektor_id" id="sektor_id" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Sektor</option>
                                @foreach($sektors as $sektor)
                                    <option value="{{ $sektor->id }}">
                                        {{ $sektor->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="md:col-span-4 flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                <i class="fas fa-search mr-2"></i>Terapkan Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                            <i class="fas fa-spinner fa-spin text-blue-600"></i>
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Memuat Data BPS...</h3>
                        <p class="text-sm text-gray-500 mt-1">Sedang memproses laporan advanced</p>
                    </div>
                </div>
            </div>

            <!-- Komoditas Unggulan Detail -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-green-800">
                            <i class="fas fa-star mr-2"></i>Analisis Komoditas Unggulan per Kecamatan
                        </h3>
                        <button onclick="loadKomoditasUnggulan()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh Data
                        </button>
                    </div>
                    
                    <div id="komoditasUnggulanContent">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-chart-bar text-4xl mb-3"></i>
                            <p>Klik "Refresh Data" untuk memuat analisis komoditas unggulan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ranking Komoditas per Kecamatan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-blue-800">
                            <i class="fas fa-leaf mr-2"></i>Ranking Komoditas per Kecamatan
                        </h3>
                        <button onclick="loadRankingKomoditas()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh Data
                        </button>
                    </div>
                    
                    <div id="rankingKomoditasContent">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-leaf text-4xl mb-3"></i>
                            <p>Klik "Refresh Data" untuk memuat ranking komoditas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ranking Kecamatan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-yellow-800">
                            <i class="fas fa-trophy mr-2"></i>Ranking Produktivitas Kecamatan
                        </h3>
                        <button onclick="loadRankingKecamatan()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh Data
                        </button>
                    </div>
                    
                    <div id="rankingKecamatanContent">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-trophy text-4xl mb-3"></i>
                            <p>Klik "Refresh Data" untuk memuat ranking kecamatan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Section -->
<!-- Export Section -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-red-800">
                <i class="fas fa-file-export mr-2"></i>Export Laporan Analisis
            </h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <button onclick="exportKomoditasUnggulan()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-md transition duration-200">
                <i class="fas fa-star mr-2"></i>Export Komoditas Unggulan
            </button>
            <button onclick="exportRankingKomoditas()" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-3 px-4 rounded-md transition duration-200">
                <i class="fas fa-leaf mr-2"></i>Export Ranking Komoditas
            </button>
            <button onclick="exportRankingKecamatan()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition duration-200">
                <i class="fas fa-trophy mr-2"></i>Export Ranking Kecamatan
            </button>
        </div>
    </div>
</div>
        </div>
    </div>

@push('scripts')
<script>
    let currentFilters = {};

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form submission
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            loadAllReports();
        });

        // Load all reports on page load
        loadAllReports();
    });

    function getFilters() {
        return {
            tahun: document.getElementById('tahun').value,
            kabupaten_id: document.getElementById('kabupaten_id').value,
            kecamatan_id: document.getElementById('kecamatan_id').value,
            sektor_id: document.getElementById('sektor_id').value
        };
    }

    function showLoading() {
        document.getElementById('loadingIndicator').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('loadingIndicator').classList.add('hidden');
    }

    function loadAllReports() {
        currentFilters = getFilters();
        loadKomoditasUnggulan();
        loadRankingKomoditas();
        loadRankingKecamatan();
    }

    function loadKomoditasUnggulan() {
        showLoading();
        const filters = getFilters();
        
        fetch(`/bps-data/api/advanced-reports/komoditas-unggulan?${new URLSearchParams(filters)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                renderKomoditasUnggulan(data);
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                document.getElementById('komoditasUnggulanContent').innerHTML = `
                    <div class="text-center py-8 text-red-600">
                        <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                        <p>Gagal memuat data komoditas unggulan: ${error.message}</p>
                    </div>
                `;
            });
    }

    function renderKomoditasUnggulan(data) {
        const content = document.getElementById('komoditasUnggulanContent');
        
        if (!data.success || !data.data || data.data.length === 0) {
            content.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-bar text-4xl mb-3"></i>
                    <p>Tidak ada data komoditas unggulan untuk filter yang dipilih</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="mb-4 text-sm text-gray-600">
                Menampilkan ${data.total} data komoditas unggulan
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse border border-gray-300">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2">Kecamatan</th>
                            <th class="border border-gray-300 px-4 py-2">Komoditas</th>
                            <th class="border border-gray-300 px-4 py-2">Sektor</th>
                            <th class="border border-gray-300 px-4 py-2">Luas Lahan (Ha)</th>
                            <th class="border border-gray-300 px-4 py-2">Produksi (Ton)</th>
                            <th class="border border-gray-300 px-4 py-2">Produktivitas</th>
                            <th class="border border-gray-300 px-4 py-2">Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        let currentKecamatan = '';
        data.data.forEach((item, index) => {
            if (currentKecamatan !== item.kecamatan) {
                currentKecamatan = item.kecamatan;
                // Count rows for this kecamatan
                const kecamatanRows = data.data.filter(d => d.kecamatan === item.kecamatan).length;
                html += `
                    <tr class="bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2 font-semibold" rowspan="${kecamatanRows}">
                            ${item.kecamatan}
                            <div class="text-xs text-gray-600">${item.kabupaten}</div>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">${item.komoditas}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getSektorBadgeColor(item.sektor)}">
                                ${item.sektor}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-right">${formatNumber(item.luas_lahan)}</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">${formatNumber(item.produksi)}</td>
                        <td class="border border-gray-300 px-4 py-2 text-right font-semibold">${item.produktivitas} Ton/Ha</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">${item.kontribusi}%</td>
                    </tr>
                `;
            } else {
                html += `
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">${item.komoditas}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getSektorBadgeColor(item.sektor)}">
                                ${item.sektor}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-right">${formatNumber(item.luas_lahan)}</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">${formatNumber(item.produksi)}</td>
                        <td class="border border-gray-300 px-4 py-2 text-right font-semibold">${item.produktivitas} Ton/Ha</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">${item.kontribusi}%</td>
                    </tr>
                `;
            }
        });

        html += `</tbody></table></div>`;
        content.innerHTML = html;
    }

    function loadRankingKomoditas() {
        showLoading();
        const filters = getFilters();
        
        fetch(`/bps-data/api/advanced-reports/ranking-komoditas?${new URLSearchParams(filters)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                renderRankingKomoditas(data);
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                document.getElementById('rankingKomoditasContent').innerHTML = `
                    <div class="text-center py-8 text-red-600">
                        <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                        <p>Gagal memuat data ranking komoditas: ${error.message}</p>
                    </div>
                `;
            });
    }

    function renderRankingKomoditas(data) {
        const content = document.getElementById('rankingKomoditasContent');
        
        if (!data.success || !data.data || data.data.length === 0) {
            content.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-leaf text-4xl mb-3"></i>
                    <p>Tidak ada data ranking komoditas untuk filter yang dipilih</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="mb-4 text-sm text-gray-600">
                Menampilkan ranking komoditas per kecamatan
            </div>
            <div class="space-y-6">
        `;

        data.data.forEach(kecamatan => {
            html += `
                <div class="border border-gray-200 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-800">
                            <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                            ${kecamatan.kecamatan}
                            <span class="text-sm text-gray-600 ml-2">${kecamatan.kabupaten}</span>
                        </h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 w-16">Rank</th>
                                    <th class="border border-gray-300 px-4 py-2">Komoditas</th>
                                    <th class="border border-gray-300 px-4 py-2">Sektor</th>
                                    <th class="border border-gray-300 px-4 py-2">Luas Lahan</th>
                                    <th class="border border-gray-300 px-4 py-2">Produksi</th>
                                    <th class="border border-gray-300 px-4 py-2">Produktivitas</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

            kecamatan.data.forEach((item, index) => {
                const rank = index + 1;
                html += `
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full ${getRankBadgeColor(rank)} text-white font-bold text-sm">
                                ${rank}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">${item.komoditas}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getSektorBadgeColor(item.sektor)}">
                                ${item.sektor}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-right">${formatNumber(item.luas_lahan)} Ha</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">${formatNumber(item.produksi)} Ton</td>
                        <td class="border border-gray-300 px-4 py-2 text-right font-semibold text-blue-600">
                            ${item.produktivitas} Ton/Ha
                        </td>
                    </tr>
                `;
            });

            html += `</tbody></table></div></div>`;
        });

        html += `</div>`;
        content.innerHTML = html;
    }

    function loadRankingKecamatan() {
        showLoading();
        const filters = getFilters();
        
        fetch(`/bps-data/api/advanced-reports/ranking-kecamatan?${new URLSearchParams(filters)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                renderRankingKecamatan(data);
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                document.getElementById('rankingKecamatanContent').innerHTML = `
                    <div class="text-center py-8 text-red-600">
                        <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                        <p>Gagal memuat data ranking kecamatan: ${error.message}</p>
                    </div>
                `;
            });
    }

    function renderRankingKecamatan(data) {
        const content = document.getElementById('rankingKecamatanContent');
        
        if (!data.success || !data.data || data.data.length === 0) {
            content.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-trophy text-4xl mb-3"></i>
                    <p>Tidak ada data ranking kecamatan untuk filter yang dipilih</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="mb-4 text-sm text-gray-600">
                Menampilkan ${data.total} kecamatan berdasarkan produktivitas
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse border border-gray-300">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 w-16">Rank</th>
                            <th class="border border-gray-300 px-4 py-2">Kecamatan</th>
                            <th class="border border-gray-300 px-4 py-2">Jumlah Komoditas</th>
                            <th class="border border-gray-300 px-4 py-2">Luas Lahan (Ha)</th>
                            <th class="border border-gray-300 px-4 py-2">Produksi (Ton)</th>
                            <th class="border border-gray-300 px-4 py-2">Produktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.data.forEach((item, index) => {
            const rank = index + 1;
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full ${getRankBadgeColor(rank)} text-white font-bold text-sm">
                            ${rank}
                        </span>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <div class="font-semibold">${item.kecamatan}</div>
                        <div class="text-sm text-gray-600">${item.kabupaten}, ${item.provinsi}</div>
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-center">${item.jumlah_komoditas}</td>
                    <td class="border border-gray-300 px-4 py-2 text-right">${formatNumber(item.luas_lahan)}</td>
                    <td class="border border-gray-300 px-4 py-2 text-right">${formatNumber(item.produksi)}</td>
                    <td class="border border-gray-300 px-4 py-2 text-right font-semibold text-blue-600">
                        ${item.produktivitas} Ton/Ha
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        content.innerHTML = html;
    }

    // Helper functions
    function getSektorBadgeColor(sektor) {
        const colors = {
            'Hortikultura': 'bg-green-100 text-green-800',
            'Tanaman Pangan': 'bg-yellow-100 text-yellow-800',
            'Perkebunan': 'bg-orange-100 text-orange-800',
            'Peternakan': 'bg-red-100 text-red-800',
            'Perikanan': 'bg-blue-100 text-blue-800'
        };
        return colors[sektor] || 'bg-gray-100 text-gray-800';
    }

    function getRankBadgeColor(rank) {
        if (rank === 1) return 'bg-red-500';
        if (rank <= 3) return 'bg-yellow-500';
        return 'bg-blue-500';
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

   function exportKomoditasUnggulan() {
    const filters = getFilters();
    const params = new URLSearchParams(filters);
    window.location.href = `/bps-data/export/komoditas-unggulan-advanced?${params}`;
}
function exportRankingKomoditas() {
    const filters = getFilters();
    const params = new URLSearchParams(filters);
    window.location.href = `/bps-data/export/ranking-komoditas-advanced?${params}`;
}
function exportRankingKecamatan() {
    const filters = getFilters();
    const params = new URLSearchParams(filters);
    window.location.href = `/bps-data/export/ranking-kecamatan-advanced?${params}`;
}
    function exportRanking() {
        alert('Fitur export ranking akan segera dilengkapi');
    }
</script>
@endpush
</x-app-layout>