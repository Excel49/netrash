<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;


class Barang extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'barang';
    
    protected $fillable = [
        'nama_barang',
        'deskripsi',
        'harga_poin',
        'stok',
        'gambar',
        'kategori_id',
        'kategori',
        'status',
    ];
    
    protected $casts = [
        'harga_poin' => 'integer',
        'stok' => 'integer',
        'status' => 'boolean',
    ];
    
    // ========== SCOPES ==========
    
    // Scope untuk barang aktif
    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }
    
    // Scope untuk barang tersedia (aktif dan ada stok)
    public function scopeTersedia($query)
    {
        return $query->where('status', true)->where('stok', '>', 0);
    }
    
    // ========== ATTRIBUTES ==========
    
    // Format harga poin
    public function getHargaPoinFormattedAttribute()
    {
        return number_format($this->harga_poin, 0, ',', '.') . ' Poin';
    }
    
    // Cek apakah stok tersedia
    public function getStokTersediaAttribute()
    {
        return $this->stok > 0;
    }
    
    // Label stok
    public function getStokLabelAttribute()
    {
        if ($this->stok > 10) {
            return ['text' => 'Tersedia', 'color' => 'success'];
        } elseif ($this->stok > 0) {
            return ['text' => 'Terbatas', 'color' => 'warning'];
        } else {
            return ['text' => 'Habis', 'color' => 'danger'];
        }
    }
    
    // ========== RELATIONS ==========
    
    // Relasi ke kategori (jika ada)
    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }
    

    // ========== HELPER METHODS ==========
    
    // Kurangi stok
    public function kurangiStok($jumlah)
    {
        if ($this->stok < $jumlah) {
            throw new \Exception('Stok tidak mencukupi');
        }
        
        $this->stok -= $jumlah;
        return $this->save();
    }
    
    // Tambah stok
    public function tambahStok($jumlah)
    {
        $this->stok += $jumlah;
        return $this->save();
    }
    
        public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'kategori_id');
    }
    
    public function barang()
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }
    public function getStokStatusAttribute()
    {
        return $this->stok_label; // Gunakan yang sudah ada
    }

    // Untuk gambar URL
    public function getGambarUrlAttribute()
    {

        
        // Jika gambar adalah URL lengkap
        if (filter_var($this->gambar, FILTER_VALIDATE_URL)) {
            return $this->gambar;
        }
        
        // **CEK DI storage/app/public/barang/ (YANG SEHARUSNYA DIGUNAKAN)**
        $storagePath = 'barang/' . $this->gambar;
        $fullPath = storage_path('app/public/' . $storagePath);
        
        if (file_exists($fullPath)) {
            return asset('storage/' . $storagePath);
        }
        
        // **CEK DI public/img/ (JIKA GAMBAR DISIMPAN DI SINI)**
        $publicPath = public_path('img/' . $this->gambar);
        if (file_exists($publicPath)) {
            return asset('img/' . $this->gambar);
        }
        
        // **CEK DI public/barang/ (ALTERNATIF LAIN)**
        $publicBarangPath = public_path('barang/' . $this->gambar);
        if (file_exists($publicBarangPath)) {
            return asset('barang/' . $this->gambar);
        }
        
        // Default image jika tidak ditemukan
        return asset('img/default-product.png');
    }
    // Untuk tipe penukaran (default)
    public function getTipePenukaranAttribute()
    {
        return 'ambil_sendiri'; // Default value
    }

    // Untuk mengakses nama kategori secara langsung
    public function getKategoriNamaAttribute()
    {
        return $this->kategori ? $this->kategori->nama_kategori : 'Tidak ada kategori';
    }
    // Cek apakah bisa ditukar
    public function bisaDitukar($jumlah, $poinUser)
    {
        return [
            'stok_cukup' => $this->stok >= $jumlah,
            'poin_cukup' => ($this->harga_poin * $jumlah) <= $poinUser,
            'total_poin' => $this->harga_poin * $jumlah,
            'stok_tersedia' => $this->stok,
        ];
    }
}