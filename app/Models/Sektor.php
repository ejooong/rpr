<?php
// app/Models/Sektor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sektor extends Model
{
    // HAPUS SoftDeletes karena kolom deleted_at tidak ada di database
    // use SoftDeletes;

    protected $table = 'sektor';

    protected $fillable = ['kode', 'nama', 'deskripsi', 'aktif'];

    protected $casts = [
        'aktif' => 'boolean'
    ];

    public function komoditas(): HasMany
    {
        return $this->hasMany(Komoditas::class);
    }

    // Scope untuk sektor aktif
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    // Accessor untuk jumlah komoditas
    public function getJumlahKomoditasAttribute()
    {
        return $this->komoditas()->count();
    }
}