<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Demplot Baru
        </h2>
        <p class="text-sm text-gray-600 mt-1">Tambah data demplot pertanian baru</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('demplot.store') }}" method="POST" enctype="multipart/form-data" id="demplotForm">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Nama Lahan -->
                            <div class="md:col-span-2">
                                <label for="nama_lahan" class="block text-sm font-medium text-gray-700">Nama Lahan *</label>
                                <input type="text" name="nama_lahan" id="nama_lahan" value="{{ old('nama_lahan') }}" 
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
                                        <option value="{{ $p->id }}" {{ old('petani_id') == $p->id ? 'selected' : '' }}>
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
                                        <option value="{{ $k->id }}" {{ old('komoditas_id') == $k->id ? 'selected' : '' }}>
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
                                <input type="number" name="luas_lahan" id="luas_lahan" value="{{ old('luas_lahan') }}" step="0.01" min="0"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('luas_lahan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tahun -->
                            <div>
                                <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun *</label>
                                <input type="number" name="tahun" id="tahun" value="{{ old('tahun', date('Y')) }}" min="2000" max="2030"
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
                                    <option value="rencana" {{ old('status') == 'rencana' ? 'selected' : '' }}>Rencana</option>
                                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
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
                                <label for="kabupaten_id" class="block text-sm font-medium text-gray-700">Kabupaten/Kota *</label>
                                <select name="kabupaten_id" id="kabupaten_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
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
                                <label for="kecamatan_id" class="block text-sm font-medium text-gray-700">Kecamatan *</label>
                                <select name="kecamatan_id" id="kecamatan_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
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
                                                {{ old('desa_id') == $desa->id ? 'selected' : '' }}>
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
                                      required>{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Peta GIS -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Lokasi di Peta *</label>
                            <div class="bg-gray-100 border border-gray-300 rounded-lg p-4">
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <button type="button" id="btnGetLocation" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Dapatkan Lokasi Saya
                                    </button>
                                    <button type="button" id="btnClearMarker" class="px-3 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus Marker
                                    </button>
                                </div>
                                
                                <div id="map" class="w-full h-96 rounded-lg border border-gray-300"></div>
                                <p class="text-xs text-gray-500 mt-2">
                                    Klik pada peta untuk menandai lokasi demplot. Gunakan tombol "Dapatkan Lokasi Saya" untuk menggunakan lokasi GPS perangkat Anda.
                                </p>
                            </div>
                        </div>

                        <!-- Koordinat -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude *</label>
                                <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Contoh: -6.2088">
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude *</label>
                                <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Contoh: 106.8456">
                                @error('longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Foto Lahan -->
                        <div class="mb-6">
                            <label for="foto_lahan" class="block text-sm font-medium text-gray-700">Foto Lahan</label>
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
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('demplot.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Simpan Demplot
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { 
            height: 400px; 
            z-index: 1;
        }
        .leaflet-container {
            font-family: inherit;
        }
        .location-marker {
            background-color: #3b82f6;
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Inisialisasi peta
        let map, marker, currentLocationMarker;

        // Default center (Jakarta)
        const defaultCenter = [-6.2088, 106.8456];

        function initMap() {
            // Inisialisasi peta
            map = L.map('map').setView(defaultCenter, 13);

            // Tambahkan tile layer (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Event click pada peta untuk menambahkan marker
            map.on('click', function(e) {
                setMarker(e.latlng.lat, e.latlng.lng);
            });

            // Coba dapatkan lokasi user
            getCurrentLocation();
        }

        function setMarker(lat, lng) {
            // Hapus marker lama jika ada
            if (marker) {
                map.removeLayer(marker);
            }

            // Buat marker baru
            marker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'location-marker',
                    html: '<div style="background-color: #ef4444; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                    iconSize: [26, 26],
                    iconAnchor: [13, 13]
                })
            }).addTo(map);

            // Update form fields
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);

            // Center map ke marker
            map.setView([lat, lng], 15);
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        // Set marker di lokasi user
                        setMarker(lat, lng);
                        
                        // Tambahkan marker untuk lokasi saat ini
                        if (currentLocationMarker) {
                            map.removeLayer(currentLocationMarker);
                        }
                        
                        currentLocationMarker = L.marker([lat, lng], {
                            icon: L.divIcon({
                                className: 'current-location-marker',
                                html: '<div style="background-color: #10b981; width: 16px; height: 16px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                                iconSize: [22, 22],
                                iconAnchor: [11, 11]
                            })
                        }).addTo(map);
                        
                        // Tambahkan circle untuk accuracy
                        L.circle([lat, lng], {
                            color: '#10b981',
                            fillColor: '#10b981',
                            fillOpacity: 0.1,
                            radius: position.coords.accuracy
                        }).addTo(map);
                        
                    },
                    function(error) {
                        console.log('Error getting location:', error);
                        // Tetap set marker di default location
                        setMarker(defaultCenter[0], defaultCenter[1]);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                // Fallback ke default location
                setMarker(defaultCenter[0], defaultCenter[1]);
            }
        }

        function clearMarker() {
            if (marker) {
                map.removeLayer(marker);
                marker = null;
            }
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
        }

        // Event listeners
        document.getElementById('btnGetLocation').addEventListener('click', getCurrentLocation);
        document.getElementById('btnClearMarker').addEventListener('click', clearMarker);

        // Validasi form sebelum submit
        document.getElementById('demplotForm').addEventListener('submit', function(e) {
            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            
            if (!lat || !lng) {
                e.preventDefault();
                alert('Silakan pilih lokasi di peta terlebih dahulu!');
                return false;
            }
        });

        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            
            // Cascading dropdown untuk wilayah (kode yang sudah ada)
            const provinsiSelect = document.getElementById('provinsi_id');
            const kabupatenSelect = document.getElementById('kabupaten_id');
            const kecamatanSelect = document.getElementById('kecamatan_id');
            const desaSelect = document.getElementById('desa_id');

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
                
                if (!selectedProvinsi) {
                    kabupatenSelect.value = '';
                    kabupatenSelect.disabled = true;
                } else {
                    kabupatenSelect.disabled = false;
                    const firstVisible = Array.from(kabupatenOptions).find(opt => opt.style.display !== 'none' && opt.value !== '');
                    if (firstVisible) {
                        kabupatenSelect.value = firstVisible.value;
                    } else {
                        kabupatenSelect.value = '';
                    }
                }
                
                filterKecamatan();
            }

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
                
                if (!selectedKabupaten) {
                    kecamatanSelect.value = '';
                    kecamatanSelect.disabled = true;
                } else {
                    kecamatanSelect.disabled = false;
                    const firstVisible = Array.from(kecamatanOptions).find(opt => opt.style.display !== 'none' && opt.value !== '');
                    if (firstVisible) {
                        kecamatanSelect.value = firstVisible.value;
                    } else {
                        kecamatanSelect.value = '';
                    }
                }
                
                filterDesa();
            }

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
                
                if (!selectedKecamatan) {
                    desaSelect.value = '';
                    desaSelect.disabled = true;
                } else {
                    desaSelect.disabled = false;
                    const firstVisible = Array.from(desaOptions).find(opt => opt.style.display !== 'none' && opt.value !== '');
                    if (firstVisible) {
                        desaSelect.value = firstVisible.value;
                    } else {
                        desaSelect.value = '';
                    }
                }
            }

            provinsiSelect.addEventListener('change', filterKabupaten);
            kabupatenSelect.addEventListener('change', filterKecamatan);
            kecamatanSelect.addEventListener('change', filterDesa);

            filterKabupaten();
        });
    </script>
    @endpush
</x-app-layout>