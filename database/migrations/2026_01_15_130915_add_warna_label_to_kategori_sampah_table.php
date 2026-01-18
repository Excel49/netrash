<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori_sampah', function (Blueprint $table) {
            if (!Schema::hasColumn('kategori_sampah', 'warna_label')) {
                $table->string('warna_label', 7)->nullable()->after('deskripsi')->default('#3b82f6');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kategori_sampah', function (Blueprint $table) {
            if (Schema::hasColumn('kategori_sampah', 'warna_label')) {
                $table->dropColumn('warna_label');
            }
        });
    }
};