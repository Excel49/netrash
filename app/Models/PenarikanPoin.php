<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenarikanPoin extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'penarikan_poin';
    
    public function warga()
    {
        return $this->belongsTo(User::class, 'warga_id');
    }
}