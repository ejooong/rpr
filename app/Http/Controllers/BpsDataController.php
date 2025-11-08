<?php
// app/Http/Controllers/BpsDataController.php

namespace App\Http\Controllers;

use App\Models\BpsData;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Sektor;
use App\Models\Komoditas;
use App\Imports\BpsDataImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BpsDataTemplateExport; 

class BpsDataController extends Controller
{
    public function __construct()
    {
        // Hapus middleware dari sini karena sudah dihandle di routes
    }

    public function index(Request $request)
{
    // Cek role user
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized action.');
    }

    $query = BpsData::with(['provinsi', 'kabupaten', 'kecamatan', 'sektor', 'komoditas']);
    
    // Filter berdasarkan tahun
    if ($request->has('tahun') && $request->tahun) {
        $query->where('tahun', $request->tahun);
    } 
    // else {
    //     $query->where('tahun', date('Y') - 1); // Default tahun sebelumnya
    // }
    
    // Filter berdasarkan provinsi
    if ($request->has('provinsi_id') && $request->provinsi_id) {
        $query->where('provinsi_id', $request->provinsi_id);
    }
    
    // Filter berdasarkan kabupaten
    if ($request->has('kabupaten_id') && $request->kabupaten_id) {
        $query->where('kabupaten_id', $request->kabupaten_id);
    }
    
    // Filter berdasarkan kecamatan
    if ($request->has('kecamatan_id') && $request->kecamatan_id) {
        $query->where('kecamatan_id', $request->kecamatan_id);
    }
    
    // Filter berdasarkan sektor
    if ($request->has('sektor_id') && $request->sektor_id) {
        $query->where('sektor_id', $request->sektor_id);
    }
    
    // Filter komoditas unggulan
    if ($request->has('unggulan')) {
        $query->where('status_unggulan', $request->unggulan == '1');
    }


// FITUR SEARCH BARU
    if ($request->has('search') && $request->search) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->whereHas('provinsi', function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%');
            })
            ->orWhereHas('kabupaten', function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%');
            })
            ->orWhereHas('kecamatan', function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%');
            })
            ->orWhereHas('komoditas', function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%');
            })
            ->orWhereHas('sektor', function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%');
            })
            ->orWhere('tahun', 'like', '%' . $searchTerm . '%');
        });
    }

    // Sorting
    $sortField = $request->get('sort', 'tahun');
    $sortDirection = $request->get('direction', 'desc');
    
    // Validasi field sorting untuk mencegah SQL injection
    $allowedSortFields = ['tahun', 'produksi', 'luas_lahan', 'produktivitas'];
    if (!in_array($sortField, $allowedSortFields)) {
        $sortField = 'tahun';
    }
    
    if (!in_array($sortDirection, ['asc', 'desc'])) {
        $sortDirection = 'desc';
    }
    
    $query->orderBy($sortField, $sortDirection)
          ->orderBy('provinsi_id')
          ->orderBy('kabupaten_id')
          ->orderBy('kecamatan_id');

    $data = $query->paginate(20);

    // Get data untuk dropdown filter
    $provinsis = Provinsi::where('aktif', true)->get();
    
    // Untuk kabupaten: jika ada provinsiId, ambil kabupaten dari provinsi tersebut
    $kabupatens = collect();
    if ($request->has('provinsi_id') && $request->provinsi_id) {
        $kabupatens = Kabupaten::where('aktif', true)
            ->where('provinsi_id', $request->provinsi_id)
            ->get();
    }
    
    // Untuk kecamatan: jika ada kabupatenId, ambil kecamatan dari kabupaten tersebut
    $kecamatans = collect();
    if ($request->has('kabupaten_id') && $request->kabupaten_id) {
        $kecamatans = Kecamatan::where('aktif', true)
            ->where('kabupaten_id', $request->kabupaten_id)
            ->get();
    }
    
    $sektors = Sektor::where('aktif', true)->get();
    $tahunList = BpsData::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

    return view('bps-data.index', compact(
        'data', 
        'provinsis', 
        'kabupatens', 
        'kecamatans', 
        'sektors', 
        'tahunList',
        'sortField',
        'sortDirection'
    ));
}
    // Dashboard untuk DPD (hanya view chart)
    public function dashboard(Request $request)
    {
        // Cek role user
        if (!auth()->user()->isAdmin() && !auth()->user()->isDPD()) {
            abort(403, 'Unauthorized action.');
        }

        $tahun = $request->get('tahun', date('Y') - 1);
        $provinsiId = $request->get('provinsi_id');
        $kabupatenId = $request->get('kabupaten_id');
        $kecamatanId = $request->get('kecamatan_id');
        
        // Get data untuk dropdown - SELALU ambil semua provinsi
        $provinsis = Provinsi::where('aktif', true)->get();
        
        // Untuk kabupaten: jika ada provinsiId, ambil kabupaten dari provinsi tersebut
        $kabupatens = collect();
        if ($provinsiId) {
            $kabupatens = Kabupaten::where('aktif', true)
                ->where('provinsi_id', $provinsiId)
                ->get();
        }
        
        // Untuk kecamatan: jika ada kabupatenId, ambil kecamatan dari kabupaten tersebut
        $kecamatans = collect();
        if ($kabupatenId) {
            $kecamatans = Kecamatan::where('aktif', true)
                ->where('kabupaten_id', $kabupatenId)
                ->get();
        }
        
        // Base query untuk SEMUA DATA - TANPA FILTER UNGGULAN
        $baseQuery = BpsData::with(['komoditas', 'komoditas.sektor', 'provinsi', 'kabupaten', 'kecamatan'])
            ->where('tahun', $tahun);
            
        // Apply filters
        if ($provinsiId) {
            $baseQuery->where('provinsi_id', $provinsiId);
        }
        
        if ($kabupatenId) {
            $baseQuery->where('kabupaten_id', $kabupatenId);
        }
        
        if ($kecamatanId) {
            $baseQuery->where('kecamatan_id', $kecamatanId);
        }
        
        // Get ALL data untuk statistik total
        $allData = $baseQuery->get();
        
        // Data untuk SEMUA komoditas (ranking otomatis berdasarkan produktivitas)
        $allKomoditasData = $allData->groupBy('komoditas.nama')
            ->map(function ($items) {
                $totalProduksi = $items->sum('produksi');
                $totalLuasLahan = $items->sum('luas_lahan');
                $produktivitas = $totalLuasLahan > 0 ? $totalProduksi / $totalLuasLahan : 0;
                
                return [
                    'total_produksi' => $totalProduksi,
                    'luas_lahan' => $totalLuasLahan,
                    'produktivitas' => $produktivitas,
                    'jumlah_wilayah' => $items->count(),
                    'warna' => $items->first()->komoditas->warna_chart ?? '#666666',
                    'detail_wilayah' => $items->groupBy(function($item) {
                        if ($item->kecamatan) {
                            return $item->kecamatan->nama . ', ' . $item->kabupaten->nama;
                        } elseif ($item->kabupaten) {
                            return $item->kabupaten->nama;
                        } else {
                            return $item->provinsi->nama;
                        }
                    })->map(function ($wilayahItems) {
                        return [
                            'nama' => $wilayahItems->first()->kecamatan ? $wilayahItems->first()->kecamatan->nama : 
                                     ($wilayahItems->first()->kabupaten ? $wilayahItems->first()->kabupaten->nama : 
                                     $wilayahItems->first()->provinsi->nama),
                            'level' => $wilayahItems->first()->kecamatan ? 'kecamatan' : 
                                      ($wilayahItems->first()->kabupaten ? 'kabupaten' : 'provinsi'),
                            'produksi' => $wilayahItems->sum('produksi'),
                            'luas_lahan' => $wilayahItems->sum('luas_lahan')
                        ];
                    })->values()
                ];
            });

        // Data produk unggulan = TOP 10 berdasarkan produktivitas (RANKING OTOMATIS)
        $produkUnggulan = $allKomoditasData->map(function ($item, $nama) {
            return [
                'nama' => $nama,
                'total_produksi' => $item['total_produksi'],
                'luas_lahan' => $item['luas_lahan'],
                'produktivitas' => $item['produktivitas'],
                'jumlah_wilayah' => $item['jumlah_wilayah'],
                'warna' => $item['warna'],
                'detail_wilayah' => $item['detail_wilayah']
            ];
        })->sortByDesc('produktivitas')->take(5)->values();

        // Data ranking per kecamatan (hanya tampil jika ada filter provinsi/kabupaten)
        $dataKecamatan = collect();
        if ($provinsiId || $kabupatenId) {
            $rankingQuery = BpsData::with(['kecamatan', 'komoditas', 'komoditas.sektor', 'kabupaten'])
                ->where('tahun', $tahun)
                ->whereNotNull('kecamatan_id');
            
            if ($provinsiId) {
                $rankingQuery->where('provinsi_id', $provinsiId);
            }
            
            if ($kabupatenId) {
                $rankingQuery->where('kabupaten_id', $kabupatenId);
            }
            
            $dataKecamatan = $rankingQuery->get()
                ->groupBy('kecamatan_id')
                ->map(function ($kecamatanData, $kecamatanId) {
                    $firstItem = $kecamatanData->first();
                    $totalProduksiKecamatan = $kecamatanData->sum('produksi');
                    
                    // Hitung ranking per komoditas dalam kecamatan
                    $ranking = $kecamatanData->map(function ($item) use ($totalProduksiKecamatan) {
                        $kontribusi = $totalProduksiKecamatan > 0 ? ($item->produksi / $totalProduksiKecamatan) * 100 : 0;
                        
                        return [
                            'komoditas' => $item->komoditas->nama,
                            'sektor' => $item->komoditas->sektor->nama ?? '-',
                            'luas_tanam' => $item->luas_lahan,
                            'produksi' => $item->produksi,
                            'produktivitas' => $item->produktivitas,
                            'kontribusi' => round($kontribusi, 2)
                        ];
                    })->sortByDesc('produktivitas')->take(5)->values();
                    
                    return [
                        'kecamatan' => $firstItem->kecamatan->nama ?? 'Tidak Diketahui',
                        'kabupaten' => $firstItem->kabupaten->nama ?? 'Tidak Diketahui',
                        'ranking' => $ranking
                    ];
                })
                ->sortBy('kecamatan')
                ->values();
        }

        // Data tren produksi 5 tahun terakhir dengan luas lahan
        $tahunAwal = $tahun - 4;
        $trenProduksi = BpsData::select(
                'tahun',
                DB::raw('SUM(produksi) as total_produksi'),
                DB::raw('SUM(luas_lahan) as total_luas_lahan'),
                DB::raw('COUNT(DISTINCT komoditas_id) as jumlah_komoditas')
            )
            ->whereBetween('tahun', [$tahunAwal, $tahun])
            ->when($provinsiId, function ($query) use ($provinsiId) {
                return $query->where('provinsi_id', $provinsiId);
            })
            ->when($kabupatenId, function ($query) use ($kabupatenId) {
                return $query->where('kabupaten_id', $kabupatenId);
            })
            ->when($kecamatanId, function ($query) use ($kecamatanId) {
                return $query->where('kecamatan_id', $kecamatanId);
            })
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        // Data sektor dengan produktivitas
        $dataSektor = BpsData::with('sektor')
            ->select(
                'sektor_id',
                DB::raw('SUM(produksi) as total_produksi'),
                DB::raw('SUM(luas_lahan) as total_luas_lahan'),
                DB::raw('COUNT(DISTINCT komoditas_id) as jumlah_komoditas')
            )
            ->where('tahun', $tahun)
            ->when($provinsiId, function ($query) use ($provinsiId) {
                return $query->where('provinsi_id', $provinsiId);
            })
            ->when($kabupatenId, function ($query) use ($kabupatenId) {
                return $query->where('kabupaten_id', $kabupatenId);
            })
            ->when($kecamatanId, function ($query) use ($kecamatanId) {
                return $query->where('kecamatan_id', $kecamatanId);
            })
            ->groupBy('sektor_id')
            ->get()
            ->map(function ($item) {
                $item->rata_produktivitas = $item->total_luas_lahan > 0 ? $item->total_produksi / $item->total_luas_lahan : 0;
                return $item;
            });

        // Hitung statistik tambahan untuk SEMUA DATA
        $totalProduksi = $allData->sum('produksi');
        $totalLuasLahan = $allData->sum('luas_lahan');
        $rataProduktivitas = $totalLuasLahan > 0 ? $totalProduksi / $totalLuasLahan : 0;
        
        // Top produktivitas dari SEMUA komoditas
        $topProduktivitas = $allKomoditasData->map(function ($item, $nama) {
            return [
                'nama' => $nama,
                'total_produksi' => $item['total_produksi'],
                'luas_lahan' => $item['luas_lahan'],
                'produktivitas' => $item['produktivitas']
            ];
        })->sortByDesc('produktivitas')->take(10)->values();

        $produktivitasTertinggi = $topProduktivitas->max('produktivitas');
        $komoditasProduktivitasTertinggi = $topProduktivitas->where('produktivitas', $produktivitasTertinggi)->first()['nama'] ?? '-';

        // Tren produktivitas
        $trenProduktivitas = $trenProduksi->map(function ($item, $index) use ($trenProduksi) {
            $rataProduktivitas = $item->total_luas_lahan > 0 ? $item->total_produksi / $item->total_luas_lahan : 0;
            
            // Hitung pertumbuhan dari tahun sebelumnya
            $pertumbuhan = 0;
            if ($index > 0) {
                $tahunSebelumnya = $trenProduksi[$index - 1];
                $produktivitasSebelumnya = $tahunSebelumnya->total_luas_lahan > 0 ? 
                    $tahunSebelumnya->total_produksi / $tahunSebelumnya->total_luas_lahan : 0;
                
                if ($produktivitasSebelumnya > 0) {
                    $pertumbuhan = (($rataProduktivitas - $produktivitasSebelumnya) / $produktivitasSebelumnya) * 100;
                }
            }

            return (object)[
                'tahun' => $item->tahun,
                'rata_produktivitas' => $rataProduktivitas,
                'pertumbuhan' => $pertumbuhan
            ];
        });

        // Statistik tambahan
        $rataProduksiPerKomoditas = $allKomoditasData->count() > 0 ? $allKomoditasData->avg('total_produksi') : 0;
        $totalWilayah = $allKomoditasData->sum('jumlah_wilayah');
        $totalKomoditas = $allKomoditasData->count();

        $tahunList = BpsData::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $sektors = Sektor::where('aktif', true)->get();
        return view('bps-data.dashboard', compact(
            'produkUnggulan',
            'allKomoditasData',
            'topProduktivitas',
            'trenProduktivitas',
            'trenProduksi',
            'dataSektor',
            'totalProduksi',
            'totalLuasLahan',
            'rataProduktivitas',
            'produktivitasTertinggi',
            'komoditasProduktivitasTertinggi',
            'rataProduksiPerKomoditas',
            'totalWilayah',
            'totalKomoditas',
            'provinsis',
            'kabupatens',
            'kecamatans',
            'sektors',
            'tahunList',
            'tahun',
            'provinsiId',
            'kabupatenId',
            'kecamatanId',
            'dataKecamatan' // TAMBAHKAN INI
            
        ));


    }

    // Create form untuk admin
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $provinsis = Provinsi::where('aktif', true)->get();
        $kabupatens = Kabupaten::where('aktif', true)->get();
        $kecamatans = Kecamatan::where('aktif', true)->get();
        $sektors = Sektor::where('aktif', true)->get();
        $komoditasList = Komoditas::where('aktif', true)->get();
        
        return view('bps-data.create', compact('provinsis', 'kabupatens', 'kecamatans', 'sektors', 'komoditasList'));
    }

    // Store data baru
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:'.(date('Y')+1),
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'nullable|exists:kabupatens,id',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'sektor_id' => 'required|exists:sektor,id',
            'komoditas_id' => 'required|exists:komoditas,id',
            'luas_lahan' => 'nullable|numeric|min:0',
            'produksi' => 'required|numeric|min:0',
            'produktivitas' => 'nullable|numeric|min:0',
            'peringkat_wilayah' => 'nullable|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Hitung produktivitas jika tidak diisi
        if (empty($validated['produktivitas']) && !empty($validated['luas_lahan']) && $validated['luas_lahan'] > 0) {
            $validated['produktivitas'] = $validated['produksi'] / $validated['luas_lahan'];
        }

        BpsData::create($validated);

        return redirect()->route('bps-data.index')
            ->with('success', 'Data BPS berhasil ditambahkan.');
    }

    // Edit form
    public function edit(BpsData $bpsData)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $provinsis = Provinsi::where('aktif', true)->get();
        $kabupatens = Kabupaten::where('aktif', true)->get();
        $kecamatans = Kecamatan::where('aktif', true)->get();
        $sektors = Sektor::where('aktif', true)->get();
        $komoditasList = Komoditas::where('aktif', true)->get();
        
        return view('bps-data.edit', compact('bpsData', 'provinsis', 'kabupatens', 'kecamatans', 'sektors', 'komoditasList'));
    }

    // Update data
    public function update(Request $request, BpsData $bpsData)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:'.(date('Y')+1),
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'nullable|exists:kabupatens,id',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'sektor_id' => 'required|exists:sektor,id',
            'komoditas_id' => 'required|exists:komoditas,id',
            'luas_lahan' => 'nullable|numeric|min:0',
            'produksi' => 'required|numeric|min:0',
            'produktivitas' => 'nullable|numeric|min:0',
            'peringkat_wilayah' => 'nullable|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Hitung produktivitas jika tidak diisi
        if (empty($validated['produktivitas']) && !empty($validated['luas_lahan']) && $validated['luas_lahan'] > 0) {
            $validated['produktivitas'] = $validated['produksi'] / $validated['luas_lahan'];
        }

        $bpsData->update($validated);

        return redirect()->route('bps-data.index')
            ->with('success', 'Data BPS berhasil diperbarui.');
    }

    // Delete data
    public function destroy(BpsData $bpsData)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $bpsData->delete();

        return redirect()->route('bps-data.index')
            ->with('success', 'Data BPS berhasil dihapus.');
    }

    // API untuk chart data - UPDATE: HAPUS FILTER STATUS_UNGGULAN
    public function apiChartData(Request $request)
    {
        $tahun = $request->get('tahun', date('Y') - 1);
        $provinsiId = $request->get('provinsi_id');
        $kabupatenId = $request->get('kabupaten_id');
        $kecamatanId = $request->get('kecamatan_id');
        
        $data = BpsData::select(
                'komoditas.nama',
                DB::raw('SUM(bps_data.produksi) as total_produksi'),
                DB::raw('SUM(bps_data.luas_lahan) as total_luas_lahan')
            )
            ->join('komoditas', 'bps_data.komoditas_id', '=', 'komoditas.id')
            ->where('bps_data.tahun', $tahun)
            ->when($provinsiId, function ($query) use ($provinsiId) {
                return $query->where('bps_data.provinsi_id', $provinsiId);
            })
            ->when($kabupatenId, function ($query) use ($kabupatenId) {
                return $query->where('bps_data.kabupaten_id', $kabupatenId);
            })
            ->when($kecamatanId, function ($query) use ($kecamatanId) {
                return $query->where('bps_data.kecamatan_id', $kecamatanId);
            })
            ->groupBy('komoditas.id', 'komoditas.nama')
            ->orderBy('total_produksi', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $item->produktivitas = $item->total_luas_lahan > 0 ? $item->total_produksi / $item->total_luas_lahan : 0;
                return $item;
            });

        return response()->json($data);
    }

    // API methods untuk select2 - UPDATE: HAPUS FILTER STATUS_UNGGULAN
    public function apiByProvinsi(Request $request)
    {
        $provinsiId = $request->get('provinsi_id');
        $tahun = $request->get('tahun', date('Y') - 1);
        
        $data = BpsData::with(['komoditas', 'kabupaten', 'kecamatan'])
            ->where('provinsi_id', $provinsiId)
            ->where('tahun', $tahun)
            ->get()
            ->groupBy('komoditas.nama');
            
        return response()->json($data);
    }

    public function apiTahunList()
    {
        $tahunList = BpsData::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
            
        return response()->json($tahunList);
    }

    // Show import form
    public function import()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('bps-data.import');
    }

    // Process import
    public function processImport(Request $request)
{
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
    ]);

    try {
        $file = $request->file('file');
        
        // Import data
        $import = new BpsDataImport;
        Excel::import($import, $file);
        
        $successCount = $import->getSuccessCount();
        $errors = $import->getErrors();
        
        $message = "Berhasil mengimport {$successCount} data BPS!";
        
        if (!empty($errors)) {
            $errorCount = count($errors);
            $message .= " Terdapat {$errorCount} error.";
            
            return redirect()->route('bps-data.import')
                ->with('success', $message)
                ->with('import_errors', $errors);
        }
        
        return redirect()->route('bps-data.index')
            ->with('success', $message);
            
    } catch (\Exception $e) {
        Log::error('Import failed: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan saat import file. Silakan cek format file dan coba lagi.');
    }
}

    // Download template - PERBAIKAN
    public function downloadTemplate()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Langsung generate template tanpa menyimpan file
        return Excel::download(new BpsDataTemplateExport, 'template_data_bps.xlsx');
    }

    // HAPUS METHOD rankingKecamatan() - SUDAH DIINTEGRASIKAN KE DASHBOARD
    // public function rankingKecamatan(Request $request) { ... }





    /**
 * Hapus data massal berdasarkan kriteria
 */
public function bulkDelete(Request $request)
{
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'criteria' => 'required|string|in:zero_production,zero_area,both_zero',
        'confirmation' => 'required|string'
    ]);

    // Validasi konfirmasi
    if ($request->confirmation !== 'HAPUS DATA BPS') {
        return redirect()->back()
            ->with('error', 'Konfirmasi tidak sesuai. Ketik "HAPUS DATA BPS" untuk melanjutkan.');
    }

    try {
        $query = BpsData::query();
        $criteria = $request->criteria;
        $deletedCount = 0;

        switch ($criteria) {
            case 'zero_production':
                // Hapus data dengan produksi = 0
                $deletedCount = $query->where('produksi', 0)->delete();
                $message = "Berhasil menghapus $deletedCount data dengan produksi 0";
                break;

            case 'zero_area':
                // Hapus data dengan luas lahan = 0
                $deletedCount = $query->where('luas_lahan', 0)->delete();
                $message = "Berhasil menghapus $deletedCount data dengan luas lahan 0";
                break;

            case 'both_zero':
                // Hapus data dengan produksi = 0 DAN luas lahan = 0
                $deletedCount = $query->where('produksi', 0)
                                    ->where('luas_lahan', 0)
                                    ->delete();
                $message = "Berhasil menghapus $deletedCount data dengan produksi 0 dan luas lahan 0";
                break;
        }

        return redirect()->route('bps-data.index')
            ->with('success', $message);

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

/**
 * Show bulk delete form
 */
public function showBulkDelete()
{
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized action.');
    }

    // Hitung statistik data
    $zeroProductionCount = BpsData::where('produksi', 0)->count();
    $zeroAreaCount = BpsData::where('luas_lahan', 0)->count();
    $bothZeroCount = BpsData::where('produksi', 0)
                           ->where('luas_lahan', 0)
                           ->count();

    return view('bps-data.bulk-delete', compact(
        'zeroProductionCount',
        'zeroAreaCount',
        'bothZeroCount'
    ));
}

// Method untuk rekomendasi komoditas demplot
public function getRekomendasiKomoditas(Request $request)
{
    $tahun = $request->get('tahun', date('Y') - 1);
    $provinsiId = $request->get('provinsi_id');
    $kabupatenId = $request->get('kabupaten_id');
    $level = $request->get('level', 'provinsi'); // 'provinsi' atau 'kabupaten'
    
    // Base query
    $query = BpsData::with(['komoditas', 'komoditas.sektor', 'provinsi', 'kabupaten'])
        ->where('tahun', $tahun);
        
    // Apply filters berdasarkan level
    if ($provinsiId) {
        $query->where('provinsi_id', $provinsiId);
    }
    
    // Untuk level kabupaten, wajib ada kabupatenId
    if ($level === 'kabupaten') {
        if (!$kabupatenId) {
            return response()->json([
                'error' => 'Kabupaten ID diperlukan untuk rekomendasi tingkat kabupaten'
            ], 400);
        }
        $query->where('kabupaten_id', $kabupatenId);
    }
    
    // Untuk level provinsi, abaikan data kecamatan dan fokus ke kabupaten
    if ($level === 'provinsi') {
        $query->whereNotNull('kabupaten_id');
    }
    
    $data = $query->get();
    
    // Group by komoditas dan hitung metrics
    $komoditasData = $data->groupBy('komoditas_id')->map(function ($items, $komoditasId) use ($level) {
        $firstItem = $items->first();
        $totalProduksi = $items->sum('produksi');
        $totalLuasLahan = $items->sum('luas_lahan');
        $produktivitas = $totalLuasLahan > 0 ? $totalProduksi / $totalLuasLahan : 0;
        
        // Untuk level provinsi, hitung jumlah kabupaten
        // Untuk level kabupaten, hitung jumlah kecamatan
        if ($level === 'provinsi') {
            $jumlahWilayah = $items->groupBy('kabupaten_id')->count();
        } else {
            $jumlahWilayah = $items->groupBy('kecamatan_id')->count();
        }
        
        // Hitung potensi pengembangan
        $skorPotensi = ($produktivitas * 0.6) + ($jumlahWilayah * 0.4);
        
        return [
            'komoditas_id' => $komoditasId,
            'nama' => $firstItem->komoditas->nama,
            'sektor' => $firstItem->komoditas->sektor->nama ?? '-',
            'total_produksi' => $totalProduksi,
            'total_luas_lahan' => $totalLuasLahan,
            'produktivitas' => $produktivitas,
            'jumlah_wilayah' => $jumlahWilayah,
            'skor_potensi' => $skorPotensi,
            'rekomendasi_level' => $this->getRekomendasiLevel($produktivitas, $jumlahWilayah, $level)
        ];
    })->sortByDesc('skor_potensi')->values();
    
    return response()->json([
        'rekomendasi' => $komoditasData->take(10),
        'total_komoditas' => $komoditasData->count(),
        'filter' => [
            'tahun' => $tahun,
            'provinsi_id' => $provinsiId,
            'kabupaten_id' => $kabupatenId,
            'level' => $level
        ]
    ]);
}

// Update helper method untuk level
private function getRekomendasiLevel($produktivitas, $jumlahWilayah, $level)
{
    $minWilayah = $level === 'provinsi' ? 3 : 2; // Standar berbeda untuk provinsi vs kabupaten
    
    if ($produktivitas > 10 && $jumlahWilayah >= $minWilayah) {
        return 'Sangat Direkomendasikan';
    } elseif ($produktivitas > 7 && $jumlahWilayah >= ($minWilayah - 1)) {
        return 'Direkomendasikan';
    } elseif ($produktivitas > 5) {
        return 'Cukup Direkomendasikan';
    } else {
        return 'Perlu Evaluasi';
    }
}
}