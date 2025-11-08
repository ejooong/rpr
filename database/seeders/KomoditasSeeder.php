<?php
// database/seeders/KomoditasSeeder.php
namespace Database\Seeders;

use App\Models\Komoditas;
use Illuminate\Database\Seeder;

class KomoditasSeeder extends Seeder
{
    public function run(): void
    {
        $komoditas = [
            // Tanaman Pangan
            ['sektor_id' => 1, 'nama' => 'Padi', 'satuan' => 'Ton', 'status_unggulan' => true, 'warna_chart' => '#4CAF50'],
            ['sektor_id' => 1, 'nama' => 'Jagung', 'satuan' => 'Ton', 'status_unggulan' => true, 'warna_chart' => '#FFC107'],
            ['sektor_id' => 1, 'nama' => 'Kedelai', 'satuan' => 'Ton', 'status_unggulan' => false, 'warna_chart' => '#795548'],

            // Hortikultura
            ['sektor_id' => 2, 'nama' => 'Cabai', 'satuan' => 'Ton', 'status_unggulan' => true, 'warna_chart' => '#F44336'],
            ['sektor_id' => 2, 'nama' => 'Bawang Merah', 'satuan' => 'Ton', 'status_unggulan' => true, 'warna_chart' => '#E91E63'],
            ['sektor_id' => 2, 'nama' => 'Mangga', 'satuan' => 'Ton', 'status_unggulan' => false, 'warna_chart' => '#FF9800'],

            // Perkebunan
            ['sektor_id' => 3, 'nama' => 'Kelapa Sawit', 'satuan' => 'Ton', 'status_unggulan' => true, 'warna_chart' => '#8BC34A'],
            ['sektor_id' => 3, 'nama' => 'Kopi', 'satuan' => 'Ton', 'status_unggulan' => false, 'warna_chart' => '#795548'],

            // Peternakan
            ['sektor_id' => 4, 'nama' => 'Sapi', 'satuan' => 'Ekor', 'status_unggulan' => true, 'warna_chart' => '#9E9E9E'],
            ['sektor_id' => 4, 'nama' => 'Ayam', 'satuan' => 'Ekor', 'status_unggulan' => true, 'warna_chart' => '#FF5722'],

            // Perikanan
            ['sektor_id' => 5, 'nama' => 'Ikan Lele', 'satuan' => 'Ton', 'status_unggulan' => true, 'warna_chart' => '#2196F3'],
            ['sektor_id' => 5, 'nama' => 'Udang', 'satuan' => 'Ton', 'status_unggulan' => false, 'warna_chart' => '#E91E63'],
        ];

        foreach ($komoditas as $item) {
            Komoditas::create($item);
        }
    }
}
