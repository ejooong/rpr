<?php
// app/Http/Controllers/DesaController.php
namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Kabupaten;
use App\Models\Provinsi;
use Illuminate\Http\Request;

class DesaController extends Controller
{
    public function index(Request $request)
    {
        $query = Desa::with(['kecamatan', 'kecamatan.kabupaten', 'kecamatan.kabupaten.provinsi']);

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('kode', 'like', "%{$request->search}%");
        }

        if ($request->has('kecamatan_id') && $request->kecamatan_id != '') {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }

        if ($request->has('kabupaten_id') && $request->kabupaten_id != '') {
            $query->whereHas('kecamatan', function($q) use ($request) {
                $q->where('kabupaten_id', $request->kabupaten_id);
            });
        }

        if ($request->has('provinsi_id') && $request->provinsi_id != '') {
            $query->whereHas('kecamatan.kabupaten', function($q) use ($request) {
                $q->where('provinsi_id', $request->provinsi_id);
            });
        }

        $desas = $query->latest()->paginate(10);
        $provinsis = Provinsi::all();
        $kabupatens = Kabupaten::all();
        $kecamatans = Kecamatan::all();

        return view('desa.index', compact('desas', 'provinsis', 'kabupatens', 'kecamatans'));
    }

    public function create()
    {
        $provinsis = Provinsi::all();
        $kabupatens = Kabupaten::all();
        $kecamatans = Kecamatan::all();
        return view('desa.create', compact('provinsis', 'kabupatens', 'kecamatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:desas,kode',
            'nama' => 'required|string|max:255',
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'tipe' => 'required|in:desa,kelurahan',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Desa::create($validated);

        return redirect()->route('desa.index')
            ->with('success', 'Data desa/kelurahan berhasil ditambahkan.');
    }

    public function show(Desa $desa)
    {
        $desa->load(['kecamatan', 'kecamatan.kabupaten', 'kecamatan.kabupaten.provinsi']);
        return view('desa.show', compact('desa'));
    }

    public function edit(Desa $desa)
    {
        $provinsis = Provinsi::all();
        $kabupatens = Kabupaten::all();
        $kecamatans = Kecamatan::all();
        return view('desa.edit', compact('desa', 'provinsis', 'kabupatens', 'kecamatans'));
    }

    public function update(Request $request, Desa $desa)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:desas,kode,' . $desa->id,
            'nama' => 'required|string|max:255',
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'tipe' => 'required|in:desa,kelurahan',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $desa->update($validated);

        return redirect()->route('desa.index')
            ->with('success', 'Data desa/kelurahan berhasil diperbarui.');
    }

    public function destroy(Desa $desa)
    {
        if ($desa->petani()->count() > 0 || $desa->poktan()->count() > 0 || $desa->demplot()->count() > 0) {
            return redirect()->route('desa.index')
                ->with('error', 'Tidak dapat menghapus desa/kelurahan karena masih memiliki data terkait.');
        }

        $desa->delete();

        return redirect()->route('desa.index')
            ->with('success', 'Data desa/kelurahan berhasil dihapus.');
    }

    public function apiByKecamatan($kecamatanId)
    {
        $desas = Desa::where('kecamatan_id', $kecamatanId)
            ->select('id', 'nama', 'kode', 'tipe')
            ->get();

        return response()->json($desas);
    }
}