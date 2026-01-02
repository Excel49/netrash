<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\KategoriSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    public function statistik()
    {
        $petugasId = Auth::id();
        
        // Data statistik
        $stats = [
            'transaksi_hari_ini' => Transaksi::where('petugas_id', $petugasId)
                ->whereDate('created_at', today())
                ->count(),
            'transaksi_bulan_ini' => Transaksi::where('petugas_id', $petugasId)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'berat_hari_ini' => Transaksi::where('petugas_id', $petugasId)
                ->whereDate('created_at', today())
                ->sum('total_berat') ?? 0,
            'berat_bulan_ini' => Transaksi::where('petugas_id', $petugasId)
                ->whereMonth('created_at', now()->month)
                ->sum('total_berat') ?? 0,
            'poin_hari_ini' => Transaksi::where('petugas_id', $petugasId)
                ->whereDate('created_at', today())
                ->sum('total_poin') ?? 0,
            'poin_bulan_ini' => Transaksi::where('petugas_id', $petugasId)
                ->whereMonth('created_at', now()->month)
                ->sum('total_poin') ?? 0,
            'total_warga' => User::where('role_id', 3)->count(),
        ];
        
        // Top warga
        $topWarga = User::where('role_id', 3)
            ->whereHas('transaksiSebagaiWarga', function($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            })
            ->withCount(['transaksiSebagaiWarga as total_transactions' => function($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            }])
            ->orderBy('total_points', 'desc')
            ->take(5)
            ->get();
        
        return view('petugas.statistik.index', compact('stats', 'topWarga'));
    }
    
    public function dailyStats()
    {
        $petugasId = Auth::id();
        
        // Logika untuk statistik harian
        return view('petugas.statistik.daily', [
            'stats' => [
                'title' => 'Statistik Harian',
                // Tambahkan data harian di sini
            ]
        ]);
    }
    
    public function monthlyStats()
    {
        $petugasId = Auth::id();
        
        // Logika untuk statistik bulanan
        return view('petugas.statistik.monthly', [
            'stats' => [
                'title' => 'Statistik Bulanan',
                // Tambahkan data bulanan di sini
            ]
        ]);
    }
    
public function performance()
{
    $petugasId = Auth::id();
    
    // Hitung data performa
    $performanceData = [
        'rating' => 4.8,
        'total_ratings' => 24,
        'satisfaction_rate' => 92,
        'avg_daily_transactions' => 15.2,
        'rank' => 3,
        'total_petugas' => 12
    ];
    
    return view('petugas.statistik.performance', compact('performanceData'));
}
    
    public function topWarga()
    {
        $petugasId = Auth::id();
        
        // Logika untuk top warga
        return view('petugas.statistik.top-warga', [
            'stats' => [
                'title' => 'Top Warga',
                // Tambahkan data top warga di sini
            ]
        ]);
    }
      public function wargaIndex()
    {
        $petugasId = Auth::id();
        
        // Ambil warga yang pernah bertransaksi dengan petugas ini
        $warga = User::where('role_id', 3) // role warga
            ->whereHas('transaksiSebagaiWarga', function($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            })
            ->withCount(['transaksiSebagaiWarga as total_transactions' => function($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            }])
            ->orderBy('name')
            ->paginate(15);
        
        return view('petugas.warga.index', compact('warga'));
    }
    
    /**
     * Menampilkan form tambah warga (untuk petugas)
     */
    public function wargaCreate()
    {
        return view('petugas.warga.create');
    }
    
    /**
     * Menyimpan warga baru
     */
    public function wargaStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'nik' => 'nullable|string|max:20',
            'rt_rw' => 'nullable|string|max:10',
        ]);
        
        // Set role_id = 3 (warga) secara default
        $validated['role_id'] = 3;
        $validated['password'] = bcrypt('password123'); // Password default
        
        $user = User::create($validated);
        
        return redirect()->route('petugas.warga.index')
            ->with('success', 'Warga berhasil ditambahkan');
    }
    
    /**
     * Menampilkan detail warga
     */
    public function wargaShow(User $user)
    {
        // Pastikan user adalah warga
        if ($user->role_id != 3) {
            abort(404);
        }
        
        $petugasId = Auth::id();
        
        // Ambil transaksi warga dengan petugas ini
        $transactions = Transaksi::where('warga_id', $user->id)
            ->where('petugas_id', $petugasId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('petugas.warga.show', compact('user', 'transactions'));
    }
    
    /**
     * Menampilkan form edit warga
     */
    public function wargaEdit(User $user)
    {
        // Pastikan user adalah warga
        if ($user->role_id != 3) {
            abort(404);
        }
        
        return view('petugas.warga.edit', compact('user'));
    }
    
    /**
     * Update data warga
     */
    public function wargaUpdate(Request $request, User $user)
    {
        // Pastikan user adalah warga
        if ($user->role_id != 3) {
            abort(404);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'nik' => 'nullable|string|max:20',
            'rt_rw' => 'nullable|string|max:10',
        ]);
        
        $user->update($validated);
        
        return redirect()->route('petugas.warga.show', $user)
            ->with('success', 'Data warga berhasil diperbarui');
    }
    
    /**
     * Menampilkan transaksi warga
     */
    public function wargaTransaksi(User $user)
    {
        // Pastikan user adalah warga
        if ($user->role_id != 3) {
            abort(404);
        }
        
        $petugasId = Auth::id();
        
        $transactions = Transaksi::where('warga_id', $user->id)
            ->where('petugas_id', $petugasId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('petugas.warga.transaksi', compact('user', 'transactions'));
    }
    
    /**
     * Menampilkan poin warga
     */
    public function wargaPoints(User $user)
    {
        // Pastikan user adalah warga
        if ($user->role_id != 3) {
            abort(404);
        }
        
        return view('petugas.warga.points', compact('user'));
    }
    
    /**
     * Pencarian warga
     */
    public function wargaSearch(Request $request)
    {
        $search = $request->input('search');
        $petugasId = Auth::id();
        
        $warga = User::where('role_id', 3)
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('nik', 'like', "%$search%");
            })
            ->whereHas('transaksiSebagaiWarga', function($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            })
            ->orderBy('name')
            ->paginate(15);
        
        return view('petugas.warga.index', compact('warga', 'search'));
    }
    
    /**
     * Export data warga
     */
    public function wargaExport()
    {
        $petugasId = Auth::id();
        
        $warga = User::where('role_id', 3)
            ->whereHas('transaksiSebagaiWarga', function($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            })
            ->withCount(['transaksiSebagaiWarga as total_transactions' => function($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            }])
            ->orderBy('name')
            ->get();
        
        $filename = 'data-warga-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($warga) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['Nama', 'Email', 'Telepon', 'Alamat', 'NIK', 'RT/RW', 'Total Poin', 'Total Transaksi']);
            
            // Data
            foreach ($warga as $item) {
                fputcsv($file, [
                    $item->name,
                    $item->email,
                    $item->phone,
                    $item->address,
                    $item->nik,
                    $item->rt_rw,
                    $item->total_points,
                    $item->total_transactions
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
public function exportStats()
{
    $petugasId = Auth::id();
    
    // Ambil data untuk export
    $stats = $this->getStatsData($petugasId);
    
    // Buat file CSV sederhana
    $filename = 'statistik-petugas-' . Auth::user()->name . '-' . date('Y-m-d') . '.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];
    
    $callback = function() use ($stats) {
        $file = fopen('php://output', 'w');
        
        // Header CSV
        fputcsv($file, ['Statistik Petugas', 'Nilai']);
        
        // Data
        fputcsv($file, ['Transaksi Hari Ini', $stats['transaksi_hari_ini']]);
        fputcsv($file, ['Transaksi Bulan Ini', $stats['transaksi_bulan_ini']]);
        fputcsv($file, ['Berat Sampah Hari Ini (kg)', $stats['berat_hari_ini']]);
        fputcsv($file, ['Berat Sampah Bulan Ini (kg)', $stats['berat_bulan_ini']]);
        fputcsv($file, ['Poin Diberikan Hari Ini', $stats['poin_hari_ini']]);
        fputcsv($file, ['Poin Diberikan Bulan Ini', $stats['poin_bulan_ini']]);
        fputcsv($file, ['Tanggal Export', date('Y-m-d H:i:s')]);
        fputcsv($file, ['Petugas', Auth::user()->name]);
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}

private function getStatsData($petugasId)
{
    return [
        'transaksi_hari_ini' => Transaksi::where('petugas_id', $petugasId)
            ->whereDate('created_at', today())
            ->count(),
        'transaksi_bulan_ini' => Transaksi::where('petugas_id', $petugasId)
            ->whereMonth('created_at', now()->month)
            ->count(),
        'berat_hari_ini' => Transaksi::where('petugas_id', $petugasId)
            ->whereDate('created_at', today())
            ->sum('total_berat') ?? 0,
        'berat_bulan_ini' => Transaksi::where('petugas_id', $petugasId)
            ->whereMonth('created_at', now()->month)
            ->sum('total_berat') ?? 0,
        'poin_hari_ini' => Transaksi::where('petugas_id', $petugasId)
            ->whereDate('created_at', today())
            ->sum('total_poin') ?? 0,
        'poin_bulan_ini' => Transaksi::where('petugas_id', $petugasId)
            ->whereMonth('created_at', now()->month)
            ->sum('total_poin') ?? 0,
    ];
}
}