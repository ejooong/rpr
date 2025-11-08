<?php
// app/Http/Controllers/DemplotController.php
namespace App\Http\Controllers;

use App\Models\Demplot;
use App\Models\Petani;
use App\Models\Komoditas;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Http\Requests\StoreDemplotRequest;
use App\Http\Requests\UpdateDemplotRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DemplotController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Demplot::with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'petani', 'komoditas']);

        // HAPUS FILTER BERDASARKAN WILAYAH_ID - tidak ada lagi
        if ($user->isPoktan()) {
            $query->whereHas('petani.poktan', function ($q) use ($user) {
                $q->where('id', $user->poktan_id);
            });
        }

        // Filter status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter komoditas
        if ($request->has('komoditas_id')) {
            $query->where('komoditas_id', $request->komoditas_id);
        }

        $demplots = $query->latest()->paginate(20);
        $komoditas = Komoditas::where('aktif', true)->get();

        return view('demplot.index', compact('demplots', 'komoditas'));
    }

    public function create()
    {
        $user = auth()->user();

        // Wilayah berdasarkan role
        $provinsis = Provinsi::where('aktif', true)->get();
        $kabupatens = Kabupaten::where('aktif', true)->get();
        $kecamatans = Kecamatan::where('aktif', true)->get();
        $desas = Desa::where('aktif', true)->get();

        // Petani berdasarkan role
        $petaniQuery = Petani::where('aktif', true);
        if ($user->isPoktan()) {
            $petaniQuery->where('poktan_id', $user->poktan_id);
        }
        $petani = $petaniQuery->get();

        $komoditas = Komoditas::where('aktif', true)->get();

        return view('demplot.create', compact('provinsis', 'kabupatens', 'kecamatans', 'desas', 'petani', 'komoditas'));
    }

    public function store(StoreDemplotRequest $request)
    {
        $data = $request->validated();

        // Upload foto lahan
        if ($request->hasFile('foto_lahan')) {
            $data['foto_lahan'] = $request->file('foto_lahan')->store('demplot', 'public');
        }

        Demplot::create($data);

        return redirect()->route('demplot.index')
            ->with('success', 'Data demplot berhasil ditambahkan.');
    }

    public function show(Demplot $demplot)
    {
        $this->authorize('view', $demplot);
        $demplot->load(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'petani.poktan', 'komoditas.sektor', 'produksi']);

        return view('demplot.show', compact('demplot'));
    }

    public function edit(Demplot $demplot)
    {
        $this->authorize('update', $demplot);

        $user = auth()->user();

        // Wilayah untuk edit
        $provinsis = Provinsi::where('aktif', true)->get();
        $kabupatens = Kabupaten::where('aktif', true)->get();
        $kecamatans = Kecamatan::where('aktif', true)->get();
        $desas = Desa::where('aktif', true)->get();

        $petaniQuery = Petani::where('aktif', true);
        if ($user->isPoktan()) {
            $petaniQuery->where('poktan_id', $user->poktan_id);
        }
        $petani = $petaniQuery->get();

        $komoditas = Komoditas::where('aktif', true)->get();

        return view('demplot.edit', compact('demplot', 'provinsis', 'kabupatens', 'kecamatans', 'desas', 'petani', 'komoditas'));
    }

    public function update(UpdateDemplotRequest $request, Demplot $demplot)
    {
        $this->authorize('update', $demplot);

        $data = $request->validated();

        // Update foto lahan
        if ($request->hasFile('foto_lahan')) {
            // Hapus foto lama
            if ($demplot->foto_lahan) {
                Storage::disk('public')->delete($demplot->foto_lahan);
            }
            $data['foto_lahan'] = $request->file('foto_lahan')->store('demplot', 'public');
        }

        $demplot->update($data);

        return redirect()->route('demplot.index')
            ->with('success', 'Data demplot berhasil diperbarui.');
    }

    public function destroy(Demplot $demplot)
    {
        $this->authorize('delete', $demplot);

        // Hapus foto
        if ($demplot->foto_lahan) {
            Storage::disk('public')->delete($demplot->foto_lahan);
        }

        $demplot->delete();

        return redirect()->route('demplot.index')
            ->with('success', 'Data demplot berhasil dihapus.');
    }

    /**
     * API untuk data GIS - HAPUS FILTER WILAYAH_ID
     */
    public function apiDemplot(Request $request)
    {
        $query = Demplot::with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'komoditas.sektor']);

        // HAPUS: Filter wilayah_id tidak ada lagi
        // if ($request->has('wilayah_id')) {
        //     $query->where('wilayah_id', $request->wilayah_id);
        // }

        if ($request->has('komoditas_id')) {
            $query->where('komoditas_id', $request->komoditas_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $demplots = $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($demplot) {
                return [
                    'id' => $demplot->id,
                    'nama_lahan' => $demplot->nama_lahan,
                    'latitude' => $demplot->latitude,
                    'longitude' => $demplot->longitude,
                    'luas_lahan' => $demplot->luas_lahan,
                    'status' => $demplot->status,
                    'tahun' => $demplot->tahun,
                    'komoditas' => $demplot->komoditas->nama,
                    'sektor' => $demplot->komoditas->sektor->nama,
                    'wilayah' => $demplot->provinsi ? $demplot->provinsi->nama : '-',
                    'warna' => $demplot->komoditas->warna_chart ?? '#4CAF50',
                    'popup_content' => view('components.popup-demplot', compact('demplot'))->render()
                ];
            });

        return response()->json($demplots);
    }

    // Di DemplotController.php - method demplotPoktan()
    public function demplotPoktan(Request $request)
    {
        $user = auth()->user();
        
        // Query khusus untuk poktan
        $query = Demplot::with(['petani', 'komoditas.sektor', 'provinsi', 'kabupaten', 'kecamatan', 'desa'])
            ->whereHas('petani', function ($q) use ($user) {
                $q->where('poktan_id', $user->poktan_id);
            });

        // Filter status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter sektor
        if ($request->has('sektor_id') && $request->sektor_id != '') {
            $query->whereHas('komoditas', function ($q) use ($request) {
                $q->where('sektor_id', $request->sektor_id);
            });
        }

        // Filter komoditas
        if ($request->has('komoditas_id') && $request->komoditas_id != '') {
            $query->where('komoditas_id', $request->komoditas_id);
        }

        $demplots = $query->orderBy('created_at', 'desc')
                         ->paginate(20);

        $sektor = \App\Models\Sektor::where('aktif', true)->get();
        $komoditas = Komoditas::where('aktif', true)->get();
        
        // Jika sektor dipilih, filter komoditas berdasarkan sektor
        if ($request->has('sektor_id') && $request->sektor_id != '') {
            $komoditas = Komoditas::where('sektor_id', $request->sektor_id)
                                ->where('aktif', true)
                                ->get();
        }
        
        // Hitung statistik
        $stats = [
            'total_demplot' => $query->count(),
            'total_luas_lahan' => $query->sum('luas_lahan'),
            'demplot_aktif' => $query->where('status', 'aktif')->count(),
            'demplot_selesai' => $query->where('status', 'selesai')->count(),
        ];

        return view('poktan.demplot', compact('demplots', 'sektor', 'komoditas', 'stats'));
    }

    // HAPUS METHOD getWilayahChildren() - tidak diperlukan lagi
}