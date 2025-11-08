<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Import Data BPS
        </h2>
        <p class="text-sm text-gray-600 mt-1">Import data produksi pertanian dari file Excel</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Petunjuk Import Data</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Download template Excel terlebih dahulu</li>
                                        <li>Isi data sesuai dengan format template</li>
                                        <li>Pastikan nama Provinsi, Sektor, dan Komoditas sesuai dengan data master</li>
                                        <li>File harus berformat .xlsx, .xls, atau .csv</li>
                                        <li>Maksimal ukuran file: 10MB</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Download Template -->
                    <div class="mb-6">
                        <a href="{{ route('bps-data.download-template') }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Template Excel
                        </a>
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('bps-data.process-import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            <!-- File Upload -->
                            <div>
                                <label for="file" class="block text-sm font-medium text-gray-700">File Excel</label>
                                <input type="file" name="file" id="file" required
                                       accept=".xlsx,.xls,.csv"
                                       class="mt-1 block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100">
                                @error('file')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('bps-data.index') }}" 
                                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    Kembali
                                </a>
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    Import Data
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(session('import_errors'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Error saat Import</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p class="font-semibold mb-2">Beberapa data gagal diimport:</p>
                                    <ul class="list-disc list-inside space-y-1 max-h-60 overflow-y-auto">
                                        @foreach(session('import_errors') as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <div class="mt-3 p-3 bg-red-100 rounded">
                                        <p class="text-xs font-medium text-red-800">Tips:</p>
                                        <ul class="text-xs text-red-700 mt-1 space-y-1">
                                            <li>• Pastikan nilai numerik tidak menggunakan tanda '-'</li>
                                            <li>• Gunakan angka 0 untuk nilai kosong</li>
                                            <li>• Format angka menggunakan titik (.) untuk desimal</li>
                                            <li>• Contoh: 1000.50 (bukan 1.000,50)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    
                    <!-- Data Master Info -->
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Data Master yang Tersedia</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Provinsi -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Provinsi</h4>
                                <div class="text-xs text-gray-600 max-h-32 overflow-y-auto">
                                    @foreach(\App\Models\Provinsi::where('aktif', true)->limit(10)->get() as $provinsi)
                                        <div>{{ $provinsi->nama }}</div>
                                    @endforeach
                                    @if(\App\Models\Provinsi::count() > 10)
                                        <div class="text-gray-500">... dan {{ \App\Models\Provinsi::count() - 10 }} lainnya</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Sektor -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Sektor</h4>
                                <div class="text-xs text-gray-600">
                                    @foreach(\App\Models\Sektor::where('aktif', true)->get() as $sektor)
                                        <div>{{ $sektor->nama }} ({{ $sektor->kode }})</div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Komoditas -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Komoditas Populer</h4>
                                <div class="text-xs text-gray-600 max-h-32 overflow-y-auto">
                                    @foreach(\App\Models\Komoditas::where('aktif', true)->limit(10)->get() as $komoditas)
                                        <div>{{ $komoditas->nama }} - {{ $komoditas->sektor->nama }}</div>
                                    @endforeach
                                    @if(\App\Models\Komoditas::count() > 10)
                                        <div class="text-gray-500">... dan {{ \App\Models\Komoditas::count() - 10 }} lainnya</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>