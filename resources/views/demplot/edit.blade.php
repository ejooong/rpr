<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Demplot - {{ $demplot->nama_lahan }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Edit data demplot pertanian</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('demplot.update', $demplot->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Nama Lahan -->
                            <div class="md:col-span-2">
                                <label for="nama_lahan" class="block text-sm font-medium text-gray-700">Nama Lahan *</label>
                                <input type="text" name="nama_lahan" id="nama_lahan" value="{{ old('nama_lahan', $demplot->nama_lahan) }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('nama_lahan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Petani -->
                            <div>
                                <label for="petani_id" class="block text-sm font-medium text-gray-700">Petani *</label>
                                <select name="petani_id" id="petani_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Petani</option>
                                    @foreach($petani as $p)
                                        <option value="{{ $p->id }}" {{ old('petani_id', $demplot->petani_id) == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama }} - {{ $p->nik }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('petani_id')
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
                                        <option value="{{ $k->id }}" {{ old('komoditas_id', $demplot->komoditas_id) == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('komoditas_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Luas Lahan -->
                            <div>
                                <label for="luas_lahan" class="block text-sm font-medium text-gray-700">Luas Lahan (Ha) *</label>
                                <input type="number" name="luas_lahan" id="luas_lahan" value="{{ old('luas_lahan', $demplot->luas_lahan) }}" step="0.01" min="0"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('luas_lahan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tahun -->
                            <div>
                                <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun *</label>
                                <input type="number" name="tahun" id="tahun" value="{{ old('tahun', $demplot->tahun) }}" min="2000" max="2030"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('tahun')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                <select name="status" id="status" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Status</option>
                                    <option value="rencana" {{ old('status', $demplot->status) == 'rencana' ? 'selected' : '' }}>Rencana</option>
                                    <option value="aktif" {{ old('status', $demplot->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="selesai" {{ old('status', $demplot->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Wilayah - Cascading Dropdown -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Provinsi -->
                            <div>
                                <label for="provinsi_id" class="block text-sm font-medium text-gray-700">Provinsi *</label>
                                <select name="provinsi_id" id="provinsi_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Provinsi</option>
                                    @foreach($provinsis as $provinsi)
                                        <option value="{{ $provinsi->id }}" {{ old('provinsi_id', $demplot->provinsi_id) == $provinsi->id ? 'selected' : '' }}>
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
                                <label for="kabupaten_id" class="block text-sm font-medium text-gray-700">Kabupaten/Kota *</label>
                                <select name="kabupaten_id" id="kabupaten_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Kabupaten/Kota</option>
                                    @foreach($kabupatens as $kabupaten)
                                        <option value="{{ $kabupaten->id }}" 
                                                data-provinsi="{{ $kabupaten->provinsi_id }}"
                                                {{ old('kabupaten_id', $demplot->kabupaten_id) == $kabupaten->id ? 'selected' : '' }}>
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
                                <label for="kecamatan_id" class="block text-sm font-medium text-gray-700">Kecamatan *</label>
                                <select name="kecamatan_id" id="kecamatan_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Kecamatan</option>
                                    @foreach($kecamatans as $kecamatan)
                                        <option value="{{ $kecamatan->id }}" 
                                                data-kabupaten="{{ $kecamatan->kabupaten_id }}"
                                                {{ old('kecamatan_id', $demplot->kecamatan_id) == $kecamatan->id ? 'selected' : '' }}>
                                            {{ $kecamatan->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kecamatan_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Desa -->
                            <div>
                                <label for="desa_id" class="block text-sm font-medium text-gray-700">Desa/Kelurahan *</label>
                                <select name="desa_id" id="desa_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Desa/Kelurahan</option>
                                    @foreach($desas as $desa)
                                        <option value="{{ $desa->id }}" 
                                                data-kecamatan="{{ $desa->kecamatan_id }}"
                                                {{ old('desa_id', $demplot->desa_id) == $desa->id ? 'selected' : '' }}>
                                            {{ $desa->nama }} ({{ $desa->tipe }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('desa_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="mb-6">
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat Lengkap *</label>
                            <textarea name="alamat" id="alamat" rows="3" 
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                      required>{{ old('alamat', $demplot->alamat) }}</textarea>
                            @error('alamat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Koordinat -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude *</label>
                                <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $demplot->latitude) }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Contoh: -6.2088" required>
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude *</label>
                                <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $demplot->longitude) }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Contoh: 106.8456" required>
                                @error('longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Foto Lahan -->
                        <div class="mb-6">
                            <label for="foto_lahan" class="block text-sm font-medium text-gray-700">Foto Lahan</label>
                            @if($demplot->foto_lahan)
                            <div class="mb-2">
                                <img src="{{ Storage::url($demplot->foto_lahan) }}" 
                                     alt="Foto Lahan Saat Ini" 
                                     class="max-w-xs rounded-lg shadow-md mb-2">
                                <p class="text-sm text-gray-500">Foto saat ini</p>
                            </div>
                            @endif
                            <input type="file" name="foto_lahan" id="foto_lahan" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   accept="image/*">
                            @error('foto_lahan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-6">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" 
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('keterangan', $demplot->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('demplot.show', $demplot->id) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Update Demplot
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Cascading dropdown untuk wilayah
        document.addEventListener('DOMContentLoaded', function() {
            const provinsiSelect = document.getElementById('provinsi_id');
            const kabupatenSelect = document.getElementById('kabupaten_id');
            const kecamatanSelect = document.getElementById('kecamatan_id');
            const desaSelect = document.getElementById('desa_id');

            // Filter kabupaten berdasarkan provinsi
            function filterKabupaten() {
                const selectedProvinsi = provinsiSelect.value;
                const kabupatenOptions = kabupatenSelect.querySelectorAll('option');
                
                kabupatenOptions.forEach(option => {
                    if (option.value === '') return;
                    
                    if (option.dataset.provinsi === selectedProvinsi) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                // Reset dan disable jika provinsi tidak dipilih
                if (!selectedProvinsi) {
                    kabupatenSelect.value = '';
                    kabupatenSelect.disabled = true;
                } else {
                    kabupatenSelect.disabled = false;
                }
                
                filterKecamatan();
            }

            // Filter kecamatan berdasarkan kabupaten
            function filterKecamatan() {
                const selectedKabupaten = kabupatenSelect.value;
                const kecamatanOptions = kecamatanSelect.querySelectorAll('option');
                
                kecamatanOptions.forEach(option => {
                    if (option.value === '') return;
                    
                    if (option.dataset.kabupaten === selectedKabupaten) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                // Reset dan disable jika kabupaten tidak dipilih
                if (!selectedKabupaten) {
                    kecamatanSelect.value = '';
                    kecamatanSelect.disabled = true;
                } else {
                    kecamatanSelect.disabled = false;
                }
                
                filterDesa();
            }

            // Filter desa berdasarkan kecamatan
            function filterDesa() {
                const selectedKecamatan = kecamatanSelect.value;
                const desaOptions = desaSelect.querySelectorAll('option');
                
                desaOptions.forEach(option => {
                    if (option.value === '') return;
                    
                    if (option.dataset.kecamatan === selectedKecamatan) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                // Reset dan disable jika kecamatan tidak dipilih
                if (!selectedKecamatan) {
                    desaSelect.value = '';
                    desaSelect.disabled = true;
                } else {
                    desaSelect.disabled = false;
                }
            }

            // Event listeners
            provinsiSelect.addEventListener('change', filterKabupaten);
            kabupatenSelect.addEventListener('change', filterKecamatan);
            kecamatanSelect.addEventListener('change', filterDesa);

            // Initialize on page load
            filterKabupaten();
        });
    </script>
    @endpush
</x-app-layout>