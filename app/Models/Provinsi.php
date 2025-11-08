<?php
// app/Models/Provinsi.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Provinsi extends Model
{
    protected $table = 'provinsis';
    protected $fillable = ['kode', 'nama', 'latitude', 'longitude', 'aktif'];

    public function kabupatens(): HasMany
    {
        return $this->hasMany(Kabupaten::class);
    }

    // Fix: Use HasManyThrough for kecamatans
    public function kecamatans(): HasManyThrough
    {
        return $this->hasManyThrough(
            Kecamatan::class, 
            Kabupaten::class,
            'provinsi_id', // Foreign key on kabupatens table
            'kabupaten_id', // Foreign key on kecamatans table
            'id', // Local key on provinsis table
            'id' // Local key on kabupatens table
        );
    }

    // Fix: Use HasManyThrough for desas
    public function desas(): HasManyThrough
    {
        return $this->hasManyThrough(
            Desa::class,
            Kecamatan::class,
            'kabupaten_id', // Foreign key on kecamatans table
            'kecamatan_id', // Foreign key on desas table
            'id', // Local key on provinsis table
            'id' // Local key on kecamatans table
        )->through('kabupaten'); // Add this to go through kabupaten relationship
    }

    // Alternative method for desas if the above doesn't work
    public function allDesas()
    {
        return Desa::whereHas('kecamatan.kabupaten', function($query) {
            $query->where('provinsi_id', $this->id);
        });
    }

    public function poktan()
    {
        return $this->hasManyThrough(Poktan::class, Kabupaten::class);
    }

    public function demplot()
    {
        return $this->hasManyThrough(Demplot::class, Kabupaten::class);
    }

    // Add scope for counting
    public function scopeWithAllCounts($query)
    {
        return $query->withCount(['kabupatens', 'kecamatans', 'desas']);
    }
}