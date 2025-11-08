<?php
// app/Models/Petani.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Petani extends Model
{
    protected $table = 'petani';

    protected $fillable = [
        'poktan_id',
        'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id',
        'nik', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'pendidikan',
        'alamat', 'luas_lahan_garap', 'status_lahan', 'latitude', 'longitude',
        'no_hp', 'aktif'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'aktif' => 'boolean',
        'luas_lahan_garap' => 'decimal:2',
    ];

    // TAMBAHKAN INI untuk set default values
    protected $attributes = [
        'pendidikan' => 'Tidak Sekolah',
        'no_hp' => '-',
        'luas_lahan_garap' => 0,
        'status_lahan' => 'Milik Sendiri',
    ];

    public function poktan(): BelongsTo
    {
        return $this->belongsTo(Poktan::class);
    }

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

    public function demplot(): HasMany
    {
        return $this->hasMany(Demplot::class);
    }

    // Accessor untuk usia
    public function getUsiaAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }

    // Accessor untuk wilayah lengkap
    public function getWilayahLengkapAttribute()
    {
        $wilayah = [];
        if ($this->desa) $wilayah[] = $this->desa->nama;
        if ($this->kecamatan) $wilayah[] = $this->kecamatan->nama;
        if ($this->kabupaten) $wilayah[] = $this->kabupaten->nama;
        if ($this->provinsi) $wilayah[] = $this->provinsi->nama;
        
        return implode(', ', array_reverse($wilayah));
    }
}