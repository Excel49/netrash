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

        // 1. Seed Roles (3 data)
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator Sistem'],
            ['name' => 'petugas', 'description' => 'Petugas Pengelola Sampah'],
            ['name' => 'warga', 'description' => 'Warga Pengguna Sistem'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // 2. Seed Users (10+ data)
        $users = [
            // Admin
            [
                'name' => 'Admin NetraTrash',
                'email' => 'admin@netratrash.com',
                'password' => Hash::make('admin123'),
                'phone' => '081234567890',
                'address' => 'Jl. Admin No. 1',
                'role_id' => 1,
                'total_points' => 0,
                'qr_code' => 'qrcodes/admin.svg',
            ],
            
            // Petugas (3 data)
            [
                'name' => 'Petugas Sampah 1',
                'email' => 'petugas1@netratrash.com',
                'password' => Hash::make('petugas123'),
                'phone' => '081234567891',
                'address' => 'Jl. Petugas No. 1',
                'role_id' => 2,
                'total_points' => 0,
                'qr_code' => 'qrcodes/petugas1.svg',
            ],
            [
                'name' => 'Petugas Sampah 2',
                'email' => 'petugas2@netratrash.com',
                'password' => Hash::make('petugas123'),
                'phone' => '081234567892',
                'address' => 'Jl. Petugas No. 2',
                'role_id' => 2,
                'total_points' => 0,
                'qr_code' => 'qrcodes/petugas2.svg',
            ],
            [
                'name' => 'Petugas Sampah 3',
                'email' => 'petugas3@netratrash.com',
                'password' => Hash::make('petugas123'),
                'phone' => '081234567893',
                'address' => 'Jl. Petugas No. 3',
                'role_id' => 2,
                'total_points' => 0,
                'qr_code' => 'qrcodes/petugas3.svg',
            ],
            
            // Warga (10+ data)
            [
                'name' => 'Budi Santoso',
                'email' => 'budisantoso@netratrash.com',
                'password' => Hash::make('warga123'),
                'phone' => '081234567894',
                'address' => 'Jl. Merdeka No. 10, RT 01/RW 01',
                'role_id' => 3,
                'total_points' => 1500,
                'qr_code' => 'qrcodes/budi.svg',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'sitiaminah@netratrash.com',
                'password' => Hash::make('warga123'),
                'phone' => '081234567895',
                'address' => 'Jl. Sejahtera No. 5, RT 02/RW 01',
                'role_id' => 3,
                'total_points' => 850,
                'qr_code' => 'qrcodes/siti.svg',
            ],
            [
                'name' => 'Joko Widodo',
                'email' => 'jokowidodo@netratrash.com',
                'password' => Hash::make('warga123'),
                'phone' => '081234567896',
                'address' => 'Jl. Pahlawan No. 3, RT 03/RW 01',
                'role_id' => 3,
                'total_points' => 2300,
                'qr_code' => 'qrcodes/joko.svg',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmadfauzi@netratrash.com',
                'password' => Hash::make('warga123'),
                'phone' => '081234567897',
                'address' => 'Jl. Melati No. 7, RT 04/RW 01',
                'role_id' => 3,
                'total_points' => 1200,
                'qr_code' => 'qrcodes/ahmad.svg',
            ],
            [
                'name' => 'Rina Marlina',
                'email' => 'rinamarlina@netratrash.com',
                'password' => Hash::make('warga123'),
                'phone' => '081234567898',
                'address' => 'Jl. Anggrek No. 12, RT 05/RW 01',
                'role_id' => 3,
                'total_points' => 950,
                'qr_code' => 'qrcodes/rina.svg',
            ],
            [
                'name' => 'Dewi Sartika',
                'email' => 'dewisartika@netratrash.com',
                'password' => Hash::make('warga123'),
                'phone' => '081234567899',
                'address' => 'Jl. Kenanga No. 8, RT 06/RW 01',
                'role_id' => 3,
                'total_points' => 1800,
                'qr_code' => 'qrcodes/dewi.svg',
            ],
            [
                'name' => 'Hadi Pranoto',
                'email' => 'hadipranoto@netratrash.com',
                'password' => Hash::make('warga123'),
                'phone' => '081234567800',
                'address' => 'Jl. Mawar No. 15, RT 07/RW 01',
                'role_id' => 3,
                'total_points' => 700,
                'qr_code' => 'qrcodes/hadi.svg',
            ],
            [
                'name' => 'Linda Hartati',
                'email' => 'lindahartati@netratrash.com',
                'password' => Hash::make('warga123'),
                'phone' => '081234567801',
                'address' => 'Jl. Flamboyan No. 9, RT 08/RW 01',
                'role_id' => 3,
                'total_points' => 1400,
                'qr_code' => 'qrcodes/linda.svg',
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'ekoprasetyo@netratrash.com',
                'password' => Hash::make('warga123'),
                'phone' => '081234567802',
                'address' => 'Jl. Dahlia No. 11, RT 09/RW 01',
                'role_id' => 3,
                'total_points' => 2100,
                'qr_code' => 'qrcodes/eko.svg',
            ],
            [
                'name' => 'Maya Indah',
                'email' => 'mayaindah@netratrash.com',
                'password' => Hash::make('warga123'),
                'phone' => '081234567803',
                'address' => 'Jl. Teratai No. 6, RT 10/RW 01',
                'role_id' => 3,
                'total_points' => 1100,
                'qr_code' => 'qrcodes/maya.svg',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // 3. Seed Kategori Sampah (15+ data)
        $kategoriSampah = [
            [
                'nama_kategori' => 'Plastik PET (Botol)',
                'jenis_sampah' => 'plastik',
                'harga_per_kg' => 5000,
                'poin_per_kg' => 50,
                'deskripsi' => 'Botol plastik bening (PET) seperti botol air mineral',
                'gambar' => 'plastik-pet.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Plastik HDPE',
                'jenis_sampah' => 'plastik',
                'harga_per_kg' => 4500,
                'poin_per_kg' => 45,
                'deskripsi' => 'Plastik HDPE untuk botol susu, deterjen',
                'gambar' => 'plastik-hdpe.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Kertas Koran',
                'jenis_sampah' => 'kertas',
                'harga_per_kg' => 3000,
                'poin_per_kg' => 30,
                'deskripsi' => 'Kertas koran bekas',
                'gambar' => 'kertas-koran.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Karton/Kardus',
                'jenis_sampah' => 'kertas',
                'harga_per_kg' => 2500,
                'poin_per_kg' => 25,
                'deskripsi' => 'Kardus bekas kemasan',
                'gambar' => 'kardus.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Besi/Baja',
                'jenis_sampah' => 'logam',
                'harga_per_kg' => 8000,
                'poin_per_kg' => 80,
                'deskripsi' => 'Besi dan baja bekas',
                'gambar' => 'besi.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Aluminium',
                'jenis_sampah' => 'logam',
                'harga_per_kg' => 12000,
                'poin_per_kg' => 120,
                'deskripsi' => 'Kaleng aluminium dan bekas aluminium',
                'gambar' => 'aluminium.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Kaca/Beling',
                'jenis_sampah' => 'kaca',
                'harga_per_kg' => 2000,
                'poin_per_kg' => 20,
                'deskripsi' => 'Botol kaca dan beling',
                'gambar' => 'kaca.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Kabel Tembaga',
                'jenis_sampah' => 'logam',
                'harga_per_kg' => 25000,
                'poin_per_kg' => 250,
                'deskripsi' => 'Kabel bekas dengan tembaga',
                'gambar' => 'tembaga.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Sampah Elektronik',
                'jenis_sampah' => 'elektronik',
                'harga_per_kg' => 15000,
                'poin_per_kg' => 150,
                'deskripsi' => 'Barang elektronik rusak (HP, charger, dll)',
                'gambar' => 'elektronik.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Baterai Bekas',
                'jenis_sampah' => 'berbahaya',
                'harga_per_kg' => 10000,
                'poin_per_kg' => 100,
                'deskripsi' => 'Baterai bekas termasuk baterai HP dan aki',
                'gambar' => 'baterai.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Lampu Neon',
                'jenis_sampah' => 'berbahaya',
                'harga_per_kg' => 5000,
                'poin_per_kg' => 50,
                'deskripsi' => 'Lampu neon dan TL bekas',
                'gambar' => 'lampu.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Botol Infus',
                'jenis_sampah' => 'plastik',
                'harga_per_kg' => 6000,
                'poin_per_kg' => 60,
                'deskripsi' => 'Botol infus bekas medis',
                'gambar' => 'infus.jpg',
                'status' => false,
            ],
            [
                'nama_kategori' => 'Plastik LDPE',
                'jenis_sampah' => 'plastik',
                'harga_per_kg' => 4000,
                'poin_per_kg' => 40,
                'deskripsi' => 'Plastik kemasan makanan dan tas kresek',
                'gambar' => 'plastik-ldpe.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Styrofoam',
                'jenis_sampah' => 'plastik',
                'harga_per_kg' => 2000,
                'poin_per_kg' => 20,
                'deskripsi' => 'Busa sterofoam bekas kemasan',
                'gambar' => 'styrofoam.jpg',
                'status' => true,
            ],
            [
                'nama_kategori' => 'Sampah Organik',
                'jenis_sampah' => 'organik',
                'harga_per_kg' => 1000,
                'poin_per_kg' => 10,
                'deskripsi' => 'Sampah organik untuk kompos',
                'gambar' => 'organik.jpg',
                'status' => true,
            ],
        ];

        foreach ($kategoriSampah as $kategori) {
            KategoriSampah::create($kategori);
        }

        // 4. Seed Transaksi (15+ data dummy)
        $transaksiCounter = 1;
        $wargaIds = [4, 5, 6, 7, 8, 9, 10, 11, 12, 13]; // ID warga dari user seeding
        
        foreach ($wargaIds as $wargaId) {
            for ($i = 1; $i <= 2; $i++) { // 2 transaksi per warga
                $totalBerat = rand(1, 20) + (rand(0, 99) / 100); // 1-20 kg
                $totalHarga = $totalBerat * rand(3000, 15000);
                $totalPoin = $totalBerat * rand(30, 150);
                
                $transaksi = Transaksi::create([
                    'kode_transaksi' => 'TRX-' . date('Ymd') . '-' . str_pad($transaksiCounter, 3, '0', STR_PAD_LEFT),
                    'warga_id' => $wargaId,
                    'petugas_id' => 2, // petugas 1
                    'total_berat' => $totalBerat,
                    'total_harga' => $totalHarga,
                    'total_poin' => $totalPoin,
                    'status' => 'completed',
                    'tanggal_transaksi' => now()->subDays(rand(1, 30)),
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ]);
                
                $transaksiCounter++;
            }
        }

        // 5. Seed Penarikan Poin (10+ data)
        $statuses = ['pending', 'approved', 'rejected', 'completed'];
        
        for ($i = 1; $i <= 10; $i++) {
            $wargaId = $wargaIds[array_rand($wargaIds)];
            $jumlahPoin = rand(100, 2000);
            $jumlahRupiah = $jumlahPoin * 100; // 1 poin = Rp 100
            
            PenarikanPoin::create([
                'warga_id' => $wargaId,
                'jumlah_poin' => $jumlahPoin,
                'jumlah_rupiah' => $jumlahRupiah,
                'status' => $statuses[array_rand($statuses)],
                'admin_id' => 1, // admin
                'tanggal_pengajuan' => now()->subDays(rand(1, 30)),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        // 6. Seed Notifikasi (15+ data)
        $notificationTypes = ['info', 'success', 'warning', 'error'];
        $notificationTitles = [
            'Transaksi Berhasil',
            'Poin Bertambah',
            'Penarikan Disetujui',
            'Penarikan Ditolak',
            'Promo Spesial',
            'Pengumuman Sistem',
            'Verifikasi Berhasil',
            'Peringatan Sistem',
        ];
        
        for ($i = 1; $i <= 15; $i++) {
            $userId = rand(1, 13); // random user ID
            
            Notifikasi::create([
                'user_id' => $userId,
                'judul' => $notificationTitles[array_rand($notificationTitles)],
                'pesan' => 'Ini adalah pesan notifikasi dummy untuk testing sistem.',
                'tipe' => $notificationTypes[array_rand($notificationTypes)],
                'dibaca' => rand(0, 1),
                'link' => '/dashboard',
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('✅ Database NetraTrash berhasil di-seed dengan data lengkap!');
        $this->command->info('📋 Data login:');
        $this->command->info('   👨‍💼 Admin: admin@netratrash.com / admin123');
        $this->command->info('   👷 Petugas: petugas1@netratrash.com / petugas123');
        $this->command->info('   👨‍🌾 Warga: budisantoso@netratrash.com / warga123');
        $this->command->info('📊 Statistik Data:');
        $this->command->info('   👤 Users: ' . User::count() . ' data');
        $this->command->info('   🏷️  Kategori: ' . KategoriSampah::count() . ' data');
        $this->command->info('   💰 Transaksi: ' . Transaksi::count() . ' data');
        $this->command->info('   💸 Penarikan: ' . PenarikanPoin::count() . ' data');
        $this->command->info('   🔔 Notifikasi: ' . Notifikasi::count() . ' data');
    }
}