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
            $foto = $demplot->foto_lahan ? 
                '<img src="' . asset('storage/' . $demplot->foto_lahan) . '" class="w-full h-32 object-cover rounded mb-2" alt="Foto Lahan">' : 
                '<div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center text-gray-500">Tidak ada foto</div>';

            return '
            <div class="w-64">
                ' . $foto . '
                <h3 class="font-bold text-lg text-blue-800">' . $namaLahan . '</h3>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="font-medium">Komoditas:</span>
                        <span>' . ($demplot->komoditas->nama ?? '-') . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Sektor:</span>
                        <span>' . ($demplot->komoditas->sektor->nama ?? '-') . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Luas:</span>
                        <span>' . number_format($demplot->luas_lahan, 2) . ' Ha</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Status:</span>
                        <span class="' . $this->getStatusBadgeClass($demplot->status) . '">' . ucfirst($demplot->status) . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Tanggal Tanam:</span>
                        <span>' . ($demplot->tanggal_tanam ? $demplot->tanggal_tanam->format('d M Y') : '-') . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Petani:</span>
                        <span>' . ($demplot->petani->nama ?? '-') . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Poktan:</span>
                        <span>' . ($demplot->petani->poktan->nama ?? '-') . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Lokasi:</span>
                        <span>' . ($demplot->desa->nama ?? '-') . ', ' . ($demplot->kecamatan->nama ?? '-') . '</span>
                    </div>
                </div>
                ' . ($demplot->keterangan ? '<div class="mt-2 text-xs text-gray-600"><strong>Keterangan:</strong> ' . $demplot->keterangan . '</div>' : '') . '
                <div class="mt-3 flex space-x-2">
                    <a href="' . route('demplot.show', $demplot->id) . '" class="flex-1 bg-green-600 text-white text-center py-1 px-2 rounded text-xs hover:bg-blue-700 transition">
                        Detail
                    </a>
                    <button onclick="focusOnMarker(' . $demplot->id . ')" class="flex-1 bg-green-600 text-white py-1 px-2 rounded text-xs hover:bg-green-700 transition">
                        Focus
                    </button>
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