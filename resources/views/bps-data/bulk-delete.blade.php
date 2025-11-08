{{-- resources/views/bps-data/bulk-delete.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hapus Data BPS Massal
        </h2>
        <p class="text-sm text-gray-600 mt-1">Hapus data berdasarkan kriteria tertentu</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Warning Alert -->
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-red-800">Perhatian!</h3>
                        <p class="text-sm text-red-700 mt-1">
                            Tindakan ini akan menghapus data secara permanen dan tidak dapat dikembalikan.
                            Pastikan Anda telah membackup data penting sebelum melanjutkan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Data dengan Produksi 0 -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Data Produksi 0</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($zeroProductionCount) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Data dengan nilai produksi = 0</p>
                        </div>
                        <div class="p-3 rounded-full bg-yellow-100">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Data dengan Luas Lahan 0 -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Data Luas Lahan 0</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($zeroAreaCount) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Data dengan luas lahan = 0</p>
                        </div>
                        <div class="p-3 rounded-full bg-orange-100">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Data dengan Keduanya 0 -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Keduanya 0</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($bothZeroCount) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Produksi = 0 dan Luas Lahan = 0</p>
                        </div>
                        <div class="p-3 rounded-full bg-red-100">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Delete Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Kriteria Penghapusan</h3>
                
                <form action="{{ route('bps-data.bulk-delete') }}" method="POST" id="bulkDeleteForm">
                    @csrf
                    @method('DELETE')

                    <!-- Kriteria Options -->
                    <div class="space-y-4 mb-6">
                        <!-- Option 1: Produksi 0 -->
                        <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="criteria" value="zero_production" class="mt-1 mr-3" required>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">Hapus Data dengan Produksi 0</span>
                                    <span class="text-sm text-gray-500">{{ number_format($zeroProductionCount) }} data</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    Menghapus semua data yang memiliki nilai produksi = 0
                                </p>
                            </div>
                        </label>

                        <!-- Option 2: Luas Lahan 0 -->
                        <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="criteria" value="zero_area" class="mt-1 mr-3" required>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">Hapus Data dengan Luas Lahan 0</span>
                                    <span class="text-sm text-gray-500">{{ number_format($zeroAreaCount) }} data</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    Menghapus semua data yang memiliki nilai luas lahan = 0
                                </p>
                            </div>
                        </label>

                        <!-- Option 3: Keduanya 0 -->
                        <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="criteria" value="both_zero" class="mt-1 mr-3" required>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">Hapus Data dengan Produksi 0 dan Luas Lahan 0</span>
                                    <span class="text-sm text-gray-500">{{ number_format($bothZeroCount) }} data</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    Menghapus data yang memiliki produksi = 0 DAN luas lahan = 0
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- Confirmation Input -->
                    <div class="mb-6">
                        <label for="confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Penghapusan
                        </label>
                        <input 
                            type="text" 
                            name="confirmation" 
                            id="confirmation"
                            placeholder="Ketik 'HAPUS DATA BPS' untuk konfirmasi"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            Ketik <span class="font-mono text-red-600">HAPUS DATA BPS</span> untuk mengkonfirmasi penghapusan data
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('bps-data.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                            Kembali
                        </a>
                        <button 
                            type="button"
                            onclick="confirmBulkDelete()"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-200 flex items-center"
                            id="deleteButton"
                            disabled
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Data Massal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Additional Info -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-800">Tips</h4>
                        <ul class="text-sm text-blue-700 mt-1 list-disc list-inside space-y-1">
                            <li>Data dengan nilai 0 biasanya berasal dari import data yang tidak lengkap</li>
                            <li>Disarankan untuk menghapus data dengan kedua nilai 0 terlebih dahulu</li>
                            <li>Selalu backup database sebelum melakukan penghapusan massal</li>
                            <li>Proses penghapusan tidak dapat dibatalkan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Enable/disable delete button based on confirmation
        document.getElementById('confirmation').addEventListener('input', function() {
            const deleteButton = document.getElementById('deleteButton');
            const confirmationText = this.value.trim();
            
            if (confirmationText === 'HAPUS DATA BPS') {
                deleteButton.disabled = false;
            } else {
                deleteButton.disabled = true;
            }
        });

        // Confirm bulk delete
        function confirmBulkDelete() {
            const selectedCriteria = document.querySelector('input[name="criteria"]:checked');
            if (!selectedCriteria) {
                Swal.fire({
                    title: 'Pilih Kriteria',
                    text: 'Silakan pilih kriteria penghapusan terlebih dahulu',
                    icon: 'warning',
                    confirmButtonColor: '#3B82F6',
                });
                return;
            }

            const criteriaText = selectedCriteria.parentElement.querySelector('.font-medium').textContent;
            const dataCount = selectedCriteria.parentElement.querySelector('.text-sm').textContent;

            Swal.fire({
                title: 'Hapus Data Massal?',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Anda akan menghapus data dengan kriteria:</p>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="font-semibold text-red-800">${criteriaText}</div>
                            <div class="text-sm text-red-700 mt-1">${dataCount}</div>
                        </div>
                        <p class="text-red-600 font-medium mt-3">Tindakan ini tidak dapat dibatalkan!</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang menghapus data, harap tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    document.getElementById('bulkDeleteForm').submit();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>