<?php
// database/seeders/PoktanSeeder.php
namespace Database\Seeders;

use App\Models\Poktan;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PoktanSeeder extends Seeder
{
    public function run(): void
    {
        // Non-aktifkan foreign key constraints sementara
        Schema::disableForeignKeyConstraints();
        Poktan::truncate();
        Schema::enableForeignKeyConstraints();

        // Pastikan data wilayah sudah ada
        $jabar = Provinsi::where('kode', '32')->first();
        $kabBandung = Kabupaten::where('kode', '32.04')->first();
        $soreang = Kecamatan::where('kode', '32.04.01')->first();
        $desaSoreang = Desa::where('kode', '32.04.01.2001')->first();
        $desaPamekaran = Desa::where('kode', '32.04.01.2002')->first();

        // Cek apakah data wilayah ditemukan
        if (!$jabar || !$kabBandung || !$soreang || !$desaSoreang || !$desaPamekaran) {
            $this->command->error('Data wilayah tidak ditemukan! Jalankan WilayahSeeder terlebih dahulu.');
            return;
        }

        // Poktan 1
        Poktan::create([
            'nama' => 'Poktan Soreang Maju',
            'ketua' => 'Budi Santoso',
            'alamat' => 'Jl. Raya Soreang No. 123',
            'tanggal_terbentuk' => '2020-01-15',
            'provinsi_id' => $jabar->id,
            'kabupaten_id' => $kabBandung->id,
            'kecamatan_id' => $soreang->id,
            'desa_id' => $desaSoreang->id,
            'jumlah_anggota' => 25,
            'aktif' => true,
        ]);

        // Poktan 2
        Poktan::create([
            'nama' => 'Poktan Pamekaran Sejahtera',
            'ketua' => 'Siti Rahayu',
            'alamat' => 'Jl. Desa Pamekaran No. 45',
            'tanggal_terbentuk' => '2019-03-20',
            'provinsi_id' => $jabar->id,
            'kabupaten_id' => $kabBandung->id,
            'kecamatan_id' => $soreang->id,
            'desa_id' => $desaPamekaran->id,
            'jumlah_anggota' => 18,
            'aktif' => true,
        ]);

        $this->command->info('Data poktan berhasil diseed!');
        $this->command->info('Total poktan: ' . Poktan::count());
    }
}