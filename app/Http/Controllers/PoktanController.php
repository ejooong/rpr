<?php
// app/Http/Controllers/PoktanController.php
namespace App\Http\Controllers;

use App\Models\Poktan;
use App\Models\Petani;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Komoditas; // TAMBAH IMPORT INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoktanController extends Controller
{
    public function index(Request $request)
    {
        $query = Poktan::with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'komoditasUtama']); // TAMBAH komoditasUtama

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('ketua', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan wilayah
        if ($request->has('provinsi_id') && $request->provinsi_id != '') {
            $query->where('provinsi_id', $request->provinsi_id);
        }
        if ($request->has('kabupaten_id') && $request->kabupaten_id != '') {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }
        if ($request->has('kecamatan_id') && $request->kecamatan_id != '') {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }
        if ($request->has('desa_id') && $request->desa_id != '') {
            $query->where('desa_id', $request->desa_id);
        }

        // TAMBAH FILTER KOMPODITAS UTAMA
        if ($request->has('komoditas_utama_id') && $request->komoditas_utama_id != '') {
            $query->where('komoditas_utama_id', $request->komoditas_utama_id);
        }

        $poktans = $query->latest()->paginate(10);
        $provinsis = Provinsi::all();
        $komoditas = Komoditas::where('aktif', true)->get(); // TAMBAH INI

        return view('poktan.index', compact('poktans', 'provinsis', 'komoditas')); // TAMBAH komoditas
    }

    public function create()
    {
        $provinsis = Provinsi::all();
        $komoditas = Komoditas::where('aktif', true)->get(); // TAMBAH INI
        return view('poktan.create', compact('provinsis', 'komoditas')); // TAMBAH komoditas
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'ketua' => 'required|string|max:255',
            'alamat' => 'required|string',
            'tanggal_terbentuk' => 'required|date',
            'jumlah_anggota' => 'required|integer|min:1',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'desa_id' => 'required|exists:desas,id',
            'komoditas_utama_id' => 'nullable|exists:komoditas,id', // TAMBAH VALIDASI
            'aktif' => 'boolean',
        ]);

        Poktan::create($validated);

        return redirect()->route('poktan.index')
            ->with('success', 'Data POKTAN berhasil ditambahkan.');
    }

    public function show(Poktan $poktan)
    {
        $poktan->load(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'petani', 'komoditasUtama']); // TAMBAH komoditasUtama
        return view('poktan.show', compact('poktan'));
    }

    public function edit(Poktan $poktan)
    {
        $provinsis = Provinsi::all();
        $kabupatens = Kabupaten::where('provinsi_id', $poktan->provinsi_id)->get();
        $kecamatans = Kecamatan::where('kabupaten_id', $poktan->kabupaten_id)->get();
        $desas = Desa::where('kecamatan_id', $poktan->kecamatan_id)->get();
        $komoditas = Komoditas::where('aktif', true)->get(); // TAMBAH INI

        return view('poktan.edit', compact('poktan', 'provinsis', 'kabupatens', 'kecamatans', 'desas', 'komoditas')); // TAMBAH komoditas
    }

    public function update(Request $request, Poktan $poktan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'ketua' => 'required|string|max:255',
            'alamat' => 'required|string',
            'tanggal_terbentuk' => 'required|date',
            'jumlah_anggota' => 'required|integer|min:1',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'desa_id' => 'required|exists:desas,id',
            'komoditas_utama_id' => 'nullable|exists:komoditas,id', // TAMBAH VALIDASI
            'aktif' => 'boolean',
        ]);

        $poktan->update($validated);

        return redirect()->route('poktan.index')
            ->with('success', 'Data POKTAN berhasil diperbarui.');
    }

    // METHOD-METHOD BERIKUTNYA TETAP SAMA TANPA PERUBAHAN
    public function destroy(Poktan $poktan)
    {
        // Cek apakah poktan memiliki petani
        if ($poktan->petani()->count() > 0) {
            return redirect()->route('poktan.index')
                ->with('error', 'Tidak dapat menghapus POKTAN karena masih memiliki data petani.');
        }

        $poktan->delete();

        return redirect()->route('poktan.index')
            ->with('success', 'Data POKTAN berhasil dihapus.');
    }

    public function apiIndex(Request $request)
    {
        $query = Poktan::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        if ($request->has('kecamatan_id') && $request->kecamatan_id != '') {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }

        $poktans = $query->select('id', 'nama', 'kecamatan_id')
            ->with('kecamatan')
            ->limit(10)
            ->get();

        return response()->json($poktans);
    }

    public function profil()
    {
        $user = auth()->user();
        $poktan = Poktan::findOrFail($user->poktan_id);
        
        return view('poktan.profil', compact('poktan'));
    }

    public function updateProfil(Request $request)
    {
        $user = auth()->user();
        $poktan = Poktan::findOrFail($user->poktan_id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'ketua' => 'required|string|max:255',
            'alamat' => 'required|string',
            'jumlah_anggota' => 'required|integer|min:1',
        ]);

        $poktan->update($validated);

        return redirect()->route('poktan.profil')
            ->with('success', 'Profil POKTAN berhasil diperbarui.');
    }

    // Method untuk melihat anggota poktan tertentu (dengan parameter)
    public function anggota(Request $request, Poktan $poktan)
    {
        // Authorization - hanya admin, petugas yang berwenang, atau poktan yang bersangkutan
        $user = auth()->user();
        
        // Cek akses berdasarkan role
        if ($user->isPoktan() && $user->poktan_id != $poktan->id) {
            abort(403, 'Anda tidak memiliki akses ke anggota poktan ini.');
        }
        
        // Jika petugas, cek apakah memiliki akses ke wilayah poktan ini
        if ($user->isPetugas() && $user->wilayah_id) {
            // Logika pengecekan akses wilayah untuk petugas
            // Sesuaikan dengan struktur wilayah Anda
        }

        $query = $poktan->petani();

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
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

        // Hitung statistik - PERBAIKAN: gunakan luas_lahan_garap
        $totalAnggota = $poktan->petani()->count();
        $anggotaAktif = $poktan->petani()->where('aktif', true)->count();
        $anggotaNonAktif = $poktan->petani()->where('aktif', false)->count();
        $totalLahan = $poktan->petani()->sum('luas_lahan_garap');

        return view('poktan.anggota', compact(
            'poktan', 
            'petanis', 
            'totalAnggota', 
            'anggotaAktif', 
            'anggotaNonAktif', 
            'totalLahan'
        ));
    }

    // Method untuk poktan melihat anggota mereka sendiri (tanpa parameter)
    public function anggotaSaya(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isPoktan()) {
            return back()->with('error', 'Hanya poktan yang dapat mengakses halaman ini.')
                    ->with('auto_modal', true);
        }
        
        if (!$user->poktan_id) {
            abort(404, 'Data poktan tidak ditemukan.');
        }
        
        $poktan = Poktan::findOrFail($user->poktan_id);
        
        // Reuse logic dari method anggota()
        $query = $poktan->petani();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'aktif') {
                $query->where('aktif', true);
            } elseif ($request->status == 'tidak_aktif') {
                $query->where('aktif', false);
            }
        }

        $petanis = $query->latest()->paginate(15);

        // Hitung statistik - PERBAIKAN: gunakan luas_lahan_garap
        $totalAnggota = $poktan->petani()->count();
        $anggotaAktif = $poktan->petani()->where('aktif', true)->count();
        $anggotaNonAktif = $poktan->petani()->where('aktif', false)->count();
        $totalLahan = $poktan->petani()->sum('luas_lahan_garap');

        return view('poktan.anggota', compact(
            'poktan',
            'petanis',
            'totalAnggota',
            'anggotaAktif',
            'anggotaNonAktif',
            'totalLahan'
        ));
    }

    /**
     * Show form untuk tambah anggota (hanya untuk poktan sendiri)
     */
    public function createAnggota()
    {
        $user = auth()->user();
        
        if (!$user->isPoktan() || !$user->poktan_id) {
            return back()->with('error', 'Hanya poktan yang dapat mengakses halaman ini.')
                    ->with('auto_modal', true);
        }
        
        $poktan = Poktan::findOrFail($user->poktan_id);
        
        return view('poktan.anggota-create', compact('poktan'));
    }

    /**
     * Store anggota baru (hanya untuk poktan sendiri)
     */
    public function storeAnggota(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isPoktan() || !$user->poktan_id) {
            return back()->with('error', 'Hanya poktan yang dapat mengakses halaman ini.')
                    ->with('auto_modal', true);
        }
        
        $validated = $request->validate([
            'nik' => 'required|string|max:16|unique:petani,nik',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan' => 'required|string|max:50',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:15',
            'luas_lahan_garap' => 'required|numeric|min:0',
            'status_lahan' => 'required|string|max:50',
            'latitude' => 'nullable|string|max:20',
            'longitude' => 'nullable|string|max:20',
            'aktif' => 'boolean',
        ]);

        // Otomatis set poktan_id dari user yang login
        $validated['poktan_id'] = $user->poktan_id;
        $validated['aktif'] = $validated['aktif'] ?? true;

        Petani::create($validated);

        // Update jumlah anggota poktan
        $this->updateJumlahAnggota($user->poktan_id);

        return redirect()->route('poktan.anggota')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    /**
     * Show form edit anggota (hanya anggota dari poktan sendiri)
     */
    public function editAnggota(Petani $petani)
    {
        $user = auth()->user();
        
        if (!$user->isPoktan() || !$user->poktan_id) {
           return back()->with('error', 'Hanya poktan yang dapat mengakses halaman ini.')
                    ->with('auto_modal', true);
        }
        
        // Pastikan petani adalah anggota dari poktan user
        if ($petani->poktan_id != $user->poktan_id) {
            return back()->with('error', 'Hanya poktan yang dapat mengakses halaman ini.')
                    ->with('auto_modal', true);
        }
        
        $poktan = Poktan::findOrFail($user->poktan_id);
        
        return view('poktan.anggota-edit', compact('poktan', 'petani'));
    }

    /**
     * Update anggota (hanya anggota dari poktan sendiri)
     */
    public function updateAnggota(Request $request, Petani $petani)
    {
        $user = auth()->user();
        
        if (!$user->isPoktan() || !$user->poktan_id) {
            return back()->with('error', 'Hanya poktan yang dapat mengakses halaman ini.')
                    ->with('auto_modal', true);
        }
        
        // Pastikan petani adalah anggota dari poktan user
        if ($petani->poktan_id != $user->poktan_id) {
            return back()->with('error', 'Hanya poktan yang dapat mengakses halaman ini.')
                    ->with('auto_modal', true);
        }
        
        $validated = $request->validate([
            'nik' => 'required|string|max:16|unique:petani,nik,' . $petani->id,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan' => 'required|string|max:50',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:15',
            'luas_lahan_garap' => 'required|numeric|min:0',
            'status_lahan' => 'required|string|max:50',
            'latitude' => 'nullable|string|max:20',
            'longitude' => 'nullable|string|max:20',
            'aktif' => 'boolean',
        ]);

        $validated['aktif'] = $validated['aktif'] ?? true;

        $petani->update($validated);

        return redirect()->route('poktan.anggota')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Hapus anggota (hanya anggota dari poktan sendiri)
     */
    public function destroyAnggota(Petani $petani)
    {
        $user = auth()->user();
        
        if (!$user->isPoktan() || !$user->poktan_id) {
            return back()->with('error', 'Hanya poktan yang dapat mengakses halaman ini.')
                    ->with('auto_modal', true);
        }
        
        // Pastikan petani adalah anggota dari poktan user
        if ($petani->poktan_id != $user->poktan_id) {
            return back()->with('error', 'Hanya poktan yang dapat mengakses halaman ini.')
                    ->with('auto_modal', true);
        }
        
        $poktanId = $petani->poktan_id;
        $petani->delete();

        // Update jumlah anggota poktan
        $this->updateJumlahAnggota($poktanId);

        return redirect()->route('poktan.anggota')
            ->with('success', 'Anggota berhasil dihapus.');
    }

    /**
     * Helper method untuk update jumlah anggota poktan
     */
    private function updateJumlahAnggota($poktanId)
    {
        $jumlah = Petani::where('poktan_id', $poktanId)
            ->where('aktif', true)
            ->count();

        Poktan::where('id', $poktanId)->update(['jumlah_anggota' => $jumlah]);
    }
}