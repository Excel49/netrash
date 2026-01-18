<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('warga_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null'); // UBAH INI
            $table->decimal('total_berat', 10, 2);
            $table->decimal('total_harga', 15, 2);
            $table->integer('total_poin');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->enum('jenis_transaksi', ['setoran', 'penukaran', 'transfer'])->default('setoran'); // TAMBAHKAN INI
            $table->text('catatan')->nullable();
            $table->dateTime('tanggal_transaksi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};