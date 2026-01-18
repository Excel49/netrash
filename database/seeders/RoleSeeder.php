<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key checks sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $roles = [
            [
                'id' => 1, 
                'name' => 'admin', 
                'display_name' => 'Administrator',
                'description' => 'Administrator Sistem'
            ],
            [
                'id' => 2, 
                'name' => 'petugas', 
                'display_name' => 'Petugas',
                'description' => 'Petugas Pengelola Sampah'
            ],
            [
                'id' => 3, 
                'name' => 'warga', 
                'display_name' => 'Warga',
                'description' => 'Warga Pengguna Sistem'
            ],
        ];
        
        foreach ($roles as $role) {
            Role::create($role);
        }
        
        $this->command->info('Seeder Role berhasil dijalankan!');
        $this->command->info('3 role telah dibuat: Admin, Petugas, Warga');
    }
}