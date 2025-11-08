<?php
// app/Models/Komoditas.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Komoditas extends Model
{
    // HAPUS SoftDeletes karena kolom deleted_at tidak ada di database
    // use SoftDeletes;

    protected $table = 'komoditas';

    protected $fillable = [
        'sektor_id', 'nama', 'satuan', 'deskripsi', 
        'status_unggulan', 'ikon', 'warna_chart', 'aktif'
    ];

    protected $casts = [
        'status_unggulan' => 'boolean',
        'aktif' => 'boolean'
    ];

    public function sektor(): BelongsTo
    {
        return $this->belongsTo(Sektor::class);
    }

    public function demplot(): HasMany
    {
        return $this->hasMany(Demplot::class);
    }

    public function produksi(): HasMany
    {
        return $this->hasMany(Produksi::class);
    }

    // Scope untuk komoditas aktif
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    // Scope untuk komoditas unggulan
    public function scopeUnggulan($query)
    {
        return $query->where('status_unggulan', true);
    }
}