<!-- resources/views/produksi/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Data Produksi
        </h2>
        <p class="text-sm text-gray-600 mt-1">Input data produksi pertanian baru</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('produksi.store') }}" method="POST" id="produksiForm">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Demplot -->
                            <div class="md:col-span-2">
                                <label for="demplot_id" class="block text-sm font-medium text-gray-700">Demplot *</label>
                                <select name="demplot_id" id="demplot_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Demplot</option>
                                    @foreach($demplots as $demplot)
                                        <option value="{{ $demplot->id }}" {{ old('demplot_id') == $demplot->id ? 'selected' : '' }}>
                                            {{ $demplot->nama_lahan }} - {{ $demplot->petani->nama }} ({{ $demplot->komoditas->nama }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('demplot_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Komoditas -->
                            <div>
                                <label for="komoditas_id" class="block text-sm font-medium text-gray-700">Komoditas *</label>
                                <select name="komoditas_id" id="komoditas_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Komoditas</option>
                                    @foreach($komoditas as $k)
                                        <option value="{{ $k->id }}" {{ old('komoditas_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('komoditas_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tahun -->
                            <div>
                                <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun *</label>
                                <select name="tahun" id="tahun" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Tahun</option>
                                    @for($i = date('Y'); $i >= 2020; $i--)
                                        <option value="{{ $i }}" {{ old('tahun', date('Y')) == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('tahun')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bulan -->
                            <div>
                                <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan *</label>
                                <select name="bulan" id="bulan" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Bulan</option>
                                    @foreach([
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ] as $key => $value)
                                        <option value="{{ $key }}" {{ old('bulan', date('n')) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bulan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Luas Panen -->
                            <div>
                                <label for="luas_panen" class="block text-sm font-medium text-gray-700">Luas Panen (Ha) *</label>
                                <input type="number" name="luas_panen" id="luas_panen" value="{{ old('luas_panen') }}" step="0.01" min="0"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('luas_panen')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Produksi -->
                            <div>
                                <label for="total_produksi" class="block text-sm font-medium text-gray-700">Total Produksi (Ton) *</label>
                                <input type="number" name="total_produksi" id="total_produksi" value="{{ old('total_produksi') }}" step="0.01" min="0"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('total_produksi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Produktivitas (Auto-calculate) -->
                            <div>
                                <label for="produktivitas" class="block text-sm font-medium text-gray-700">Produktivitas (Ton/Ha)</label>
                                <input type="number" name="produktivitas" id="produktivitas" value="{{ old('produktivitas') }}" step="0.01" min="0" readonly
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-100 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                @error('produktivitas')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Dihitung otomatis dari Total Produksi / Luas Panen</p>
                            </div>

                            <!-- Tanggal Input -->
                            <div>
                                <label for="tanggal_input" class="block text-sm font-medium text-gray-700">Tanggal Input *</label>
                                <input type="date" name="tanggal_input" id="tanggal_input" value="{{ old('tanggal_input', date('Y-m-d')) }}"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('tanggal_input')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sumber Data -->
                            <div class="md:col-span-2">
                                <label for="sumber_data" class="block text-sm font-medium text-gray-700">Sumber Data</label>
                                <input type="text" name="sumber_data" id="sumber_data" value="{{ old('sumber_data') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Contoh: Laporan Lapangan, Survey, dll">
                                @error('sumber_data')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('produksi.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Simpan Produksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const luasPanenInput = document.getElementById('luas_panen');
            const totalProduksiInput = document.getElementById('total_produksi');
            const produktivitasInput = document.getElementById('produktivitas');

            function calculateProduktivitas() {
                const luasPanen = parseFloat(luasPanenInput.value) || 0;
                const totalProduksi = parseFloat(totalProduksiInput.value) || 0;
                
                if (luasPanen > 0 && totalProduksi > 0) {
                    const produktivitas = totalProduksi / luasPanen;
                    produktivitasInput.value = produktivitas.toFixed(2);
                } else {
                    produktivitasInput.value = '0';
                }
            }

            // Hitung produktivitas saat input berubah
            luasPanenInput.addEventListener('input', calculateProduktivitas);
            totalProduksiInput.addEventListener('input', calculateProduktivitas);

            // Auto-select komoditas berdasarkan demplot yang dipilih
            const demplotSelect = document.getElementById('demplot_id');
            const komoditasSelect = document.getElementById('komoditas_id');

            demplotSelect.addEventListener('change', function() {
                const selectedOption = demplotSelect.options[demplotSelect.selectedIndex];
                if (selectedOption.value) {
                    // Extract komoditas_id from demplot option text or data attribute
                    // This assumes the option text contains komoditas info
                    // You might need to adjust this based on your data structure
                    const optionText = selectedOption.text;
                    // Simple approach - you might want to add data-komoditas attribute to options
                    console.log('Demplot selected:', optionText);
                }
            });

            // Validasi form
            document.getElementById('produksiForm').addEventListener('submit', function(e) {
                const luasPanen = parseFloat(luasPanenInput.value);
                const totalProduksi = parseFloat(totalProduksiInput.value);

                if (luasPanen === 0 && totalProduksi > 0) {
                    e.preventDefault();
                    alert('Luas panen tidak boleh 0 jika ada total produksi!');
                    luasPanenInput.focus();
                    return false;
                }

                if (totalProduksi === 0 && luasPanen > 0) {
                    e.preventDefault();
                    alert('Total produksi tidak boleh 0 jika ada luas panen!');
                    totalProduksiInput.focus();
                    return false;
                }
            });

            // Hitung awal jika ada nilai old
            calculateProduktivitas();
        });
    </script>
    @endpush
</x-app-layout>