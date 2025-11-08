<?php
// app/Http/Controllers/API/MobileDemplotController.php
namespace App\Http\Controllers\API;

use App\Models\Demplot;
use App\Models\Wilayah;
use App\Models\Petani;
use App\Models\Komoditas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class MobileDemplotController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $query = Demplot::with(['wilayah', 'petani.poktan', 'komoditas.sektor']);

            // Filter berdasarkan role
            if ($user->isPetugas() && $user->wilayah_id) {
                $wilayahIds = $this->getWilayahChildren($user->wilayah_id);
                $query->whereIn('wilayah_id', $wilayahIds);
            }

            if ($user->isPoktan()) {
                $query->whereHas('petani.poktan', function($q) use ($user) {
                    $q->where('id', $user->wilayah_id);
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $demplots = $query->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $demplots,
                'message' => 'Data demplot berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $demplot = Demplot::with([
                'wilayah', 
                'petani.poktan', 
                'komoditas.sektor',
                'produksi' => function($query) {
                    $query->orderBy('tanggal_input', 'desc')->take(10);
                }
            ])->find($id);

            if (!$demplot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data demplot tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $demplot,
                'message' => 'Detail demplot berhasil diambil'
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
                'wilayah_id' => 'required|exists:wilayah,id',
                'petani_id' => 'required|exists:petani,id',
                'komoditas_id' => 'required|exists:komoditas,id',
                'nama_lahan' => 'required|string|max:255',
                'luas_lahan' => 'required|numeric|min:0',
                'status' => 'required|in:rencana,aktif,selesai',
                'tahun' => 'required|integer|min:2020|max:2030',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Authorization check
            $user = $request->user();
            $wilayah = Wilayah::find($request->wilayah_id);
            $petani = Petani::find($request->petani_id);

            if ($user->isPetugas() && $user->wilayah_id) {
                $wilayahIds = $this->getWilayahChildren($user->wilayah_id);
                if (!in_array($request->wilayah_id, $wilayahIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke wilayah ini'
                    ], 403);
                }
            }

            if ($user->isPoktan()) {
                if ($petani->poktan_id != $user->wilayah_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke petani ini'
                    ], 403);
                }
            }

            $demplot = Demplot::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $demplot,
                'message' => 'Data demplot berhasil ditambahkan'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadPhoto(Request $request, $id)
    {
        try {
            $demplot = Demplot::find($id);

            if (!$demplot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data demplot tidak ditemukan'
                ], 404);
            }

            // Authorization check
            $user = $request->user();
            if ($user->isPetugas() && $user->wilayah_id) {
                $wilayahIds = $this->getWilayahChildren($user->wilayah_id);
                if (!in_array($demplot->wilayah_id, $wilayahIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke demplot ini'
                    ], 403);
                }
            }

            $validator = Validator::make($request->all(), [
                'foto_lahan' => 'required|image|mimes:jpeg,png,jpg|max:5120' // 5MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Delete old photo if exists
            if ($demplot->foto_lahan) {
                Storage::disk('public')->delete($demplot->foto_lahan);
            }

            // Upload new photo
            $path = $request->file('foto_lahan')->store('demplot', 'public');
            $demplot->update(['foto_lahan' => $path]);

            return response()->json([
                'success' => true,
                'data' => [
                    'foto_url' => asset('storage/' . $path)
                ],
                'message' => 'Foto berhasil diupload'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat upload foto'
            ], 500);
        }
    }

    private function getWilayahChildren($wilayahId)
    {
        $wilayah = Wilayah::with('children')->find($wilayahId);
        $ids = [$wilayahId];
        
        if ($wilayah->children) {
            foreach ($wilayah->children as $child) {
                $ids = array_merge($ids, $this->getWilayahChildren($child->id));
            }
        }
        
        return $ids;
    }
}