<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role_id',
        'total_points',
        'qr_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // TAMBAHKAN METHOD INI JIKA BELUM ADA
    public function isAdmin(): bool
    {
        return $this->role_id === 1; // Sesuaikan dengan ID role admin di database
    }

    public function isPetugas(): bool
    {
        return $this->role_id === 2; // Sesuaikan dengan ID role petugas
    }

    public function isWarga(): bool
    {
        return $this->role_id === 3; // Sesuaikan dengan ID role warga
    }
    // AKHIR TAMBAHAN

    public function transaksiSebagaiWarga(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'warga_id');
    }

    public function transaksiSebagaiPetugas(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'petugas_id');
    }

    public function penarikanPoin(): HasMany
    {
        return $this->hasMany(PenarikanPoin::class, 'warga_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }
}