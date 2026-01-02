<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'notifikasi';
    
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Get sender name
    public function getSenderNameAttribute()
    {
        return $this->data['sender_name'] ?? 'Sistem';
    }
    
    // Get sender role
    public function getSenderRoleAttribute()
    {
        return $this->data['sender_role'] ?? 'system';
    }
    
    // Get notification type icon
    public function getIconAttribute()
    {
        $icons = [
            'info' => 'bi-info-circle',
            'warning' => 'bi-exclamation-triangle',
            'important' => 'bi-exclamation-circle',
            'transaction' => 'bi-receipt',
        ];
        
        return $icons[$this->tipe] ?? 'bi-bell';
    }
    
    // Get notification type color
    public function getColorAttribute()
    {
        $colors = [
            'info' => 'primary',
            'warning' => 'warning',
            'important' => 'danger',
            'transaction' => 'success',
        ];
        
        return $colors[$this->tipe] ?? 'secondary';
    }
    
    // Mark as read
    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
    }
}