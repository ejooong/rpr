<?php
// app/Models/Kecamatan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    protected $table = 'kecamatans';
    protected $fillable = ['kode', 'nama', 'kabupaten_id', 'latitude', 'longitude', 'aktif'];

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function desas(): HasMany
    {
        return $this->hasMany(Desa::class);
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