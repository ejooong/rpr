<!-- resources/views/petani/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Anggota Petani Baru
        </h2>
        <p class="text-sm text-gray-600 mt-1">Tambah data anggota petani baru ke kelompok tani</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('petani.store') }}" method="POST">
                        @csrf
                        
                        <!-- Informasi Poktan (jika dari halaman poktan) -->
                        @if(isset($selectedPoktan))
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-700 mb-2">Informasi Kelompok Tani</h4>
                            <p class="text-sm text-blue-600">
                                <strong>POKTAN:</strong> {{ $selectedPoktan->nama }} | 
                                <strong>Ketua:</strong> {{ $selectedPoktan->ketua }}
                            </p>
                            <input type="hidden" name="poktan_id" value="{{ $selectedPoktan->id }}">
                        </div>
                        @else
                        <!-- Pilih Poktan -->
                        <div class="mb-6">
                            <label for="poktan_id" class="block text-sm font-medium text-gray-700">Pilih Kelompok Tani *</label>
                            <select name="poktan_id" id="poktan_id" 
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                    required>
                                <option value="">Pilih Kelompok Tani</option>
                                @foreach($poktans as $poktan)
                                    <option value="{{ $poktan->id }}" {{ old('poktan_id') == $poktan->id ? 'selected' : '' }}>
                                        {{ $poktan->nama }} - {{ $poktan->ketua }}
                                    </option>
                                @endforeach
                            </select>
                            @error('poktan_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Nama -->
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap *</label>
                                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('nama')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- NIK -->
                            <div>
                                <label for="nik" class="block text-sm font-medium text-gray-700">NIK *</label>
                                <input type="text" name="nik" id="nik" value="{{ old('nik') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required maxlength="16">
                                @error('nik')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jenis Kelamin -->
                            <div>
                                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="jenis_kelamin" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Lahir -->
                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                @error('tanggal_lahir')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Pendidikan -->
                            <div>
                                <label for="pendidikan" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
                                <select name="pendidikan" id="pendidikan" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih Pendidikan</option>
                                    <option value="Tidak Sekolah" {{ old('pendidikan') == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                                    <option value="SD" {{ old('pendidikan') == 'SD' ? 'selected' : '' }}>SD</option>
                                    <option value="SMP" {{ old('pendidikan') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                    <option value="SMA" {{ old('pendidikan') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                    <option value="D1/D2/D3" {{ old('pendidikan') == 'D1/D2/D3' ? 'selected' : '' }}>D1/D2/D3</option>
                                    <option value="S1" {{ old('pendidikan') == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('pendidikan') == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('pendidikan') == 'S3' ? 'selected' : '' }}>S3</option>
                                </select>
                                @error('pendidikan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- No HP (Telepon) -->
                            <div>
                                <label for="no_hp" class="block text-sm font-medium text-gray-700">Nomor HP *</label>
                                <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       required>
                                @error('no_hp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Luas Lahan Garap -->
                            <div>
                                <label for="luas_lahan_garap" class="block text-sm font-medium text-gray-700">Luas Lahan Garap (Ha)</label>
                                <input type="number" name="luas_lahan_garap" id="luas_lahan_garap" value="{{ old('luas_lahan_garap') }}" step="0.01" min="0"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                @error('luas_lahan_garap')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Lahan -->
                            <div>
                                <label for="status_lahan" class="block text-sm font-medium text-gray-700">Status Lahan</label>
                                <select name="status_lahan" id="status_lahan" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih Status Lahan</option>
                                    <option value="Milik Sendiri" {{ old('status_lahan') == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                                    <option value="Sewa" {{ old('status_lahan') == 'Sewa' ? 'selected' : '' }}>Sewa</option>
                                    <option value="Gadai" {{ old('status_lahan') == 'Gadai' ? 'selected' : '' }}>Gadai</option>
                                    <option value="Plasma" {{ old('status_lahan') == 'Plasma' ? 'selected' : '' }}>Plasma</option>
                                    <option value="Lainnya" {{ old('status_lahan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('status_lahan')
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

                        <!-- Koordinat (opsional) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude (Koordinat)</label>
                                <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Contoh: -6.2088">
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude (Koordinat)</label>
                                <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Contoh: 106.8456">
                                @error('longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="aktif" value="1" {{ old('aktif', true) ? 'checked' : '' }} 
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Anggota Aktif</span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ url()->previous() }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Simpan Anggota
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>