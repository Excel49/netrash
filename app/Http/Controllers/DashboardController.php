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
use Carbon\Carbon;

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
    
    public function adminDashboard()
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
    
    public function petugasDashboard()
    {
        $petugasId = Auth::id();
        $today = Carbon::today();
        
        // Hari ini
        $transaksiHariIni = Transaksi::where('petugas_id', $petugasId)
            ->whereDate('created_at', $today)
            ->count();
            
        $beratHariIni = Transaksi::where('petugas_id', $petugasId)
            ->whereDate('created_at', $today)
            ->sum('total_berat');
            
        $poinHariIni = Transaksi::where('petugas_id', $petugasId)
            ->whereDate('created_at', $today)
            ->sum('total_poin');
        
        // Bulan ini
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
        
        // Warga terlayani
        $wargaTerlayani = User::where('role_id', 3)
            ->whereHas('transaksiSebagaiWarga', function($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            })
            ->count();
            
        $wargaHariIni = User::where('role_id', 3)
            ->whereHas('transaksiSebagaiWarga', function($query) use ($petugasId, $today) {
                $query->where('petugas_id', $petugasId)
                      ->whereDate('created_at', $today);
            })
            ->count();
        
        // Rating petugas (jika ada fitur rating)
        $averageRating = 4.5; // Default value
        $totalRatings = 10; // Default value
        
        // Transaksi terbaru (5 hari terakhir)
        $recentTransactions = Transaksi::with('warga')
            ->where('petugas_id', $petugasId)
            ->whereDate('created_at', '>=', $today->subDays(5))
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();
        
        // Warga teraktif (5 warga teratas)
        $topWarga = User::where('role_id', 3)
            ->whereHas('transaksiSebagaiWarga', function($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            })
            ->withCount(['transaksiSebagaiWarga as total_transactions' => function($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            }])
            ->orderBy('total_transactions', 'desc')
            ->take(5)
            ->get();
        
        // Data performa 7 hari terakhir untuk chart
        $performanceData = [];
        $performanceLabels = [];
        $performanceWeight = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayLabel = $date->translatedFormat('D'); // Singkatan hari (Sen, Sel, dst)
            
            $dayTransactions = Transaksi::where('petugas_id', $petugasId)
                ->whereDate('created_at', $date)
                ->count();
                
            $dayWeight = Transaksi::where('petugas_id', $petugasId)
                ->whereDate('created_at', $date)
                ->sum('total_berat');
            
            $performanceLabels[] = $dayLabel;
            $performanceData[] = $dayTransactions;
            $performanceWeight[] = $dayWeight;
        }
        
        // Kategori sampah untuk referensi
        $kategoriSampah = KategoriSampah::where('status', true)->get();
        
        // Data yang dibutuhkan view
        $data = [
            'todayTransactions' => $transaksiHariIni,
            'todayWeight' => $beratHariIni,
            'todayPoints' => $poinHariIni,
            'uniqueWargaToday' => $wargaHariIni,
            'totalPointsDistributed' => $totalPoinDiberikan,
            'totalWargaServed' => $wargaTerlayani,
            'averageRating' => $averageRating,
            'totalRatings' => $totalRatings,
            'recentTransactions' => $recentTransactions,
            'topWarga' => $topWarga,
            'performanceLabels' => $performanceLabels,
            'performanceData' => $performanceData,
            'performanceWeight' => $performanceWeight,
            'transaksiHariIni' => $transaksiHariIni, // untuk kompatibilitas
            'beratHariIni' => $beratHariIni, // untuk kompatibilitas
            'transaksiBulanIni' => $transaksiBulanIni, // untuk kompatibilitas
            'beratBulanIni' => $beratBulanIni, // untuk kompatibilitas
            'totalPoinDiberikan' => $totalPoinDiberikan, // untuk kompatibilitas
            'kategoriSampah' => $kategoriSampah, // untuk kompatibilitas
        ];
        
        return view('petugas.dashboard', $data);
    }
    
    public function wargaDashboard()
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