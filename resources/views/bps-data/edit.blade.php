{{-- resources/views/bps-data/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Data BPS
        </h2>
        <p class="text-sm text-gray-600 mt-1">Edit data produksi pertanian dari Badan Pusat Statistik</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('bps-data.update', $bpsData) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Tahun -->
                            <div>
                                <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun *</label>
                                <select name="tahun" id="tahun" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @for($i = date('Y'); $i >= 2000; $i--)
                                        <option value="{{ $i }}" {{ $bpsData->tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach($provinsis as $provinsi)
                                        <option value="{{ $provinsi->id }}" {{ $bpsData->provinsi_id == $provinsi->id ? 'selected' : '' }}>
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Kabupaten/Kota</option>
                                    <!-- Kabupaten akan di-load via AJAX -->
                                </select>
                                @error('kabupaten_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kecamatan (Optional) -->
                            <div>
                                <label for="kecamatan_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                                <select name="kecamatan_id" id="kecamatan_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Kecamatan</option>
                                    <!-- Kecamatan akan di-load via AJAX -->
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Sektor</option>
                                    @foreach($sektors as $sektor)
                                        <option value="{{ $sektor->id }}" {{ $bpsData->sektor_id == $sektor->id ? 'selected' : '' }}>
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Komoditas</option>
                                    @foreach($komoditasList as $komoditas)
                                        <option value="{{ $komoditas->id }}" {{ $bpsData->komoditas_id == $komoditas->id ? 'selected' : '' }}
                                            data-warna="{{ $komoditas->warna_chart }}"
                                            data-satuan="{{ $komoditas->satuan }}">
                                            {{ $komoditas->nama }} ({{ $komoditas->satuan }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('komoditas_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Status Unggulan -->
                            <div class="flex items-center">
                                <input type="checkbox" name="status_unggulan" id="status_unggulan" value="1" 
                                    {{ $bpsData->status_unggulan ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <label for="status_unggulan" class="ml-2 text-sm text-gray-700">
                                    Tandai sebagai Komoditas Unggulan
                                </label>
                            </div>

                            <!-- Peringkat Wilayah -->
                            <div>
                                <label for="peringkat_wilayah" class="block text-sm font-medium text-gray-700">Peringkat Wilayah</label>
                                <input type="number" name="peringkat_wilayah" id="peringkat_wilayah" min="1"
                                    value="{{ $bpsData->peringkat_wilayah }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Peringkat di wilayah">
                                @error('peringkat_wilayah')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sumber Data -->
                            <div>
                                <label for="sumber_data" class="block text-sm font-medium text-gray-700">Sumber Data</label>
                                <select name="sumber_data" id="sumber_data"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="BPS" {{ $bpsData->sumber_data == 'BPS' ? 'selected' : '' }}>BPS</option>
                                    <option value="Kementan" {{ $bpsData->sumber_data == 'Kementan' ? 'selected' : '' }}>Kementan</option>
                                    <option value="Dinas Pertanian" {{ $bpsData->sumber_data == 'Dinas Pertanian' ? 'selected' : '' }}>Dinas Pertanian</option>
                                    <option value="Lainnya" {{ $bpsData->sumber_data == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Luas Lahan -->
                            <div>
                                <label for="luas_lahan" class="block text-sm font-medium text-gray-700">Luas Lahan (Hektar)</label>
                                <input type="number" name="luas_lahan" id="luas_lahan" step="0.01" min="0"
                                    value="{{ $bpsData->luas_lahan }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="0.00">
                                @error('luas_lahan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Produksi -->
                            <div>
                                <label for="produksi" class="block text-sm font-medium text-gray-700">Total Produksi *</label>
                                <input type="number" name="produksi" id="produksi" step="0.01" min="0" required
                                    value="{{ $bpsData->produksi }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="0.00">
                                @error('produksi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Produktivitas -->
                            <div>
                                <label for="produktivitas" class="block text-sm font-medium text-gray-700">Produktivitas (Ton/Ha)</label>
                                <input type="number" name="produktivitas" id="produktivitas" step="0.01" min="0"
                                    value="{{ $bpsData->produktivitas }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Otomatis terhitung">
                                <p class="mt-1 text-xs text-gray-500">Akan terhitung otomatis jika luas lahan diisi</p>
                                @error('produktivitas')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-6">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Catatan tambahan...">{{ $bpsData->keterangan }}</textarea>
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
                                Update Data
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
            // Hitung produktivitas otomatis
            const luasLahanInput = document.getElementById('luas_lahan');
            const produksiInput = document.getElementById('produksi');
            const produktivitasInput = document.getElementById('produktivitas');

            function hitungProduktivitas() {
                const luasLahan = parseFloat(luasLahanInput.value) || 0;
                const produksi = parseFloat(produksiInput.value) || 0;
                
                if (luasLahan > 0 && produksi > 0) {
                    const produktivitas = produksi / luasLahan;
                    produktivitasInput.value = produktivitas.toFixed(2);
                } else if (produktivitasInput.value === '') {
                    produktivitasInput.value = '';
                }
            }

            luasLahanInput.addEventListener('input', hitungProduktivitas);
            produksiInput.addEventListener('input', hitungProduktivitas);

            // Load kabupaten berdasarkan provinsi
            const provinsiSelect = document.getElementById('provinsi_id');
            const kabupatenSelect = document.getElementById('kabupaten_id');
            const kecamatanSelect = document.getElementById('kecamatan_id');

            // Data dari PHP untuk pre-select
            const currentKabupatenId = {{ $bpsData->kabupaten_id ?? 0 }};
            const currentKecamatanId = {{ $bpsData->kecamatan_id ?? 0 }};

            function loadKabupaten(provinsiId, callback = null) {
                if (provinsiId) {
                    fetch(`/api/kabupaten/${provinsiId}`)
                        .then(response => response.json())
                        .then(data => {
                            kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                            data.forEach(kabupaten => {
                                const option = document.createElement('option');
                                option.value = kabupaten.id;
                                option.textContent = kabupaten.nama;
                                if (kabupaten.id == currentKabupatenId) {
                                    option.selected = true;
                                }
                                kabupatenSelect.appendChild(option);
                            });
                            
                            // Jika ada callback, jalankan setelah kabupaten di-load
                            if (callback) callback();
                            
                            // Trigger change event untuk load kecamatan
                            if (currentKabupatenId) {
                                kabupatenSelect.dispatchEvent(new Event('change'));
                            }
                        })
                        .catch(error => {
                            console.error('Error loading kabupaten:', error);
                        });
                } else {
                    kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                    kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                }
            }

            function loadKecamatan(kabupatenId) {
                if (kabupatenId) {
                    fetch(`/api/kecamatan/${kabupatenId}`)
                        .then(response => response.json())
                        .then(data => {
                            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                            data.forEach(kecamatan => {
                                const option = document.createElement('option');
                                option.value = kecamatan.id;
                                option.textContent = kecamatan.nama;
                                if (kecamatan.id == currentKecamatanId) {
                                    option.selected = true;
                                }
                                kecamatanSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error loading kecamatan:', error);
                        });
                } else {
                    kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                }
            }

            provinsiSelect.addEventListener('change', function() {
                loadKabupaten(this.value);
            });

            kabupatenSelect.addEventListener('change', function() {
                loadKecamatan(this.value);
            });

            // Load data saat halaman dimuat
            if (provinsiSelect.value) {
                loadKabupaten(provinsiSelect.value, function() {
                    // Pre-select kecamatan setelah kabupaten di-load
                    if (currentKecamatanId) {
                        loadKecamatan(currentKabupatenId);
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>