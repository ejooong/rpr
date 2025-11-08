<!-- resources/views/kecamatan/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Kecamatan
        </h2>
        <p class="text-sm text-gray-600 mt-1">Ubah data kecamatan</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Card Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('kecamatan.update', $kecamatan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <!-- Kode -->
                            <div>
                                <label for="kode" class="block text-sm font-medium text-gray-700">
                                    Kode Kecamatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="kode" 
                                       id="kode"
                                       value="{{ old('kode', $kecamatan->kode) }}"
                                       required
                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kode') border-red-500 @enderror">
                                @error('kode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama -->
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700">
                                    Nama Kecamatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="nama" 
                                       id="nama"
                                       value="{{ old('nama', $kecamatan->nama) }}"
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
                                        <option value="{{ $provinsi->id }}" 
                                            {{ old('provinsi_id', $kecamatan->kabupaten->provinsi_id) == $provinsi->id ? 'selected' : '' }}>
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
                                        class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kabupaten_id') border-red-500 @enderror">
                                    <option value="">Pilih Kabupaten/Kota</option>
                                    @foreach($kabupatens as $kabupaten)
                                        <option value="{{ $kabupaten->id }}" 
                                            data-provinsi="{{ $kabupaten->provinsi_id }}"
                                            {{ old('kabupaten_id', $kecamatan->kabupaten_id) == $kabupaten->id ? 'selected' : '' }}>
                                            {{ $kabupaten->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kabupaten_id')
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
                                           value="{{ old('latitude', $kecamatan->latitude) }}"
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
                                           value="{{ old('longitude', $kecamatan->longitude) }}"
                                           placeholder="Contoh: 106.8456"
                                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('longitude') border-red-500 @enderror">
                                    @error('longitude')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                                <a href="{{ route('kecamatan.index') }}" 
                                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                                    Batal
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md border border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out h-10">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone -->
            @if($kecamatan->desas()->count() == 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6 border border-red-200">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-red-800 mb-2">Zona Berbahaya</h3>
                    <p class="text-sm text-red-600 mb-4">
                        Hapus permanen data kecamatan. Aksi ini tidak dapat dibatalkan.
                    </p>
                    <form action="{{ route('kecamatan.destroy', $kecamatan) }}" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kecamatan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Kecamatan
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-yellow-800">Tidak Dapat Dihapus</h4>
                        <p class="text-sm text-yellow-700 mt-1">
                            Kecamatan ini memiliki {{ $kecamatan->desas()->count() }} data desa/kelurahan. 
                            Hapus semua data desa/kelurahan terlebih dahulu untuk dapat menghapus kecamatan ini.
                        </p>
                    </div>
                </div>
            </div>
            @endif
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
            
            if (!provinsiId) {
                kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                return;
            }
            
            // Show only kabupaten from selected provinsi
            const kabupatenOptions = kabupatenSelect.querySelectorAll('option');
            kabupatenOptions.forEach(option => {
                if (option.value === '' || option.getAttribute('data-provinsi') === provinsiId) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Reset selection if current selection doesn't match
            const currentKabupaten = kabupatenSelect.value;
            if (currentKabupaten) {
                const selectedOption = kabupatenSelect.querySelector(`option[value="${currentKabupaten}"]`);
                if (selectedOption && selectedOption.style.display === 'none') {
                    kabupatenSelect.value = '';
                }
            }
        }

        // Initialize filters on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateKabupaten();
        });
    </script>
    @endpush
</x-app-layout>