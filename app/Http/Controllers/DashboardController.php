<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\Petani;
use App\Models\Demplot;
use App\Models\Komoditas;
use App\Models\Sektor;
use App\Models\Poktan;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Data berdasarkan role user
        switch ($user->role) {
            case 'admin':
                return $this->adminDashboard($request);
            case 'petugas':
                return $this->petugasDashboard($request);
            case 'dpd':
                return $this->dpdDashboard($request);
            case 'poktan':
                return $this->poktanDashboard($request);
            default:
                return view('dashboard');
        }
    }

    private function adminDashboard(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        $stats = [
            'total_produksi' => Produksi::whereYear('tanggal_input', $tahun)->sum('total_produksi'),
            'total_petani' => Petani::where('aktif', true)->count(),
            'total_demplot' => Demplot::count(),
            'total_komoditas' => Komoditas::where('aktif', true)->count(),
            'total_poktan' => Poktan::count(),
            'total_provinsi' => Provinsi::count(),
        ];

        // Produksi per sektor
        $produksiPerSektor = DB::table('produksi')
            ->join('komoditas', 'produksi.komoditas_id', '=', 'komoditas.id')
            ->join('sektor', 'komoditas.sektor_id', '=', 'sektor.id')
            ->whereYear('produksi.tanggal_input', $tahun)
            ->select('sektor.nama as sektor', DB::raw('SUM(produksi.total_produksi) as total'))
            ->groupBy('sektor.id', 'sektor.nama')
            ->get();

        // Tren produksi bulanan
        $trenProduksi = DB::table('produksi')
            ->whereYear('tanggal_input', $tahun)
            ->select(DB::raw('MONTH(tanggal_input) as bulan'), DB::raw('SUM(total_produksi) as total'))
            ->groupBy(DB::raw('MONTH(tanggal_input)'))
            ->orderBy('bulan')
            ->get();

        // Top 5 komoditas
        $topKomoditas = DB::table('produksi')
            ->join('komoditas', 'produksi.komoditas_id', '=', 'komoditas.id')
            ->whereYear('produksi.tanggal_input', $tahun)
            ->select('komoditas.nama', DB::raw('SUM(produksi.total_produksi) as total'))
            ->groupBy('komoditas.id', 'komoditas.nama')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'produksiPerSektor', 'trenProduksi', 'topKomoditas', 'tahun'));
    }

// app/Http/Controllers/DashboardController.php

private function petugasDashboard(Request $request)
{
    $user = auth()->user();
    $tahun = $request->get('tahun', date('Y'));

    // STATS TANPA FILTER WILAYAH - SEMUA DATA
    $stats = [
        'total_produksi' => Produksi::whereYear('tanggal_input', $tahun)->sum('total_produksi') ?? 0,
        'total_petani' => Petani::where('aktif', true)->count(),
        'total_demplot' => Demplot::count(),
        'demplot_aktif' => Demplot::where('status', 'aktif')->count(),
    ];

    // KOMODITAS UNGGULAN - SEMUA DATA
    $komoditasUnggulan = DB::table('produksi')
        ->join('komoditas', 'produksi.komoditas_id', '=', 'komoditas.id')
        ->whereYear('produksi.tanggal_input', $tahun)
        ->select('komoditas.nama', DB::raw('SUM(produksi.total_produksi) as total'))
        ->groupBy('komoditas.id', 'komoditas.nama')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

    // DATA PETA GIS - SEMUA DATA
    $demplotsMap = Demplot::with(['petani', 'komoditas'])
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get()
        ->map(function($demplot) {
            return [
                'id' => $demplot->id,
                'nama_lahan' => $demplot->nama_lahan,
                'latitude' => (float)$demplot->latitude,
                'longitude' => (float)$demplot->longitude,
                'luas_lahan' => $demplot->luas_lahan,
                'status' => $demplot->status,
                'petani' => [
                    'nama' => $demplot->petani->nama ?? 'Tidak diketahui',
                    'nik' => $demplot->petani->nik ?? '-',
                ],
                'komoditas' => [
                    'nama' => $demplot->komoditas->nama ?? 'Tidak diketahui',
                    'id' => $demplot->komoditas->id ?? null,
                ],
                'komoditas_id' => $demplot->komoditas_id,
            ];
        });

    // DAFTAR KOMODITAS UNTUK FILTER
    $komoditasList = Komoditas::where('aktif', true)->get(['id', 'nama']);

    return view('dashboard.petugas', compact(
        'stats', 
        'komoditasUnggulan', 
        'tahun',
        'demplotsMap',
        'komoditasList'
    ));
}

private function dpdDashboard(Request $request)
{
    $tahun = $request->get('tahun', date('Y'));

    // PERBAIKAN 1: Query untuk trend komoditas yang benar
    $trendKomoditas = DB::table('demplot')
        ->join('komoditas', 'demplot.komoditas_id', '=', 'komoditas.id')
        ->where('komoditas.aktif', true)
        ->whereYear('demplot.created_at', $tahun)
        ->select(
            'komoditas.id',
            'komoditas.nama', 
            DB::raw('COUNT(demplot.id) as total_demplot'),
            DB::raw('SUM(demplot.luas_lahan) as total_luas')
        )
        ->groupBy('komoditas.id', 'komoditas.nama')
        ->orderByDesc('total_demplot')
        ->get();

    // PERBAIKAN 2: Jika data kosong, ambil semua komoditas aktif
    if ($trendKomoditas->isEmpty()) {
        $trendKomoditas = Komoditas::where('aktif', true)
            ->get()
            ->map(function($komoditas) {
                return (object)[
                    'id' => $komoditas->id,
                    'nama' => $komoditas->nama,
                    'total_demplot' => 0,
                    'total_luas' => 0
                ];
            });
    }

    // PERBAIKAN 3: Data sebaran demplot dengan relasi yang benar
    $sebaranDemplot = Demplot::with([
            'provinsi', 
            'kabupaten', 
            'kecamatan', 
            'desa', 
            'komoditas'
        ])
        ->whereYear('created_at', $tahun)
        ->get();

    $stats = [
        'total_produksi' => Produksi::whereYear('tanggal_input', $tahun)->sum('total_produksi'),
        'total_demplot' => Demplot::where('status', 'aktif')->count(),
        'total_komoditas_unggulan' => Komoditas::where('status_unggulan', true)->count(),
        'total_komoditas' => Komoditas::where('aktif', true)->count(), // Tambahan
    ];

    return view('dashboard.dpd', compact('trendKomoditas', 'sebaranDemplot', 'stats', 'tahun'));
}

 // app/Http/Controllers/DashboardController.php
private function poktanDashboard(Request $request)
{
    $user = auth()->user();
    
    if (!$user->isPoktan() || !$user->poktan_id) {
        abort(403, 'Akses ditolak. Hanya poktan yang dapat mengakses dashboard ini.');
    }

    $poktanId = $user->poktan_id;
    $currentYear = date('Y');

    try {
        // Stats dengan eager loading dan optimasi query
        $stats = [
            'total_anggota' => Petani::where('poktan_id', $poktanId)
                                ->where('aktif', true)
                                ->count(),
            
            'total_demplot' => Demplot::whereHas('petani', function ($query) use ($poktanId) {
                                $query->where('poktan_id', $poktanId);
                            })->count(),
            
            'luas_lahan_total' => Petani::where('poktan_id', $poktanId)
                                  ->where('aktif', true)
                                  ->sum('luas_lahan_garap') ?? 0,
            
            'total_produksi' => Produksi::where('tahun', $currentYear)
            ->whereHas('demplot.petani', function ($query) use ($poktanId) {
                $query->where('poktan_id', $poktanId);
            })->sum('total_produksi') ?? 0,
        ];

        // Produksi per komoditas
        $produksiPoktan = DB::table('produksi')
            ->join('demplot', 'produksi.demplot_id', '=', 'demplot.id')
            ->join('petani', 'demplot.petani_id', '=', 'petani.id')
            ->join('komoditas', 'produksi.komoditas_id', '=', 'komoditas.id')
            ->where('petani.poktan_id', $poktanId)
            ->whereYear('produksi.tanggal_input', $currentYear)
            ->where('petani.aktif', true)
            ->select(
                'komoditas.id',
                'komoditas.nama', 
                'komoditas.satuan',
                DB::raw('SUM(produksi.total_produksi) as total')
            )
            ->groupBy('komoditas.id', 'komoditas.nama', 'komoditas.satuan')
            ->orderBy('total', 'desc')
            ->get();

        // Data tambahan untuk dashboard
        $recentProduksi = Produksi::with(['komoditas', 'demplot.petani'])
            ->whereHas('demplot.petani', function ($query) use ($poktanId) {
                $query->where('poktan_id', $poktanId);
            })
            ->whereYear('tanggal_input', $currentYear)
            ->orderBy('tanggal_input', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.poktan', compact(
            'stats', 
            'produksiPoktan', 
            'recentProduksi'
        ));

    } catch (\Exception $e) {
        \Log::error('Error in poktanDashboard: ' . $e->getMessage());
        
        // Fallback data jika ada error
        $stats = [
            'total_anggota' => 0,
            'total_demplot' => 0,
            'luas_lahan_total' => 0,
            'total_produksi' => 0,
        ];
        
        $produksiPoktan = collect();
        $recentProduksi = collect();

        return view('dashboard.poktan', compact(
            'stats', 
            'produksiPoktan', 
            'recentProduksi'
        ));
    }
}
    /**
     * Build wilayah query berdasarkan user
     */
    private function buildWilayahQuery($user)
    {
        return function ($query) use ($user) {
            if ($user->desa_id) {
                $query->where('desa_id', $user->desa_id);
            } elseif ($user->kecamatan_id) {
                $query->where('kecamatan_id', $user->kecamatan_id);
            } elseif ($user->kabupaten_id) {
                $query->where('kabupaten_id', $user->kabupaten_id);
            } elseif ($user->provinsi_id) {
                $query->where('provinsi_id', $user->provinsi_id);
            }
        };
    }
}