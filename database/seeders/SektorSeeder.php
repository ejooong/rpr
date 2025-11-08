<?php
// database/seeders/SektorSeeder.php
namespace Database\Seeders;

use App\Models\Sektor;
use Illuminate\Database\Seeder;

class SektorSeeder extends Seeder
{
    public function run(): void
    {
        $sektors = [
            ['kode' => 'A1', 'nama' => 'Tanaman Pangan', 'deskripsi' => 'Komoditas tanaman pangan'],
            ['kode' => 'A2', 'nama' => 'Hortikultura', 'deskripsi' => 'Sayuran, buah-buahan, dan tanaman hias'],
            ['kode' => 'A3', 'nama' => 'Perkebunan', 'deskripsi' => 'Tanaman perkebunan tahunan'],
            ['kode' => 'A4', 'nama' => 'Peternakan', 'deskripsi' => 'Hewan ternak dan produk peternakan'],
            ['kode' => 'A5', 'nama' => 'Perikanan', 'deskripsi' => 'Budidaya perikanan air tawar dan laut'],
        ];

        foreach ($sektors as $sektor) {
            Sektor::updateOrCreate(
                ['kode' => $sektor['kode']], // Cari berdasarkan kode
                $sektor // Update atau buat data
            );
        }

        $this->command->info('Data sektor berhasil diupdate/dibuat!');
        $this->command->info('Total sektor: ' . Sektor::count());
    }
}