<?php
// app/Models/Poktan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poktan extends Model
{
    // TAMBAHKAN BARIS INI
    protected $table = 'poktan';

    protected $fillable = [
        'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id',
        'nama', 'ketua', 'alamat', 'tanggal_terbentuk', 'jumlah_anggota', 'aktif',
        'komoditas_utama_id' // TAMBAH INI SAJA
    ];

    // TAMBAHKAN APPENDS JIKA INGIN MENGGUNAKAN ACCESSOR
    protected $appends = ['wilayah_lengkap'];

    // RELASI YANG SUDAH ADA - TIDAK DIUBAH
    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function petani(): HasMany
    {
        return $this->hasMany(Petani::class);
    }

    // TAMBAHKAN RELASI BARU UNTUK KOMODITAS UTAMA
    public function komoditasUtama(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class, 'komoditas_utama_id');
    }

    // Helper method untuk mendapatkan wilayah lengkap
    public function getWilayahLengkapAttribute()
    {
        $wilayah = [];
        if ($this->desa) $wilayah[] = $this->desa->nama;
        if ($this->kecamatan) $wilayah[] = $this->kecamatan->nama;
        if ($this->kabupaten) $wilayah[] = $this->kabupaten->nama;
        if ($this->provinsi) $wilayah[] = $this->provinsi->nama;
        
        return implode(', ', array_reverse($wilayah));
    }

    // TAMBAHKAN ACCESSOR UNTUK KOMODITAS UTAMA (OPSIONAL)
    public function getNamaKomoditasUtamaAttribute()
    {
        return $this->komoditasUtama ? $this->komoditasUtama->nama : 'Belum ditentukan';
    }
}