<?php
// app/Http/Controllers/KabupatenController.php
namespace App\Http\Controllers;

use App\Models\Kabupaten;
use App\Models\Provinsi;
use Illuminate\Http\Request;

class KabupatenController extends Controller
{
    public function index(Request $request)
    {
        $query = Kabupaten::with('provinsi');

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('kode', 'like', "%{$request->search}%");
        }

        if ($request->has('provinsi_id') && $request->provinsi_id != '') {
            $query->where('provinsi_id', $request->provinsi_id);
        }

        $kabupatens = $query->latest()->paginate(10);
        $provinsis = Provinsi::all();

        return view('kabupaten.index', compact('kabupatens', 'provinsis'));
    }

    public function create()
    {
        $provinsis = Provinsi::all();
        return view('kabupaten.create', compact('provinsis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:kabupatens,kode',
            'nama' => 'required|string|max:255',
            'provinsi_id' => 'required|exists:provinsis,id',
            'tipe' => 'required|in:kabupaten,kota',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Kabupaten::create($validated);

        return redirect()->route('kabupaten.index')
            ->with('success', 'Data kabupaten/kota berhasil ditambahkan.');
    }

public function show(Kabupaten $kabupaten)
{
    $kabupaten->load([
        'provinsi', 
        'kecamatans' => function($query) {
            $query->withCount('desas');
        }, 
        'kecamatans.desas'
    ]);
    return view('kabupaten.show', compact('kabupaten'));
}

    public function edit(Kabupaten $kabupaten)
    {
        $provinsis = Provinsi::all();
        return view('kabupaten.edit', compact('kabupaten', 'provinsis'));
    }

    public function update(Request $request, Kabupaten $kabupaten)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:kabupatens,kode,' . $kabupaten->id,
            'nama' => 'required|string|max:255',
            'provinsi_id' => 'required|exists:provinsis,id',
            'tipe' => 'required|in:kabupaten,kota',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $kabupaten->update($validated);

        return redirect()->route('kabupaten.index')
            ->with('success', 'Data kabupaten/kota berhasil diperbarui.');
    }

    public function destroy(Kabupaten $kabupaten)
    {
        if ($kabupaten->kecamatans()->count() > 0) {
            return redirect()->route('kabupaten.index')
                ->with('error', 'Tidak dapat menghapus kabupaten/kota karena masih memiliki data kecamatan.');
        }

        $kabupaten->delete();

        return redirect()->route('kabupaten.index')
            ->with('success', 'Data kabupaten/kota berhasil dihapus.');
    }

    public function apiByProvinsi($provinsiId)
    {
        $kabupatens = Kabupaten::where('provinsi_id', $provinsiId)
            ->select('id', 'nama', 'kode', 'tipe')
            ->get();

        return response()->json($kabupatens);
    }
}