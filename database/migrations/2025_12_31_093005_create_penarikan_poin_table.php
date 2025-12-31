<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penarikan_poin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('users')->onDelete('cascade');
            $table->integer('jumlah_poin');
            $table->decimal('jumlah_rupiah', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('alasan_penarikan')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('tanggal_pengajuan');
            $table->dateTime('tanggal_approval')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penarikan_poin');
    }
};