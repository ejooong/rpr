<?php
// app/Http/Controllers/ProvinsiController.php
namespace App\Http\Controllers;

use App\Models\Provinsi;
use Illuminate\Http\Request;

class ProvinsiController extends Controller
{
    public function index(Request $request)
    {
        $query = Provinsi::withCount('kabupatens');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $provinsis = $query->latest()->paginate(10);
        return view('provinsi.index', compact('provinsis'));
    }

    public function create()
    {
        return view('provinsi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:provinsis,kode',
            'nama' => 'required|string|max:255|unique:provinsis,nama',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $provinsi = Provinsi::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Provinsi berhasil ditambahkan',
                'redirect' => route('provinsi.index')
            ]);
        }

        return redirect()->route('provinsi.index')
            ->with('success', 'Data provinsi berhasil ditambahkan.');
    }

    public function show(Provinsi $provinsi)
    {
        // Method 1: Using manual counting (more reliable)
        $kabupatensCount = $provinsi->kabupatens()->count();
        
        // Count kecamatans through kabupatens
        $kecamatansCount = \App\Models\Kecamatan::whereHas('kabupaten', function($query) use ($provinsi) {
            $query->where('provinsi_id', $provinsi->id);
        })->count();
        
        // Count desas through kecamatans and kabupatens
        $desasCount = \App\Models\Desa::whereHas('kecamatan.kabupaten', function($query) use ($provinsi) {
            $query->where('provinsi_id', $provinsi->id);
        })->count();

        return view('provinsi.show', compact('provinsi', 'kabupatensCount', 'kecamatansCount', 'desasCount'));
    }

    // Alternative method for show() if you want to use relationships
    public function showAlternative(Provinsi $provinsi)
    {
        // Load counts using the fixed relationships
        $provinsi->loadCount([
            'kabupatens',
            'kecamatans as kecamatans_count',
            'desas as desas_count'
        ]);

        return view('provinsi.show', compact('provinsi'));
    }

    public function edit(Provinsi $provinsi)
    {
        return view('provinsi.edit', compact('provinsi'));
    }

    public function update(Request $request, Provinsi $provinsi)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:provinsis,kode,' . $provinsi->id,
            'nama' => 'required|string|max:255|unique:provinsis,nama,' . $provinsi->id,
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $provinsi->update($validated);

        return redirect()->route('provinsi.index')
            ->with('success', 'Data provinsi berhasil diperbarui.');
    }

   public function destroy(Provinsi $provinsi)
{
    try {
        // Mulai database transaction
        \DB::beginTransaction();

        // Hapus semua desa yang terkait melalui kecamatan -> kabupaten -> provinsi
        \App\Models\Desa::whereHas('kecamatan.kabupaten', function($query) use ($provinsi) {
            $query->where('provinsi_id', $provinsi->id);
        })->delete();

        // Hapus semua kecamatan yang terkait melalui kabupaten -> provinsi
        \App\Models\Kecamatan::whereHas('kabupaten', function($query) use ($provinsi) {
            $query->where('provinsi_id', $provinsi->id);
        })->delete();

        // Hapus semua kabupaten yang terkait dengan provinsi
        $provinsi->kabupatens()->delete();

        // Hapus provinsi itu sendiri
        $provinsi->delete();

        // Commit transaction
        \DB::commit();

        return redirect()->route('provinsi.index')
            ->with('success', 'Data provinsi beserta semua kabupaten, kecamatan, dan desa terkait berhasil dihapus.');

    } catch (\Exception $e) {
        // Rollback transaction jika ada error
        \DB::rollBack();

        return redirect()->route('provinsi.index')
            ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
    }
}

    public function apiIndex(Request $request)
    {
        $query = Provinsi::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        $provinsis = $query->select('id', 'nama', 'kode')
            ->limit(10)
            ->get();

        return response()->json($provinsis);
    }
}