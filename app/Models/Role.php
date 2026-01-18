<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $table = 'roles';
    
    protected $fillable = [
        'name',
        'display_name', // Tambahkan ini
        'description',
    ];
    
    // Relasi ke users
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}