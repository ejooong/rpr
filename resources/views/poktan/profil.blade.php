<!-- resources/views/poktan/profil.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Profil Kelompok Tani - {{ $poktan->nama }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Kelola profil kelompok tani Anda</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('poktan.update-profil') }}" method="POST">
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

                            <!-- Tanggal Terbentuk (Readonly) -->
                            <div>
                                <label for="tanggal_terbentuk" class="block text-sm font-medium text-gray-700">Tanggal Terbentuk</label>
                                <input type="text" id="tanggal_terbentuk" value="{{ \Carbon\Carbon::parse($poktan->tanggal_terbentuk)->format('d F Y') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50" 
                                       readonly>
                                <p class="mt-1 text-xs text-gray-500">Tanggal terbentuk tidak dapat diubah</p>
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

                        <!-- Informasi Wilayah (Readonly) -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Lokasi POKTAN</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Provinsi:</span>
                                    <p class="font-medium">{{ $poktan->provinsi->nama ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Kabupaten:</span>
                                    <p class="font-medium">{{ $poktan->kabupaten->nama ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Kecamatan:</span>
                                    <p class="font-medium">{{ $poktan->kecamatan->nama ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Desa:</span>
                                    <p class="font-medium">{{ $poktan->desa->nama ?? '-' }}</p>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Untuk perubahan lokasi, hubungi administrator</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('poktan.dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>