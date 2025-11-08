<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama', 'email', 'password', 'role', 'poktan_id', // TAMBAH poktan_id DI SINI
        'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id',
        'aktif', 'last_login'
    ];

    // Juga tambahkan relationship poktan
    public function poktan(): BelongsTo
    {
        return $this->belongsTo(Poktan::class);
    }

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime',
        ];
    }

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

    // Helper methods
    public function getWilayahKerjaAttribute()
    {
        if ($this->desa) return $this->desa->nama;
        if ($this->kecamatan) return $this->kecamatan->nama;
        if ($this->kabupaten) return $this->kabupaten->nama;
        if ($this->provinsi) return $this->provinsi->nama;
        return 'Nasional';
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isPetugas(): bool { return $this->role === 'petugas'; }
    public function isDPD(): bool { return $this->role === 'dpd'; }
    public function isPoktan(): bool { return $this->role === 'poktan'; }
}