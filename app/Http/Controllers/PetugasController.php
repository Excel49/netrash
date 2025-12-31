<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    public function wargaIndex()
    {
        $warga = User::where('role_id', 3)
            ->orderBy('name')
            ->paginate(20);
            
        return view('petugas.warga.index', compact('warga'));
    }
    
    public function wargaShow($id)
    {
        $warga = User::with(['transaksiSebagaiWarga' => function($query) {
            $query->orderBy('created_at', 'desc')->take(10);
        }])->findOrFail($id);
        
        return view('petugas.warga.show', compact('warga'));
    }
    
    public function statistik()
    {
        $petugasId = Auth::id();
        
        $stats = [
            'hari_ini' => Transaksi::where('petugas_id', $petugasId)
                ->whereDate('created_at', today())
                ->count(),
            'bulan_ini' => Transaksi::where('petugas_id', $petugasId)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'total_warga' => User::where('role_id', 3)->count(),
        ];
        
        return view('petugas.statistik', compact('stats'));
    }
}