<?php
// database/seeders/KategoriSampahSeeder.php

namespace Database\Seeders;

use App\Models\KategoriSampah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSampahSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        KategoriSampah::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $kategoris = [
            [
                'nama_kategori' => 'Organik',
                'jenis_sampah' => 'organik',
                'poin_per_kg' => 10,
                'deskripsi' => 'Sampah organik seperti sisa makanan, daun, buah-buahan',
                'is_locked' => true,
                'status' => true,
            ],
            [
                'nama_kategori' => 'Anorganik',
                'jenis_sampah' => 'anorganik',
                'poin_per_kg' => 15,
                'deskripsi' => 'Sampah anorganik seperti plastik, kaca, logam',
                'is_locked' => true,
                'status' => true,
            ],
            [
                'nama_kategori' => 'B3',
                'jenis_sampah' => 'b3',
                'poin_per_kg' => 25,
                'deskripsi' => 'Sampah Bahan Berbahaya dan Beracun seperti baterai, obat kadaluarsa',
                'is_locked' => true,
                'status' => true,
            ],
            [
                'nama_kategori' => 'Campuran',
                'jenis_sampah' => 'campuran',
                'poin_per_kg' => 7.5,
                'deskripsi' => 'Sampah campuran berbagai jenis',
                'is_locked' => true,
                'status' => true,
            ],
            [
                'nama_kategori' => 'Plastik',
                'jenis_sampah' => 'anorganik',
                'poin_per_kg' => 12,
                'deskripsi' => 'Sampah plastik botol, kemasan',
                'is_locked' => false,
                'status' => true,
            ],
            [
                'nama_kategori' => 'Kertas',
                'jenis_sampah' => 'anorganik',
                'poin_per_kg' => 8,
                'deskripsi' => 'Sampah kertas koran, kardus, buku',
                'is_locked' => false,
                'status' => true,
            ],
                       [
                'nama_kategori' => 'besi',
                'jenis_sampah' => 'anorganik',
                'poin_per_kg' => 12,
                'deskripsi' => 'segala jenis besi bekas',
                'is_locked' => false,
                'status' => true,
            ],
                       [
                'nama_kategori' => 'baterai',
                'jenis_sampah' => 'B3',
                'poin_per_kg' => 9,
                'deskripsi' => 'bateran bekas atau baterai kering',
                'is_locked' => false,
                'status' => true,
            ],
                       [
                'nama_kategori' => 'daun',
                'jenis_sampah' => 'organik',
                'poin_per_kg' => 5,
                'deskripsi' => 'Sampah daun kering',
                'is_locked' => false,
                'status' => true,
            ],
                       [
                'nama_kategori' => 'bekas oli',
                'jenis_sampah' => 'B3',
                'poin_per_kg' => 8,
                'deskripsi' => 'Sampah bekas oli',
                'is_locked' => false,
                'status' => true,
            ],
        ];
        
        foreach ($kategoris as $kategori) {
            KategoriSampah::create($kategori);
        }
        
        $this->command->info('Seeder Kategori Sampah berhasil dijalankan!');
        $this->command->info(count($kategoris) . ' kategori telah dibuat.');
    }
}