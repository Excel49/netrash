<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\KategoriSampah;
use App\Models\PenarikanPoin;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isPetugas()) {
            return $this->petugasDashboard();
        } elseif ($user->isWarga()) {
            return $this->wargaDashboard();
        }
        
        return redirect('/login');
    }
    
    private function adminDashboard()
    {
        // Total Statistics
        $totalUsers = User::count();
        $totalWarga = User::where('role_id', 3)->count();
        $totalPetugas = User::where('role_id', 2)->count();
        $totalTransaksi = Transaksi::count();
        
        $totalRevenue = Transaksi::where('status', 'completed')->sum('total_harga');
        $totalSampah = Transaksi::where('status', 'completed')->sum('total_berat');
        
        $pendingPenarikan = PenarikanPoin::where('status', 'pending')->count();
        $totalPendingPoin = PenarikanPoin::where('status', 'pending')->sum('jumlah_poin');
        
        // Recent Data
        $recentTransactions = Transaksi::with(['warga', 'petugas'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $recentNotifications = Notifikasi::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Chart Data (Last 6 Months)
        $chartData = [];
        $chartLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthYear = $date->format('M Y');
            
            $count = Transaksi::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $chartLabels[] = $monthYear;
            $chartData[] = $count;
        }
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalWarga',
            'totalPetugas',
            'totalTransaksi',
            'totalRevenue',
            'totalSampah',
            'pendingPenarikan',
            'totalPendingPoin',
            'recentTransactions',
            'recentNotifications',
            'chartLabels',
            'chartData'
        ));
    }
    
    private function petugasDashboard()
    {
        $petugasId = Auth::id();
        
        // Today's Statistics
        $transaksiHariIni = Transaksi::where('petugas_id', $petugasId)
            ->whereDate('created_at', today())
            ->count();
            
        $beratHariIni = Transaksi::where('petugas_id', $petugasId)
            ->whereDate('created_at', today())
            ->sum('total_berat');
        
        // This Month's Statistics
        $transaksiBulanIni = Transaksi::where('petugas_id', $petugasId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        $beratBulanIni = Transaksi::where('petugas_id', $petugasId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_berat');
            
        $totalPoinDiberikan = Transaksi::where('petugas_id', $petugasId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_poin');
        
        // Other Data
        $totalWarga = User::where('role_id', 3)->count();
        $kategoriSampah = KategoriSampah::where('status', true)->get();
        
        // Today's Transactions
        $transaksiToday = Transaksi::with('warga')
            ->where('petugas_id', $petugasId)
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Top 5 Warga by Points
        $topWarga = User::where('role_id', 3)
            ->orderBy('total_points', 'desc')
            ->take(5)
            ->get();
        
        return view('petugas.dashboard', compact(
            'transaksiHariIni',
            'beratHariIni',
            'transaksiBulanIni',
            'beratBulanIni',
            'totalPoinDiberikan',
            'totalWarga',
            'kategoriSampah',
            'transaksiToday',
            'topWarga'
        ));
    }
    
    private function wargaDashboard()
    {
        $wargaId = Auth::id();
        $user = Auth::user();
        
        // Statistics
        $totalTransaksi = Transaksi::where('warga_id', $wargaId)->count();
        $totalBerat = Transaksi::where('warga_id', $wargaId)->sum('total_berat');
        
        $pendingPenarikan = PenarikanPoin::where('warga_id', $wargaId)
            ->where('status', 'pending')
            ->count();
            
        $totalPendingPoin = PenarikanPoin::where('warga_id', $wargaId)
            ->where('status', 'pending')
            ->sum('jumlah_poin');
            
        $totalPoinDitarik = PenarikanPoin::where('warga_id', $wargaId)
            ->where('status', 'completed')
            ->sum('jumlah_poin');
        
        // Recent Transactions
        $recentTransactions = Transaksi::with('petugas')
            ->where('warga_id', $wargaId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Chart Data (Last 30 Days)
        $chartData = [];
        $chartLabels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $day = $date->format('d/m');
            
            $total = Transaksi::where('warga_id', $wargaId)
                ->whereDate('created_at', $date)
                ->sum('total_poin');
            
            $chartLabels[] = $day;
            $chartData[] = $total;
        }
        
        return view('warga.dashboard', compact(
            'user',
            'totalTransaksi',
            'totalBerat',
            'pendingPenarikan',
            'totalPendingPoin',
            'totalPoinDitarik',
            'recentTransactions',
            'chartLabels',
            'chartData'
        ));
    }
}