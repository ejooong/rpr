<?php
// database/seeders/WilayahHierarkiSeeder.php
namespace Database\Seeders;

use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Database\Seeder;

class WilayahHierarkiSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        Desa::truncate();
        Kecamatan::truncate();
        Kabupaten::truncate();
        Provinsi::truncate();

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

        // Desa/Kelurahan di Kecamatan Soreang
        Desa::create([
            'kode' => '32.04.01.2001',
            'nama' => 'Soreang',
            'kecamatan_id' => $soreang->id,
            'tipe' => 'kelurahan',
            'latitude' => -7.0330,
            'longitude' => 107.5190,
        ]);

        Desa::create([
            'kode' => '32.04.01.2002',
            'nama' => 'Pamekaran',
            'kecamatan_id' => $soreang->id,
            'tipe' => 'desa',
            'latitude' => -7.0400,
            'longitude' => 107.5300,
        ]);

        // Kecamatan di Kota Bandung
        $sukajadi = Kecamatan::create([
            'kode' => '32.73.01',
            'nama' => 'Sukajadi',
            'kabupaten_id' => $kotaBandung->id,
            'latitude' => -6.8900,
            'longitude' => 107.5900,
        ]);

        // Kelurahan di Kecamatan Sukajadi
        Desa::create([
            'kode' => '32.73.01.1001',
            'nama' => 'Sukagalih',
            'kecamatan_id' => $sukajadi->id,
            'tipe' => 'kelurahan',
            'latitude' => -6.8910,
            'longitude' => 107.5910,
        ]);
    }
}