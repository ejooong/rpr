<?php

namespace App\Http\Controllers;

use App\Models\Komoditas;
use App\Models\Sektor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KomoditasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Komoditas::with('sektor');

        // FITUR SEARCH BARU
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%')
                  ->orWhere('satuan', 'like', '%' . $searchTerm . '%')
                  ->orWhere('deskripsi', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('sektor', function($q) use ($searchTerm) {
                      $q->where('nama', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Filter berdasarkan status aktif
        if ($request->has('status') && $request->status !== '') {
            $query->where('aktif', $request->status == '1');
        }

        // Filter berdasarkan status unggulan
        if ($request->has('unggulan') && $request->unggulan !== '') {
            $query->where('status_unggulan', $request->unggulan == '1');
        }

        // Filter berdasarkan sektor
        if ($request->has('sektor_id') && $request->sektor_id) {
            $query->where('sektor_id', $request->sektor_id);
        }

        $komoditas = $query->orderBy('nama')->paginate(10);
        
        $sektors = Sektor::aktif()->orderBy('nama')->get();
            
        return view('komoditas.index', compact('komoditas', 'sektors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sektors = Sektor::aktif()->orderBy('nama')->get();
        return view('komoditas.create', compact('sektors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sektor_id' => 'required|exists:sektor,id',
            'nama' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'status_unggulan' => 'boolean',
            'ikon' => 'nullable|string|max:255',
            'warna_chart' => 'nullable|string|max:7',
            'aktif' => 'boolean'
        ]);

        Komoditas::create($validated);

        return redirect()->route('komoditas.index')
            ->with('success', 'Komoditas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $komoditas = Komoditas::with(['sektor', 'demplot', 'produksi'])
            ->findOrFail($id);

        return view('komoditas.show', compact('komoditas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $komoditas = Komoditas::findOrFail($id);
        $sektors = Sektor::aktif()->orderBy('nama')->get();

        return view('komoditas.edit', compact('komoditas', 'sektors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $komoditas = Komoditas::findOrFail($id);

        $validated = $request->validate([
            'sektor_id' => 'required|exists:sektor,id',
            'nama' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'status_unggulan' => 'boolean',
            'ikon' => 'nullable|string|max:255',
            'warna_chart' => 'nullable|string|max:7',
            'aktif' => 'boolean'
        ]);

        $komoditas->update($validated);

        return redirect()->route('komoditas.index')
            ->with('success', 'Komoditas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $komoditas = Komoditas::findOrFail($id);
        
        // Cek apakah komoditas digunakan di data lain
        if ($komoditas->demplot()->count() > 0 || $komoditas->produksi()->count() > 0) {
            return redirect()->route('komoditas.index')
                ->with('error', 'Tidak dapat menghapus komoditas yang masih digunakan di data demplot atau produksi.');
        }

        $komoditas->delete();

        return redirect()->route('komoditas.index')
            ->with('success', 'Komoditas berhasil dihapus.');
    }

    /**
     * API untuk select2
     */
    public function apiIndex(Request $request)
    {
        $komoditas = Komoditas::aktif()
            ->when($request->has('q'), function($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->q . '%');
            })
            ->when($request->has('sektor_id'), function($query) use ($request) {
                $query->where('sektor_id', $request->sektor_id);
            })
            ->orderBy('nama')
            ->get(['id', 'nama', 'satuan', 'sektor_id']);

        return response()->json($komoditas);
    }
}