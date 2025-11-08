<?php
// database/seeders/UserSeeder.php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing users except maybe your own
        User::where('email', '!=', 'your-email@example.com')->delete();

        // Get sample wilayah data
        $jabar = Provinsi::where('kode', '32')->first();
        $kabBandung = Kabupaten::where('kode', '32.04')->first();
        $kotaBandung = Kabupaten::where('kode', '32.73')->first();
        $soreang = Kecamatan::where('kode', '32.04.01')->first();
        $desaSoreang = Desa::where('kode', '32.04.01.2001')->first();

        // Admin Nasional (no specific wilayah)
        User::create([
            'nama' => 'Admin Nasional',
            'email' => 'admin@rpr-nasdem.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'aktif' => true,
        ]);

        // Petugas Provinsi Jawa Barat
        User::create([
            'nama' => 'Petugas Jawa Barat',
            'email' => 'petugas@jabar.rpr-nasdem.id',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'provinsi_id' => $jabar->id,
            'aktif' => true,
        ]);

        // Petugas Kabupaten Bandung
        User::create([
            'nama' => 'Petugas Kab Bandung', 
            'email' => 'petugas@kab-bandung.rpr-nasdem.id',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'provinsi_id' => $jabar->id,
            'kabupaten_id' => $kabBandung->id,
            'aktif' => true,
        ]);

        // Ketua DPD
        User::create([
            'nama' => 'Ketua DPD NasDem',
            'email' => 'dpd@nasdem.rpr-nasdem.id',
            'password' => Hash::make('password123'),
            'role' => 'dpd',
            'aktif' => true,
        ]);

        // Poktan di Desa Soreang
        User::create([
            'nama' => 'Ketua Poktan Soreang',
            'email' => 'poktan@soreang.rpr-nasdem.id',
            'password' => Hash::make('password123'),
            'role' => 'poktan',
            'provinsi_id' => $jabar->id,
            'kabupaten_id' => $kabBandung->id,
            'kecamatan_id' => $soreang->id,
            'desa_id' => $desaSoreang->id,
            'aktif' => true,
        ]);

        $this->command->info('Data user berhasil diseed!');
        $this->command->info('Total users: ' . User::count());
    }
}