<?php
// app/Models/Wilayah.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    protected $table = 'wilayah';
    protected $fillable = ['kode', 'nama', 'level', 'parent_id', 'latitude', 'longitude', 'aktif'];

    public function children(): HasMany
    {
        return $this->hasMany(Wilayah::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Wilayah::class, 'parent_id');
    }

    public function poktan(): HasMany
    {
        return $this->hasMany(Poktan::class);
    }

    public function demplot(): HasMany
    {
        return $this->hasMany(Demplot::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
