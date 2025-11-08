<?php
// app/Http/Controllers/WilayahController.php
namespace App\Http\Controllers;

use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function create()
    {
        return view('wilayah.create');
    }

    public function store(Request $request)
    {
        // Debug data yang dikirim
        \Log::info('=== WILAYAH STORE DEBUG ===');
        \Log::info('Level: ' . $request->level);
        \Log::info('All Data:', $request->all());

        $level = $request->level;

        if ($level === 'provinsi') {
            $validated = $request->validate([
                'kode_provinsi' => 'required|string|max:10|unique:provinsis,kode',
                'nama_provinsi' => 'required|string|max:255|unique:provinsis,nama',
                'latitude_provinsi' => 'nullable|numeric',
                'longitude_provinsi' => 'nullable|numeric',
            ]);

            Provinsi::create([
                'kode' => $validated['kode_provinsi'],
                'nama' => $validated['nama_provinsi'],
                'latitude' => $validated['latitude_provinsi'],
                'longitude' => $validated['longitude_provinsi'],
            ]);

            return redirect()->route('provinsi.index')
                ->with('success', 'Data provinsi berhasil ditambahkan.');

        } elseif ($level === 'kabupaten') {
            $validated = $request->validate([
                'provinsi_id_kabupaten' => 'required|exists:provinsis,id',
                'kode_kabupaten' => 'required|string|max:20|unique:kabupatens,kode',
                'nama_kabupaten' => 'required|string|max:255',
                'tipe_kabupaten' => 'required|in:kabupaten,kota',
                'latitude_kabupaten' => 'nullable|numeric',
                'longitude_kabupaten' => 'nullable|numeric',
            ]);

            Kabupaten::create([
                'kode' => $validated['kode_kabupaten'],
                'nama' => $validated['nama_kabupaten'],
                'provinsi_id' => $validated['provinsi_id_kabupaten'],
                'tipe' => $validated['tipe_kabupaten'],
                'latitude' => $validated['latitude_kabupaten'],
                'longitude' => $validated['longitude_kabupaten'],
            ]);

            return redirect()->route('kabupaten.index')
                ->with('success', 'Data kabupaten/kota berhasil ditambahkan.');

        } elseif ($level === 'kecamatan') {
            // TAMBAHKAN VALIDASI UNTUK KECAMATAN
            $validated = $request->validate([
                'provinsi_id_kecamatan' => 'required|exists:provinsis,id',
                'kabupaten_id_kecamatan' => 'required|exists:kabupatens,id',
                'kode_kecamatan' => 'required|string|max:20|unique:kecamatans,kode',
                'nama_kecamatan' => 'required|string|max:255',
                'latitude_kecamatan' => 'nullable|numeric',
                'longitude_kecamatan' => 'nullable|numeric',
            ]);

            Kecamatan::create([
                'kode' => $validated['kode_kecamatan'],
                'nama' => $validated['nama_kecamatan'],
                'kabupaten_id' => $validated['kabupaten_id_kecamatan'],
                'latitude' => $validated['latitude_kecamatan'],
                'longitude' => $validated['longitude_kecamatan'],
            ]);

            return redirect()->route('kecamatan.index')
                ->with('success', 'Data kecamatan berhasil ditambahkan.');

        } elseif ($level === 'desa') {
            // TAMBAHKAN VALIDASI UNTUK DESA
            $validated = $request->validate([
                'provinsi_id_desa' => 'required|exists:provinsis,id',
                'kabupaten_id_desa' => 'required|exists:kabupatens,id',
                'kecamatan_id_desa' => 'required|exists:kecamatans,id',
                'kode_desa' => 'required|string|max:20|unique:desas,kode',
                'nama_desa' => 'required|string|max:255',
                'tipe_desa' => 'required|in:desa,kelurahan',
                'latitude_desa' => 'nullable|numeric',
                'longitude_desa' => 'nullable|numeric',
            ]);

            Desa::create([
                'kode' => $validated['kode_desa'],
                'nama' => $validated['nama_desa'],
                'kecamatan_id' => $validated['kecamatan_id_desa'],
                'tipe' => $validated['tipe_desa'],
                'latitude' => $validated['latitude_desa'],
                'longitude' => $validated['longitude_desa'],
            ]);

            return redirect()->route('desa.index')
                ->with('success', 'Data desa/kelurahan berhasil ditambahkan.');
        }

        return redirect()->back()
            ->with('error', 'Level wilayah tidak valid.')
            ->withInput();
    }
}