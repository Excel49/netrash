<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\KategoriSampah;
use App\Models\DetailTransaksi;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DashboardExport;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    /**
     * Display reports dashboard (Integrated with transaction reports)
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'sampah');
        
        $petugasList = User::whereHas('role', function($q) {
            $q->where('name', 'petugas');
        })->get();
        
        $adminList = User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->get();
        
        // ========== DEBUG: Tambahkan ini untuk melihat apa yang terjadi ==========
        if (app()->environment('local')) {
            \Log::debug('ReportController - Active Tab: ' . $activeTab);
            \Log::debug('ReportController - Request: ', $request->all());
            
            $totalTransaksi = \App\Models\Transaksi::count();
            $totalPenukaran = \App\Models\Transaksi::where('jenis_transaksi', 'penukaran')->count();
            $totalSampah = \App\Models\Transaksi::where('jenis_transaksi', '!=', 'penukaran')->count();
            
            \Log::debug("Total Transaksi: {$totalTransaksi}");
            \Log::debug("Total Penukaran: {$totalPenukaran}");
            \Log::debug("Total Sampah: {$totalSampah}");
        }
        // ========== END DEBUG ==========
        
        if ($activeTab === 'penukaran') {
            // ========== REPORT PENUKARAN BARANG ==========
            // PERBAIKAN: HAPUS DEFAULT DATE, gunakan null/kosong
            $startDate = $request->input('start_date_penukaran');
            $endDate = $request->input('end_date_penukaran');
            $statusPenukaran = $request->input('status_penukaran');
            $adminId = $request->input('admin_id');
            
            // Query khusus untuk penukaran barang
            $penukaranQuery = Transaksi::with(['warga', 'admin'])
                ->where('jenis_transaksi', 'penukaran');
            
            // PERBAIKAN: Filter tanggal hanya jika diisi
            if ($startDate && $endDate) {
                $penukaranQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            } elseif ($startDate) {
                $penukaranQuery->whereDate('created_at', '>=', $startDate);
            } elseif ($endDate) {
                $penukaranQuery->whereDate('created_at', '<=', $endDate);
            }
            
            if ($statusPenukaran) {
                // PERBAIKAN: Periksa apakah kolom status_penukaran ada
                if (Schema::hasColumn('transaksi', 'status_penukaran')) {
                    $penukaranQuery->where('status_penukaran', $statusPenukaran);
                } else {
                    // Fallback ke kolom status biasa
                    $penukaranQuery->where('status', $statusPenukaran);
                }
            }
            
            if ($adminId) {
                $penukaranQuery->where('admin_id', $adminId);
            }
            
            // Clone query UNTUK SUMMARY (sebelum pagination)
            $summaryQuery = clone $penukaranQuery;
            
            // Hitung summary untuk penukaran - PERBAIKAN: Gunakan $summaryQuery
            $penukaranSummary = [
                'total_transaksi' => $summaryQuery->count(),
                'total_poin' => abs($summaryQuery->sum('total_poin')), // Gunakan abs() karena poin negatif
                'pending' => Schema::hasColumn('transaksi', 'status_penukaran') 
                    ? $summaryQuery->where('status_penukaran', 'pending')->count()
                    : $summaryQuery->where('status', 'pending')->count(),
                'completed' => Schema::hasColumn('transaksi', 'status_penukaran')
                    ? $summaryQuery->where('status_penukaran', 'completed')->count()
                    : $summaryQuery->where('status', 'completed')->count(),
                'cancelled' => Schema::hasColumn('transaksi', 'status_penukaran')
                    ? $summaryQuery->where('status_penukaran', 'cancelled')->count()
                    : $summaryQuery->where('status', 'cancelled')->count(),
                'total_warga' => $summaryQuery->distinct('warga_id')->count('warga_id'),
            ];
            
            // PAGINATION: Pastikan semua parameter filter dimasukkan ke pagination links
            $penukaranTransaksi = $penukaranQuery->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString(); // Ini akan otomatis menambahkan semua parameter GET
            
            // ========== DEBUG: Cek hasil query ==========
            if (app()->environment('local')) {
                \Log::debug('Penukaran Query Count: ' . $penukaranQuery->count());
                \Log::debug('Penukaran Transaksi Count: ' . $penukaranTransaksi->count());
                \Log::debug('Penukaran Summary: ', $penukaranSummary);
            }
            
            return view('admin.reports.index', compact(
                'activeTab',
                'penukaranTransaksi',
                'penukaranSummary',
                'startDate',
                'endDate',
                'statusPenukaran',
                'adminId',
                'petugasList',
                'adminList'
            ));
            
        } else {
            // ========== REPORT TRANSAKSI SAMPAH ==========
            // PERBAIKAN: HAPUS DEFAULT DATE, gunakan null/kosong
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $status = $request->input('status');
            $petugasId = $request->input('petugas_id');
            
            // Query khusus untuk transaksi sampah (bukan penukaran)
            $sampahQuery = Transaksi::with(['warga', 'petugas'])
                ->where(function($query) {
                    $query->where('jenis_transaksi', '!=', 'penukaran')
                        ->orWhereNull('jenis_transaksi'); // Juga ambil yang null
                });
            
            // PERBAIKAN: Filter tanggal hanya jika diisi
            if ($startDate && $endDate) {
                $sampahQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            } elseif ($startDate) {
                $sampahQuery->whereDate('created_at', '>=', $startDate);
            } elseif ($endDate) {
                $sampahQuery->whereDate('created_at', '<=', $endDate);
            }
            
            if ($status) {
                $sampahQuery->where('status', $status);
            }
            
            if ($petugasId) {
                $sampahQuery->where('petugas_id', $petugasId);
            }
            
            // Clone query UNTUK SUMMARY (sebelum pagination)
            $summaryQuery = clone $sampahQuery;
            
            // Hitung summary untuk sampah - PERBAIKAN: Gunakan $summaryQuery
            $sampahSummary = [
                'total_transaksi' => $summaryQuery->count(),
                'total_berat' => $summaryQuery->sum('total_berat') ?? 0,
                'total_poin' => $summaryQuery->sum('total_poin') ?? 0,
                'total_warga' => $summaryQuery->distinct('warga_id')->count('warga_id'),
                'avg_berat' => $summaryQuery->avg('total_berat') ?? 0,
                'avg_poin' => $summaryQuery->avg('total_poin') ?? 0,
                'completed' => $summaryQuery->where('status', 'completed')->count(),
                'pending' => $summaryQuery->where('status', 'pending')->count(),
                'cancelled' => $summaryQuery->where('status', 'cancelled')->count(),
            ];
            
            // PAGINATION: Pastikan semua parameter filter dimasukkan ke pagination links
            $sampahTransaksi = $sampahQuery->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString(); // Ini akan otomatis menambahkan semua parameter GET
            
            // ========== DEBUG: Cek hasil query ==========
            if (app()->environment('local')) {
                \Log::debug('Sampah Query Count: ' . $sampahQuery->count());
                \Log::debug('Sampah Transaksi Count: ' . $sampahTransaksi->count());
                \Log::debug('Sampah Summary: ', $sampahSummary);
            }
            
            return view('admin.reports.index', compact(
                'activeTab',
                'sampahTransaksi',
                'sampahSummary',
                'startDate',
                'endDate',
                'status',
                'petugasId',
                'petugasList',
                'adminList'
            ));
        }
    }
    
    /**
     * Display transaction reports (legacy method - redirect to index)
     */
    public function transaksiReport(Request $request)
    {
        // Redirect to integrated dashboard with transaction filter
        return redirect()->route('admin.reports.index', $request->all());
    }
    
    /**
     * Display users reports
     */
    public function usersReport(Request $request)
    {
        $query = User::with('role');
        
        // Apply filters
        if ($request->has('role_id') && $request->role_id != '') {
            $query->where('role_id', $request->role_id);
        }
        
        if ($request->has('status') && $request->status == 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($request->has('status') && $request->status == 'unverified') {
            $query->whereNull('email_verified_at');
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Get results
        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Summary
        $summary = [
            'total' => $users->total(),
            'admin' => $users->where('role_id', 1)->count(),
            'petugas' => $users->where('role_id', 2)->count(),
            'warga' => $users->where('role_id', 3)->count(),
            'verified' => $users->whereNotNull('email_verified_at')->count(),
            'total_poin' => $users->sum('total_points'),
            'avg_poin' => $users->avg('total_points'),
            'max_poin' => $users->max('total_points'),
            'min_poin' => $users->min('total_points'),
        ];
        
        // Chart data for user registration trends
        $chartData = $this->getUsersChartData();
        
        return view('admin.reports.users', compact('users', 'summary', 'chartData'));
    }
    
    /**
     * Display kategori reports
     */
    public function kategoriReport(Request $request)
    {
        $query = KategoriSampah::withCount(['detailTransaksi as total_transaksi'])
            ->withSum('detailTransaksi as total_berat', 'berat')
            ->withSum('detailTransaksi as total_poin', 'poin')
            ->withSum('detailTransaksi as total_harga', 'harga');
        
        // Apply filters
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('kode_kategori', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('sort_by') && $request->sort_by != '') {
            $sortBy = $request->sort_by;
            $order = $request->get('order', 'desc');
            
            if ($sortBy == 'nama') {
                $query->orderBy('nama_kategori', $order);
            } elseif ($sortBy == 'harga') {
                $query->orderBy('harga_per_kg', $order);
            } elseif ($sortBy == 'poin') {
                $query->orderBy('poin_per_kg', $order);
            } elseif ($sortBy == 'transaksi') {
                $query->orderBy('total_transaksi', $order);
            } elseif ($sortBy == 'berat') {
                $query->orderBy('total_berat', $order);
            }
        } else {
            $query->orderBy('total_transaksi', 'desc');
        }
        
        $kategori = $query->paginate(20);
        
        // Summary
        $summary = [
            'total' => $kategori->total(),
            'total_berat' => $kategori->sum('total_berat'),
            'total_poin' => $kategori->sum('total_poin'),
            'total_harga' => $kategori->sum('total_harga'),
            'total_transaksi' => $kategori->sum('total_transaksi'),
            'avg_berat' => $kategori->avg('total_berat'),
            'avg_harga' => $kategori->avg('harga_per_kg'),
            'avg_poin' => $kategori->avg('poin_per_kg'),
            'max_berat' => $kategori->max('total_berat'),
            'max_transaksi' => $kategori->max('total_transaksi'),
        ];
        
        return view('admin.reports.kategori', compact('kategori', 'summary'));
    }
    
    /**
     * Export reports
     */
/**
 * Export reports
 */
    public function export(Request $request)
    {
        $request->validate([
            'report' => 'required|in:dashboard,users,kategori',
            'type' => 'required|in:excel,pdf,csv'
        ]);
        
        $exportType = $request->input('type');
        $reportType = $request->input('report');
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $status = $request->input('status');
        $petugasId = $request->input('petugas_id');
        $fileName = $request->input('file_name', 'NetraTrash_Report_' . date('Y-m-d'));
        
        if ($exportType === 'excel') {
            return Excel::download(
                new DashboardExport($startDate, $endDate, $status, $petugasId),
                $fileName . '.xlsx'
            );
        } elseif ($exportType === 'pdf') {
            // Generate PDF report dengan format sama seperti printTable()
            $data = $this->getReportData($startDate, $endDate, $status, $petugasId);
            
            // Tambahkan data tambahan untuk PDF
            $data['title'] = 'Laporan Transaksi NetraTrash';
            $data['printDate'] = date('d/m/Y H:i');
            $data['period'] = \Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y');
            
            $pdf = Pdf::loadView('admin.reports.transaksi-pdf', $data)
                ->setPaper('A4', 'landscape');
            
            return $pdf->download($fileName . '.pdf');
        } else {
            // CSV export
            return Excel::download(
                new DashboardExport($startDate, $endDate, $status, $petugasId),
                $fileName . '.csv',
                \Maatwebsite\Excel\Excel::CSV
            );
        }
    }
    
    /**
     * Get dashboard statistics (API)
     */
    public function dashboardStats()
    {
        // Daily stats for last 7 days
        $dates = [];
        $transaksiData = [];
        $beratData = [];
        $poinData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $dates[] = $date->format('d M');
            
            $transaksiCount = Transaksi::whereDate('created_at', $dateStr)->count();
            $beratTotal = Transaksi::whereDate('created_at', $dateStr)->sum('total_berat');
            $poinTotal = Transaksi::whereDate('created_at', $dateStr)->sum('total_poin');
            
            $transaksiData[] = $transaksiCount;
            $beratData[] = $beratTotal;
            $poinData[] = $poinTotal;
        }
        
        // Monthly stats for current year
        $monthlyData = [];
        $currentYear = now()->year;
        
        for ($month = 1; $month <= 12; $month++) {
            $monthlyTransaksi = Transaksi::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();
            $monthlyBerat = Transaksi::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->sum('total_berat');
            $monthlyPoin = Transaksi::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->sum('total_poin');
                
            $monthlyData[] = [
                'month' => Carbon::create()->month($month)->format('M'),
                'transaksi' => $monthlyTransaksi,
                'berat' => $monthlyBerat,
                'poin' => $monthlyPoin,
            ];
        }
        
        return response()->json([
            'success' => true,
            'daily' => [
                'dates' => $dates,
                'transaksi' => $transaksiData,
                'berat' => $beratData,
                'poin' => $poinData,
            ],
            'monthly' => $monthlyData,
            'quick_stats' => [
                'total_transaksi' => Transaksi::count(),
                'total_berat' => Transaksi::sum('total_berat'),
                'total_poin' => Transaksi::sum('total_poin'),
                'total_warga' => User::whereHas('role', function($q) {
                    $q->where('name', 'warga');
                })->whereHas('transaksi')->count(),
                'total_petugas' => User::whereHas('role', function($q) {
                    $q->where('name', 'petugas');
                })->count(),
            ]
        ]);
    }
    
    /**
     * Print receipt for transaction
     */
    public function printReceipt($id)
    {
        $transaksi = Transaksi::with(['warga', 'petugas', 'detailTransaksi.kategori'])->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.reports.print.receipt', [
            'transaksi' => $transaksi,
            'printDate' => Carbon::now()->format('d/m/Y H:i:s'),
        ]);
        
        return $pdf->download('receipt-' . $transaksi->kode_transaksi . '.pdf');
    }
    
    /**
     * Get report summary statistics
     */
    public function getSummary(Request $request)
    {
        $period = $request->get('period', 'monthly'); // daily, weekly, monthly, yearly
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        $summary = [
            'labels' => [],
            'transaksi' => [],
            'berat' => [],
            'poin' => []
        ];
        
        switch ($period) {
            case 'daily':
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                
                for ($day = 1; $day <= $endDate->day; $day++) {
                    $date = Carbon::create($year, $month, $day);
                    $dateStr = $date->format('Y-m-d');
                    
                    $summary['labels'][] = $date->format('d M');
                    $summary['transaksi'][] = Transaksi::whereDate('created_at', $dateStr)->count();
                    $summary['berat'][] = Transaksi::whereDate('created_at', $dateStr)->sum('total_berat');
                    $summary['poin'][] = Transaksi::whereDate('created_at', $dateStr)->sum('total_poin');
                }
                break;
                
            case 'weekly':
                // Last 8 weeks
                for ($week = 7; $week >= 0; $week--) {
                    $startWeek = Carbon::now()->subWeeks($week)->startOfWeek();
                    $endWeek = Carbon::now()->subWeeks($week)->endOfWeek();
                    
                    $summary['labels'][] = 'W' . $startWeek->weekOfYear;
                    $summary['transaksi'][] = Transaksi::whereBetween('created_at', [$startWeek, $endWeek])->count();
                    $summary['berat'][] = Transaksi::whereBetween('created_at', [$startWeek, $endWeek])->sum('total_berat');
                    $summary['poin'][] = Transaksi::whereBetween('created_at', [$startWeek, $endWeek])->sum('total_poin');
                }
                break;
                
            case 'monthly':
                // Last 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $startMonth = $date->copy()->startOfMonth();
                    $endMonth = $date->copy()->endOfMonth();
                    
                    $summary['labels'][] = $date->format('M Y');
                    $summary['transaksi'][] = Transaksi::whereBetween('created_at', [$startMonth, $endMonth])->count();
                    $summary['berat'][] = Transaksi::whereBetween('created_at', [$startMonth, $endMonth])->sum('total_berat');
                    $summary['poin'][] = Transaksi::whereBetween('created_at', [$startMonth, $endMonth])->sum('total_poin');
                }
                break;
                
            case 'yearly':
                // Last 5 years
                for ($i = 4; $i >= 0; $i--) {
                    $year = Carbon::now()->subYears($i)->year;
                    $startYear = Carbon::create($year, 1, 1)->startOfYear();
                    $endYear = Carbon::create($year, 12, 31)->endOfYear();
                    
                    $summary['labels'][] = $year;
                    $summary['transaksi'][] = Transaksi::whereBetween('created_at', [$startYear, $endYear])->count();
                    $summary['berat'][] = Transaksi::whereBetween('created_at', [$startYear, $endYear])->sum('total_berat');
                    $summary['poin'][] = Transaksi::whereBetween('created_at', [$startYear, $endYear])->sum('total_poin');
                }
                break;
        }
        
        return response()->json($summary);
    }
    
    /**
     * API for transaction statistics
     */
    public function apiTransaksiStats()
    {
        $stats = [
            'today' => [
                'count' => Transaksi::whereDate('created_at', today())->count(),
                'berat' => Transaksi::whereDate('created_at', today())->sum('total_berat'),
                'poin' => Transaksi::whereDate('created_at', today())->sum('total_poin'),
            ],
            'monthly' => [
                'count' => Transaksi::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'berat' => Transaksi::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->sum('total_berat'),
                'poin' => Transaksi::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->sum('total_poin'),
            ],
            'yearly' => [
                'count' => Transaksi::whereYear('created_at', now()->year)->count(),
                'berat' => Transaksi::whereYear('created_at', now()->year)->sum('total_berat'),
                'poin' => Transaksi::whereYear('created_at', now()->year)->sum('total_poin'),
            ],
            'status' => [
                'completed' => Transaksi::where('status', 'completed')->count(),
                'pending' => Transaksi::where('status', 'pending')->count(),
                'cancelled' => Transaksi::where('status', 'cancelled')->count(),
            ]
        ];
        
        return response()->json(['success' => true, 'data' => $stats]);
    }
    
    /**
     * API for user statistics
     */
    public function apiUserStats()
    {
        $stats = [
            'total' => User::count(),
            'by_role' => [
                'admin' => User::where('role_id', 1)->count(),
                'petugas' => User::where('role_id', 2)->count(),
                'warga' => User::where('role_id', 3)->count(),
            ],
            'verification' => [
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'unverified' => User::whereNull('email_verified_at')->count(),
            ],
            'points' => [
                'total' => User::sum('total_points'),
                'avg' => round(User::avg('total_points') ?? 0, 1),
                'max' => User::max('total_points'),
                'min' => User::min('total_points'),
            ],
            'registration_trend' => [
                'today' => User::whereDate('created_at', today())->count(),
                'this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => User::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'this_year' => User::whereYear('created_at', now()->year)->count(),
            ]
        ];
        
        return response()->json(['success' => true, 'data' => $stats]);
    }
    
    /**
     * API for kategori statistics
     */
    public function apiKategoriStats()
    {
        $stats = [
            'total' => KategoriSampah::count(),
            'active' => KategoriSampah::where('status', true)->count(),
            'inactive' => KategoriSampah::where('status', false)->count(),
            'price_ranges' => [
                'low' => KategoriSampah::where('harga_per_kg', '<', 2000)->count(),
                'medium' => KategoriSampah::whereBetween('harga_per_kg', [2000, 5000])->count(),
                'high' => KategoriSampah::where('harga_per_kg', '>', 5000)->count(),
            ],
            'top_5_by_transaksi' => KategoriSampah::withCount(['detailTransaksi as total_transaksi'])
                ->orderBy('total_transaksi', 'desc')
                ->take(5)
                ->get(['id', 'nama_kategori', 'total_transaksi']),
            'top_5_by_berat' => KategoriSampah::withSum('detailTransaksi as total_berat', 'berat')
                ->orderBy('total_berat', 'desc')
                ->take(5)
                ->get(['id', 'nama_kategori', 'total_berat']),
            'top_5_by_harga' => KategoriSampah::orderBy('harga_per_kg', 'desc')
                ->take(5)
                ->get(['id', 'nama_kategori', 'harga_per_kg']),
        ];
        
        return response()->json(['success' => true, 'data' => $stats]);
    }
    
    /**
     * Helper Methods
     */
    
    private function calculateDailyAverage($startDate, $endDate, $query)
    {
        $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $totalTransactions = $query->count();
        
        return $days > 0 ? round($totalTransactions / $days, 1) : 0;
    }
    
    private function calculateCompletionRate($query)
    {
        $total = $query->count();
        $completed = $query->where('status', 'completed')->count();
        
        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }
    
    private function getStatusDistribution($query)
    {
        $cloneQuery = clone $query;
        return [
            'completed' => $cloneQuery->where('status', 'completed')->count(),
            'pending' => $cloneQuery->where('status', 'pending')->count(),
            'cancelled' => $cloneQuery->where('status', 'cancelled')->count()
        ];
    }
    
    private function getReportData($startDate, $endDate, $status = null, $petugasId = null)
    {
        $query = Transaksi::with(['warga', 'petugas'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($petugasId) {
            $query->where('petugas_id', $petugasId);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->get();
        
        $summary = [
            'total' => $transactions->count(),
            'total_berat' => $transactions->sum('total_berat'),
            'total_poin' => $transactions->sum('total_poin'),
            'completed' => $transactions->where('status', 'completed')->count(),
            'pending' => $transactions->where('status', 'pending')->count(),
            'cancelled' => $transactions->where('status', 'cancelled')->count()
        ];
        
        return [
            'title' => 'Laporan Dashboard NetraTrash',
            'data' => $transactions,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
            'exportDate' => date('d/m/Y H:i:s')
        ];
    }
    
    private function getUsersChartData()
    {
        $chartData = [];
        
        // User registration trend (last 12 months)
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            
            $usersCount = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $chartData['labels'][] = $date->format('M Y');
            $chartData['registrations'][] = $usersCount;
        }
        
        // Role distribution
        $chartData['roles'] = [
            'admin' => User::where('role_id', 1)->count(),
            'petugas' => User::where('role_id', 2)->count(),
            'warga' => User::where('role_id', 3)->count(),
        ];
        
        // Verified vs unverified
        $chartData['verification'] = [
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
        ];
        
        // Top users by points
        $chartData['top_points'] = User::where('role_id', 3)
            ->orderBy('total_points', 'desc')
            ->take(10)
            ->get(['id', 'name', 'total_points']);
        
        return $chartData;
    }
}