<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Poktan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['provinsi', 'kabupaten', 'kecamatan', 'desa'])
            ->orderBy('role')
            ->orderBy('nama')
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
     public function create()
    {
        $provinsis = Provinsi::where('aktif', true)->get();
        $kabupatens = Kabupaten::where('aktif', true)->get();
        $kecamatans = Kecamatan::where('aktif', true)->get();
        $desas = Desa::where('aktif', true)->get();
        $poktans = Poktan::where('aktif', true) // AMBIL DATA POKTAN YANG AKTIF
            ->with(['provinsi', 'kabupaten', 'kecamatan', 'desa'])
            ->orderBy('nama')
            ->get();
        
        $roles = [
            'admin' => 'Administrator',
            'petugas' => 'Petugas Lapangan',
            'dpd' => 'Ketua DPD',
            'poktan' => 'Kelompok Tani'
        ];

        return view('users.create', compact('provinsis', 'kabupatens', 'kecamatans', 'desas', 'poktans', 'roles'));
    }


    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'role' => 'required|in:admin,petugas,dpd,poktan',
        'poktan_id' => 'nullable|required_if:role,poktan|exists:poktan,id', // UBAH KE nullable
        'provinsi_id' => 'nullable|exists:provinsis,id',
        'kabupaten_id' => 'nullable|exists:kabupatens,id',
        'kecamatan_id' => 'nullable|exists:kecamatans,id',
        'desa_id' => 'nullable|exists:desas,id',
        'aktif' => 'required|boolean',
    ]);

    try {
        $userData = [
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'provinsi_id' => $request->provinsi_id,
            'kabupaten_id' => $request->kabupaten_id,
            'kecamatan_id' => $request->kecamatan_id,
            'desa_id' => $request->desa_id,
            'aktif' => $request->aktif ?? true, // DEFAULT KE TRUE JIKA TIDAK DIISI
        ];

        // Tambahkan poktan_id hanya jika role adalah poktan DAN ada nilai
        if ($request->role === 'poktan' && $request->poktan_id) {
            $userData['poktan_id'] = $request->poktan_id;
        }

        User::create($userData);

        return redirect()->route('users.index')
            ->with('success', 'Data pengguna berhasil ditambahkan.');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['provinsi', 'kabupaten', 'kecamatan', 'desa']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $provinsis = Provinsi::where('aktif', true)->get();
        $kabupatens = Kabupaten::where('aktif', true)->get();
        $kecamatans = Kecamatan::where('aktif', true)->get();
        $desas = Desa::where('aktif', true)->get();
        $poktans = Poktan::where('aktif', true) // TAMBAHKAN POKTAN UNTUK EDIT
            ->with(['provinsi', 'kabupaten', 'kecamatan', 'desa'])
            ->orderBy('nama')
            ->get();
        
        $roles = [
            'admin' => 'Administrator',
            'petugas' => 'Petugas Lapangan',
            'dpd' => 'Ketua DPD',
            'poktan' => 'Kelompok Tani'
        ];

        return view('users.edit', compact('user', 'provinsis', 'kabupatens', 'kecamatans', 'desas', 'poktans', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, User $user)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'role' => 'required|in:admin,petugas,dpd,poktan',
        'poktan_id' => 'nullable|required_if:role,poktan|exists:poktan,id', // UBAH KE nullable
        'provinsi_id' => 'nullable|exists:provinsis,id',
        'kabupaten_id' => 'nullable|exists:kabupatens,id',
        'kecamatan_id' => 'nullable|exists:kecamatans,id',
        'desa_id' => 'nullable|exists:desas,id',
        'aktif' => 'required|boolean',
    ]);

    try {
        $data = $request->only([
            'nama', 'email', 'role', 'aktif',
            'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id'
        ]);

        // Update poktan_id hanya jika role adalah poktan
        if ($request->role === 'poktan') {
            $data['poktan_id'] = $request->poktan_id;
        } else {
            $data['poktan_id'] = null; // Set null jika bukan role poktan
        }

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting own account
            if ($user->id === auth()->id()) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus akun sendiri.');
            }

            $user->delete();

            return redirect()->route('users.index')
                ->with('success', 'Data pengguna berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        try {
            // Prevent deactivating own account
            if ($user->id === auth()->id() && !$user->aktif) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
            }

            $user->update(['aktif' => !$user->aktif]);

            $status = $user->aktif ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()
                ->with('success', "Pengguna berhasil $status.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user)
    {
        try {
            $user->update([
                'password' => Hash::make('password123') // Default password
            ]);

            return redirect()->back()
                ->with('success', 'Password berhasil direset ke default (password123).');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}