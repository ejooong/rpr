<?php
// app/Http/Controllers/PetaniController.php
namespace App\Http\Controllers;

use App\Models\Petani;
use App\Models\Poktan;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Http\Request;

class PetaniController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // TAMBAH eager loading ke komoditasUtama poktan
        $query = Petani::with(['poktan', 'poktan.provinsi', 'poktan.kabupaten', 'poktan.kecamatan', 'poktan.desa', 'poktan.komoditasUtama']);

        // Filter berdasarkan role
        if ($user->isPoktan()) {
            $query->where('poktan_id', $user->poktan_id);
        }

        // Filter pencarian - HAPUS TELEPON KARENA KOLOM TIDAK ADA
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->has('aktif') && $request->aktif !== '') {
            $query->where('aktif', $request->aktif);
        }

        // Filter poktan
        if ($request->has('poktan_id') && $request->poktan_id) {
            $query->where('poktan_id', $request->poktan_id);
        }

        // Filter komoditas utama
        if ($request->has('komoditas_id') && $request->komoditas_id) {
            $query->whereHas('poktan', function($q) use ($request) {
                $q->where('komoditas_utama_id', $request->komoditas_id);
            });
        }

        $petani = $query->latest()->paginate(20);
        $poktan = Poktan::where('aktif', true)->get();
        $komoditas = \App\Models\Komoditas::where('aktif', true)->get();

        return view('petani.index', compact('petani', 'poktan', 'komoditas'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        
        // Jika ada parameter poktan_id, gunakan itu
        $poktanId = $request->get('poktan_id');
        
        $poktanQuery = Poktan::where('aktif', true);
        
        // Jika user adalah poktan, hanya tampilkan poktan miliknya
        if ($user->isPoktan()) {
            $poktanQuery->where('id', $user->poktan_id);
            $poktanId = $user->poktan_id;
        }
        
        $poktans = $poktanQuery->get();
        $selectedPoktan = $poktanId ? Poktan::find($poktanId) : null;

        // TAMBAH komoditas untuk form
        $komoditas = \App\Models\Komoditas::where('aktif', true)->get();

        return view('petani.create', compact('poktans', 'selectedPoktan', 'komoditas'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'poktan_id' => 'required|exists:poktan,id',
        'nama' => 'required|string|max:255',
        'nik' => 'required|string|max:16|unique:petani,nik',
        'jenis_kelamin' => 'required|in:L,P', // UBAH jadi required
        'tanggal_lahir' => 'required|date', // UBAH jadi required
        'pendidikan' => 'required|string|max:255', // UBAH jadi required
        'no_hp' => 'required|string|max:15',
        'luas_lahan_garap' => 'required|numeric|min:0', // UBAH jadi required
        'status_lahan' => 'required|string|max:255', // UBAH jadi required
        'alamat' => 'required|string',
        'latitude' => 'nullable|string|max:255',
        'longitude' => 'nullable|string|max:255',
        'aktif' => 'boolean',
    ], [
        // Custom error messages
        'tanggal_lahir.required' => 'Tanggal lahir tidak boleh kosong.',
        'jenis_kelamin.required' => 'Jenis kelamin tidak boleh kosong.',
        'pendidikan.required' => 'Pendidikan tidak boleh kosong.',
        'luas_lahan_garap.required' => 'Luas lahan garap tidak boleh kosong.',
        'status_lahan.required' => 'Status lahan tidak boleh kosong.',
        'no_hp.required' => 'Nomor HP tidak boleh kosong.',
        'alamat.required' => 'Alamat tidak boleh kosong.',
    ]);

    $validated['aktif'] = $validated['aktif'] ?? true;

    Petani::create($validated);

    // Update jumlah anggota poktan
    $this->updateJumlahAnggota($validated['poktan_id']);

    return redirect()->route('petani.index')
        ->with('success', 'Data petani berhasil ditambahkan.');
}

    public function show(Petani $petani)
    {
        // HAPUS: $this->authorize('view', $petani);
        // GANTI dengan pengecekan manual berdasarkan role user
        
        $user = auth()->user();
        
        // Jika user adalah poktan, pastikan petani ini milik poktan user
        if ($user->isPoktan() && $petani->poktan_id != $user->poktan_id) {
            abort(403, 'Anda tidak memiliki akses ke data petani ini.');
        }

        $petani->load(['poktan', 'poktan.provinsi', 'poktan.kabupaten', 'poktan.kecamatan', 'poktan.desa']);

        return view('petani.show', compact('petani'));
    }

    public function edit(Petani $petani)
    {
        // HAPUS: $this->authorize('update', $petani);
        // GANTI dengan pengecekan manual berdasarkan role user
        
        $user = auth()->user();
        
        // Jika user adalah poktan, pastikan petani ini milik poktan user
        if ($user->isPoktan() && $petani->poktan_id != $user->poktan_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data petani ini.');
        }

        $poktanQuery = Poktan::where('aktif', true);

        if ($user->isPoktan()) {
            $poktanQuery->where('id', $user->poktan_id);
        }

        $poktans = $poktanQuery->get();
        
        // TAMBAH komoditas untuk form edit
        $komoditas = \App\Models\Komoditas::where('aktif', true)->get();

        return view('petani.edit', compact('petani', 'poktans', 'komoditas'));
    }

   public function update(Request $request, Petani $petani)
{
    $user = auth()->user();
    
    if ($user->isPoktan() && $petani->poktan_id != $user->poktan_id) {
        abort(403, 'Anda tidak memiliki akses untuk mengupdate data petani ini.');
    }

    $oldPoktanId = $petani->poktan_id;
    
    $validated = $request->validate([
        'poktan_id' => 'required|exists:poktan,id',
        'nama' => 'required|string|max:255',
        'nik' => 'required|string|max:16|unique:petani,nik,' . $petani->id,
        'jenis_kelamin' => 'required|in:L,P', // UBAH jadi required
        'tanggal_lahir' => 'required|date', // UBAH jadi required
        'pendidikan' => 'required|string|max:255', // UBAH jadi required
        'no_hp' => 'required|string|max:15',
        'luas_lahan_garap' => 'required|numeric|min:0', // UBAH jadi required
        'status_lahan' => 'required|string|max:255', // UBAH jadi required
        'alamat' => 'required|string',
        'latitude' => 'nullable|string|max:255',
        'longitude' => 'nullable|string|max:255',
        'aktif' => 'boolean',
    ], [
        // Custom error messages
        'tanggal_lahir.required' => 'Tanggal lahir tidak boleh kosong.',
        'jenis_kelamin.required' => 'Jenis kelamin tidak boleh kosong.',
        'pendidikan.required' => 'Pendidikan tidak boleh kosong.',
        'luas_lahan_garap.required' => 'Luas lahan garap tidak boleh kosong.',
        'status_lahan.required' => 'Status lahan tidak boleh kosong.',
        'no_hp.required' => 'Nomor HP tidak boleh kosong.',
        'alamat.required' => 'Alamat tidak boleh kosong.',
    ]);

    $validated['aktif'] = $validated['aktif'] ?? true;

    $petani->update($validated);

    // Update jumlah anggota jika pindah poktan
    if ($oldPoktanId != $validated['poktan_id']) {
        $this->updateJumlahAnggota($oldPoktanId);
        $this->updateJumlahAnggota($validated['poktan_id']);
    } else {
        $this->updateJumlahAnggota($validated['poktan_id']);
    }

    return redirect()->route('petani.index')
        ->with('success', 'Data petani berhasil diperbarui.');
}
    public function destroy(Petani $petani)
    {
        // HAPUS: $this->authorize('delete', $petani);
        // GANTI dengan pengecekan manual berdasarkan role user
        
        $user = auth()->user();
        
        // Jika user adalah poktan, pastikan petani ini milik poktan user
        if ($user->isPoktan() && $petani->poktan_id != $user->poktan_id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data petani ini.');
        }

        $poktanId = $petani->poktan_id;
        $petani->delete();

        // Update jumlah anggota poktan
        $this->updateJumlahAnggota($poktanId);

        return redirect()->route('petani.index')
            ->with('success', 'Data petani berhasil dihapus.');
    }

    /**
     * Update jumlah anggota poktan
     */
    private function updateJumlahAnggota($poktanId)
    {
        $jumlah = Petani::where('poktan_id', $poktanId)
            ->where('aktif', true)
            ->count();

        Poktan::where('id', $poktanId)->update(['jumlah_anggota' => $jumlah]);
    }

    /**
     * Halaman anggota untuk user poktan (tanpa parameter)
     */
    public function anggotaPoktan(Request $request)
    {
        $user = auth()->user();
        
        // Pastikan user adalah poktan
        if (!$user->isPoktan()) {
            abort(403, 'Hanya poktan yang dapat mengakses halaman ini.');
        }
        
        $poktanId = $user->poktan_id;
        $poktan = Poktan::findOrFail($poktanId);
        
        // TAMBAH eager loading ke komoditasUtama
        $query = $poktan->petani()->with(['poktan.komoditasUtama']);

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('komoditas', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'aktif') {
                $query->where('aktif', true);
            } elseif ($request->status == 'tidak_aktif') {
                $query->where('aktif', false);
            }
        }

        $petanis = $query->latest()->paginate(15);

        // Hitung statistik
        $totalAnggota = $poktan->petani()->count();
        $anggotaAktif = $poktan->petani()->where('aktif', true)->count();
        $anggotaNonAktif = $poktan->petani()->where('aktif', false)->count();
        $totalLahan = $poktan->petani()->sum('luas_lahan');

        return view('poktan.anggota', compact(
            'poktan', 
            'petanis', 
            'totalAnggota', 
            'anggotaAktif', 
            'anggotaNonAktif', 
            'totalLahan'
        ));
    }
}