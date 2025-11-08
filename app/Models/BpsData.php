<?php
// app/Models/BpsData.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpsData extends Model
{
    use HasFactory;

    protected $table = 'bps_data';
    
    protected $fillable = [
        'tahun',
        'provinsi_id',
        'kabupaten_id',
        'kecamatan_id', // TAMBAH INI
        'sektor_id',
        'komoditas_id',
        'luas_lahan',
        'produksi',
        'produktivitas',
        'status_unggulan',
        'peringkat_wilayah',
        'sumber_data',
        'keterangan'
    ];

    protected $casts = [
        'tahun' => 'integer',
        'luas_lahan' => 'decimal:2',
        'produksi' => 'decimal:2',
        'produktivitas' => 'decimal:2',
        'status_unggulan' => 'boolean',
    ];

    // Relationships
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function kecamatan() // TAMBAH INI
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function sektor()
    {
        return $this->belongsTo(Sektor::class);
    }

    public function komoditas()
    {
        return $this->belongsTo(Komoditas::class);
    }

    // Scope untuk query yang sering digunakan
    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeProvinsi($query, $provinsiId)
    {
        return $query->where('provinsi_id', $provinsiId);
    }

    public function scopeKabupaten($query, $kabupatenId) // TAMBAH INI
    {
        return $query->where('kabupaten_id', $kabupatenId);
    }

    public function scopeKecamatan($query, $kecamatanId) // TAMBAH INI
    {
        return $query->where('kecamatan_id', $kecamatanId);
    }



    public function scopeSektor($query, $sektorId)
    {
        return $query->where('sektor_id', $sektorId);
    }

    // Scope untuk filtering wilayah lengkap - TAMBAH INI
    public function scopeByWilayah($query, $provinsiId = null, $kabupatenId = null, $kecamatanId = null)
    {
        if ($kecamatanId) {
            return $query->where('kecamatan_id', $kecamatanId);
        }
        
        if ($kabupatenId) {
            return $query->where('kabupaten_id', $kabupatenId);
        }
        
        if ($provinsiId) {
            return $query->where('provinsi_id', $provinsiId);
        }
        
        return $query;
    }

    // Hitung produktivitas otomatis - TAMBAH INI
    public function calculateProduktivitas()
    {
        if ($this->luas_lahan && $this->luas_lahan > 0 && $this->produksi) {
            $this->produktivitas = $this->produksi / $this->luas_lahan;
        }
        return $this;
    }

    // Helper method untuk mendapatkan nama wilayah lengkap - TAMBAH INI
    public function getNamaWilayahAttribute()
    {
        if ($this->kecamatan) {
            return $this->kecamatan->nama . ', ' . $this->kabupaten->nama . ', ' . $this->provinsi->nama;
        } elseif ($this->kabupaten) {
            return $this->kabupaten->nama . ', ' . $this->provinsi->nama;
        } else {
            return $this->provinsi->nama;
        }
    }

    // Helper method untuk mendapatkan level wilayah - TAMBAH INI
    public function getLevelWilayahAttribute()
    {
        if ($this->kecamatan_id) {
            return 'Kecamatan';
        } elseif ($this->kabupaten_id) {
            return 'Kabupaten/Kota';
        } else {
            return 'Provinsi';
        }
    }
}