<?php
// app/Http/Controllers/KecamatanController.php
namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Kabupaten;
use App\Models\Provinsi;
use Illuminate\Http\Request;

class KecamatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kecamatan::with(['kabupaten', 'kabupaten.provinsi']);

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('kode', 'like', "%{$request->search}%");
        }

        if ($request->has('kabupaten_id') && $request->kabupaten_id != '') {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }

        if ($request->has('provinsi_id') && $request->provinsi_id != '') {
            $query->whereHas('kabupaten', function($q) use ($request) {
                $q->where('provinsi_id', $request->provinsi_id);
            });
        }

        $kecamatans = $query->latest()->paginate(10);
        $provinsis = Provinsi::all();
        $kabupatens = Kabupaten::all();

        return view('kecamatan.index', compact('kecamatans', 'provinsis', 'kabupatens'));
    }

    public function create()
    {
        $provinsis = Provinsi::all();
        $kabupatens = Kabupaten::all();
        return view('kecamatan.create', compact('provinsis', 'kabupatens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:kecamatans,kode',
            'nama' => 'required|string|max:255',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Kecamatan::create($validated);

        return redirect()->route('kecamatan.index')
            ->with('success', 'Data kecamatan berhasil ditambahkan.');
    }

    public function show(Kecamatan $kecamatan)
    {
        $kecamatan->load(['kabupaten', 'kabupaten.provinsi', 'desas']);
        return view('kecamatan.show', compact('kecamatan'));
    }

    public function edit(Kecamatan $kecamatan)
    {
        $provinsis = Provinsi::all();
        $kabupatens = Kabupaten::all();
        return view('kecamatan.edit', compact('kecamatan', 'provinsis', 'kabupatens'));
    }

    public function update(Request $request, Kecamatan $kecamatan)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:kecamatans,kode,' . $kecamatan->id,
            'nama' => 'required|string|max:255',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $kecamatan->update($validated);

        return redirect()->route('kecamatan.index')
            ->with('success', 'Data kecamatan berhasil diperbarui.');
    }

    public function destroy(Kecamatan $kecamatan)
    {
        if ($kecamatan->desas()->count() > 0) {
            return redirect()->route('kecamatan.index')
                ->with('error', 'Tidak dapat menghapus kecamatan karena masih memiliki data desa.');
        }

        $kecamatan->delete();

        return redirect()->route('kecamatan.index')
            ->with('success', 'Data kecamatan berhasil dihapus.');
    }

    public function apiByKabupaten($kabupatenId)
    {
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupatenId)
            ->select('id', 'nama', 'kode')
            ->get();

        return response()->json($kecamatans);
    }
}