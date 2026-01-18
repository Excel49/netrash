<?php
// C:\xampp\htdocs\netrash_update\netrash\app\Models\DetailTransaksi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;
    
    protected $table = 'detail_transaksi';
    
    protected $fillable = [
        'transaksi_id',
        'kategori_id',
        'berat',
        'harga',
        'poin',
    ];
    
    protected $casts = [
        'berat' => 'float',
        'harga' => 'float',
        'poin' => 'float',
    ];
    
    // Relasi ke transaksi
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
    
    // Relasi ke kategori
    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }
}