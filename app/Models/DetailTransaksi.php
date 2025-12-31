<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'detail_transaksi';
    
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
    
    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }
}