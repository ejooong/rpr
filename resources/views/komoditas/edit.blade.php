<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Komoditas: {{ $komoditas->nama }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Edit data komoditas</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('komoditas.update', $komoditas->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="sektor_id" class="block text-sm font-medium text-gray-700">Sektor *</label>
                                <select name="sektor_id" id="sektor_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('sektor_id') border-red-500 @enderror"
                                        required>
                                    <option value="">Pilih Sektor</option>
                                    @foreach($sektors as $sektor)
                                        <option value="{{ $sektor->id }}" {{ old('sektor_id', $komoditas->sektor_id) == $sektor->id ? 'selected' : '' }}>
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
                                <input type="text" name="nama" id="nama" value="{{ old('nama', $komoditas->nama) }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nama') border-red-500 @enderror"
                                       required>
                                @error('nama')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan *</label>
                                <input type="text" name="satuan" id="satuan" value="{{ old('satuan', $komoditas->satuan) }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('satuan') border-red-500 @enderror"
                                       required>
                                @error('satuan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="warna_chart" class="block text-sm font-medium text-gray-700">Warna Chart</label>
                                <input type="color" name="warna_chart" id="warna_chart" value="{{ old('warna_chart', $komoditas->warna_chart ?: '#4CAF50') }}" 
                                       class="mt-1 block w-full h-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('warna_chart') border-red-500 @enderror">
                                @error('warna_chart')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea name="deskripsi" id="deskripsi" rows="3"
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $komoditas->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="ikon" class="block text-sm font-medium text-gray-700">Ikon</label>
                                <input type="text" name="ikon" id="ikon" value="{{ old('ikon', $komoditas->ikon) }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('ikon') border-red-500 @enderror"
                                       placeholder="fa fa-icon atau nama ikon">
                                @error('ikon')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="status_unggulan" id="status_unggulan" value="1" 
                                           {{ old('status_unggulan', $komoditas->status_unggulan) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="status_unggulan" class="ml-2 block text-sm text-gray-900">
                                        Status Unggulan
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="aktif" id="aktif" value="1" 
                                           {{ old('aktif', $komoditas->aktif) ? 'checked' : '' }}
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
                                Update Komoditas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>