<!-- resources/views/wilayah/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Data Wilayah
        </h2>
        <p class="text-sm text-gray-600 mt-1">Tambah data wilayah administrasi lengkap</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form id="wilayahForm" action="{{ route('wilayah.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Level Wilayah -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Level Wilayah yang Ditambah</label>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="level" value="provinsi" checked 
                                               class="level-radio focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">Provinsi</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="level" value="kabupaten"
                                               class="level-radio focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">Kabupaten/Kota</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="level" value="kecamatan"
                                               class="level-radio focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">Kecamatan</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="level" value="desa"
                                               class="level-radio focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">Desa/Kelurahan</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Form Provinsi (Always Show) -->
                            <div id="provinsi-form" class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-6">
                                <div>
                                    <label for="kode_provinsi" class="block text-sm font-medium text-gray-700">Kode Provinsi *</label>
                                    <input type="text" name="kode_provinsi" id="kode_provinsi" value="{{ old('kode_provinsi') }}" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kode_provinsi') border-red-500 @enderror">
                                    @error('kode_provinsi')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="nama_provinsi" class="block text-sm font-medium text-gray-700">Nama Provinsi *</label>
                                    <input type="text" name="nama_provinsi" id="nama_provinsi" value="{{ old('nama_provinsi') }}" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_provinsi') border-red-500 @enderror">
                                    @error('nama_provinsi')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="latitude_provinsi" class="block text-sm font-medium text-gray-700">Latitude</label>
                                    <input type="number" step="any" name="latitude_provinsi" id="latitude_provinsi" value="{{ old('latitude_provinsi') }}" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('latitude_provinsi') border-red-500 @enderror">
                                    @error('latitude_provinsi')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="longitude_provinsi" class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <input type="number" step="any" name="longitude_provinsi" id="longitude_provinsi" value="{{ old('longitude_provinsi') }}" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('longitude_provinsi') border-red-500 @enderror">
                                    @error('longitude_provinsi')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Kabupaten (Hidden by default) -->
                            <div id="kabupaten-form" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-6">
                                <div class="md:col-span-2">
                                    <label for="provinsi_id_kabupaten" class="block text-sm font-medium text-gray-700">Provinsi *</label>
                                    <select name="provinsi_id_kabupaten" id="provinsi_id_kabupaten" 
                                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('provinsi_id_kabupaten') border-red-500 @enderror">
                                        <option value="">Pilih Provinsi</option>
                                        <!-- Options will be loaded via AJAX -->
                                    </select>
                                    @error('provinsi_id_kabupaten')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="kode_kabupaten" class="block text-sm font-medium text-gray-700">Kode Kabupaten *</label>
                                    <input type="text" name="kode_kabupaten" id="kode_kabupaten" value="{{ old('kode_kabupaten') }}" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kode_kabupaten') border-red-500 @enderror">
                                    @error('kode_kabupaten')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="nama_kabupaten" class="block text-sm font-medium text-gray-700">Nama Kabupaten/Kota *</label>
                                    <input type="text" name="nama_kabupaten" id="nama_kabupaten" value="{{ old('nama_kabupaten') }}" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_kabupaten') border-red-500 @enderror">
                                    @error('nama_kabupaten')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="tipe_kabupaten" class="block text-sm font-medium text-gray-700">Tipe *</label>
                                    <select name="tipe_kabupaten" id="tipe_kabupaten" 
                                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tipe_kabupaten') border-red-500 @enderror">
                                        <option value="">Pilih Tipe</option>
                                        <option value="kabupaten" {{ old('tipe_kabupaten') == 'kabupaten' ? 'selected' : '' }}>Kabupaten</option>
                                        <option value="kota" {{ old('tipe_kabupaten') == 'kota' ? 'selected' : '' }}>Kota</option>
                                    </select>
                                    @error('tipe_kabupaten')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="latitude_kabupaten" class="block text-sm font-medium text-gray-700">Latitude</label>
                                    <input type="number" step="any" name="latitude_kabupaten" id="latitude_kabupaten" value="{{ old('latitude_kabupaten') }}" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('latitude_kabupaten') border-red-500 @enderror">
                                    @error('latitude_kabupaten')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="longitude_kabupaten" class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <input type="number" step="any" name="longitude_kabupaten" id="longitude_kabupaten" value="{{ old('longitude_kabupaten') }}" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('longitude_kabupaten') border-red-500 @enderror">
                                    @error('longitude_kabupaten')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Kecamatan (Hidden by default) -->
                            <div id="kecamatan-form" class="hidden space-y-6 border-t pt-6">
                                <!-- Parent Selection (Dropdown Bertingkat) -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="provinsi_id_kecamatan" class="block text-sm font-medium text-gray-700">Provinsi *</label>
                                        <select name="provinsi_id_kecamatan" id="provinsi_id_kecamatan" 
                                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('provinsi_id_kecamatan') border-red-500 @enderror">
                                            <option value="">Pilih Provinsi</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                        @error('provinsi_id_kecamatan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="kabupaten_id_kecamatan" class="block text-sm font-medium text-gray-700">Kabupaten/Kota *</label>
                                        <select name="kabupaten_id_kecamatan" id="kabupaten_id_kecamatan" 
                                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kabupaten_id_kecamatan') border-red-500 @enderror">
                                            <option value="">Pilih Kabupaten/Kota</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                        @error('kabupaten_id_kecamatan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Kecamatan Data -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="kode_kecamatan" class="block text-sm font-medium text-gray-700">Kode Kecamatan *</label>
                                        <input type="text" name="kode_kecamatan" id="kode_kecamatan" value="{{ old('kode_kecamatan') }}" 
                                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kode_kecamatan') border-red-500 @enderror"
                                               placeholder="Contoh: 32.04.01">
                                        @error('kode_kecamatan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="nama_kecamatan" class="block text-sm font-medium text-gray-700">Nama Kecamatan *</label>
                                        <input type="text" name="nama_kecamatan" id="nama_kecamatan" value="{{ old('nama_kecamatan') }}" 
                                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_kecamatan') border-red-500 @enderror"
                                               placeholder="Contoh: Soreang">
                                        @error('nama_kecamatan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="latitude_kecamatan" class="block text-sm font-medium text-gray-700">Latitude</label>
                                        <input type="number" step="any" name="latitude_kecamatan" id="latitude_kecamatan" value="{{ old('latitude_kecamatan') }}" 
                                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('latitude_kecamatan') border-red-500 @enderror"
                                               placeholder="Contoh: -7.0330">
                                        @error('latitude_kecamatan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="longitude_kecamatan" class="block text-sm font-medium text-gray-700">Longitude</label>
                                        <input type="number" step="any" name="longitude_kecamatan" id="longitude_kecamatan" value="{{ old('longitude_kecamatan') }}" 
                                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('longitude_kecamatan') border-red-500 @enderror"
                                               placeholder="Contoh: 107.5190">
                                        @error('longitude_kecamatan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Form Desa (Hidden by default) -->
                            <div id="desa-form" class="hidden space-y-6 border-t pt-6">
                                <!-- Parent Selection (Dropdown Bertingkat) -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="provinsi_id_desa" class="block text-sm font-medium text-gray-700">Provinsi *</label>
                                        <select name="provinsi_id_desa" id="provinsi_id_desa" 
                                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('provinsi_id_desa') border-red-500 @enderror">
                                            <option value="">Pilih Provinsi</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                        @error('provinsi_id_desa')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="kabupaten_id_desa" class="block text-sm font-medium text-gray-700">Kabupaten/Kota *</label>
                                        <select name="kabupaten_id_desa" id="kabupaten_id_desa" 
                                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kabupaten_id_desa') border-red-500 @enderror">
                                            <option value="">Pilih Kabupaten/Kota</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                        @error('kabupaten_id_desa')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="kecamatan_id_desa" class="block text-sm font-medium text-gray-700">Kecamatan *</label>
                                        <select name="kecamatan_id_desa" id="kecamatan_id_desa" 
                                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kecamatan_id_desa') border-red-500 @enderror">
                                            <option value="">Pilih Kecamatan</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                        @error('kecamatan_id_desa')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Desa Data -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="kode_desa" class="block text-sm font-medium text-gray-700">Kode Desa *</label>
                                        <input type="text" name="kode_desa" id="kode_desa" value="{{ old('kode_desa') }}" 
                                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kode_desa') border-red-500 @enderror"
                                               placeholder="Contoh: 32.04.01.2001">
                                        @error('kode_desa')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="nama_desa" class="block text-sm font-medium text-gray-700">Nama Desa/Kelurahan *</label>
                                        <input type="text" name="nama_desa" id="nama_desa" value="{{ old('nama_desa') }}" 
                                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_desa') border-red-500 @enderror"
                                               placeholder="Contoh: Soreang">
                                        @error('nama_desa')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="tipe_desa" class="block text-sm font-medium text-gray-700">Tipe *</label>
                                        <select name="tipe_desa" id="tipe_desa" 
                                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tipe_desa') border-red-500 @enderror">
                                            <option value="">Pilih Tipe</option>
                                            <option value="desa" {{ old('tipe_desa') == 'desa' ? 'selected' : '' }}>Desa</option>
                                            <option value="kelurahan" {{ old('tipe_desa') == 'kelurahan' ? 'selected' : '' }}>Kelurahan</option>
                                        </select>
                                        @error('tipe_desa')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="latitude_desa" class="block text-sm font-medium text-gray-700">Latitude</label>
                                        <input type="number" step="any" name="latitude_desa" id="latitude_desa" value="{{ old('latitude_desa') }}" 
                                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('latitude_desa') border-red-500 @enderror"
                                               placeholder="Contoh: -7.0330">
                                        @error('latitude_desa')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="longitude_desa" class="block text-sm font-medium text-gray-700">Longitude</label>
                                        <input type="number" step="any" name="longitude_desa" id="longitude_desa" value="{{ old('longitude_desa') }}" 
                                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('longitude_desa') border-red-500 @enderror"
                                               placeholder="Contoh: 107.5190">
                                        @error('longitude_desa')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                            <a href="{{ route('provinsi.index') }}" class="btn-nasdem-outline">
                                Batal
                            </a>
                            <button type="submit" class="btn-nasdem">
                                Simpan Data Wilayah
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
        const levelRadios = document.querySelectorAll('.level-radio');
        const provinsiForm = document.getElementById('provinsi-form');
        const kabupatenForm = document.getElementById('kabupaten-form');
        const kecamatanForm = document.getElementById('kecamatan-form');
        const desaForm = document.getElementById('desa-form');
        
        // Select elements for each level
        const provinsiSelectKabupaten = document.getElementById('provinsi_id_kabupaten');
        const provinsiSelectKecamatan = document.getElementById('provinsi_id_kecamatan');
        const provinsiSelectDesa = document.getElementById('provinsi_id_desa');
        
        const kabupatenSelectKecamatan = document.getElementById('kabupaten_id_kecamatan');
        const kabupatenSelectDesa = document.getElementById('kabupaten_id_desa');
        
        const kecamatanSelectDesa = document.getElementById('kecamatan_id_desa');

        // Load provinsi data for all dropdowns
        function loadProvinsis() {
            fetch('{{ route("api.provinsi") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const provinsiSelects = [provinsiSelectKabupaten, provinsiSelectKecamatan, provinsiSelectDesa];
                    
                    provinsiSelects.forEach(select => {
                        if (select) {
                            select.innerHTML = '<option value="">Pilih Provinsi</option>';
                            data.forEach(provinsi => {
                                const option = document.createElement('option');
                                option.value = provinsi.id;
                                option.textContent = provinsi.nama;
                                select.appendChild(option);
                            });
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading provinsi:', error);
                    // Fallback: show error in dropdown
                    const provinsiSelects = [provinsiSelectKabupaten, provinsiSelectKecamatan, provinsiSelectDesa];
                    provinsiSelects.forEach(select => {
                        if (select) {
                            select.innerHTML = '<option value="">Error loading data</option>';
                        }
                    });
                });
        }

        // Load kabupaten based on provinsi
        function loadKabupatens(provinsiId, targetSelect) {
            if (!provinsiId || !targetSelect) {
                if (targetSelect) {
                    targetSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                }
                return;
            }
            
            fetch(`/api/kabupaten/${provinsiId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    targetSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                    data.forEach(kabupaten => {
                        const option = document.createElement('option');
                        option.value = kabupaten.id;
                        option.textContent = kabupaten.nama;
                        targetSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading kabupaten:', error);
                    targetSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        }

        // Load kecamatan based on kabupaten
        function loadKecamatans(kabupatenId, targetSelect) {
            if (!kabupatenId || !targetSelect) {
                if (targetSelect) {
                    targetSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                }
                return;
            }
            
            fetch(`/api/kecamatan/${kabupatenId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    targetSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                    data.forEach(kecamatan => {
                        const option = document.createElement('option');
                        option.value = kecamatan.id;
                        option.textContent = kecamatan.nama;
                        targetSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading kecamatan:', error);
                    targetSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        }

        // Show/hide forms based on selected level
        function toggleForms() {
            const selectedLevel = document.querySelector('input[name="level"]:checked').value;
            
            // Reset all forms to hidden
            provinsiForm.classList.add('hidden');
            kabupatenForm.classList.add('hidden');
            kecamatanForm.classList.add('hidden');
            desaForm.classList.add('hidden');

            // Show forms based on level
            if (selectedLevel === 'provinsi') {
                provinsiForm.classList.remove('hidden');
            } else if (selectedLevel === 'kabupaten') {
                kabupatenForm.classList.remove('hidden');
                loadProvinsis();
            } else if (selectedLevel === 'kecamatan') {
                kecamatanForm.classList.remove('hidden');
                loadProvinsis();
            } else if (selectedLevel === 'desa') {
                desaForm.classList.remove('hidden');
                loadProvinsis();
            }
        }

        // Event listeners untuk dropdown bertingkat
        // Kabupaten: Provinsi -> Kabupaten
        if (provinsiSelectKabupaten) {
            provinsiSelectKabupaten.addEventListener('change', function() {
                loadKabupatens(this.value, kabupatenSelectKecamatan);
            });
        }

        // Kecamatan: Provinsi -> Kabupaten
        if (provinsiSelectKecamatan) {
            provinsiSelectKecamatan.addEventListener('change', function() {
                loadKabupatens(this.value, kabupatenSelectKecamatan);
                // Reset kecamatan ketika provinsi berubah
                if (kecamatanSelectDesa) {
                    kecamatanSelectDesa.innerHTML = '<option value="">Pilih Kecamatan</option>';
                }
            });
        }

        // Kecamatan: Kabupaten -> Kecamatan
        if (kabupatenSelectKecamatan) {
            kabupatenSelectKecamatan.addEventListener('change', function() {
                loadKecamatans(this.value, kecamatanSelectDesa);
            });
        }

        // Desa: Provinsi -> Kabupaten
        if (provinsiSelectDesa) {
            provinsiSelectDesa.addEventListener('change', function() {
                loadKabupatens(this.value, kabupatenSelectDesa);
                // Reset kecamatan ketika provinsi berubah
                if (kecamatanSelectDesa) {
                    kecamatanSelectDesa.innerHTML = '<option value="">Pilih Kecamatan</option>';
                }
            });
        }

        // Desa: Kabupaten -> Kecamatan
        if (kabupatenSelectDesa) {
            kabupatenSelectDesa.addEventListener('change', function() {
                loadKecamatans(this.value, kecamatanSelectDesa);
            });
        }

        // Event listeners untuk level radio
        levelRadios.forEach(radio => {
            radio.addEventListener('change', toggleForms);
        });

        // Initialize
        toggleForms();

        // Debug: Log form submission
        document.getElementById('wilayahForm').addEventListener('submit', function(e) {
            console.log('Form submitted');
            console.log('Level:', document.querySelector('input[name="level"]:checked').value);
        });
    });
    </script>
    @endpush
</x-app-layout>