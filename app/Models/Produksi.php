<?php
// app/Models/Produksi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produksi extends Model
{
    protected $table = 'produksi';

    protected $fillable = [
        'demplot_id',
        'komoditas_id',
        'tahun',
        'bulan',
        'luas_panen',
        'total_produksi',
        'produktivitas',
        'tanggal_input',
        'petugas_id',
        'sumber_data'
    ];

    protected $casts = [
        'tanggal_input' => 'date',
        'luas_panen' => 'decimal:2',
        'total_produksi' => 'decimal:2',
        'produktivitas' => 'decimal:2',
        'tahun' => 'integer',
        'bulan' => 'integer'
    ];
    // Auto-set petugas_id saat create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->petugas_id)) {
                $model->petugas_id = auth()->id();
            }
        });
    }
    public function demplot(): BelongsTo
    {
        return $this->belongsTo(Demplot::class);
    }

    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    // Hitung produktivitas otomatis
    public function calculateProduktivitas()
    {
        if ($this->luas_panen > 0 && $this->total_produksi > 0) {
            return $this->total_produksi / $this->luas_panen;
        }
        return 0;
    }

    // Scope untuk filter berdasarkan tahun
    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    // Scope untuk filter berdasarkan bulan
    public function scopeBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    // Accessor untuk nama bulan
    public function getNamaBulanAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulan[$this->bulan] ?? 'Tidak diketahui';
    }
}