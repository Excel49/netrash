<?php

namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WargaController extends Controller
{
    public function kategoriIndex()
    {
        $kategori = KategoriSampah::where('status', true)
            ->orderBy('nama_kategori')
            ->get();
            
        return view('warga.kategori.index', compact('kategori'));
    }
    
    public function poinHistory()
    {
        $wargaId = Auth::id();
        
        $transaksi = Transaksi::where('warga_id', $wargaId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('warga.poin.history', compact('transaksi'));
    }
    
    public function apiPoinBalance()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'total_points' => $user->total_points,
            'formatted_points' => number_format($user->total_points, 0, ',', '.')
        ]);
    }
}