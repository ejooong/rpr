<?php
// routes/api.php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MobileProduksiController;
use App\Http\Controllers\API\MobileDemplotController;
use App\Http\Controllers\API\MobilePetaniController;
use App\Http\Controllers\API\MobilePoktanController;
use App\Http\Controllers\API\MobileLaporanController;
use App\Http\Controllers\API\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public API routes for dropdowns (tanpa auth)
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

// Public data for dropdowns (cached)
Route::get('/master-data', function () {
    return response()->json([
        'komoditas' => \App\Models\Komoditas::where('aktif', true)->get(['id', 'nama', 'satuan']),
        'sektor' => \App\Models\Sektor::where('aktif', true)->get(['id', 'nama']),
        'wilayah' => \App\Models\Wilayah::where('aktif', true)->get(['id', 'nama', 'level']),
    ]);
});

// Test route
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'RPR Mobile API is working!',
        'timestamp' => now()
    ]);
});

// Protected routes (memerlukan auth)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/read/{id}', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Produksi
    Route::get('/produksi', [MobileProduksiController::class, 'index']);
    Route::get('/produksi/{id}', [MobileProduksiController::class, 'show']);
    Route::post('/produksi', [MobileProduksiController::class, 'store']);
    Route::put('/produksi/{id}', [MobileProduksiController::class, 'update']);
    Route::delete('/produksi/{id}', [MobileProduksiController::class, 'destroy']);
    Route::get('/produksi-stats', [MobileProduksiController::class, 'stats']);

    // Demplot
    Route::get('/demplot', [MobileDemplotController::class, 'index']);
    Route::get('/demplot/{id}', [MobileDemplotController::class, 'show']);
    Route::post('/demplot', [MobileDemplotController::class, 'store']);
    Route::post('/demplot/{id}/upload-photo', [MobileDemplotController::class, 'uploadPhoto']);

    // Petani
    Route::get('/petani', [MobilePetaniController::class, 'index']);
    Route::get('/petani/{id}', [MobilePetaniController::class, 'show']);
    Route::post('/petani', [MobilePetaniController::class, 'store']);
    Route::put('/petani/{id}', [MobilePetaniController::class, 'update']);

    // Poktan
    Route::get('/poktan', [MobilePoktanController::class, 'index']);
    Route::get('/poktan/{id}', [MobilePoktanController::class, 'show']);
    Route::get('/poktan/{id}/anggota', [MobilePoktanController::class, 'anggota']);

    // Laporan
    Route::get('/laporan/produksi-harian', [MobileLaporanController::class, 'produksiHarian']);
    Route::get('/laporan/komoditas-unggulan', [MobileLaporanController::class, 'komoditasUnggulan']);
    Route::get('/laporan/statistik-wilayah', [MobileLaporanController::class, 'statistikWilayah']);

    // Sync
    Route::post('/sync/offline-data', [MobileProduksiController::class, 'syncOfflineData']);
});

