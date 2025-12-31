<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSampah extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'kategori_sampah';
    
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'kategori_id');
    }
}