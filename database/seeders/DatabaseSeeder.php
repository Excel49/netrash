<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            KategoriSampahSeeder::class,
            BarangSeeder::class,
            UserSeeder::class,
            TransaksiSeeder::class,       // Transaksi dasar (10-15 per warga)
            NotifikasiSeeder::class,
        ]);
    }
}