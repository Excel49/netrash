<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'alamat' => ['required', 'string', 'max:500'],
            'nomor_hp' => ['required', 'string', 'max:15'],
        ]);
    }

    protected function create(array $data)
    {
        // Default role: Warga (role_id = 3)
        $roleWarga = Role::where('nama_role', 'Warga')->first();
        
        return User::create([
            'role_id' => $roleWarga ? $roleWarga->id : 3,
            'nama_lengkap' => $data['nama_lengkap'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'alamat' => $data['alamat'],
            'nomor_hp' => $data['nomor_hp'],
            'qr_code_token' => Str::random(32),
            'total_poin' => 0,
        ]);
    }
}