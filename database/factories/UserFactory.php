<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'role_id' => 3, // Warga
            'nama_lengkap' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'alamat' => $this->faker->address(),
            'nomor_hp' => $this->faker->phoneNumber(),
            'qr_code_token' => Str::random(32),
            'total_poin' => $this->faker->numberBetween(0, 1000),
            'remember_token' => Str::random(10),
        ];
    }
}