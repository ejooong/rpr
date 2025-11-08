<!-- resources/views/users/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Pengguna: {{ $user->nama }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">Edit data pengguna sistem</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <!-- Personal Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pribadi</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Nama -->
                                    <div>
                                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">
                                            Nama Lengkap *
                                        </label>
                                        <input type="text" name="nama" id="nama" required
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            value="{{ old('nama', $user->nama) }}"
                                            placeholder="Masukkan nama lengkap">
                                        @error('nama')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                            Email *
                                        </label>
                                        <input type="email" name="email" id="email" required
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            value="{{ old('email', $user->email) }}"
                                            placeholder="email@contoh.com">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Akun</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Role -->
                                    <div>
                                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                            Role *
                                        </label>
                                        <select name="role" id="role" required
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Pilih Role</option>
                                            @foreach($roles as $key => $value)
                                                <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Status Aktif -->
                                    <div>
                                        <label for="aktif" class="block text-sm font-medium text-gray-700 mb-1">
                                            Status *
                                        </label>
                                        <select name="aktif" id="aktif" required
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="1" {{ old('aktif', $user->aktif) == 1 ? 'selected' : '' }}>Aktif</option>
                                            <option value="0" {{ old('aktif', $user->aktif) == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                                        </select>
                                        @error('aktif')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Wilayah Kerja -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Wilayah Kerja</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Provinsi -->
                                    <div>
                                        <label for="provinsi_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Provinsi
                                        </label>
                                        <select name="provinsi_id" id="provinsi_id"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Pilih Provinsi (Opsional)</option>
                                            @foreach($provinsis as $provinsi)
                                                <option value="{{ $provinsi->id }}" {{ old('provinsi_id', $user->provinsi_id) == $provinsi->id ? 'selected' : '' }}>
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
                                        <label for="kabupaten_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Kabupaten/Kota
                                        </label>
                                        <select name="kabupaten_id" id="kabupaten_id"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Pilih Kabupaten/Kota (Opsional)</option>
                                            @foreach($kabupatens as $kabupaten)
                                                <option value="{{ $kabupaten->id }}" {{ old('kabupaten_id', $user->kabupaten_id) == $kabupaten->id ? 'selected' : '' }}>
                                                    {{ $kabupaten->nama }} ({{ $kabupaten->tipe }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kabupaten_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Kecamatan -->
                                    <div>
                                        <label for="kecamatan_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Kecamatan
                                        </label>
                                        <select name="kecamatan_id" id="kecamatan_id"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Pilih Kecamatan (Opsional)</option>
                                            @foreach($kecamatans as $kecamatan)
                                                <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id', $user->kecamatan_id) == $kecamatan->id ? 'selected' : '' }}>
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
                                        <label for="desa_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Desa/Kelurahan
                                        </label>
                                        <select name="desa_id" id="desa_id"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Pilih Desa/Kelurahan (Opsional)</option>
                                            @foreach($desas as $desa)
                                                <option value="{{ $desa->id }}" {{ old('desa_id', $user->desa_id) == $desa->id ? 'selected' : '' }}>
                                                    {{ $desa->nama }} ({{ $desa->tipe }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('desa_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Pilih wilayah kerja sesuai dengan tanggung jawab pengguna. Biarkan kosong untuk akses nasional.
                                </p>
                            </div>

                            <!-- Password (Optional) -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Password</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Password -->
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                            Password Baru
                                        </label>
                                        <input type="password" name="password" id="password"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="Kosongkan jika tidak ingin mengubah">
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Confirm Password -->
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                            Konfirmasi Password Baru
                                        </label>
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="Ulangi password baru">
                                        @error('password_confirmation')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Biarkan kosong jika tidak ingin mengubah password. Password harus minimal 8 karakter.
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex justify-end space-x-4">
                            <a href="{{ route('users.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded transition duration-200">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="btn-nasdem flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Dynamic wilayah based on role selection
        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            const wilayahSelects = [
                document.getElementById('provinsi_id'),
                document.getElementById('kabupaten_id'),
                document.getElementById('kecamatan_id'),
                document.getElementById('desa_id')
            ];
            
            // Admin doesn't need wilayah, others might
            if (role === 'admin') {
                wilayahSelects.forEach(select => {
                    if (select) {
                        select.value = '';
                        select.disabled = true;
                    }
                });
            } else {
                wilayahSelects.forEach(select => {
                    if (select) {
                        select.disabled = false;
                    }
                });
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const role = document.getElementById('role').value;
            const wilayahSelects = [
                document.getElementById('provinsi_id'),
                document.getElementById('kabupaten_id'),
                document.getElementById('kecamatan_id'),
                document.getElementById('desa_id')
            ];
            
            if (role === 'admin') {
                wilayahSelects.forEach(select => {
                    if (select) select.disabled = true;
                });
            }
        });
    </script>
    @endpush
</x-app-layout>