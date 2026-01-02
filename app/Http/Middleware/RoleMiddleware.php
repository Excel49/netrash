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
        
        // Cek role berdasarkan role_id
        // $roles berisi parameter seperti '1' untuk admin, '2' untuk petugas, '3' untuk warga
        if (in_array((string) $user->role_id, $roles)) {
            return $next($request);
        }
        
        // Jika role tidak diizinkan
        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}