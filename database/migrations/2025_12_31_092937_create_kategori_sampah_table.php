<?php
// C:\xampp\htdocs\netrash_update\netrash\database\migrations\2025_12_31_092937_create_kategori_sampah_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_sampah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->string('jenis_sampah'); // organik, anorganik, b3, campuran
            $table->integer('poin_per_kg');
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_locked')->default(false); // NEW: untuk lock 4 kategori utama
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_sampah');
    }
};