<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada sebelum menambahkan
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('bio');
            }
            
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik')->nullable()->after('profile_photo_path');
            }
            
            if (!Schema::hasColumn('users', 'rt_rw')) {
                $table->string('rt_rw')->nullable()->after('nik');
            }
            
            if (!Schema::hasColumn('users', 'area')) {
                $table->string('area')->nullable()->after('rt_rw');
            }
            
            if (!Schema::hasColumn('users', 'total_points')) {
                $table->integer('total_points')->default(0)->after('area');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hanya drop kolom jika mereka ada
            $columns = ['phone', 'address', 'bio', 'profile_photo_path', 'nik', 'rt_rw', 'area', 'total_points'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};