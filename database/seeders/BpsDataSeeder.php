<?php
// database/seeders/BpsDataSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BpsData;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Sektor;
use App\Models\Komoditas;

class BpsDataSeeder extends Seeder
{
    public function run()
    {
        $provinsis = Provinsi::all();
        $tahun = date('Y') - 1; // Tahun sebelumnya
        
        foreach ($provinsis as $provinsi) {
            $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
            
            // Data level provinsi (tanpa kabupaten/kecamatan spesifik)
            $this->createProvinsiLevelData($provinsi, $tahun);
            
            // Data level kabupaten
            foreach ($kabupatens as $kabupaten) {
                $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
                
                $this->createKabupatenLevelData($kabupaten, $tahun);
                
                // Data level kecamatan (untuk beberapa kecamatan saja)
                foreach ($kecamatans->take(2) as $kecamatan) { // Ambil 2 kecamatan per kabupaten
                    $this->createKecamatanLevelData($kecamatan, $tahun);
                }
            }
        }
    }
    
    private function createProvinsiLevelData($provinsi, $tahun)
    {
        $sektors = Sektor::all();
        
        foreach ($sektors as $sektor) {
            $komoditasList = Komoditas::where('sektor_id', $sektor->id)->get();
            
            foreach ($komoditasList as $komoditas) {
                BpsData::create([
                    'tahun' => $tahun,
                    'provinsi_id' => $provinsi->id,
                    'kabupaten_id' => null, // Data level provinsi
                    'kecamatan_id' => null, // Data level provinsi
                    'sektor_id' => $sektor->id,
                    'komoditas_id' => $komoditas->id,
                    'luas_lahan' => rand(10000, 50000),
                    'produksi' => rand(30000, 200000),
                    'status_unggulan' => $komoditas->status_unggulan,
                    'sumber_data' => 'BPS',
                    'keterangan' => 'Data level provinsi - simulasi BPS'
                ]);
            }
        }
    }
    
    private function createKabupatenLevelData($kabupaten, $tahun)
    {
        $sektors = Sektor::all();
        
        foreach ($sektors as $sektor) {
            $komoditasList = Komoditas::where('sektor_id', $sektor->id)
                                     ->where('status_unggulan', true) // Hanya komoditas unggulan di level kabupaten
                                     ->get();
            
            foreach ($komoditasList as $komoditas) {
                BpsData::create([
                    'tahun' => $tahun,
                    'provinsi_id' => $kabupaten->provinsi_id,
                    'kabupaten_id' => $kabupaten->id,
                    'kecamatan_id' => null, // Data level kabupaten
                    'sektor_id' => $sektor->id,
                    'komoditas_id' => $komoditas->id,
                    'luas_lahan' => rand(5000, 20000),
                    'produksi' => rand(10000, 100000),
                    'status_unggulan' => true, // Di level kabupaten selalu unggulan
                    'sumber_data' => 'BPS',
                    'keterangan' => 'Data level kabupaten - simulasi BPS'
                ]);
            }
        }
    }
    
    private function createKecamatanLevelData($kecamatan, $tahun)
    {
        $sektors = Sektor::all();
        
        foreach ($sektors as $sektor) {
            // Untuk level kecamatan, pilih 1-2 komoditas unggulan per sektor
            $komoditasList = Komoditas::where('sektor_id', $sektor->id)
                                     ->where('status_unggulan', true)
                                     ->inRandomOrder()
                                     ->take(rand(1, 2))
                                     ->get();
            
            foreach ($komoditasList as $komoditas) {
                BpsData::create([
                    'tahun' => $tahun,
                    'provinsi_id' => $kecamatan->kabupaten->provinsi_id,
                    'kabupaten_id' => $kecamatan->kabupaten_id,
                    'kecamatan_id' => $kecamatan->id,
                    'sektor_id' => $sektor->id,
                    'komoditas_id' => $komoditas->id,
                    'luas_lahan' => rand(1000, 5000),
                    'produksi' => rand(5000, 50000),
                    'status_unggulan' => true, // Di level kecamatan selalu unggulan
                    'sumber_data' => 'BPS',
                    'keterangan' => 'Data level kecamatan - simulasi BPS'
                ]);
            }
        }
    }
}