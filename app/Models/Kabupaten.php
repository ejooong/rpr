<?php
// app/Models/Kabupaten.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kabupaten extends Model
{
    protected $table = 'kabupatens';
    protected $fillable = ['kode', 'nama', 'provinsi_id', 'tipe', 'latitude', 'longitude', 'aktif'];

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kecamatans(): HasMany
    {
        return $this->hasMany(Kecamatan::class);
    }

    public function desas()
    {
        return Desa::whereHas('kecamatan', function($query) {
            $query->where('kabupaten_id', $this->id);
        });
    }

    public function poktan(): HasMany
    {
        return $this->hasMany(Poktan::class);
    }

    public function demplot(): HasMany
    {
        return $this->hasMany(Demplot::class);
    }
}