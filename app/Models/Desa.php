<?php
// app/Models/Desa.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Desa extends Model
{
    protected $table = 'desas';
    protected $fillable = ['kode', 'nama', 'kecamatan_id', 'tipe', 'latitude', 'longitude', 'aktif'];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    // Hapus method kabupaten() dan provinsi() yang menggunakan through()
    // karena through() tidak tersedia di Laravel untuk belongsTo
    
    public function poktan(): HasMany
    {
        return $this->hasMany(Poktan::class);
    }

    public function demplot(): HasMany
    {
        return $this->hasMany(Demplot::class);
    }

    public function petani(): HasMany
    {
        return $this->hasMany(Petani::class);
    }

    // Accessor untuk mendapatkan kabupaten melalui kecamatan
    public function getKabupatenAttribute()
    {
        return $this->kecamatan->kabupaten;
    }

    // Accessor untuk mendapatkan provinsi melalui kecamatan->kabupaten
    public function getProvinsiAttribute()
    {
        return $this->kecamatan->kabupaten->provinsi;
    }
}