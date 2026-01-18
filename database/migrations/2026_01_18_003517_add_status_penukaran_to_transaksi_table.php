<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Cek apakah kolom sudah ada
            if (!Schema::hasColumn('transaksi', 'status_penukaran')) {
                $table->enum('status_penukaran', ['pending', 'completed', 'cancelled', 'processed'])
                      ->nullable()
                      ->after('status');
            }
            
            // Tambah kolom lain jika perlu
            if (!Schema::hasColumn('transaksi', 'alasan_batal')) {
                $table->text('alasan_batal')->nullable()->after('status_penukaran');
            }
            
            if (!Schema::hasColumn('transaksi', 'admin_id')) {
                $table->foreignId('admin_id')->nullable()->constrained('users')->after('alasan_batal');
            }
            
            if (!Schema::hasColumn('transaksi', 'diproses_pada')) {
                $table->timestamp('diproses_pada')->nullable()->after('admin_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Hapus foreign key jika ada
            if (Schema::hasColumn('transaksi', 'admin_id')) {
                $table->dropForeign(['admin_id']);
            }
            
            // Hapus kolom
            $columns = ['status_penukaran', 'alasan_batal', 'admin_id', 'diproses_pada'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('transaksi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};