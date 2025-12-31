<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotifikasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Gunakan query langsung
        $notifikasi = Notifikasi::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('warga.notifikasi.index', compact('notifikasi'));
    }
    
    public function markAsRead($id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        
        // Cek jika notifikasi milik user
        if ($notifikasi->user_id != Auth::id()) {
            abort(403);
        }
        
        // Update menggunakan DB query builder
        DB::table('notifikasi')
            ->where('id', $id)
            ->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
    
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        // Update semua notifikasi user
        DB::table('notifikasi')
            ->where('user_id', $user->id)
            ->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
}