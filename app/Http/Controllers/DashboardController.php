<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\KategoriSampah;
use App\Models\Notifikasi;
use App\Models\DetailTransaksi;
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
        
        // Transaksi terbaru (5 hari terakhir)
        $recentTransactions = Transaksi::with('warga')
            ->where('petugas_id', $petugasId)
            ->whereDate('created_at', '>=', $today->copy()->subDays(5))
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();
        
        // Data grafik transaksi 7 hari terakhir
        $chartData = [];
        $chartLabels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayLabel = $date->translatedFormat('D'); // Singkatan hari
            
            $dayTransactions = Transaksi::where('petugas_id', $petugasId)
                ->whereDate('created_at', $date)
                ->count();
            
            $chartLabels[] = $dayLabel;
            $chartData[] = $dayTransactions;
        }
        
        // Data grafik kategori sampah (7 hari terakhir)
        $categoryData = $this->getCategoryDistributionData($petugasId);
        
        // Data untuk dashboard
        return view('petugas.dashboard', [
            'todayTransactions' => $transaksiHariIni,
            'todayWeight' => $beratHariIni,
            'todayPoints' => $poinHariIni,
            'monthlyTransactions' => $transaksiBulanIni,
            'monthlyWeight' => $beratBulanIni,
            'uniqueWargaToday' => $wargaHariIni,
            'totalPointsDistributed' => $totalPoinDiberikan,
            'totalWargaServed' => $wargaTerlayani,
            'recentTransactions' => $recentTransactions,
            'chartData' => [
                'labels' => $chartLabels,
                'transactions' => $chartData
            ],
            'categoryData' => $categoryData
        ]);
    }
    
    /**
     * Get category distribution data for chart
     */
    private function getCategoryDistributionData($petugasId)
    {
        // Ambil data 7 hari terakhir
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        
        // Ambil transaksi dalam 7 hari terakhir
        $transactions = Transaksi::where('petugas_id', $petugasId)
            ->where('created_at', '>=', $startDate)
            ->with(['detailTransaksi.kategori'])
            ->get();
        
        // Kumpulkan data per kategori
        $categoryWeights = [];
        
        foreach ($transactions as $transaksi) {
            foreach ($transaksi->detailTransaksi as $detail) {
                if ($detail->kategori) {
                    $categoryName = $detail->kategori->nama_kategori;
                    $weight = $detail->berat;
                    
                    if (!isset($categoryWeights[$categoryName])) {
                        $categoryWeights[$categoryName] = 0;
                    }
                    
                    $categoryWeights[$categoryName] += $weight;
                }
            }
        }
        
        // Jika tidak ada data, beri contoh data
        if (empty($categoryWeights)) {
            return [
                'labels' => ['Plastik', 'Kertas', 'Logam', 'Organik', 'Lainnya'],
                'data' => [30, 25, 20, 15, 10]
            ];
        }
        
        // Urutkan berdasarkan berat (descending)
        arsort($categoryWeights);
        
        // Ambil top 5 kategori
        $topCategories = array_slice($categoryWeights, 0, 5, true);
        
        // Siapkan data untuk chart
        $labels = array_keys($topCategories);
        $data = array_values($topCategories);
        
        // Jika kurang dari 5 kategori, tambahkan kategori lainnya
        if (count($labels) < 5) {
            $otherWeight = array_sum(array_slice($categoryWeights, 5));
            if ($otherWeight > 0) {
                $labels[] = 'Lainnya';
                $data[] = $otherWeight;
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    public function wargaDashboard()
    {
        $wargaId = Auth::id();
        $user = Auth::user();
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Statistics
        $totalTransaksi = Transaksi::where('warga_id', $wargaId)->count();
        $totalBerat = Transaksi::where('warga_id', $wargaId)->sum('total_berat');
        
        // Statistics for current month
        $poinBulanIni = Transaksi::where('warga_id', $wargaId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total_poin');
            
        $beratBulanIni = Transaksi::where('warga_id', $wargaId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total_berat');
            
        $transaksiBulanIni = Transaksi::where('warga_id', $wargaId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();
        
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
            'poinBulanIni',
            'beratBulanIni',
            'transaksiBulanIni',
            'recentTransactions',
            'chartLabels',
            'chartData'
        ));
    }
}