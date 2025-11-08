<!-- resources/views/poktan/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Kelompok Tani - {{ $poktan->nama }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Edit data kelompok tani</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('poktan.update', $poktan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Nama POKTAN -->
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700">Nama POKTAN *</label>
                                <input type="text" name="nama" id="nama" value="{{ old('nama', $poktan->nama) }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('nama')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ketua -->
                            <div>
                                <label for="ketua" class="block text-sm font-medium text-gray-700">Nama Ketua *</label>
                                <input type="text" name="ketua" id="ketua" value="{{ old('ketua', $poktan->ketua) }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('ketua')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Terbentuk -->
                            <div>
                                <label for="tanggal_terbentuk" class="block text-sm font-medium text-gray-700">Tanggal Terbentuk *</label>
                                <input type="date" name="tanggal_terbentuk" id="tanggal_terbentuk" value="{{ old('tanggal_terbentuk', $poktan->tanggal_terbentuk) }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('tanggal_terbentuk')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jumlah Anggota -->
                            <div>
                                <label for="jumlah_anggota" class="block text-sm font-medium text-gray-700">Jumlah Anggota *</label>
                                <input type="number" name="jumlah_anggota" id="jumlah_anggota" value="{{ old('jumlah_anggota', $poktan->jumlah_anggota) }}" min="1" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                       required>
                                @error('jumlah_anggota')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Provinsi -->
                            <div>
                                <label for="provinsi_id" class="block text-sm font-medium text-gray-700">Provinsi *</label>
                                <select name="provinsi_id" id="provinsi_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Provinsi</option>
                                    @foreach($provinsis as $provinsi)
                                        <option value="{{ $provinsi->id }}" {{ old('provinsi_id', $poktan->provinsi_id) == $provinsi->id ? 'selected' : '' }}>
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
                                <label for="kabupaten_id" class="block text-sm font-medium text-gray-700">Kabupaten *</label>
                                <select name="kabupaten_id" id="kabupaten_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Kabupaten</option>
                                    @foreach($kabupatens as $kabupaten)
                                        <option value="{{ $kabupaten->id }}" {{ old('kabupaten_id', $poktan->kabupaten_id) == $kabupaten->id ? 'selected' : '' }}>
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
                                        <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id', $poktan->kecamatan_id) == $kecamatan->id ? 'selected' : '' }}>
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
                                <label for="desa_id" class="block text-sm font-medium text-gray-700">Desa *</label>
                                <select name="desa_id" id="desa_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                        required>
                                    <option value="">Pilih Desa</option>
                                    @foreach($desas as $desa)
                                        <option value="{{ $desa->id }}" {{ old('desa_id', $poktan->desa_id) == $desa->id ? 'selected' : '' }}>
                                            {{ $desa->nama }}
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
                                      required>{{ old('alamat', $poktan->alamat) }}</textarea>
                            @error('alamat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="aktif" value="1" {{ old('aktif', $poktan->aktif) ? 'checked' : '' }} 
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">POKTAN Aktif</span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('poktan.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Update POKTAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>