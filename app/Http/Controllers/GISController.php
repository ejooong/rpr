<?php
    // app/Http/Controllers/GISController.php
    namespace App\Http\Controllers;

    use App\Models\Demplot;
    use App\Models\Komoditas;
    use App\Models\Sektor;
    use App\Models\Provinsi;
    use App\Models\Kabupaten;
    use App\Models\Kecamatan;
    use App\Models\Desa;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
    class GISController extends Controller
    {
        public function index(Request $request)
        {
            $komoditas = Komoditas::where('aktif', true)->get();
            $provinsi = Provinsi::where('aktif', true)->get();
            $sektor = Sektor::where('aktif', true)->get();

            // Statistics for GIS dashboard
            $stats = [
                'total_demplot' => Demplot::count(),
                'demplot_aktif' => Demplot::where('status', 'aktif')->count(),
                'total_komoditas' => Komoditas::where('aktif', true)->count(),
                'provinsi_tercover' => Demplot::whereNotNull('provinsi_id')->distinct('provinsi_id')->count('provinsi_id')
            ];

            return view('gis.index', compact('komoditas', 'provinsi', 'sektor', 'stats'));
        }

       public function apiDemplot(Request $request)
    {
        try {
            Log::info('=== GIS API REQUEST START ===');
            Log::info('Request Parameters:', $request->all());

            // Start query - HAPUS FILTER KOORDINAT UNTUK TESTING
            $query = Demplot::with([
                'provinsi', 
                'kabupaten', 
                'kecamatan', 
                'desa',
                'komoditas.sektor', 
                'petani.poktan'
            ]);

            // HAPUS: ->whereNotNull('latitude')->whereNotNull('longitude');

            // Total sebelum filter
            $totalBefore = $query->count();
            Log::info("Total demplots in database: {$totalBefore}");

            // Hitung yang punya koordinat
            $withCoordinates = Demplot::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->count();
            Log::info("Demplots with coordinates: {$withCoordinates}");

            // FILTER SEKTOR
            if ($request->filled('sektor_id')) {
                $sektorId = $request->sektor_id;
                Log::info("🔍 FILTER SEKTOR APPLIED: {$sektorId}");

                // Dapatkan semua komoditas untuk sektor ini
                $komoditasIds = Komoditas::where('sektor_id', $sektorId)
                    ->where('aktif', true)
                    ->pluck('id')
                    ->toArray();

                Log::info("Komoditas IDs for sektor {$sektorId}:", $komoditasIds);

                if (count($komoditasIds) > 0) {
                    $beforeCount = $query->count();
                    $query->whereIn('komoditas_id', $komoditasIds);
                    $afterCount = $query->count();
                    
                    Log::info("📊 SEKTOR FILTER RESULTS: Before={$beforeCount}, After={$afterCount}");
                } else {
                    Log::warning("❌ No komoditas found for sektor_id: {$sektorId}");
                }
            }

            // FILTER KOMODITAS
            if ($request->filled('komoditas_id')) {
                $komoditasId = $request->komoditas_id;
                Log::info("🔍 FILTER KOMODITAS APPLIED: {$komoditasId}");
                
                $beforeCount = $query->count();
                $query->where('komoditas_id', $komoditasId);
                $afterCount = $query->count();
                
                Log::info("📊 KOMODITAS FILTER RESULTS: Before={$beforeCount}, After={$afterCount}");
            }

            // Filter lainnya
            if ($request->filled('provinsi_id')) {
                $query->where('provinsi_id', $request->provinsi_id);
            }

            if ($request->filled('kabupaten_id')) {
                $query->where('kabupaten_id', $request->kabupaten_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Eksekusi query
            $demplots = $query->get();
            Log::info("🎯 FINAL RESULTS: {$demplots->count()} demplots found");

            // Filter hanya yang punya koordinat untuk peta
            $demplotsWithCoordinates = $demplots->filter(function($demplot) {
                return !is_null($demplot->latitude) && !is_null($demplot->longitude);
            });

            Log::info("📍 Demplots with coordinates for map: {$demplotsWithCoordinates->count()}");

            // Mapping data
            $mappedDemplots = $demplotsWithCoordinates->map(function($demplot) {
                $komoditasName = $demplot->komoditas->nama ?? 'NULL';
                $sektorName = $demplot->komoditas->sektor->nama ?? 'NULL';
                $sektorId = $demplot->komoditas->sektor_id ?? 'NULL';

                Log::info("📍 Demplot {$demplot->id}: Komoditas='{$komoditasName}', Sektor='{$sektorName}' (ID: {$sektorId})");

    return [
        'id' => $demplot->id,
        'nama_lahan' => $demplot->nama_lahan,
        'latitude' => (float) $demplot->latitude,
        'longitude' => (float) $demplot->longitude,
        'luas_lahan' => $demplot->luas_lahan,
        'status' => $demplot->status,
        'komoditas' => $komoditasName,
        'sektor' => $sektorName,
        'sektor_id' => $sektorId,
        'provinsi' => $demplot->provinsi->nama ?? 'Tidak diketahui',
        'provinsi_kode' => $demplot->provinsi->kode ?? $demplot->provinsi->kode_prov ?? null,
        'kabupaten' => $demplot->kabupaten->nama ?? 'Tidak diketahui',
        'kabupaten_kode' => $demplot->kabupaten->kode ?? $demplot->kabupaten->kode_kab ?? null,
        'kecamatan' => $demplot->kecamatan->nama ?? 'Tidak diketahui',
        'desa' => $demplot->desa->nama ?? 'Tidak diketahui',
        'petani' => $demplot->petani->nama ?? 'Tidak diketahui',
        'poktan' => $demplot->petani->poktan->nama ?? 'Tidak diketahui',
        'foto_lahan' => $demplot->foto_lahan ? asset('storage/' . $demplot->foto_lahan) : null,
        'keterangan' => $demplot->keterangan,
        'tanggal_tanam' => $demplot->tanggal_tanam ? $demplot->tanggal_tanam->format('d M Y') : '-',
        'warna' => $this->getColorBySektor($sektorId ?? 1),
        'icon' => $this->getIconByStatus($demplot->status),
        'popup_content' => $this->getPopupContent($demplot)
    ];
            });

            Log::info('=== GIS API REQUEST COMPLETE ===');

            return response()->json([
                'success' => true,
                'data' => $mappedDemplots,
                'total' => $mappedDemplots->count(),
                'filters' => $request->all(),
                'debug' => [
                    'total_demplots' => $totalBefore,
                    'total_with_coordinates' => $demplotsWithCoordinates->count(),
                    'total_after_filters' => $demplots->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ GIS API ERROR: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching demplot data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

        // ... (method lainnya tetap sama)
        public function apiStatistics(Request $request)
        {
            try {
                $stats = DB::table('demplot')
                    ->join('komoditas', 'demplot.komoditas_id', '=', 'komoditas.id')
                    ->join('sektor', 'komoditas.sektor_id', '=', 'sektor.id')
                    ->select(
                        'sektor.nama as sektor',
                        DB::raw('COUNT(demplot.id) as total_demplot'),
                        DB::raw('SUM(demplot.luas_lahan) as total_luas'),
                        DB::raw('AVG(demplot.luas_lahan) as rata_luas')
                    )
                    ->groupBy('sektor.id', 'sektor.nama')
                    ->get();

                $statusStats = DB::table('demplot')
                    ->select('status', DB::raw('COUNT(*) as total'))
                    ->groupBy('status')
                    ->get();

                return response()->json([
                    'sektor_stats' => $stats,
                    'status_stats' => $statusStats
                ]);
            } catch (\Exception $e) {
                Log::error('GIS Statistics Error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error fetching statistics',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        public function apiWilayah(Request $request)
        {
            try {
                $level = $request->get('level', 'provinsi');
                $parentId = $request->get('parent_id');

                $data = [];

                switch ($level) {
                    case 'provinsi':
                        $data = Provinsi::where('aktif', true)->get(['id', 'nama']);
                        break;
                    case 'kabupaten':
                        $data = Kabupaten::where('provinsi_id', $parentId)
                            ->where('aktif', true)
                            ->get(['id', 'nama']);
                        break;
                    case 'kecamatan':
                        $data = Kecamatan::where('kabupaten_id', $parentId)
                            ->where('aktif', true)
                            ->get(['id', 'nama']);
                        break;
                    case 'desa':
                        $data = Desa::where('kecamatan_id', $parentId)
                            ->where('aktif', true)
                            ->get(['id', 'nama']);
                        break;
                }

                return response()->json($data);
            } catch (\Exception $e) {
                Log::error('GIS Wilayah API Error: ' . $e->getMessage());
                return response()->json([], 500);
            }
        }

        private function getColorBySektor($sektorId)
        {
            $colors = [
                1 => '#1e3a8a', // Tanaman Pangan - Biru
                2 => '#16a34a', // Hortikultura - Hijau
                3 => '#d97706', // Perkebunan - Orange
                4 => '#dc2626', // Peternakan - Merah
                5 => '#7c3aed', // Perikanan - Ungu
            ];

            return $colors[$sektorId] ?? '#6b7280';
        }

        private function getIconByStatus($status)
        {
            $icons = [
                'rencana' => '📋',
                'aktif' => '🌱', 
                'selesai' => '✅',
                'nonaktif' => '❌'
            ];

            return $icons[$status] ?? '📍';
        }

        private function getPopupContent($demplot)
{
    $namaLahan = $demplot->nama_lahan ?? $demplot->nama ?? 'Unnamed';
    
    // Handle foto lahan - cek beberapa kemungkinan path
    $fotoLahan = null;
    if ($demplot->foto_lahan) {
        // Cek berbagai kemungkinan path storage
        if (strpos($demplot->foto_lahan, 'http') === 0) {
            $fotoLahan = $demplot->foto_lahan; // Sudah full URL
        } elseif (Storage::exists($demplot->foto_lahan)) {
            $fotoLahan = asset('storage/' . $demplot->foto_lahan);
        } elseif (Storage::exists('public/' . $demplot->foto_lahan)) {
            $fotoLahan = asset('storage/' . $demplot->foto_lahan);
        } elseif (file_exists(public_path('storage/' . $demplot->foto_lahan))) {
            $fotoLahan = asset('storage/' . $demplot->foto_lahan);
        } else {
            $fotoLahan = $demplot->foto_lahan; // Fallback ke path asli
        }
    }

    $fotoHtml = $fotoLahan ? 
        '<img src="' . $fotoLahan . '" class="w-full h-32 object-cover rounded mb-2" alt="Foto Lahan" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';">' . 
        '<div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center text-gray-500 hidden">Foto tidak tersedia</div>' : 
        '<div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center text-gray-500">Tidak ada foto</div>';

    // Data petani dan poktan
    $namaPetani = $demplot->petani->nama ?? '-';
    $namaPoktan = $demplot->petani->poktan->nama ?? '-';
    $alamatPetani = $demplot->petani->alamat ?? '-';

    return '
    <div class="w-72 max-w-sm">
        ' . $fotoHtml . '
        <h3 class="font-bold text-lg text-blue-800 mb-2">' . htmlspecialchars($namaLahan) . '</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="font-medium text-gray-700">Komoditas:</span>
                <span class="text-gray-900">' . htmlspecialchars($demplot->komoditas->nama ?? '-') . '</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-700">Sektor:</span>
                <span class="text-gray-900">' . htmlspecialchars($demplot->komoditas->sektor->nama ?? '-') . '</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-700">Luas Lahan:</span>
                <span class="text-gray-900">' . number_format($demplot->luas_lahan, 2) . ' Ha</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-700">Status:</span>
                <span class="' . $this->getStatusBadgeClass($demplot->status) . '">' . ucfirst($demplot->status) . '</span>
            </div>
            <div class="border-t pt-2 mt-2">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700">Nama Petani:</span>
                    <span class="text-gray-900 font-semibold">' . htmlspecialchars($namaPetani) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700">Kelompok Tani:</span>
                    <span class="text-gray-900">' . htmlspecialchars($namaPoktan) . '</span>
                </div>
                ' . ($alamatPetani != '-' ? '
                <div class="mt-1">
                    <span class="font-medium text-gray-700">Alamat:</span>
                    <span class="text-gray-900 text-xs block">' . htmlspecialchars($alamatPetani) . '</span>
                </div>' : '') . '
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-700">Tanggal Tanam:</span>
                <span class="text-gray-900">' . ($demplot->tanggal_tanam ? $demplot->tanggal_tanam->format('d M Y') : '-') . '</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-700">Lokasi:</span>
                <span class="text-gray-900 text-right">' . 
                    htmlspecialchars($demplot->desa->nama ?? '-') . ', ' . 
                    htmlspecialchars($demplot->kecamatan->nama ?? '-') . '<br>' .
                    htmlspecialchars($demplot->kabupaten->nama ?? '-') . ', ' . 
                    htmlspecialchars($demplot->provinsi->nama ?? '-') . 
                '</span>
            </div>
        </div>
        ' . ($demplot->keterangan ? 
        '<div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded">
            <strong class="text-yellow-800 text-sm">Keterangan:</strong>
            <p class="text-yellow-700 text-xs mt-1">' . htmlspecialchars($demplot->keterangan) . '</p>
        </div>' : '') . '
        <div class="mt-3 flex space-x-2">
            <a href="' . route('demplot.show', $demplot->id) . '" 
               class="flex-1 bg-blue-600 text-white text-center py-2 px-3 rounded text-sm hover:bg-blue-700 transition duration-200 font-medium">
                📋 Detail
            </a>
            <button onclick="focusOnMarker(' . $demplot->id . ')" 
                    class="flex-1 bg-green-600 text-white py-2 px-3 rounded text-sm hover:bg-green-700 transition duration-200 font-medium">
                🔍 Focus
            </button>
        </div>
        <div class="mt-2 text-center">
            <span class="text-xs text-gray-500">RPR NasDem - Rumah Pangan Rakyat</span>
        </div>
    </div>';
}

        private function getStatusBadgeClass($status)
        {
            $classes = [
                'rencana' => 'bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs',
                'aktif' => 'bg-green-100 text-green-800 px-2 py-1 rounded text-xs',
                'selesai' => 'bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs',
                'nonaktif' => 'bg-red-100 text-red-800 px-2 py-1 rounded text-xs'
            ];

            return $classes[$status] ?? 'bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs';
        }

        // ... di dalam class GISController
    // ganti method apiCountsByProvinsi dengan yang ini:
    public function apiCountsByProvinsi(Request $request)
{
    try {
        $provinsiId = $request->get('provinsi_id');
        $provinsiName = $request->get('provinsi_name');

        // Pastikan model ada
        $kabModel = new Kabupaten();
        $demplotModel = new Demplot();
        $provModel = new Provinsi();

        $kabTable = $kabModel->getTable();
        $demplotTable = $demplotModel->getTable();
        $provTable = $provModel->getTable();

        // Cari kolom kode pada tabel kabupaten
        $kodeCandidates = ['kode', 'kode_kab', 'kode_kabupaten', 'kode_daerah', 'kdkab', 'kode_kabk'];
        $kodeColumn = '';
        foreach ($kodeCandidates as $c) {
            if (Schema::hasColumn($kabTable, $c)) {
                $kodeColumn = $c;
                break;
            }
        }
        $kodeSelect = $kodeColumn ? "COALESCE(k.{$kodeColumn}, '') as kabupaten_kode" : "'' as kabupaten_kode";

        // Query: ambil kabupaten berdasarkan provinsi, left join demplot
        $query = DB::table("{$kabTable} as k")
            ->select(
                DB::raw('k.id as kabupaten_id'),
                DB::raw("COALESCE(k.nama, '') as kabupaten"),
                DB::raw($kodeSelect),
                DB::raw("COALESCE(count(d.id), 0) as total")
            )
            ->leftJoin("{$demplotTable} as d", function($join) use ($provinsiId, $provinsiName) {
                $join->on('k.id', '=', 'd.kabupaten_id');
                
                // Filter demplot berdasarkan provinsi
                if ($provinsiId) {
                    $join->where('d.provinsi_id', '=', $provinsiId);
                } elseif ($provinsiName) {
                    // Filter demplot berdasarkan nama provinsi
                    $join->whereExists(function($q) use ($provinsiName) {
                        $q->select(DB::raw(1))
                          ->from('provinsi as p')
                          ->whereRaw('p.id = d.provinsi_id')
                          ->whereRaw('LOWER(TRIM(p.nama)) LIKE ?', ['%'.strtolower(trim($provinsiName)).'%']);
                    });
                }
            });

        // FILTER PENTING: Hanya ambil kabupaten dari provinsi yang dimaksud
        if ($provinsiId) {
            $query->where('k.provinsi_id', $provinsiId);
        } elseif ($provinsiName) {
            $query->whereExists(function($q) use ($provinsiName, $provTable) {
                $q->select(DB::raw(1))
                  ->from($provTable)
                  ->whereRaw("{$provTable}.id = k.provinsi_id")
                  ->whereRaw('LOWER(TRIM(' . $provTable . '.nama)) LIKE ?', ['%'.strtolower(trim($provinsiName)).'%']);
            });
        }

        $query->groupBy('k.id', 'k.nama', 'kabupaten_kode')
              ->orderBy('k.nama', 'asc');

        $results = $query->get();

        Log::info("API Counts Results for {$provinsiName} ({$provinsiId}): " . $results->count() . " kabupaten found");

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    } catch (\Exception $e) {
        Log::error('apiCountsByProvinsi error: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        return response()->json([
            'success' => false,
            'message' => 'Error fetching counts',
            'error' => $e->getMessage()
        ], 500);
    }
}
    }