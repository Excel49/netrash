<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\KategoriSampah;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\PenarikanPoin;
use App\Models\Notifikasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class NetraTrashSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncate tables dalam urutan yang benar
        Notifikasi::truncate();
        DetailTransaksi::truncate();
        PenarikanPoin::truncate();
        Transaksi::truncate();
        KategoriSampah::truncate();
        User::truncate();
        Role::truncate();
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 1. Seed Roles
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator Sistem'],
            ['name' => 'petugas', 'description' => 'Petugas Pengelola Sampah'],
            ['name' => 'warga', 'description' => 'Warga Pengguna Sistem'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // 2. Seed Users dengan password yang benar
        $users = [
            [
                'name' => 'Admin NetraTrash',
                'email' => 'admin@netratrash.com',
                'password' => Hash::make('admin123'), // PASTIKAN INI
                'phone' => '081234567890',
                'address' => 'Jl. Admin No. 1',
                'role_id' => 1,
                'total_points' => 0,
                'qr_code' => 'qrcodes/admin.svg',
            ],
            [
                'name' => 'Petugas Sampah 1',
                'email' => 'petugas1@netratrash.com',
                'password' => Hash::make('petugas123'), // PASTIKAN INI
                'phone' => '081234567891',
                'address' => 'Jl. Petugas No. 1',
                'role_id' => 2,
                'total_points' => 0,
                'qr_code' => 'qrcodes/petugas1.svg',
            ],
            [
                'name' => 'Petugas Sampah 2',
                'email' => 'petugas2@netratrash.com',
                'password' => Hash::make('petugas123'), // PASTIKAN INI
                'phone' => '081234567892',
                'address' => 'Jl. Petugas No. 2',
                'role_id' => 2,
                'total_points' => 0,
                'qr_code' => 'qrcodes/petugas2.svg',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budisantoso@netratrash.com',
                'password' => Hash::make('warga123'), // PASTIKAN INI
                'phone' => '081234567893',
                'address' => 'Jl. Merdeka No. 10, RT 01/RW 01',
                'role_id' => 3,
                'total_points' => 1500,
                'qr_code' => 'qrcodes/budi.svg',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'sitiaminah@netratrash.com',
                'password' => Hash::make('warga123'), // PASTIKAN INI
                'phone' => '081234567894',
                'address' => 'Jl. Sejahtera No. 5, RT 02/RW 01',
                'role_id' => 3,
                'total_points' => 850,
                'qr_code' => 'qrcodes/siti.svg',
            ],
            [
                'name' => 'Joko Widodo',
                'email' => 'jokowidodo@netratrash.com',
                'password' => Hash::make('warga123'), // PASTIKAN INI
                'phone' => '081234567895',
                'address' => 'Jl. Pahlawan No. 3, RT 03/RW 01',
                'role_id' => 3,
                'total_points' => 2300,
                'qr_code' => 'qrcodes/joko.svg',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // 3. Seed Kategori Sampah (sama seperti sebelumnya)
        $kategoriSampah = [
            [
                'nama_kategori' => 'Plastik PET',
                'jenis_sampah' => 'plastik',
                'harga_per_kg' => 5000,
                'poin_per_kg' => 50,
                'deskripsi' => 'Botol plastik bening (PET)',
                'gambar' => 'plastik-pet.jpg',
                'status' => true,
            ],
            // ... tambahkan kategori lainnya
        ];

        foreach ($kategoriSampah as $kategori) {
            KategoriSampah::create($kategori);
        }

        // 4. Seed Transaksi (opsional untuk testing)
        // ... kode transaksi ...

        $this->command->info('✅ Database NetraTrash berhasil di-seed!');
        $this->command->info('📋 Data login:');
        $this->command->info('   👨‍💼 Admin: admin@netratrash.com / admin123');
        $this->command->info('   👷 Petugas: petugas1@netratrash.com / petugas123');
        $this->command->info('   👨‍🌾 Warga: budisantoso@netratrash.com / warga123');
    }
}