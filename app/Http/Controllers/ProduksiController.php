<?php
// app/Http/Controllers/ProduksiController.php

namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\Demplot;
use App\Models\Komoditas;
use App\Http\Requests\StoreProduksiRequest;
use App\Http\Requests\UpdateProduksiRequest;
use Illuminate\Http\Request;

class ProduksiController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Produksi::class);

        $user = auth()->user();
        $query = Produksi::with(['demplot.petani', 'komoditas', 'petugas']);

        // Filter berdasarkan role
        if ($user->isPoktan()) {
            $query->whereHas('demplot.petani', function ($q) use ($user) {
                $q->where('poktan_id', $user->poktan_id);
            });
        }

        // Filter tahun
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun', $request->tahun);
        }

        // Filter bulan
        if ($request->has('bulan') && $request->bulan != '') {
            $query->where('bulan', $request->bulan);
        }

        // Filter komoditas
        if ($request->has('komoditas_id') && $request->komoditas_id != '') {
            $query->where('komoditas_id', $request->komoditas_id);
        }

        $produksi = $query->orderBy('tahun', 'desc')
                         ->orderBy('bulan', 'desc')
                         ->orderBy('tanggal_input', 'desc')
                         ->paginate(20);
        
        $komoditas = Komoditas::where('aktif', true)->get();
        $tahunList = Produksi::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('produksi.index', compact('produksi', 'komoditas', 'tahunList'));
    }

    public function create()
    {
        $this->authorize('create', Produksi::class);

        $user = auth()->user();
        
        // Query demplot berdasarkan role
        $demplotQuery = Demplot::with(['petani', 'komoditas'])->where('status', 'aktif');
        if ($user->isPoktan()) {
            $demplotQuery->whereHas('petani', function ($q) use ($user) {
                $q->where('poktan_id', $user->poktan_id);
            });
        }
        $demplots = $demplotQuery->get();

        $komoditas = Komoditas::where('aktif', true)->get();

        return view('produksi.create', compact('demplots', 'komoditas'));
    }

 public function store(StoreProduksiRequest $request)
{
    $this->authorize('create', Produksi::class);

    $data = $request->validated();

    // TAMBAHKAN PETUGAS_ID SECARA MANUAL
    $data['petugas_id'] = auth()->id();

    // Hitung produktivitas
    if ($data['luas_panen'] > 0) {
        $data['produktivitas'] = $data['total_produksi'] / $data['luas_panen'];
    } else {
        $data['produktivitas'] = 0;
    }

    // Debug untuk memastikan data lengkap
    \Log::info('Data yang akan disimpan:', $data);

    Produksi::create($data);

    return redirect()->route('produksi.index')
        ->with('success', 'Data produksi berhasil ditambahkan.');
}

    public function show(Produksi $produksi)
    {
        $this->authorize('view', $produksi);

    // Load relasi dengan benar
    $produksi->load([
        'demplot.petani.poktan', 
        'demplot.provinsi',
        'demplot.kabupaten', 
        'demplot.kecamatan',
        'demplot.desa',
        'komoditas.sektor', 
        'petugas'
    ]);

    return view('produksi.show', compact('produksi'));
}

    public function edit(Produksi $produksi)
    {
        $this->authorize('update', $produksi);

        $user = auth()->user();
        
        $demplotQuery = Demplot::with(['petani', 'komoditas'])->where('status', 'aktif');
        if ($user->isPoktan()) {
            $demplotQuery->whereHas('petani', function ($q) use ($user) {
                $q->where('poktan_id', $user->poktan_id);
            });
        }
        $demplots = $demplotQuery->get();

        $komoditas = Komoditas::where('aktif', true)->get();

        return view('produksi.edit', compact('produksi', 'demplots', 'komoditas'));
    }

    public function update(UpdateProduksiRequest $request, Produksi $produksi)
    {
        $this->authorize('update', $produksi);

        $data = $request->validated();

        // Hitung produktivitas
        if ($data['luas_panen'] > 0) {
            $data['produktivitas'] = $data['total_produksi'] / $data['luas_panen'];
        } else {
            $data['produktivitas'] = 0;
        }

        $produksi->update($data);

        return redirect()->route('produksi.index')
            ->with('success', 'Data produksi berhasil diperbarui.');
    }

    public function destroy(Produksi $produksi)
    {
        $this->authorize('delete', $produksi);

        $produksi->delete();

        return redirect()->route('produksi.index')
            ->with('success', 'Data produksi berhasil dihapus.');
    }

    /**
     * Method khusus untuk poktan
     */
    public function produksiPoktan(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isPoktan()) {
            abort(403, 'Akses ditolak.');
        }

        $query = Produksi::with(['demplot.petani', 'komoditas'])
            ->whereHas('demplot.petani', function ($q) use ($user) {
                $q->where('poktan_id', $user->poktan_id);
            });

        // Filter tahun
        if ($request->has('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter komoditas
        if ($request->has('komoditas_id')) {
            $query->where('komoditas_id', $request->komoditas_id);
        }

        $produksi = $query->orderBy('tahun', 'desc')
                         ->orderBy('bulan', 'desc')
                         ->paginate(20);
        
        $komoditas = Komoditas::where('aktif', true)->get();
        $tahunList = Produksi::whereHas('demplot.petani', function ($q) use ($user) {
                $q->where('poktan_id', $user->poktan_id);
            })
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('poktan.produksi', compact('produksi', 'komoditas', 'tahunList'));
    }
}