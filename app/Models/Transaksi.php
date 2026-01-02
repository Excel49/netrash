<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    // **TAMBAHKAN INI:**
    protected $table = 'transaksi'; // Nama tabel di database
    
    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'tgl_transaksi' => 'datetime',
        'total_berat' => 'decimal:2',
        'total_harga' => 'decimal:2',
        'total_poin' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function warga()
    {
        return $this->belongsTo(User::class, 'warga_id');
    }
    
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
    
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
    
    // Hitung total poin dari detail transaksi
    public function getTotalPoinAttribute()
    {
        return $this->detailTransaksi->sum('subtotal_poin');
    }
}