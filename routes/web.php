<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\DemplotController;
use App\Http\Controllers\PetaniController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\GISController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\SektorController;
use App\Http\Controllers\KomoditasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PoktanController;
use App\Http\Controllers\ProvinsiController;
use App\Http\Controllers\KabupatenController;
use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\BpsDataController; // TAMBAHKAN INI



// Redirect semua route ke maintenance (saat mode maintenance)
// Route::get('/{any}', function () {
//     return redirect()->route('maintenance');
// })->where('any', '.*');

Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
require __DIR__ . '/auth.php';

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ========== DATA BPS ROUTES - TAMBAHKAN INI ==========
    Route::prefix('bps-data')->group(function () {
    // Dashboard untuk DPD dan Admin (view only)
    Route::get('/dashboard', [BpsDataController::class, 'dashboard'])->name('bps-data.dashboard');
    


    // API untuk chart data
    Route::get('/api/chart-data', [BpsDataController::class, 'apiChartData'])->name('bps-data.api.chart');
    
        // FITUR BARU: Ranking per Kecamatan
    Route::get('/ranking-kecamatan', [BpsDataController::class, 'rankingKecamatan'])->name('bps-data.ranking-kecamatan');
    
    

        // FITUR BARU: Rekomendasi Komoditas untuk Demplot
    Route::get('/rekomendasi-komoditas', [BpsDataController::class, 'getRekomendasiKomoditas'])->name('bps-data.rekomendasi-komoditas');
 
      // TAMBAHKAN INI: Route yang benar
    Route::get('/rekomendasi-per-sektor', [BpsDataController::class, 'getRekomendasiPerSektor'])->name('bps-data.rekomendasi-per-sektor');
    


    // Routes khusus admin (CRUD + Import)
    Route::get('/', [BpsDataController::class, 'index'])->name('bps-data.index');
    Route::get('/create', [BpsDataController::class, 'create'])->name('bps-data.create');
    Route::post('/', [BpsDataController::class, 'store'])->name('bps-data.store');
    Route::get('/{bpsData}/edit', [BpsDataController::class, 'edit'])->name('bps-data.edit');
    Route::put('/{bpsData}', [BpsDataController::class, 'update'])->name('bps-data.update');
    Route::delete('/{bpsData}', [BpsDataController::class, 'destroy'])->name('bps-data.destroy');
    Route::get('/import', [BpsDataController::class, 'import'])->name('bps-data.import');
    Route::post('/import', [BpsDataController::class, 'processImport'])->name('bps-data.process-import');
    Route::get('/download-template', [BpsDataController::class, 'downloadTemplate'])->name('bps-data.download-template');
});

    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/wilayah', [ProvinsiController::class, 'index'])->name('wilayah.index');
        // Wilayah Management - Form Terpusat
        Route::get('/wilayah/create', [WilayahController::class, 'create'])->name('wilayah.create');
        Route::post('/wilayah', [WilayahController::class, 'store'])->name('wilayah.store');
        
        // Wilayah Individual (CRUD terpisah)
        Route::resource('provinsi', ProvinsiController::class);
        Route::resource('kabupaten', KabupatenController::class);
        Route::resource('kecamatan', KecamatanController::class);
        Route::resource('desa', DesaController::class);

        // Master Data
        Route::resource('sektor', SektorController::class);
        Route::resource('komoditas', KomoditasController::class);
        Route::resource('users', UserController::class);
        Route::resource('poktan', PoktanController::class);
        Route::resource('petani', PetaniController::class);
        Route::resource('demplot', DemplotController::class);
        Route::resource('produksi', ProduksiController::class);
        
        // Menghapus Data Secara Massal
        Route::get('/bps-data/bulk-delete', [BpsDataController::class, 'showBulkDelete'])->name('bps-data.bulk-delete');
        Route::delete('/bps-data/bulk-delete', [BpsDataController::class, 'bulkDelete'])->name('bps-data.bulk-delete');

        // Route untuk admin melihat anggota poktan tertentu
        Route::get('/poktan/{poktan}/anggota', [PoktanController::class, 'anggota'])->name('admin.poktan.anggota');
    });

    // Petugas Routes
    Route::prefix('petugas')->group(function () {
        Route::resource('petani', PetaniController::class);
        Route::resource('demplot', DemplotController::class);
        Route::resource('produksi', ProduksiController::class);
        
        // Additional routes untuk petugas
        Route::get('/poktan/{poktan}/anggota', [PetaniController::class, 'byPoktan'])->name('petani.by-poktan');
        Route::get('/laporan/harian', [LaporanController::class, 'harian'])->name('laporan.harian');
        
        // Route untuk petugas melihat anggota poktan tertentu
        Route::get('/poktan/{poktan}/anggota', [PoktanController::class, 'anggota'])->name('petugas.poktan.anggota');
    });

    // DPD Routes
    Route::prefix('dpd')->group(function () {
        Route::get('/laporan/tren-komoditas', [LaporanController::class, 'trenKomoditas'])->name('laporan.tren');
        Route::get('/laporan/komoditas-unggulan', [LaporanController::class, 'komoditasUnggulan'])->name('laporan.unggulan');
        Route::get('/laporan/kinerja-wilayah', [LaporanController::class, 'kinerjaWilayah'])->name('laporan.kinerja');
        Route::get('/peta-demplot', [GISController::class, 'index'])->name('gis.demplot');
        Route::get('/statistik-nasional', [DashboardController::class, 'statistikNasional'])->name('dpd.statistik');
        
        // Route untuk DPD melihat anggota poktan tertentu
        Route::get('/poktan/{poktan}/anggota', [PoktanController::class, 'anggota'])->name('dpd.poktan.anggota');
    });

    // ========== POKTAN ROUTES ==========
    Route::prefix('poktan')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'poktanDashboard'])->name('poktan.dashboard');
        
        // Route untuk poktan melihat anggota SENDIRI
        Route::get('/anggota', [PoktanController::class, 'anggotaSaya'])->name('poktan.anggota');
        
        // ROUTE BARU: Untuk poktan menambah/edit anggota mereka sendiri
        Route::get('/anggota/create', [PoktanController::class, 'createAnggota'])->name('poktan.anggota.create');
        Route::post('/anggota', [PoktanController::class, 'storeAnggota'])->name('poktan.anggota.store');
        Route::get('/anggota/{petani}/edit', [PoktanController::class, 'editAnggota'])->name('poktan.anggota.edit');
        Route::put('/anggota/{petani}', [PoktanController::class, 'updateAnggota'])->name('poktan.anggota.update');
        Route::delete('/anggota/{petani}', [PoktanController::class, 'destroyAnggota'])->name('poktan.anggota.destroy');
        
        Route::get('/produksi', [ProduksiController::class, 'produksiPoktan'])->name('poktan.produksi');
        Route::get('/demplot', [DemplotController::class, 'demplotPoktan'])->name('poktan.demplot');
        Route::get('/profil', [PoktanController::class, 'profil'])->name('poktan.profil');
        Route::put('/profil', [PoktanController::class, 'updateProfil'])->name('poktan.update-profil');
    });

    // GIS Routes - DIPINDAH KE LEVEL ATAS AGAR LEBIH MUDAH DIACCESS
    Route::prefix('gis')->group(function () {
        Route::get('/demplot', [GISController::class, 'index'])->name('gis.demplot');
        Route::get('/api/demplot', [GISController::class, 'apiDemplot'])->name('gis.api.demplot');
        // NEW: counts per kabupaten untuk sebuah provinsi (server-side choropleth)
         Route::get('/api/demplot/counts', [GISController::class, 'apiCountsByProvinsi'])->name('gis.api.demplot.counts');
        Route::get('/api/statistics', [GISController::class, 'apiStatistics'])->name('gis.api.statistics');
        Route::get('/api/wilayah', [GISController::class, 'apiWilayah'])->name('gis.api.wilayah');
        Route::post('/save-area', [GISController::class, 'saveDrawnArea'])->name('gis.save.area');
    });

    // Laporan Routes (Umum)
    Route::prefix('laporan')->group(function () {
        Route::get('/produksi', [LaporanController::class, 'produksi'])->name('laporan.produksi');
        Route::get('/petani', [LaporanController::class, 'petani'])->name('laporan.petani');
        Route::get('/demplot', [LaporanController::class, 'demplot'])->name('laporan.demplot');
        Route::get('/poktan', [LaporanController::class, 'poktan'])->name('laporan.poktan');
        Route::get('/realisasi', [LaporanController::class, 'realisasi'])->name('laporan.realisasi');
    });

    // Export Routes
    Route::prefix('export')->group(function () {
        // Produksi
        Route::get('/produksi/excel', [ExportController::class, 'exportProduksiExcel'])->name('export.produksi.excel');
        Route::get('/produksi/pdf', [ExportController::class, 'exportProduksiPDF'])->name('export.produksi.pdf');
        
        // Demplot
        Route::get('/demplot/excel', [ExportController::class, 'exportDemplotExcel'])->name('export.demplot.excel');
        Route::get('/demplot/pdf', [ExportController::class, 'exportDemplotPDF'])->name('export.demplot.pdf');
        
        // Petani
        Route::get('/petani/excel', [ExportController::class, 'exportPetaniExcel'])->name('export.petani.excel');
        Route::get('/petani/pdf', [ExportController::class, 'exportPetaniPDF'])->name('export.petani.pdf');
        
        // Poktan
        Route::get('/poktan/excel', [ExportController::class, 'exportPoktanExcel'])->name('export.poktan.excel');
        Route::get('/poktan/pdf', [ExportController::class, 'exportPoktanPDF'])->name('export.poktan.pdf');
        
        // Komoditas & Laporan Khusus
        Route::get('/komoditas-unggulan/excel', [ExportController::class, 'exportKomoditasUnggulan'])->name('export.komoditas.excel');
        Route::get('/dashboard-summary/pdf', [ExportController::class, 'exportDashboardSummary'])->name('export.dashboard.pdf');
        Route::get('/laporan-tren/pdf', [ExportController::class, 'exportLaporanTren'])->name('export.tren.pdf');
        
        // Export Anggota Poktan
        Route::get('/poktan/{poktan}/anggota/excel', [ExportController::class, 'exportAnggotaPoktanExcel'])->name('export.poktan.anggota.excel');
        Route::get('/poktan/{poktan}/anggota/pdf', [ExportController::class, 'exportAnggotaPoktanPDF'])->name('export.poktan.anggota.pdf');

        // Export Data BPS - TAMBAHKAN INI
        Route::get('/bps-data/excel', [ExportController::class, 'exportBpsDataExcel'])->name('export.bps-data.excel');
        Route::get('/bps-data/pdf', [ExportController::class, 'exportBpsDataPDF'])->name('export.bps-data.pdf');
        Route::get('/bps-data/komoditas-unggulan/excel', [ExportController::class, 'exportBpsKomoditasUnggulanExcel'])->name('export.bps-komoditas.excel');

        Route::get('/tren-komoditas/excel', [ExportController::class, 'exportTrenKomoditasExcel'])->name('export.tren.excel');
        Route::get('/tren-komoditas/pdf', [ExportController::class, 'exportTrenKomoditasPDF'])->name('export.tren.pdf');
    });

// API Routes untuk Select2 dan Autocomplete
Route::prefix('api')->group(function () {
    Route::get('/provinsi', [ProvinsiController::class, 'apiIndex'])->name('api.provinsi');
    Route::get('/kabupaten/{provinsi_id}', [KabupatenController::class, 'apiByProvinsi'])->name('api.kabupaten');
    Route::get('/kecamatan/{kabupaten_id}', [KecamatanController::class, 'apiByKabupaten'])->name('api.kecamatan');
    Route::get('/desa/{kecamatan_id}', [DesaController::class, 'apiByKecamatan'])->name('api.desa');
    Route::get('/poktan', [PoktanController::class, 'apiIndex'])->name('api.poktan');
    Route::get('/komoditas', [KomoditasController::class, 'apiIndex'])->name('api.komoditas');
    Route::get('/sektor', [SektorController::class, 'apiIndex'])->name('api.sektor');
    
    // API untuk petani berdasarkan poktan
    Route::get('/petani/{poktan_id}', [PetaniController::class, 'apiByPoktan'])->name('api.petani.by-poktan');
    
    // API untuk Data BPS - TAMBAHKAN INI
    Route::get('/bps-data/provinsi', [BpsDataController::class, 'apiByProvinsi'])->name('api.bps-data.provinsi');
    Route::get('/bps-data/tahun', [BpsDataController::class, 'apiTahunList'])->name('api.bps-data.tahun');
    



    // ========== TAMBAHKAN ROUTE INI UNTUK FORM CREATE BPS DATA ==========
    Route::get('/kabupaten/{provinsiId}', function($provinsiId) {
        $kabupatens = \App\Models\Kabupaten::where('provinsi_id', $provinsiId)
            ->where('aktif', 1)
            ->orderBy('nama')
            ->get(['id', 'nama']);
        
        return response()->json($kabupatens);
    });


// ========== TAMBAHKAN ROUTE INI UNTUK FORM CREATE BPS DATA ==========
    Route::get('/kabupaten/{provinsiId}', function($provinsiId) {
        $kabupatens = \App\Models\Kabupaten::where('provinsi_id', $provinsiId)
            ->where('aktif', 1)
            ->orderBy('nama')
            ->get(['id', 'nama']);
        
        return response()->json($kabupatens);
    });

    Route::get('/kecamatan/{kabupatenId}', function($kabupatenId) {
        $kecamatans = \App\Models\Kecamatan::where('kabupaten_id', $kabupatenId)
            ->where('aktif', 1)
            ->orderBy('nama')
            ->get(['id', 'nama']);
        
        return response()->json($kecamatans);
    });

    // ========== TAMBAHKAN ROUTE INI UNTUK BPS DASHBOARD FILTER ==========
    Route::get('/kabupaten-by-provinsi/{provinsiId}', function($provinsiId) {
        $kabupatens = \App\Models\Kabupaten::where('provinsi_id', $provinsiId)
            ->where('aktif', true)
            ->orderBy('nama')
            ->get(['id', 'nama']);
        
        return response()->json($kabupatens);
    })->name('api.kabupaten-by-provinsi');

     Route::get('/kecamatan-by-kabupaten/{kabupatenId}', function($kabupatenId) {
        $kecamatans = \App\Models\Kecamatan::where('kabupaten_id', $kabupatenId)
            ->where('aktif', true)
            ->orderBy('nama')
            ->get(['id', 'nama']);
        
        return response()->json($kecamatans);
    })->name('api.kecamatan-by-kabupaten');
    // ========== END TAMBAHAN BPS DASHBOARD ==========

    Route::get('/komoditas/{sektorId}', function($sektorId) {
        try {
            $komoditas = \App\Models\Komoditas::where('sektor_id', $sektorId)
                ->where('aktif', 1)
                ->orderBy('nama')
                ->get(['id', 'nama', 'satuan', 'warna_chart']);
            
            return response()->json($komoditas);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load komoditas'], 500);
        }
    });
    // ========== END TAMBAHAN ==========
});

    // ========== ROUTE UMUM UNTUK ANGGOTA POKTAN ==========
    // Route utama untuk melihat anggota poktan (dengan parameter)
    Route::get('/poktan/{poktan}/anggota', [PoktanController::class, 'anggota'])
        ->name('poktan.anggota.detail')
        ->middleware('auth');
}); // <- INI KURUNG PENUTUP UNTUK MIDDLEWARE AUTH

// Public API (Untuk integrasi eksternal)
Route::prefix('api/public')->group(function () {
    Route::get('/master-data', function () {
        return response()->json([
            'komoditas' => \App\Models\Komoditas::where('aktif', true)->get(['id', 'nama', 'satuan']),
            'sektor' => \App\Models\Sektor::where('aktif', true)->get(['id', 'nama']),
            'provinsi' => \App\Models\Provinsi::all(['id', 'nama']),
        ]);
    })->name('api.public.master-data');
    
    Route::get('/statistics', function () {
        return response()->json([
            'total_produksi' => \App\Models\Produksi::whereYear('tanggal_input', date('Y'))->sum('total_produksi'),
            'total_petani' => \App\Models\Petani::where('aktif', true)->count(),
            'total_demplot' => \App\Models\Demplot::count(),
            'total_poktan' => \App\Models\Poktan::count(),
        ]);
    })->name('api.public.statistics');
    
    // Public API untuk Data BPS - TAMBAHKAN INI
    Route::get('/bps-data/unggulan', function () {
        $tahun = request('tahun', date('Y') - 1);
        $data = \App\Models\BpsData::with(['komoditas', 'provinsi'])
            ->where('tahun', $tahun)
            ->where('status_unggulan', true)
            ->get()
            ->groupBy('komoditas.nama')
            ->map(function ($items) {
                return [
                    'total_produksi' => $items->sum('produksi'),
                    'jumlah_wilayah' => $items->count(),
                    'warna' => $items->first()->komoditas->warna_chart ?? '#666666'
                ];
            });
            
        return response()->json($data);
    })->name('api.public.bps-unggulan');
});