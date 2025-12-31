<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek jika user belum login
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Cek jika user tidak memiliki role
        if (!$user->role) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['error' => 'Role tidak ditemukan.']);
        }
        
        // PERBAIKAN: gunakan field 'name' bukan 'nama_role'
        // Cek jika role user ada dalam daftar roles yang diizinkan
        if (in_array($user->role->name, $roles)) {
            return $next($request);
        }
        
        // Jika role tidak diizinkan
        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}