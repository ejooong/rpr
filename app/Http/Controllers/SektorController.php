<?php

namespace App\Http\Controllers;

use App\Models\Sektor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SektorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sektors = Sektor::withCount('komoditas')
            ->orderBy('kode')
            ->paginate(10);
            
        return view('sektor.index', compact('sektors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sektor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:sektor,kode',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean'
        ]);

        Sektor::create($validated);

        return redirect()->route('sektor.index')
            ->with('success', 'Sektor berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sektor = Sektor::with(['komoditas' => function($query) {
            $query->orderBy('nama');
        }])->findOrFail($id);

        return view('sektor.show', compact('sektor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sektor = Sektor::findOrFail($id);
        return view('sektor.edit', compact('sektor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sektor = Sektor::findOrFail($id);

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:10',
                Rule::unique('sektor')->ignore($sektor->id)
            ],
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean'
        ]);

        $sektor->update($validated);

        return redirect()->route('sektor.index')
            ->with('success', 'Sektor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sektor = Sektor::findOrFail($id);
        
        // Cek apakah sektor memiliki komoditas
        if ($sektor->komoditas()->count() > 0) {
            return redirect()->route('sektor.index')
                ->with('error', 'Tidak dapat menghapus sektor yang masih memiliki komoditas.');
        }

        $sektor->delete();

        return redirect()->route('sektor.index')
            ->with('success', 'Sektor berhasil dihapus.');
    }

    /**
     * API untuk select2
     */
    public function apiIndex(Request $request)
    {
        $sektors = Sektor::aktif()
            ->when($request->has('q'), function($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->q . '%');
            })
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode']);

        return response()->json($sektors);
    }
}