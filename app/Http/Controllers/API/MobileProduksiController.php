<?php
// app/Http/Controllers/API/MobileProduksiController.php
namespace App\Http\Controllers\API;

use App\Models\Produksi;
use App\Models\Demplot;
use App\Models\Komoditas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MobileProduksiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $query = Produksi::with(['demplot', 'komoditas', 'petugas'])
                ->orderBy('tanggal_input', 'desc');

            // Filter berdasarkan role dan wilayah
            if ($user->isPetugas() && $user->wilayah_id) {
                $wilayahIds = $this->getWilayahChildren($user->wilayah_id);
                $query->whereHas('demplot.wilayah', function($q) use ($wilayahIds) {
                    $q->whereIn('id', $wilayahIds);
                });
            }

            if ($user->isPoktan()) {
                $query->whereHas('demplot.petani.poktan', function($q) use ($user) {
                    $q->where('id', $user->wilayah_id);
                });
            }

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal_input', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            // Filter by komoditas
            if ($request->has('komoditas_id')) {
                $query->where('komoditas_id', $request->komoditas_id);
            }

            $produksi = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $produksi,
                'message' => 'Data produksi berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $produksi = Produksi::with(['demplot.wilayah', 'komoditas.sektor', 'petugas'])
                ->find($id);

            if (!$produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data produksi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $produksi,
                'message' => 'Detail produksi berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'demplot_id' => 'required|exists:demplot,id',
                'komoditas_id' => 'required|exists:komoditas,id',
                'tahun' => 'required|integer|min:2020|max:2030',
                'bulan' => 'nullable|integer|min:1|max:12',
                'luas_panen' => 'required|numeric|min:0',
                'total_produksi' => 'required|numeric|min:0',
                'sumber_data' => 'nullable|string|max:255',
                'tanggal_input' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if demplot belongs to user's wilayah
            $user = $request->user();
            $demplot = Demplot::find($request->demplot_id);

            if ($user->isPetugas() && $user->wilayah_id) {
                $wilayahIds = $this->getWilayahChildren($user->wilayah_id);
                if (!in_array($demplot->wilayah_id, $wilayahIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke demplot ini'
                    ], 403);
                }
            }

            if ($user->isPoktan()) {
                if ($demplot->petani->poktan_id != $user->wilayah_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke demplot ini'
                    ], 403);
                }
            }

            $data = $request->all();
            $data['petugas_id'] = $user->id;
            $data['tanggal_input'] = $request->tanggal_input ?? now();

            // Hitung produktivitas otomatis
            if ($data['luas_panen'] > 0) {
                $data['produktivitas'] = $data['total_produksi'] / $data['luas_panen'];
            }

            $produksi = Produksi::create($data);

            return response()->json([
                'success' => true,
                'data' => $produksi,
                'message' => 'Data produksi berhasil ditambahkan'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $produksi = Produksi::find($id);

            if (!$produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data produksi tidak ditemukan'
                ], 404);
            }

            // Authorization check
            $user = $request->user();
            if ($produksi->petugas_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah data ini'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'luas_panen' => 'required|numeric|min:0',
                'total_produksi' => 'required|numeric|min:0',
                'sumber_data' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only(['luas_panen', 'total_produksi', 'sumber_data']);

            // Hitung ulang produktivitas
            if ($data['luas_panen'] > 0) {
                $data['produktivitas'] = $data['total_produksi'] / $data['luas_panen'];
            }

            $produksi->update($data);

            return response()->json([
                'success' => true,
                'data' => $produksi,
                'message' => 'Data produksi berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $produksi = Produksi::find($id);

            if (!$produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data produksi tidak ditemukan'
                ], 404);
            }

            // Authorization check
            $user = $request->user();
            if ($produksi->petugas_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus data ini'
                ], 403);
            }

            $produksi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data produksi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    public function stats(Request $request)
    {
        try {
            $user = $request->user();
            $query = Produksi::query();

            // Filter berdasarkan role dan wilayah
            if ($user->isPetugas() && $user->wilayah_id) {
                $wilayahIds = $this->getWilayahChildren($user->wilayah_id);
                $query->whereHas('demplot.wilayah', function($q) use ($wilayahIds) {
                    $q->whereIn('id', $wilayahIds);
                });
            }

            if ($user->isPoktan()) {
                $query->whereHas('demplot.petani.poktan', function($q) use ($user) {
                    $q->where('id', $user->wilayah_id);
                });
            }

            $stats = [
                'total_produksi' => $query->sum('total_produksi'),
                'total_luas_panen' => $query->sum('luas_panen'),
                'rata_produktivitas' => $query->avg('produktivitas'),
                'total_input' => $query->count()
            ];

            // Produksi bulan ini
            $produksiBulanIni = $query->clone()
                ->whereYear('tanggal_input', now()->year)
                ->whereMonth('tanggal_input', now()->month)
                ->sum('total_produksi');

            $stats['produksi_bulan_ini'] = $produksiBulanIni;

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistik produksi berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    public function syncOfflineData(Request $request)
    {
        try {
            $user = $request->user();
            $offlineData = $request->input('data', []);
            $syncedIds = [];

            foreach ($offlineData as $data) {
                // Validasi data
                $validator = Validator::make($data, [
                    'demplot_id' => 'required|exists:demplot,id',
                    'komoditas_id' => 'required|exists:komoditas,id',
                    'luas_panen' => 'required|numeric|min:0',
                    'total_produksi' => 'required|numeric|min:0',
                    'tahun' => 'required|integer',
                    'bulan' => 'nullable|integer'
                ]);

                if ($validator->fails()) {
                    continue; // Skip invalid data
                }

                // Check authorization
                $demplot = Demplot::find($data['demplot_id']);
                if ($user->isPetugas() && $user->wilayah_id) {
                    $wilayahIds = $this->getWilayahChildren($user->wilayah_id);
                    if (!in_array($demplot->wilayah_id, $wilayahIds)) {
                        continue;
                    }
                }

                if ($user->isPoktan()) {
                    if ($demplot->petani->poktan_id != $user->wilayah_id) {
                        continue;
                    }
                }

                // Create production record
                $produksiData = [
                    'demplot_id' => $data['demplot_id'],
                    'komoditas_id' => $data['komoditas_id'],
                    'tahun' => $data['tahun'],
                    'bulan' => $data['bulan'] ?? null,
                    'luas_panen' => $data['luas_panen'],
                    'total_produksi' => $data['total_produksi'],
                    'petugas_id' => $user->id,
                    'tanggal_input' => now(),
                    'sumber_data' => 'mobile-offline'
                ];

                // Hitung produktivitas
                if ($produksiData['luas_panen'] > 0) {
                    $produksiData['produktivitas'] = $produksiData['total_produksi'] / $produksiData['luas_panen'];
                }

                $produksi = Produksi::create($produksiData);
                $syncedIds[] = $produksi->id;
            }

            return response()->json([
                'success' => true,
                'message' => 'Data offline berhasil disinkronisasi',
                'data' => [
                    'synced_count' => count($syncedIds),
                    'synced_ids' => $syncedIds
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat sinkronisasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getWilayahChildren($wilayahId)
    {
        $wilayah = \App\Models\Wilayah::with('children')->find($wilayahId);
        $ids = [$wilayahId];
        
        if ($wilayah->children) {
            foreach ($wilayah->children as $child) {
                $ids = array_merge($ids, $this->getWilayahChildren($child->id));
            }
        }
        
        return $ids;
    }
}