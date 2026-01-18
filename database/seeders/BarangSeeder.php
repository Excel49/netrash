<?php
// database/seeders/BarangSeeder.php (update)

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barang = [
            [
                'nama_barang' => 'Beras 5kg',
                'deskripsi' => 'Beras premium kualitas terbaik',
                'harga_poin' => 5000,
                'stok' => 50,
                'gambar' => 'beras.jpg',
                'status' => true,
            ],
            [
                'nama_barang' => 'Minyak Goreng 2L',
                'deskripsi' => 'Minyak goreng kemasan 2 liter',
                'harga_poin' => 2500,
                'stok' => 100,
                'gambar' => 'minyak.jpg',
                'status' => true,
            ],
            [
                'nama_barang' => 'Sabun Mandi',
                'deskripsi' => 'Sabun mandi batang isi 3',
                'harga_poin' => 1000,
                'stok' => 200,
                'gambar' => 'sabun.jpg',
                'status' => true,
            ],
            [
                'nama_barang' => 'Pasta Gigi',
                'deskripsi' => 'Pasta gigi ukuran besar',
                'harga_poin' => 1500,
                'stok' => 150,
                'gambar' => 'pasta_gigi.jpg',
                'status' => true,
            ],
            [
                'nama_barang' => 'Shampoo',
                'deskripsi' => 'Shampoo anti ketombe',
                'harga_poin' => 2000,
                'stok' => 120,
                'gambar' => 'shampoo.jpg',
                'status' => true,
            ],
            [
                'nama_barang' => 'Gula 1kg',
                'deskripsi' => 'Gula pasir putih 1kg',
                'harga_poin' => 1500,
                'stok' => 80,
                'gambar' => 'gula.jpg',
                'status' => true,
            ],
            [
                'nama_barang' => 'Garam 1kg',
                'deskripsi' => 'Garam halus 1kg',
                'harga_poin' => 800,
                'stok' => 150,
                'gambar' => 'garam.jpg',
                'status' => true,
            ],
            [
                'nama_barang' => 'Teh Celup',
                'deskripsi' => 'Teh celup isi 25',
                'harga_poin' => 1200,
                'stok' => 100,
                'gambar' => 'teh.jpg',
                'status' => true,
            ],
            [
                'nama_barang' => 'Kopi Sachet',
                'deskripsi' => 'Kopi sachet isi 10',
                'harga_poin' => 1000,
                'stok' => 120,
                'gambar' => 'kopi.jpg',
                'status' => true,
            ],
            [
                'nama_barang' => 'Susu Kental Manis',
                'deskripsi' => 'Susu kental manis kaleng',
                'harga_poin' => 1800,
                'stok' => 70,
                'gambar' => 'susu.jpg',
                'status' => true,
            ],
            [
                'nama_barang' => 'Mie Instan',
                'deskripsi' => 'Mie instan isi 5',
                'harga_poin' => 800,
                'stok' => 200,
                'gambar' => 'mie.jpg',
                'status' => true,
            ],
            [
                'nama_barang' => 'Kecap Manis',
                'deskripsi' => 'Kecap manis botol 500ml',
                'harga_poin' => 1200,
                'stok' => 90,
                'gambar' => 'kecap.jpg',
                'status' => true,
            ],
                        [
                'nama_barang' => 'Tumbler',
                'deskripsi' => 'Tumbler kaca 500ml',
                'harga_poin' => 1200,
                'stok' => 90,
                'gambar' => 'tumbler.jpg',
                'status' => true,
            ],
        ];
        
        foreach ($barang as $item) {
            Barang::create($item);
        }
        
        $this->command->info('Seeder Barang berhasil dijalankan!');
        $this->command->info(count($barang) . ' barang telah ditambahkan.');
    }
}