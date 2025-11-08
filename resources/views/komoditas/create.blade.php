<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Komoditas Baru
        </h2>
        <p class="text-sm text-gray-600 mt-1">Tambah data komoditas baru</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('komoditas.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="sektor_id" class="block text-sm font-medium text-gray-700">Sektor *</label>
                                <select name="sektor_id" id="sektor_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('sektor_id') border-red-500 @enderror"
                                        required>
                                    <option value="">Pilih Sektor</option>
                                    @foreach($sektors as $sektor)
                                        <option value="{{ $sektor->id }}" {{ old('sektor_id') == $sektor->id ? 'selected' : '' }}>
                                            {{ $sektor->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sektor_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Komoditas *</label>
                                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nama') border-red-500 @enderror"
                                       required>
                                @error('nama')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan *</label>
                                <input type="text" name="satuan" id="satuan" value="{{ old('satuan') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('satuan') border-red-500 @enderror"
                                       required placeholder="Contoh: Ton, Kg, Ekor">
                                @error('satuan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="warna_chart" class="block text-sm font-medium text-gray-700">Warna Chart</label>
                                <div class="mt-1 flex items-center space-x-3">
                                    <input type="color" name="warna_chart" id="warna_chart" value="{{ old('warna_chart', '#4CAF50') }}" 
                                           class="block w-16 h-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('warna_chart') border-red-500 @enderror">
                                    <input type="text" name="warna_chart_hex" id="warna_chart_hex" value="{{ old('warna_chart', '#4CAF50') }}" 
                                           class="block w-32 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
                                           placeholder="#4CAF50">
                                </div>
                                @error('warna_chart')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea name="deskripsi" id="deskripsi" rows="3"
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('deskripsi') border-red-500 @enderror"
                                          placeholder="Deskripsi singkat tentang komoditas">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="ikon" class="block text-sm font-medium text-gray-700">Ikon</label>
                                <input type="text" name="ikon" id="ikon" value="{{ old('ikon') }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('ikon') border-red-500 @enderror"
                                       placeholder="fa fa-leaf atau nama ikon">
                                @error('ikon')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Gunakan class FontAwesome atau ikon lainnya</p>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="status_unggulan" id="status_unggulan" value="1" 
                                           {{ old('status_unggulan') ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="status_unggulan" class="ml-2 block text-sm text-gray-900">
                                        Status Unggulan
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="aktif" id="aktif" value="1" 
                                           {{ old('aktif', true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="aktif" class="ml-2 block text-sm text-gray-900">
                                        Aktif
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('komoditas.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Batal
                            </a>
                            <button type="submit" class="btn-nasdem">
                                Simpan Komoditas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sync color picker dengan input text
        document.getElementById('warna_chart').addEventListener('input', function(e) {
            document.getElementById('warna_chart_hex').value = e.target.value;
        });

        document.getElementById('warna_chart_hex').addEventListener('input', function(e) {
            const colorValue = e.target.value;
            if (colorValue.match(/^#[0-9A-F]{6}$/i)) {
                document.getElementById('warna_chart').value = colorValue;
            }
        });
    </script>
</x-app-layout>