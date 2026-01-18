<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('id_ID');

        // Admin (1 orang)
        User::create([
            'name' => 'Admin NetraTrash',
            'email' => 'admin@netratrash.com',
            'password' => Hash::make('admin123'),
            'role_id' => 1,
            'email_verified_at' => now(),
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1, Kota NetraTrash',
        ]);

        // Petugas (5 orang dengan nama random)
        $petugasNames = [
            'Budi Santoso',
            'Siti Rahayu', 
            'Agus Wijaya',
            'Rina Dewi',
            'Ahmad Fauzi',
            'Dewi Lestari',
            'Rudi Hartono',
            'Maya Sari',
            'Eko Prasetyo',
            'Linda Wati'
        ];

        // Ambil 5 nama random dari array
        $selectedPetugas = array_slice($petugasNames, 0, 5);

        foreach ($selectedPetugas as $index => $name) {
            // Buat email dari nama (lowercase, spasi diganti titik)
            $emailName = strtolower(str_replace(' ', '.', $name));
            
            User::create([
                'name' => $name,
                'email' => 'ptg_' . $emailName . '@gmail.com',
                'password' => Hash::make('petugas123'),
                'role_id' => 2,
                'email_verified_at' => now(),
                'phone' => '08' . rand(1111111111, 9999999999),
                'address' => $faker->address(),
                'total_points' => 0,
            ]);
        }

        // Warga (10 orang dengan nama random)
        $wargaNames = [
            'Tono Suhartono',
            'Sari Indah',
            'Joko Susilo',
            'Rina Anggraini',
            'Bambang Setiawan',
            'Diana Putri',
            'Hendra Kurniawan',
            'Ani Wijaya',
            'Fajar Nugroho',
            'Mila Sari',
            'Irfan Maulana',
            'Yuni Astuti',
            'Dimas Pratama',
            'Ratna Dewi',
            'Adi Saputra'
        ];

        // Ambil 10 nama random dari array
        $selectedWarga = array_slice($wargaNames, 0, 10);

        foreach ($selectedWarga as $index => $name) {
            // Buat email dari nama (lowercase, spasi diganti titik)
            $emailName = strtolower(str_replace(' ', '.', $name));
            
            User::create([
                'name' => $name,
                'email' => $emailName . '@gmail.com',
                'password' => Hash::make('warga123'),
                'role_id' => 3,
                'email_verified_at' => now(),
                'phone' => '08' . rand(1111111111, 9999999999),
                'address' => $faker->address(),
                'total_points' => rand(1000, 10000),
            ]);
        }
        
        $this->command->info('Seeder User berhasil dijalankan!');
        $this->command->info('1 Admin, 5 Petugas, dan 10 Warga telah dibuat.');
    }
}