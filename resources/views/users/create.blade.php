<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Pengguna Baru
        </h2>
        <p class="text-sm text-gray-600 mt-1">Tambah data pengguna sistem baru</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        
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
                                            value="{{ old('nama') }}"
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
                                            value="{{ old('email') }}"
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
                    <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Poktan ID (akan muncul hanya untuk role poktan) -->
        <div id="poktan-field" style="display: none;">
            <label for="poktan_id" class="block text-sm font-medium text-gray-700 mb-1">
                Kelompok Tani *
            </label>
            <select name="poktan_id" id="poktan_id"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Pilih Kelompok Tani</option>
                @foreach($poktans as $poktan)
                    <option value="{{ $poktan->id }}" {{ old('poktan_id') == $poktan->id ? 'selected' : '' }}>
                        {{ $poktan->nama }} - {{ $poktan->id }}
                    </option>
                @endforeach
            </select>
            @error('poktan_id')
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
                <option value="1" {{ old('aktif', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('aktif') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
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
                                        <label for="kabupaten_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Kabupaten/Kota
                                        </label>
                                        <select name="kabupaten_id" id="kabupaten_id"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Pilih Kabupaten/Kota (Opsional)</option>
                                            @foreach($kabupatens as $kabupaten)
                                                <option value="{{ $kabupaten->id }}" {{ old('kabupaten_id') == $kabupaten->id ? 'selected' : '' }}>
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
                                                <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>
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
                                                <option value="{{ $desa->id }}" {{ old('desa_id') == $desa->id ? 'selected' : '' }}>
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

                            <!-- Password -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Password</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Password -->
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                            Password *
                                        </label>
                                        <input type="password" name="password" id="password" required
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="Minimal 8 karakter">
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Confirm Password -->
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                            Konfirmasi Password *
                                        </label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" required
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="Ulangi password">
                                        @error('password_confirmation')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Password harus minimal 8 karakter dan mengandung huruf dan angka.
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
                                Simpan Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

   @push('scripts')
<script>
    // Toggle poktan field based on role selection
    document.getElementById('role').addEventListener('change', function() {
        const role = this.value;
        const poktanField = document.getElementById('poktan-field');
        const poktanSelect = document.getElementById('poktan_id');
        
        // Show poktan field only for role 'poktan'
        if (role === 'poktan') {
            poktanField.style.display = 'block';
            poktanSelect.required = true;
        } else {
            poktanField.style.display = 'none';
            poktanSelect.required = false;
            poktanSelect.value = '';
        }

        // Dynamic wilayah based on role selection
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
        const roleSelect = document.getElementById('role');
        const role = roleSelect.value;
        const poktanField = document.getElementById('poktan-field');
        const poktanSelect = document.getElementById('poktan_id');
        
        // Initialize poktan field
        if (role === 'poktan') {
            poktanField.style.display = 'block';
            poktanSelect.required = true;
        } else {
            poktanField.style.display = 'none';
            poktanSelect.required = false;
            poktanSelect.value = '';
        }

        // Initialize wilayah fields
        const wilayahSelects = [
            document.getElementById('provinsi_id'),
            document.getElementById('kabupaten_id'),
            document.getElementById('kecamatan_id'),
            document.getElementById('desa_id')
        ];
        
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

        // Trigger change event to set initial state
        roleSelect.dispatchEvent(new Event('change'));
    });

    // Form validation before submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const role = document.getElementById('role').value;
        const poktanSelect = document.getElementById('poktan_id');
        
        // Reset poktan validation for non-poktan roles
        if (role !== 'poktan') {
            poktanSelect.required = false;
            poktanSelect.value = '';
        }
    });
</script>
@endpush
</x-app-layout>