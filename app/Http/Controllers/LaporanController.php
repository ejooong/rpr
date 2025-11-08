<?php
// app/Http/Controllers/LaporanController.php
namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\Komoditas;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function trenKomoditas(Request $request)
    {
        $tahunAwal = $request->get('tahun_awal', date('Y') - 5);
        $tahunAkhir = $request->get('tahun_akhir', date('Y'));
        $komoditasId = $request->get('komoditas_id');
        $wilayahId = $request->get('wilayah_id');

        $query = Produksi::with(['komoditas'])
            ->whereBetween('tahun', [$tahunAwal, $tahunAkhir]);

        // Filter komoditas
        if ($komoditasId) {
            $query->where('komoditas_id', $komoditasId);
        }

        // Filter wilayah
        if ($wilayahId) {
            $query->whereHas('demplot.wilayah', function ($q) use ($wilayahId) {
                $wilayahIds = $this->getWilayahChildren($wilayahId);
                $q->whereIn('id', $wilayahIds);
            });
        }

        $trenData = $query->select(
            'tahun',
            'komoditas_id',
            DB::raw('SUM(total_produksi) as total_produksi'),
            DB::raw('AVG(produktivitas) as rata_produktivitas')
        )
            ->groupBy('tahun', 'komoditas_id')
            ->orderBy('tahun')
            ->get()
            ->groupBy('tahun');

        $komoditas = Komoditas::where('aktif', true)->get();
        $wilayah = Wilayah::where('level', 'provinsi')->get();

        return view('laporan.tren-komoditas', compact('trenData', 'komoditas', 'wilayah', 'tahunAwal', 'tahunAkhir'));
    }

    public function komoditasUnggulan(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $wilayahId = $request->get('wilayah_id');

        $query = Produksi::with(['komoditas.sektor'])
            ->where('tahun', $tahun)
            ->whereHas('komoditas', function ($q) {
                $q->where('status_unggulan', true);
            });

        // Filter wilayah
        if ($wilayahId) {
            $query->whereHas('demplot.wilayah', function ($q) use ($wilayahId) {
                $wilayahIds = $this->getWilayahChildren($wilayahId);
                $q->whereIn('id', $wilayahIds);
            });
        }

        $unggulanData = $query->select(
            'komoditas_id',
            DB::raw('SUM(total_produksi) as total_produksi'),
            DB::raw('SUM(luas_panen) as total_luas'),
            DB::raw('AVG(produktivitas) as rata_produktivitas')
        )
            ->groupBy('komoditas_id')
            ->orderByDesc('total_produksi')
            ->get();

        $wilayah = Wilayah::where('level', 'provinsi')->get();

        return view('laporan.komoditas-unggulan', compact('unggulanData', 'wilayah', 'tahun'));
    }

    public function produksiPerSektor(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $wilayahId = $request->get('wilayah_id');

        $query = DB::table('produksi')
            ->join('komoditas', 'produksi.komoditas_id', '=', 'komoditas.id')
            ->join('sektor', 'komoditas.sektor_id', '=', 'sektor.id')
            ->where('produksi.tahun', $tahun);

        // Filter wilayah
        if ($wilayahId) {
            $query->join('demplot', 'produksi.demplot_id', '=', 'demplot.id')
                ->whereIn('demplot.wilayah_id', $this->getWilayahChildren($wilayahId));
        }

        $sektorData = $query->select(
            'sektor.nama as sektor',
            DB::raw('SUM(produksi.total_produksi) as total_produksi'),
            DB::raw('COUNT(DISTINCT produksi.demplot_id) as jumlah_demplot')
        )
            ->groupBy('sektor.id', 'sektor.nama')
            ->get();

        $wilayah = Wilayah::where('level', 'provinsi')->get();

        return view('laporan.produksi-sektor', compact('sektorData', 'wilayah', 'tahun'));
    }
}
