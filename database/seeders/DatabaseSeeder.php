<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WilayahSeeder::class,
            SektorSeeder::class,
            KomoditasSeeder::class,
            PoktanSeeder::class,
            UserSeeder::class,
            // PetaniSeeder dan lainnya bisa ditambahkan kemudian
        ]);
    }
}