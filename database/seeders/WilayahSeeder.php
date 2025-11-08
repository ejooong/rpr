<?php
// database/seeders/WilayahSeeder.php
namespace Database\Seeders;

use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Tambahkan ini

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus foreign key constraints sementara
        Schema::disableForeignKeyConstraints();

        // Clear existing data
        Desa::truncate();
        Kecamatan::truncate();
        Kabupaten::truncate();
        Provinsi::truncate();

        // Aktifkan kembali constraints
        Schema::enableForeignKeyConstraints();

        // Reset auto increment
        DB::statement('ALTER TABLE desas AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE kecamatans AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE kabupatens AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE provinsis AUTO_INCREMENT = 1');

        // Provinsi
        $jabar = Provinsi::create([
            'kode' => '32',
            'nama' => 'Jawa Barat',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
        ]);

        $jateng = Provinsi::create([
            'kode' => '33', 
            'nama' => 'Jawa Tengah',
            'latitude' => -7.1500,
            'longitude' => 110.1400,
        ]);

        $banten = Provinsi::create([
            'kode' => '36',
            'nama' => 'Banten',
            'latitude' => -6.1200,
            'longitude' => 106.1500,
        ]);

        // Kabupaten/Kota di Jawa Barat
        $kabBandung = Kabupaten::create([
            'kode' => '32.04',
            'nama' => 'Kabupaten Bandung',
            'provinsi_id' => $jabar->id,
            'tipe' => 'kabupaten',
            'latitude' => -7.1349,
            'longitude' => 107.6210,
        ]);

        $kotaBandung = Kabupaten::create([
            'kode' => '32.73',
            'nama' => 'Kota Bandung', 
            'provinsi_id' => $jabar->id,
            'tipe' => 'kota',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
        ]);

        $kabBogor = Kabupaten::create([
            'kode' => '32.01',
            'nama' => 'Kabupaten Bogor',
            'provinsi_id' => $jabar->id,
            'tipe' => 'kabupaten',
            'latitude' => -6.6000,
            'longitude' => 106.8000,
        ]);

        // Kecamatan di Kabupaten Bandung
        $soreang = Kecamatan::create([
            'kode' => '32.04.01',
            'nama' => 'Soreang',
            'kabupaten_id' => $kabBandung->id,
            'latitude' => -7.0330,
            'longitude' => 107.5190,
        ]);

        $cicalengka = Kecamatan::create([
            'kode' => '32.04.02', 
            'nama' => 'Cicalengka',
            'kabupaten_id' => $kabBandung->id,
            'latitude' => -7.0000,
            'longitude' => 107.8500,
        ]);

        // Kecamatan di Kota Bandung
        $sukajadi = Kecamatan::create([
            'kode' => '32.73.01',
            'nama' => 'Sukajadi',
            'kabupaten_id' => $kotaBandung->id,
            'latitude' => -6.8900,
            'longitude' => 107.5900,
        ]);

        // Desa/Kelurahan di Kecamatan Soreang
        $desaSoreang = Desa::create([
            'kode' => '32.04.01.2001',
            'nama' => 'Soreang',
            'kecamatan_id' => $soreang->id,
            'tipe' => 'kelurahan',
            'latitude' => -7.0330,
            'longitude' => 107.5190,
        ]);

        $desaPamekaran = Desa::create([
            'kode' => '32.04.01.2002',
            'nama' => 'Pamekaran',
            'kecamatan_id' => $soreang->id,
            'tipe' => 'desa',
            'latitude' => -7.0400,
            'longitude' => 107.5300,
        ]);

        // Kelurahan di Kecamatan Sukajadi
        $kelSukagalih = Desa::create([
            'kode' => '32.73.01.1001',
            'nama' => 'Sukagalih',
            'kecamatan_id' => $sukajadi->id,
            'tipe' => 'kelurahan', 
            'latitude' => -6.8910,
            'longitude' => 107.5910,
        ]);

        $this->command->info('Data wilayah berhasil diseed!');
        $this->command->info('Provinsi: ' . Provinsi::count());
        $this->command->info('Kabupaten: ' . Kabupaten::count());
        $this->command->info('Kecamatan: ' . Kecamatan::count());
        $this->command->info('Desa: ' . Desa::count());
    }
}