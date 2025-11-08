<!-- resources/views/wilayah/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Wilayah: {{ $wilayah->nama }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Edit data wilayah administrasi</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('wilayah.update', $wilayah) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kode Wilayah -->
                            <div>
                                <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">
                                    Kode Wilayah *
                                </label>
                                <input type="text" name="kode" id="kode" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    value="{{ old('kode', $wilayah->kode) }}"
                                    placeholder="Contoh: 32.01.01">
                                @error('kode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Wilayah -->
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Wilayah *
                                </label>
                                <input type="text" name="nama" id="nama" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    value="{{ old('nama', $wilayah->nama) }}"
                                    placeholder="Contoh: Jawa Barat">
                                @error('nama')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Level Wilayah -->
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">
                                    Level Wilayah *
                                </label>
                                <select name="level" id="level" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih Level</option>
                                    <option value="provinsi" {{ old('level', $wilayah->level) == 'provinsi' ? 'selected' : '' }}>Provinsi</option>
                                    <option value="kabupaten" {{ old('level', $wilayah->level) == 'kabupaten' ? 'selected' : '' }}>Kabupaten/Kota</option>
                                    <option value="kecamatan" {{ old('level', $wilayah->level) == 'kecamatan' ? 'selected' : '' }}>Kecamatan</option>
                                    <option value="desa" {{ old('level', $wilayah->level) == 'desa' ? 'selected' : '' }}>Desa/Kelurahan</option>
                                </select>
                                @error('level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Parent Wilayah -->
                            <div>
                                <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Wilayah Induk
                                </label>
                                <select name="parent_id" id="parent_id"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Tidak Ada (Provinsi)</option>
                                    @foreach($parentWilayah as $parent)
                                        <option value="{{ $parent->id }}" 
                                            {{ old('parent_id', $wilayah->parent_id) == $parent->id ? 'selected' : '' }}
                                            {{ $parent->id == $wilayah->id ? 'disabled' : '' }}>
                                            {{ $parent->nama }} ({{ ucfirst($parent->level) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Latitude -->
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">
                                    Latitude
                                </label>
                                <input type="number" step="any" name="latitude" id="latitude"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    value="{{ old('latitude', $wilayah->latitude) }}"
                                    placeholder="Contoh: -6.2088">
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Longitude -->
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">
                                    Longitude
                                </label>
                                <input type="number" step="any" name="longitude" id="longitude"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    value="{{ old('longitude', $wilayah->longitude) }}"
                                    placeholder="Contoh: 106.8456">
                                @error('longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Aktif -->
                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="aktif" value="1" 
                                        {{ old('aktif', $wilayah->aktif) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Wilayah aktif</span>
                                </label>
                                <p class="mt-1 text-sm text-gray-500">
                                    Jika tidak dicentang, wilayah ini tidak akan muncul dalam pilihan
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex justify-end space-x-4">
                            <a href="{{ route('wilayah.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded transition duration-200">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="btn-nasdem flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Wilayah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('level').addEventListener('change', function() {
            const level = this.value;
            const parentSelect = document.getElementById('parent_id');
            
            if (level === 'provinsi') {
                parentSelect.value = '';
                parentSelect.disabled = true;
            } else {
                parentSelect.disabled = false;
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            const level = document.getElementById('level').value;
            const parentSelect = document.getElementById('parent_id');
            
            if (level === 'provinsi') {
                parentSelect.disabled = true;
            }
        });
    </script>
    @endpush
</x-app-layout>