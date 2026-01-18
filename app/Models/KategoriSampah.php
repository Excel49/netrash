<?php
// C:\xampp\htdocs\netrash_update\netrash\app\Models\KategoriSampah.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriSampah extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'kategori_sampah';
    
    protected $fillable = [
        'nama_kategori',
        'jenis_sampah',
        'poin_per_kg',
        'deskripsi',
        'warna_label',
        'gambar',
        'status',
        'is_locked',
    ];
    
    protected $casts = [
        'poin_per_kg' => 'float',
        'status' => 'boolean',
        'is_locked' => 'boolean',
    ];
    
    // Scope untuk kategori utama yang terkunci
    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }
    
    // Scope untuk kategori yang bisa di-CRUD
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }
    
    // Cek apakah kategori digunakan dalam transaksi
    public function getIsUsedInTransactionsAttribute()
    {
        return $this->detailTransaksi()->exists();
    }
    
    // Relasi ke detail transaksi
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'kategori_id');
    }
    
    // Helper untuk mendapatkan label jenis sampah
    public function getJenisLabelAttribute()
    {
        $labels = [
            'organik' => 'Organik',
            'anorganik' => 'Anorganik',
            'b3' => 'B3 (Bahan Berbahaya)',
            'campuran' => 'Campuran',
        ];
        
        return $labels[$this->jenis_sampah] ?? 'Lainnya';
    }
    
    // Helper untuk mendapatkan warna badge berdasarkan jenis
    public function getJenisBadgeAttribute()
    {
        $badges = [
            'organik' => 'success',
            'anorganik' => 'info',
            'b3' => 'danger',
            'campuran' => 'warning',
        ];
        
        return $badges[$this->jenis_sampah] ?? 'secondary';
    }
    
    // Format poin
    public function getPoinFormattedAttribute()
    {
        return number_format($this->poin_per_kg, 1) . ' pts';
    }
    
    // Cek apakah kategori bisa dihapus
    public function getCanDeleteAttribute()
    {
        return !$this->is_locked && !$this->isUsedInTransactions;
    }
}