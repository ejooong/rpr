{{-- resources/views/bps-data/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Data BPS
        </h2>
        <p class="text-sm text-gray-600 mt-1">Tambah data produksi pertanian dari Badan Pusat Statistik</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('bps-data.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Tahun -->
                            <div>
                                <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun *</label>
                                <select name="tahun" id="tahun" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @for($i = date('Y'); $i >= 2000; $i--)
                                        <option value="{{ $i }}" {{ old('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('tahun')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Provinsi -->
                            <div>
                                <label for="provinsi_id" class="block text-sm font-medium text-gray-700">Provinsi *</label>
                                <select name="provinsi_id" id="provinsi_id" required
                                    class="provinsi-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach($provinsis as $provinsi)
                                        <option value="{{ $provinsi->id }}" {{ old('provinsi_id') == $provinsi->id ? 'selected' : '' }}>
                                            {{ $provinsi->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('provinsi_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Kabupaten (Optional) -->
                            <div>
                                <label for="kabupaten_id" class="block text-sm font-medium text-gray-700">Kabupaten/Kota</label>
                                <select name="kabupaten_id" id="kabupaten_id"
                                    class="kabupaten-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    {{ !old('provinsi_id') ? 'disabled' : '' }}>
                                    <option value="">Pilih Kabupaten/Kota</option>
                                    @if(old('provinsi_id'))
                                        <!-- Kabupaten akan di-load via AJAX -->
                                    @endif
                                </select>
                                @error('kabupaten_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kecamatan (Optional) -->
                            <div>
                                <label for="kecamatan_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                                <select name="kecamatan_id" id="kecamatan_id"
                                    class="kecamatan-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    {{ !old('kabupaten_id') ? 'disabled' : '' }}>
                                    <option value="">Pilih Kecamatan</option>
                                    @if(old('kabupaten_id'))
                                        <!-- Kecamatan akan di-load via AJAX -->
                                    @endif
                                </select>
                                @error('kecamatan_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Sektor -->
                            <div>
                                <label for="sektor_id" class="block text-sm font-medium text-gray-700">Sektor *</label>
                                <select name="sektor_id" id="sektor_id" required
                                    class="sektor-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Sektor</option>
                                    @foreach($sektors as $sektor)
                                        <option value="{{ $sektor->id }}" {{ old('sektor_id') == $sektor->id ? 'selected' : '' }}>
                                            {{ $sektor->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sektor_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Komoditas -->
                            <div>
                                <label for="komoditas_id" class="block text-sm font-medium text-gray-700">Komoditas *</label>
                                <select name="komoditas_id" id="komoditas_id" required
                                    class="komoditas-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    {{ !old('sektor_id') ? 'disabled' : '' }}>
                                    <option value="">Pilih Sektor terlebih dahulu</option>
                                    @if(old('sektor_id'))
                                        <!-- Komoditas akan di-load via AJAX berdasarkan sektor -->
                                    @endif
                                </select>
                                @error('komoditas_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Peringkat Wilayah -->
                            <div>
                                <label for="peringkat_wilayah" class="block text-sm font-medium text-gray-700">Peringkat Wilayah</label>
                                <input type="number" name="peringkat_wilayah" id="peringkat_wilayah" min="1"
                                    value="{{ old('peringkat_wilayah') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Opsional">
                                @error('peringkat_wilayah')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sumber Data -->
                            <div>
                                <label for="sumber_data" class="block text-sm font-medium text-gray-700">Sumber Data</label>
                                <select name="sumber_data" id="sumber_data"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="BPS" {{ old('sumber_data', 'BPS') == 'BPS' ? 'selected' : '' }}>BPS</option>
                                    <option value="Kementan" {{ old('sumber_data') == 'Kementan' ? 'selected' : '' }}>Kementan</option>
                                    <option value="Dinas Pertanian" {{ old('sumber_data') == 'Dinas Pertanian' ? 'selected' : '' }}>Dinas Pertanian</option>
                                    <option value="Lainnya" {{ old('sumber_data') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <!-- Produktivitas -->
                            <div>
                                <label for="produktivitas" class="block text-sm font-medium text-gray-700">Produktivitas (Ton/Ha)</label>
                                <input type="number" name="produktivitas" id="produktivitas" step="0.01" min="0"
                                    value="{{ old('produktivitas') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Otomatis terhitung" readonly>
                                <p class="mt-1 text-xs text-gray-500">Akan terhitung otomatis dari luas lahan dan produksi</p>
                                @error('produktivitas')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Luas Lahan -->
                            <div>
                                <label for="luas_lahan" class="block text-sm font-medium text-gray-700">Luas Lahan (Hektar)</label>
                                <input type="number" name="luas_lahan" id="luas_lahan" step="0.01" min="0"
                                    value="{{ old('luas_lahan') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="0.00">
                                @error('luas_lahan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Produksi -->
                            <div>
                                <label for="produksi" class="block text-sm font-medium text-gray-700">Total Produksi (Ton) *</label>
                                <input type="number" name="produksi" id="produksi" step="0.01" min="0" required
                                    value="{{ old('produksi') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="0.00">
                                @error('produksi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-6">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('bps-data.index') }}" 
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                                Batal
                            </a>
                            <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                Simpan Data
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
            // Elements
            const provinsiSelect = document.getElementById('provinsi_id');
            const kabupatenSelect = document.getElementById('kabupaten_id');
            const kecamatanSelect = document.getElementById('kecamatan_id');
            const sektorSelect = document.getElementById('sektor_id');
            const komoditasSelect = document.getElementById('komoditas_id');
            const luasLahanInput = document.getElementById('luas_lahan');
            const produksiInput = document.getElementById('produksi');
            const produktivitasInput = document.getElementById('produktivitas');

            // Hitung produktivitas otomatis
            function hitungProduktivitas() {
                const luasLahan = parseFloat(luasLahanInput.value) || 0;
                const produksi = parseFloat(produksiInput.value) || 0;
                
                if (luasLahan > 0 && produksi > 0) {
                    const produktivitas = produksi / luasLahan;
                    produktivitasInput.value = produktivitas.toFixed(2);
                } else {
                    produktivitasInput.value = '';
                }
            }

            luasLahanInput.addEventListener('input', hitungProduktivitas);
            produksiInput.addEventListener('input', hitungProduktivitas);

            // Load kabupaten berdasarkan provinsi
            function loadKabupaten(provinsiId) {
                if (!provinsiId) {
                    kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                    kabupatenSelect.disabled = true;
                    resetKecamatan();
                    return;
                }

                kabupatenSelect.innerHTML = '<option value="">Loading...</option>';
                kabupatenSelect.disabled = true;

                fetch(`/api/kabupaten/${provinsiId}`)
                    .then(response => response.json())
                    .then(data => {
                        kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                        data.forEach(kabupaten => {
                            const option = document.createElement('option');
                            option.value = kabupaten.id;
                            option.textContent = kabupaten.nama;
                            kabupatenSelect.appendChild(option);
                        });
                        kabupatenSelect.disabled = false;
                        resetKecamatan();
                    })
                    .catch(error => {
                        console.error('Error loading kabupaten:', error);
                        kabupatenSelect.innerHTML = '<option value="">Error loading data</option>';
                    });
            }

            // Load kecamatan berdasarkan kabupaten
            function loadKecamatan(kabupatenId) {
                if (!kabupatenId) {
                    resetKecamatan();
                    return;
                }

                kecamatanSelect.innerHTML = '<option value="">Loading...</option>';
                kecamatanSelect.disabled = true;

                fetch(`/api/kecamatan/${kabupatenId}`)
                    .then(response => response.json())
                    .then(data => {
                        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                        data.forEach(kecamatan => {
                            const option = document.createElement('option');
                            option.value = kecamatan.id;
                            option.textContent = kecamatan.nama;
                            kecamatanSelect.appendChild(option);
                        });
                        kecamatanSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading kecamatan:', error);
                        kecamatanSelect.innerHTML = '<option value="">Error loading data</option>';
                    });
            }

            // Reset kecamatan
            function resetKecamatan() {
                kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                kecamatanSelect.disabled = true;
            }

            // Load komoditas berdasarkan sektor
            function loadKomoditas(sektorId) {
                if (!sektorId) {
                    komoditasSelect.innerHTML = '<option value="">Pilih Sektor terlebih dahulu</option>';
                    komoditasSelect.disabled = true;
                    return;
                }

                komoditasSelect.innerHTML = '<option value="">Loading...</option>';
                komoditasSelect.disabled = true;

                fetch(`/api/komoditas/${sektorId}`)
                    .then(response => response.json())
                    .then(data => {
                        komoditasSelect.innerHTML = '<option value="">Pilih Komoditas</option>';
                        data.forEach(komoditas => {
                            const option = document.createElement('option');
                            option.value = komoditas.id;
                            option.textContent = komoditas.nama + ' (' + (komoditas.satuan || 'Ton') + ')';
                            komoditasSelect.appendChild(option);
                        });
                        komoditasSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading komoditas:', error);
                        komoditasSelect.innerHTML = '<option value="">Error loading komoditas</option>';
                    });
            }

            // Event listeners
            provinsiSelect.addEventListener('change', function() {
                loadKabupaten(this.value);
            });

            kabupatenSelect.addEventListener('change', function() {
                loadKecamatan(this.value);
            });

            sektorSelect.addEventListener('change', function() {
                loadKomoditas(this.value);
            });

            // Initialize form based on existing values
            if (provinsiSelect.value) {
                loadKabupaten(provinsiSelect.value);
            }
            if (kabupatenSelect.value) {
                loadKecamatan(kabupatenSelect.value);
            }
            if (sektorSelect.value) {
                loadKomoditas(sektorSelect.value);
            }
            
            // Hitung produktivitas awal jika ada nilai
            hitungProduktivitas();
        });
    </script>
    @endpush
</x-app-layout>