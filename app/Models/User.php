<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        'profile_photo_path',
        'bio',
        'nik',
        'rt_rw',
        'area',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'notification_preferences' => 'array',
        'privacy_settings' => 'array',
    ];

    protected $appends = ['profile_photo_url'];

    /**
     * Get the URL to the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . 
               '&background=2E8B57&color=ffffff&size=200&bold=true';
    }

    // ========== HELPER METHODS ==========
    
    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    public function wishlist()
{
    return $this->belongsToMany(Barang::class, 'wishlists')
                ->withTimestamps();
}
    /**
     * Check if user is petugas
     */
    public function isPetugas(): bool
    {
        return $this->role_id === 2;
    }
    
    /**
     * Check if user is warga
     */
    public function isWarga(): bool
    {
        return $this->role_id === 3;
    }
    
    /**
     * Get user role name
     */
    public function getRoleNameAttribute(): string
    {
        $roles = [
            1 => 'admin',
            2 => 'petugas',
            3 => 'warga'
        ];
        
        return $roles[$this->role_id] ?? 'unknown';
    }
    
    // ========== RELATIONSHIPS ==========

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // ALIAS untuk kompatibilitas dengan kode yang sudah ada
    public function transaksi(): HasMany
    {
        return $this->transaksiSebagaiWarga();
    }

    public function transaksiSebagaiWarga(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'warga_id');
    }

    public function transaksiSebagaiPetugas(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'petugas_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    // ========== EMAIL VERIFICATION ==========

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function sendEmailVerificationNotification(): void
    {
        // Implementasi notifikasi verifikasi email
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }
}