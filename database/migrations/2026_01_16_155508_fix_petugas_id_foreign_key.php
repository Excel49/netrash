<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // 1. Hapus foreign key constraint yang ada
            $table->dropForeign(['petugas_id']);
            
            // 2. Ubah kolom menjadi nullable
            $table->foreignId('petugas_id')
                  ->nullable()
                  ->change();
            
            // 3. Tambahkan foreign key constraint baru
            $table->foreign('petugas_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Hapus foreign key constraint
            $table->dropForeign(['petugas_id']);
            
            // Ubah kembali ke not nullable
            $table->foreignId('petugas_id')
                  ->nullable(false)
                  ->change();
            
            // Tambahkan foreign key constraint asli
            $table->foreign('petugas_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }
};