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
    
    protected $fillable = [
        'kode_transaksi',
        'warga_id',
        'petugas_id',  // TAMBAHKAN INI
        'total_berat',
        'total_harga',
        'total_poin',
        'status',
        'status_penukaran', // TAMBAH
        'alasan_batal',     // TAMBAH
        'admin_id',         // TAMBAH
        'diproses_pada',    // TAMBAH
        'jenis_transaksi', // PASTIKAN INI ADA
        'catatan',
        'tanggal_transaksi'
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'tgl_transaksi' => 'datetime',
         'diproses_pada' => 'datetime',
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
    // Tambah relasi admin
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Scope untuk penukaran
    public function scopePenukaran($query)
    {
        return $query->where('jenis_transaksi', 'penukaran');
    }

    // Scope berdasarkan status penukaran
    public function scopeStatusPenukaran($query, $status)
    {
        return $query->where('status_penukaran', $status);
    }

}