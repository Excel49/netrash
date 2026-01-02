<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSampah extends Model
{
    use HasFactory;
    
    // Tentukan nama tabel secara eksplisit
    protected $table = 'kategori_sampah';
    
    protected $fillable = [
        'nama_kategori',
        'jenis_sampah',
        'harga_per_kg',
        'poin_per_kg',
        'deskripsi',
        'gambar',
        'status',
    ];
    
    protected $casts = [
        'harga_per_kg' => 'float',
        'poin_per_kg' => 'float',
        'status' => 'boolean',
    ];
    
    // Helper untuk mendapatkan label jenis sampah
    public function getJenisLabelAttribute()
    {
        $labels = [
            'organik' => 'Organik',
            'anorganik' => 'Anorganik',
            'berbahaya' => 'Bahan Berbahaya',
            'daur_ulang' => 'Daur Ulang',
            'lainnya' => 'Lainnya',
            'plastik' => 'Plastik',
            'kertas' => 'Kertas',
            'logam' => 'Logam',
            'kaca' => 'Kaca',
            'elektronik' => 'Elektronik',
        ];
        
        return $labels[$this->jenis_sampah] ?? 'Lainnya';
    }
    
    // Helper untuk mendapatkan warna badge berdasarkan jenis
    public function getJenisBadgeAttribute()
    {
        $badges = [
            'organik' => 'success',
            'anorganik' => 'info',
            'berbahaya' => 'danger',
            'daur_ulang' => 'primary',
            'lainnya' => 'secondary',
            'plastik' => 'warning',
            'kertas' => 'light',
            'logam' => 'dark',
            'kaca' => 'info',
            'elektronik' => 'danger',
        ];
        
        return $badges[$this->jenis_sampah] ?? 'secondary';
    }
    
    // Format harga
    public function getHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga_per_kg, 0, ',', '.');
    }
    
    // Format poin
    public function getPoinFormattedAttribute()
    {
        return number_format($this->poin_per_kg, 1) . ' pts';
    }
}