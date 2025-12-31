<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\PenarikanPoin;
use App\Models\KategoriSampah;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiExport;
use App\Exports\PenarikanExport;
use App\Exports\UsersExport;
use App\Exports\KategoriExport;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        // Statistics for dashboard
        $stats = [
            'total_transaksi' => Transaksi::count(),
            'total_penarikan' => PenarikanPoin::count(),
            'total_users' => User::count(),
            'total_kategori' => KategoriSampah::count(),
            'total_pendapatan' => Transaksi::where('status', 'completed')->sum('total_harga'),
            'total_poin_dikeluarkan' => Transaksi::sum('total_poin'),
            'total_poin_ditarik' => PenarikanPoin::whereIn('status', ['approved', 'completed'])->sum('jumlah_poin'),
            'pending_penarikan' => PenarikanPoin::where('status', 'pending')->count(),
            'pending_transaksi' => Transaksi::where('status', 'pending')->count(),
        ];
        
        // Recent transactions (last 5)
        $recentTransaksi = Transaksi::with(['warga', 'petugas'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Recent withdrawals (last 5)
        $recentPenarikan = PenarikanPoin::with(['warga', 'admin'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('admin.reports.index', compact('stats', 'recentTransaksi', 'recentPenarikan'));
    }
    
    /**
     * Display transaction reports
     */
    public function transaksiReport(Request $request)
    {
        // Default date range (last 30 days)
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $query = Transaksi::with(['warga', 'petugas', 'detailTransaksi.kategori'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        
        // Apply filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('petugas_id') && $request->petugas_id != '') {
            $query->where('petugas_id', $request->petugas_id);
        }
        
        if ($request->has('warga_id') && $request->warga_id != '') {
            $query->where('warga_id', $request->warga_id);
        }
        
        // Get results
        $transaksi = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Summary calculations
        $summary = [
            'total_transaksi' => $transaksi->total(),
            'total_berat' => $transaksi->sum('total_berat'),
            'total_harga' => $transaksi->sum('total_harga'),
            'total_poin' => $transaksi->sum('total_poin'),
            'avg_berat' => $transaksi->avg('total_berat'),
            'avg_harga' => $transaksi->avg('total_harga'),
            'avg_poin' => $transaksi->avg('total_poin'),
            'completed' => $transaksi->where('status', 'completed')->count(),
            'pending' => $transaksi->where('status', 'pending')->count(),
            'cancelled' => $transaksi->where('status', 'cancelled')->count(),
        ];
        
        // Get lists for filters
        $petugasList = User::where('role_id', 2)->orderBy('name')->get();
        $wargaList = User::where('role_id', 3)->orderBy('name')->get();
        
        // Chart data for monthly trends
        $chartData = $this->getTransaksiChartData($startDate, $endDate);
        
        return view('admin.reports.transaksi', compact(
            'transaksi', 
            'summary', 
            'startDate', 
            'endDate',
            'petugasList',
            'wargaList',
            'chartData'
        ));
    }
    
    /**
     * Display withdrawal reports
     */
    public function penarikanReport(Request $request)
    {
        // Default date range (last 30 days)
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $query = PenarikanPoin::with(['warga', 'admin'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        
        // Apply filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('warga_id') && $request->warga_id != '') {
            $query->where('warga_id', $request->warga_id);
        }
        
        // Get results
        $penarikan = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Summary by status
        $summary = [
            'total' => $penarikan->total(),
            'total_poin' => $penarikan->sum('jumlah_poin'),
            'total_rupiah' => $penarikan->sum('jumlah_rupiah'),
            'avg_poin' => $penarikan->avg('jumlah_poin'),
            'avg_rupiah' => $penarikan->avg('jumlah_rupiah'),
            'pending' => $penarikan->where('status', 'pending')->count(),
            'approved' => $penarikan->where('status', 'approved')->count(),
            'completed' => $penarikan->where('status', 'completed')->count(),
            'rejected' => $penarikan->where('status', 'rejected')->count(),
        ];
        
        // Get warga list for filter
        $wargaList = User::where('role_id', 3)->orderBy('name')->get();
        
        // Chart data
        $chartData = $this->getPenarikanChartData($startDate, $endDate);
        
        return view('admin.reports.penarikan', compact(
            'penarikan', 
            'summary', 
            'startDate', 
            'endDate',
            'wargaList',
            'chartData'
        ));
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
        
        // Chart data for kategori performance
        $chartData = $this->getKategoriChartData();
        
        return view('admin.reports.kategori', compact('kategori', 'summary', 'chartData'));
    }
    
    /**
     * Export reports
     */
    public function export(Request $request)
    {
        $request->validate([
            'report' => 'required|in:transaksi,penarikan,users,kategori',
            'type' => 'required|in:excel,pdf'
        ]);
        
        $type = $request->get('type', 'excel');
        $report = $request->get('report', 'transaksi');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');
        $petugas_id = $request->get('petugas_id');
        $role_id = $request->get('role_id');
        $warga_id = $request->get('warga_id');
        
        $filename = $this->generateFilename($report, $startDate, $endDate, $type);
        
        if ($type == 'excel') {
            return $this->exportExcel($report, $filename, $startDate, $endDate, $status, $petugas_id, $role_id, $warga_id);
        } elseif ($type == 'pdf') {
            return $this->exportPDF($report, $filename, $startDate, $endDate, $status, $petugas_id, $role_id, $warga_id);
        }
        
        return back()->with('error', 'Format export tidak didukung');
    }
    
    /**
     * Generate filename for export
     */
    private function generateFilename($report, $startDate, $endDate, $type)
    {
        $reportNames = [
            'transaksi' => 'Laporan-Transaksi',
            'penarikan' => 'Laporan-Penarikan',
            'users' => 'Laporan-Users',
            'kategori' => 'Laporan-Kategori'
        ];
        
        $name = $reportNames[$report] ?? 'Laporan';
        $dateRange = '';
        
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->format('Ymd');
            $end = Carbon::parse($endDate)->format('Ymd');
            if ($start == $end) {
                $dateRange = '-' . $start;
            } else {
                $dateRange = '-' . $start . '-' . $end;
            }
        } else {
            $dateRange = '-' . Carbon::now()->format('Ymd');
        }
        
        $timestamp = Carbon::now()->format('His');
        $extensions = [
            'excel' => 'xlsx',
            'pdf' => 'pdf'
        ];
        
        return $name . $dateRange . '-' . $timestamp . '.' . ($extensions[$type] ?? $type);
    }
    
    /**
     * Export to Excel
     */
    private function exportExcel($report, $filename, $startDate, $endDate, $status = null, $petugas_id = null, $role_id = null, $warga_id = null)
    {
        try {
            switch ($report) {
                case 'transaksi':
                    $export = new TransaksiExport($startDate, $endDate, $status, $petugas_id, $warga_id);
                    return Excel::download($export, $filename);
                    
                case 'penarikan':
                    $export = new PenarikanExport($startDate, $endDate, $status, $warga_id);
                    return Excel::download($export, $filename);
                    
                case 'users':
                    $export = new UsersExport($role_id, $status);
                    return Excel::download($export, $filename);
                    
                case 'kategori':
                    $export = new KategoriExport();
                    return Excel::download($export, $filename);
                    
                default:
                    throw new \Exception('Jenis laporan tidak ditemukan');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
    
    /**
     * Export to PDF
     */
    private function exportPDF($report, $filename, $startDate, $endDate, $status = null, $petugas_id = null, $role_id = null, $warga_id = null)
    {
        try {
            $data = [];
            $title = '';
            $view = '';
            $query = null;
            
            switch ($report) {
                case 'transaksi':
                    $query = Transaksi::with(['warga', 'petugas', 'detailTransaksi.kategori']);
                    
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    }
                    
                    if ($status) {
                        $query->where('status', $status);
                    }
                    
                    if ($petugas_id) {
                        $query->where('petugas_id', $petugas_id);
                    }
                    
                    if ($warga_id) {
                        $query->where('warga_id', $warga_id);
                    }
                    
                    $data = $query->orderBy('created_at', 'desc')->get();
                    $title = 'Laporan Transaksi Sampah';
                    $view = 'admin.reports.exports.transaksi-pdf';
                    break;
                    
                case 'penarikan':
                    $query = PenarikanPoin::with(['warga', 'admin']);
                    
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    }
                    
                    if ($status) {
                        $query->where('status', $status);
                    }
                    
                    if ($warga_id) {
                        $query->where('warga_id', $warga_id);
                    }
                    
                    $data = $query->orderBy('created_at', 'desc')->get();
                    $title = 'Laporan Penarikan Poin';
                    $view = 'admin.reports.exports.penarikan-pdf';
                    break;
                    
                case 'users':
                    $query = User::with('role');
                    
                    if ($role_id) {
                        $query->where('role_id', $role_id);
                    }
                    
                    if ($status) {
                        if ($status == 'verified') {
                            $query->whereNotNull('email_verified_at');
                        } elseif ($status == 'unverified') {
                            $query->whereNull('email_verified_at');
                        }
                    }
                    
                    $data = $query->orderBy('created_at', 'desc')->get();
                    $title = 'Laporan Data Pengguna';
                    $view = 'admin.reports.exports.users-pdf';
                    break;
                    
                case 'kategori':
                    $data = KategoriSampah::withCount(['detailTransaksi as total_transaksi'])
                        ->withSum('detailTransaksi as total_berat', 'berat')
                        ->withSum('detailTransaksi as total_poin', 'poin')
                        ->withSum('detailTransaksi as total_harga', 'harga')
                        ->orderBy('total_transaksi', 'desc')
                        ->get();
                    $title = 'Laporan Kategori Sampah';
                    $view = 'admin.reports.exports.kategori-pdf';
                    break;
                    
                default:
                    throw new \Exception('Jenis laporan tidak ditemukan');
            }
            
            // Hitung summary
            $summary = $this->calculateSummary($report, $data);
            
            $pdf = Pdf::loadView($view, [
                'data' => $data,
                'title' => $title,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'status' => $status,
                'summary' => $summary,
                'exportDate' => Carbon::now()->format('d/m/Y H:i:s')
            ]);
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Calculate summary for export
     */
    private function calculateSummary($report, $data)
    {
        switch ($report) {
            case 'transaksi':
                return [
                    'total' => $data->count(),
                    'total_berat' => $data->sum('total_berat'),
                    'total_harga' => $data->sum('total_harga'),
                    'total_poin' => $data->sum('total_poin'),
                    'completed' => $data->where('status', 'completed')->count(),
                    'pending' => $data->where('status', 'pending')->count(),
                    'cancelled' => $data->where('status', 'cancelled')->count(),
                ];
                
            case 'penarikan':
                return [
                    'total' => $data->count(),
                    'total_poin' => $data->sum('jumlah_poin'),
                    'total_rupiah' => $data->sum('jumlah_rupiah'),
                    'pending' => $data->where('status', 'pending')->count(),
                    'approved' => $data->where('status', 'approved')->count(),
                    'completed' => $data->where('status', 'completed')->count(),
                    'rejected' => $data->where('status', 'rejected')->count(),
                ];
                
            case 'users':
                return [
                    'total' => $data->count(),
                    'admin' => $data->where('role_id', 1)->count(),
                    'petugas' => $data->where('role_id', 2)->count(),
                    'warga' => $data->where('role_id', 3)->count(),
                    'verified' => $data->whereNotNull('email_verified_at')->count(),
                    'total_poin' => $data->sum('total_points'),
                ];
                
            case 'kategori':
                return [
                    'total' => $data->count(),
                    'total_berat' => $data->sum('total_berat'),
                    'total_poin' => $data->sum('total_poin'),
                    'total_harga' => $data->sum('total_harga'),
                    'total_transaksi' => $data->sum('total_transaksi'),
                ];
                
            default:
                return [];
        }
    }
    
    /**
     * Get transaction chart data
     */
    private function getTransaksiChartData($startDate, $endDate)
    {
        $chartData = [];
        
        // Daily transactions for the selected period
        $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        $days = min($days, 30); // Limit to 30 days
        
        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::parse($endDate)->subDays($i);
            $dateStr = $date->format('Y-m-d');
            
            $transaksiCount = Transaksi::whereDate('created_at', $dateStr)->count();
            $transaksiBerat = Transaksi::whereDate('created_at', $dateStr)->sum('total_berat');
            $transaksiPendapatan = Transaksi::whereDate('created_at', $dateStr)->sum('total_harga');
            
            $chartData['labels'][] = $date->format('d M');
            $chartData['transaksi'][] = $transaksiCount;
            $chartData['berat'][] = $transaksiBerat;
            $chartData['pendapatan'][] = $transaksiPendapatan;
        }
        
        // Status distribution
        $chartData['status'] = [
            'completed' => Transaksi::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('status', 'completed')->count(),
            'pending' => Transaksi::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('status', 'pending')->count(),
            'cancelled' => Transaksi::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('status', 'cancelled')->count(),
        ];
        
        // Top petugas - menggunakan transaksiSebagaiPetugas
        $chartData['top_petugas'] = User::where('role_id', 2)
            ->withCount(['transaksiSebagaiPetugas' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }])
            ->orderBy('transaksi_sebagai_petugas_count', 'desc')
            ->take(5)
            ->get();
            
        // Top warga - menggunakan transaksiSebagaiWarga
        $chartData['top_warga'] = User::where('role_id', 3)
            ->withCount(['transaksiSebagaiWarga' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }])
            ->orderBy('transaksi_sebagai_warga_count', 'desc')
            ->take(5)
            ->get();
        
        return $chartData;
    }
    
    /**
     * Get withdrawal chart data
     */
    private function getPenarikanChartData($startDate, $endDate)
    {
        $chartData = [];
        
        // Daily withdrawals for the selected period
        $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        $days = min($days, 30); // Limit to 30 days
        
        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::parse($endDate)->subDays($i);
            $dateStr = $date->format('Y-m-d');
            
            $penarikanCount = PenarikanPoin::whereDate('created_at', $dateStr)->count();
            $penarikanPoin = PenarikanPoin::whereDate('created_at', $dateStr)->sum('jumlah_poin');
            $penarikanRupiah = PenarikanPoin::whereDate('created_at', $dateStr)->sum('jumlah_rupiah');
            
            $chartData['labels'][] = $date->format('d M');
            $chartData['penarikan'][] = $penarikanCount;
            $chartData['poin'][] = $penarikanPoin;
            $chartData['rupiah'][] = $penarikanRupiah;
        }
        
        // Status distribution
        $chartData['status'] = [
            'pending' => PenarikanPoin::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('status', 'pending')->count(),
            'approved' => PenarikanPoin::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('status', 'approved')->count(),
            'completed' => PenarikanPoin::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('status', 'completed')->count(),
            'rejected' => PenarikanPoin::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('status', 'rejected')->count(),
        ];
        
        // Top warga by withdrawals - menggunakan penarikanPoin
        $chartData['top_warga'] = User::where('role_id', 3)
            ->withCount(['penarikanPoin' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }])
            ->orderBy('penarikan_poin_count', 'desc')
            ->take(5)
            ->get();
        
        return $chartData;
    }
    
    /**
     * Get users chart data
     */
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
    
    /**
     * Get kategori chart data
     */
    private function getKategoriChartData()
    {
        $chartData = [];
        
        // Top 10 kategori by transactions
        $topKategori = KategoriSampah::withCount(['detailTransaksi as total_transaksi'])
            ->withSum('detailTransaksi as total_berat', 'berat')
            ->orderBy('total_transaksi', 'desc')
            ->take(10)
            ->get();
        
        foreach ($topKategori as $kategori) {
            $chartData['labels'][] = $kategori->nama_kategori;
            $chartData['transaksi'][] = $kategori->total_transaksi;
            $chartData['berat'][] = $kategori->total_berat;
        }
        
        // Kategori by price range
        $chartData['price_range'] = [
            'low' => KategoriSampah::where('harga_per_kg', '<', 2000)->count(),
            'medium' => KategoriSampah::whereBetween('harga_per_kg', [2000, 5000])->count(),
            'high' => KategoriSampah::where('harga_per_kg', '>', 5000)->count(),
        ];
        
        return $chartData;
    }
    
    /**
     * Get dashboard statistics (API)
     */
    public function dashboardStats()
    {
        // Daily stats for last 7 days
        $dates = [];
        $transaksiData = [];
        $penarikanData = [];
        $pendapatanData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $dates[] = $date->format('d M');
            
            $transaksiCount = Transaksi::whereDate('created_at', $dateStr)->count();
            $penarikanCount = PenarikanPoin::whereDate('created_at', $dateStr)->count();
            $pendapatan = Transaksi::whereDate('created_at', $dateStr)->sum('total_harga');
            
            $transaksiData[] = $transaksiCount;
            $penarikanData[] = $penarikanCount;
            $pendapatanData[] = $pendapatan;
        }
        
        // Monthly stats for current year
        $monthlyData = [];
        $currentYear = now()->year;
        
        for ($month = 1; $month <= 12; $month++) {
            $monthlyTransaksi = Transaksi::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();
            $monthlyPenarikan = PenarikanPoin::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();
            $monthlyPendapatan = Transaksi::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->sum('total_harga');
                
            $monthlyData[] = [
                'month' => Carbon::create()->month($month)->format('M'),
                'transaksi' => $monthlyTransaksi,
                'penarikan' => $monthlyPenarikan,
                'pendapatan' => $monthlyPendapatan,
            ];
        }
        
        return response()->json([
            'daily' => [
                'dates' => $dates,
                'transaksi' => $transaksiData,
                'penarikan' => $penarikanData,
                'pendapatan' => $pendapatanData,
            ],
            'monthly' => $monthlyData,
            'quick_stats' => [
                'total_transaksi' => Transaksi::count(),
                'total_penarikan' => PenarikanPoin::count(),
                'total_users' => User::count(),
                'total_pendapatan' => Transaksi::sum('total_harga'),
                'active_users' => User::where('status', 'active')->count(),
                'pending_actions' => PenarikanPoin::where('status', 'pending')->count() + 
                                   Transaksi::where('status', 'pending')->count(),
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
     * Print receipt for withdrawal
     */
    public function printWithdrawalReceipt($id)
    {
        $penarikan = PenarikanPoin::with(['warga', 'admin'])->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.reports.print.withdrawal-receipt', [
            'penarikan' => $penarikan,
            'printDate' => Carbon::now()->format('d/m/Y H:i:s'),
        ]);
        
        return $pdf->download('withdrawal-receipt-' . $penarikan->id . '.pdf');
    }
    
    /**
     * Get report summary statistics
     */
    public function getSummary(Request $request)
    {
        $period = $request->get('period', 'monthly'); // daily, weekly, monthly, yearly
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        $summary = [];
        
        switch ($period) {
            case 'daily':
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                
                for ($day = 1; $day <= $endDate->day; $day++) {
                    $date = Carbon::create($year, $month, $day);
                    $dateStr = $date->format('Y-m-d');
                    
                    $summary['labels'][] = $date->format('d M');
                    $summary['transaksi'][] = Transaksi::whereDate('created_at', $dateStr)->count();
                    $summary['penarikan'][] = PenarikanPoin::whereDate('created_at', $dateStr)->count();
                    $summary['pendapatan'][] = Transaksi::whereDate('created_at', $dateStr)->sum('total_harga');
                }
                break;
                
            case 'weekly':
                // Last 8 weeks
                for ($week = 7; $week >= 0; $week--) {
                    $startWeek = Carbon::now()->subWeeks($week)->startOfWeek();
                    $endWeek = Carbon::now()->subWeeks($week)->endOfWeek();
                    
                    $summary['labels'][] = 'W' . $startWeek->weekOfYear;
                    $summary['transaksi'][] = Transaksi::whereBetween('created_at', [$startWeek, $endWeek])->count();
                    $summary['penarikan'][] = PenarikanPoin::whereBetween('created_at', [$startWeek, $endWeek])->count();
                    $summary['pendapatan'][] = Transaksi::whereBetween('created_at', [$startWeek, $endWeek])->sum('total_harga');
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
                    $summary['penarikan'][] = PenarikanPoin::whereBetween('created_at', [$startMonth, $endMonth])->count();
                    $summary['pendapatan'][] = Transaksi::whereBetween('created_at', [$startMonth, $endMonth])->sum('total_harga');
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
                    $summary['penarikan'][] = PenarikanPoin::whereBetween('created_at', [$startYear, $endYear])->count();
                    $summary['pendapatan'][] = Transaksi::whereBetween('created_at', [$startYear, $endYear])->sum('total_harga');
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
                'harga' => Transaksi::whereDate('created_at', today())->sum('total_harga'),
                'poin' => Transaksi::whereDate('created_at', today())->sum('total_poin'),
            ],
            'monthly' => [
                'count' => Transaksi::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'berat' => Transaksi::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->sum('total_berat'),
                'harga' => Transaksi::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->sum('total_harga'),
                'poin' => Transaksi::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->sum('total_poin'),
            ],
            'yearly' => [
                'count' => Transaksi::whereYear('created_at', now()->year)->count(),
                'berat' => Transaksi::whereYear('created_at', now()->year)->sum('total_berat'),
                'harga' => Transaksi::whereYear('created_at', now()->year)->sum('total_harga'),
                'poin' => Transaksi::whereYear('created_at', now()->year)->sum('total_poin'),
            ],
            'status' => [
                'completed' => Transaksi::where('status', 'completed')->count(),
                'pending' => Transaksi::where('status', 'pending')->count(),
                'cancelled' => Transaksi::where('status', 'cancelled')->count(),
            ]
        ];
        
        return response()->json($stats);
    }
    
    /**
     * API for withdrawal statistics
     */
    public function apiPenarikanStats()
    {
        $stats = [
            'today' => [
                'count' => PenarikanPoin::whereDate('created_at', today())->count(),
                'poin' => PenarikanPoin::whereDate('created_at', today())->sum('jumlah_poin'),
                'rupiah' => PenarikanPoin::whereDate('created_at', today())->sum('jumlah_rupiah'),
            ],
            'monthly' => [
                'count' => PenarikanPoin::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'poin' => PenarikanPoin::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->sum('jumlah_poin'),
                'rupiah' => PenarikanPoin::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->sum('jumlah_rupiah'),
            ],
            'yearly' => [
                'count' => PenarikanPoin::whereYear('created_at', now()->year)->count(),
                'poin' => PenarikanPoin::whereYear('created_at', now()->year)->sum('jumlah_poin'),
                'rupiah' => PenarikanPoin::whereYear('created_at', now()->year)->sum('jumlah_rupiah'),
            ],
            'status' => [
                'pending' => PenarikanPoin::where('status', 'pending')->count(),
                'approved' => PenarikanPoin::where('status', 'approved')->count(),
                'completed' => PenarikanPoin::where('status', 'completed')->count(),
                'rejected' => PenarikanPoin::where('status', 'rejected')->count(),
            ]
        ];
        
        return response()->json($stats);
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
                'avg' => User::avg('total_points'),
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
        
        return response()->json($stats);
    }
    
    /**
     * API for kategori statistics
     */
    public function apiKategoriStats()
    {
        $stats = [
            'total' => KategoriSampah::count(),
            'active' => KategoriSampah::where('status', 'active')->count(),
            'inactive' => KategoriSampah::where('status', 'inactive')->count(),
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
        
        return response()->json($stats);
    }
}