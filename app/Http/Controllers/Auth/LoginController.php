<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        // Redirect berdasarkan role_id
        if ($user->role_id == 1) { // Admin
            return redirect()->route('admin.dashboard');
        } elseif ($user->role_id == 2) { // Petugas
            return redirect()->route('petugas.dashboard');
        } elseif ($user->role_id == 3) { // Warga
            return redirect()->route('warga.dashboard');
        }
        
        return redirect($this->redirectTo);
    }
}