<!-- resources/views/desa/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Desa/Kelurahan
        </h2>
        <p class="text-sm text-gray-600 mt-1">Tambah data desa dan kelurahan baru</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Card Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('desa.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Kode -->
                            <div>
                                <label for="kode" class="block text-sm font-medium text-gray-700">
                                    Kode Desa/Kelurahan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="kode" 
                                       id="kode"
                                       value="{{ old('kode') }}"
                                       required
                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kode') border-red-500 @enderror">
                                @error('kode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama -->
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700">
                                    Nama Desa/Kelurahan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="nama" 
                                       id="nama"
                                       value="{{ old('nama') }}"
                                       required
                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama') border-red-500 @enderror">
                                @error('nama')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Provinsi -->
                            <div>
                                <label for="provinsi_id" class="block text-sm font-medium text-gray-700">
                                    Provinsi <span class="text-red-500">*</span>
                                </label>
                                <select name="provinsi_id" 
                                        id="provinsi_id"
                                        required
                                        onchange="updateKabupaten()"
                                        class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('provinsi_id') border-red-500 @enderror">
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

                            <!-- Kabupaten -->
                            <div>
                                <label for="kabupaten_id" class="block text-sm font-medium text-gray-700">
                                    Kabupaten/Kota <span class="text-red-500">*</span>
                                </label>
                                <select name="kabupaten_id" 
                                        id="kabupaten_id"
                                        required
                                        onchange="updateKecamatan()"
                                        class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kabupaten_id') border-red-500 @enderror">
                                    <option value="">Pilih Kabupaten/Kota</option>
                                    @foreach($kabupatens as $kabupaten)
                                        <option value="{{ $kabupaten->id }}" 
                                            data-provinsi="{{ $kabupaten->provinsi_id }}"
                                            {{ old('kabupaten_id') == $kabupaten->id ? 'selected' : '' }}>
                                            {{ $kabupaten->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kabupaten_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kecamatan -->
                            <div>
                                <label for="kecamatan_id" class="block text-sm font-medium text-gray-700">
                                    Kecamatan <span class="text-red-500">*</span>
                                </label>
                                <select name="kecamatan_id" 
                                        id="kecamatan_id"
                                        required
                                        class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kecamatan_id') border-red-500 @enderror">
                                    <option value="">Pilih Kecamatan</option>
                                    @foreach($kecamatans as $kecamatan)
                                        <option value="{{ $kecamatan->id }}" 
                                            data-kabupaten="{{ $kecamatan->kabupaten_id }}"
                                            {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>
                                            {{ $kecamatan->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kecamatan_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipe -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipe <span class="text-red-500">*</span>
                                </label>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" 
                                               name="tipe" 
                                               value="desa" 
                                               {{ old('tipe', 'desa') == 'desa' ? 'checked' : '' }}
                                               class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">Desa</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" 
                                               name="tipe" 
                                               value="kelurahan" 
                                               {{ old('tipe') == 'kelurahan' ? 'checked' : '' }}
                                               class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">Kelurahan</span>
                                    </label>
                                </div>
                                @error('tipe')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Koordinat -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="latitude" class="block text-sm font-medium text-gray-700">
                                        Latitude
                                    </label>
                                    <input type="number" 
                                           name="latitude" 
                                           id="latitude"
                                           step="any"
                                           value="{{ old('latitude') }}"
                                           placeholder="Contoh: -6.2088"
                                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('latitude') border-red-500 @enderror">
                                    @error('latitude')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="longitude" class="block text-sm font-medium text-gray-700">
                                        Longitude
                                    </label>
                                    <input type="number" 
                                           name="longitude" 
                                           id="longitude"
                                           step="any"
                                           value="{{ old('longitude') }}"
                                           placeholder="Contoh: 106.8456"
                                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('longitude') border-red-500 @enderror">
                                    @error('longitude')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                                <a href="{{ route('desa.index') }}" 
                                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                                    Batal
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Desa/Kelurahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Format kode input
        document.getElementById('kode').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });

        // Auto-format nama
        document.getElementById('nama').addEventListener('input', function(e) {
            this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
        });

        // Filter kabupaten berdasarkan provinsi
        function updateKabupaten() {
            const provinsiId = document.getElementById('provinsi_id').value;
            const kabupatenSelect = document.getElementById('kabupaten_id');
            const kecamatanSelect = document.getElementById('kecamatan_id');
            
            // Reset kabupaten dan kecamatan
            kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            
            if (!provinsiId) return;
            
            // Filter kabupaten
            const kabupatenOptions = kabupatenSelect.querySelectorAll('option');
            kabupatenOptions.forEach(option => {
                if (option.value === '' || option.getAttribute('data-provinsi') === provinsiId) {
                    kabupatenSelect.appendChild(option);
                }
            });
        }

        // Filter kecamatan berdasarkan kabupaten
        function updateKecamatan() {
            const kabupatenId = document.getElementById('kabupaten_id').value;
            const kecamatanSelect = document.getElementById('kecamatan_id');
            
            // Reset kecamatan
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            
            if (!kabupatenId) return;
            
            // Filter kecamatan
            const kecamatanOptions = kecamatanSelect.querySelectorAll('option');
            kecamatanOptions.forEach(option => {
                if (option.value === '' || option.getAttribute('data-kabupaten') === kabupatenId) {
                    kecamatanSelect.appendChild(option);
                }
            });
        }

        // Initialize filters on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateKabupaten();
            updateKecamatan();
        });
    </script>
    @endpush
</x-app-layout>