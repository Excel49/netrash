<?php
// database/migrations/2026_01_13_132226_create_barang_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->text('deskripsi')->nullable();
            $table->integer('harga_poin'); // Hanya poin
            $table->integer('stok')->default(0);
            $table->string('gambar')->nullable();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_sampah');
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};