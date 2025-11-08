<?php
// app/Models/Demplot.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demplot extends Model
{
    protected $table = 'demplot';

    protected $fillable = [
        'petani_id', 
        'komoditas_id',
        'provinsi_id', 
        'kabupaten_id', 
        'kecamatan_id', 
        'desa_id',
        'nama_lahan', 
        'alamat', // SUDAH ADA
        'luas_lahan', 
        'status',
        'tahun',
        'latitude', 
        'longitude',
        'foto_lahan',
        'keterangan'
    ];

    protected $casts = [
        'tahun' => 'integer',
        'luas_lahan' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

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

    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class);
    }

    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class);
    }

    public function produksi(): HasMany
    {
        return $this->hasMany(Produksi::class);
    }
}