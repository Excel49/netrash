<?php

namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Barang;

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
        $user = auth()->user();
        $history = $user->transaksiSebagaiWarga()
            ->with('detailTransaksi.kategori')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('warga.poin.history', compact('user', 'history'));
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

    public function poinIndex()
    {
        $user = auth()->user();
        
        // Statistik dasar
        $totalPoin = $user->total_points ?? 0;
        
        // Hitung poin masuk (dari transaksi sampah, bukan penukaran)
        $transaksiMasuk = $user->transaksiSebagaiWarga()
            ->where(function($query) {
                $query->where('jenis_transaksi', '!=', 'penukaran')
                    ->orWhereNull('jenis_transaksi');
            })
            ->get();
        
        $totalPoinMasuk = $transaksiMasuk->sum('total_poin');
        $transaksiHariIni = $transaksiMasuk->where('created_at', '>=', today())->sum('total_poin');
        $transaksiBulanIni = $transaksiMasuk->where('created_at', '>=', now()->startOfMonth())->sum('total_poin');
        
        // Hitung poin keluar (untuk penukaran)
        $penukaran = $user->transaksiSebagaiWarga()
            ->where('jenis_transaksi', 'penukaran')
            ->get();
        
        $totalPoinKeluar = abs($penukaran->sum('total_poin'));
        $totalPenukaran = $penukaran->count();
        
        // Data untuk tabel transaksi
        $transaksi = $transaksiMasuk
            ->sortByDesc('created_at')
            ->take(5);
        
        // Data terbaru untuk sidebar
        $recentTransaksi = $transaksiMasuk
            ->sortByDesc('created_at')
            ->take(3);
        
        $recentPenukaran = $penukaran
            ->sortByDesc('created_at')
            ->take(3);
        
        return view('warga.poin.index', compact(
            'user',
            'totalPoin',
            'totalPoinMasuk',
            'totalPoinKeluar',
            'totalPenukaran',
            'transaksiHariIni',
            'transaksiBulanIni',
            'transaksi',
            'recentTransaksi',
            'recentPenukaran'
        ));
    }

 // app/Http/Controllers/WargaController.php - barangIndex() method

    public function barangIndex(Request $request)
    {
        // Query barang yang aktif saja
        $query = Barang::where('status', true);
        
        // Filter pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        
        // Get barang dengan pagination
        $barang = $query->orderBy('created_at', 'desc')->paginate(12);
        
        // Untuk filter kategori
        $kategoriList = Barang::whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->pluck('kategori', 'kategori');
        
        // Ambil data riwayat penukaran user
        $user = auth()->user();
        $penukaran = Transaksi::where('warga_id', $user->id)
            ->where('jenis_transaksi', 'penukaran')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'penukaran_page');
        
        // Hitung statistik
        $totalPenukaran = Transaksi::where('warga_id', $user->id)
            ->where('jenis_transaksi', 'penukaran')
            ->count();
        
        $totalPoinKeluar = abs(Transaksi::where('warga_id', $user->id)
            ->where('jenis_transaksi', 'penukaran')
            ->sum('total_poin'));
        
        return view('warga.barang.index', compact(
            'barang', 
            'kategoriList',
            'penukaran',
            'totalPenukaran',
            'totalPoinKeluar'
        ));
    }

    // app/Http/Controllers/WargaController.php

    public function penukaranHistory(Request $request)
    {
        $user = auth()->user();
        
        $query = Transaksi::with('petugas')
            ->where('warga_id', $user->id)
            ->where('jenis_transaksi', 'penukaran');
        
        // Filter tanggal
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }
        
        $penukaran = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Hitung statistik penukaran
        $totalPenukaran = $penukaran->total();
        $totalPoinDikeluarkan = abs($penukaran->sum('total_poin'));
        
        return view('warga.penukaran.history', compact(
            'penukaran', 
            'user',
            'totalPenukaran',
            'totalPoinDikeluarkan'
        ));
    }

    public function barangShow($id)
    {
        $barang = Barang::aktif()->findOrFail($id);
        return view('warga.barang.show', compact('barang'));
    }

    public function barangByKategori($kategori)
    {
        $barang = Barang::aktif()
            ->where('kategori', $kategori)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('warga.barang.index', compact('barang', 'kategori'));
    }

    public function barangSearch(Request $request)
    {
        $search = $request->get('q');
        
        $barang = Barang::aktif()
            ->where('nama_barang', 'like', "%{$search}%")
            ->orWhere('deskripsi', 'like', "%{$search}%")
            ->orWhere('kategori', 'like', "%{$search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('warga.barang.index', compact('barang', 'search'));
    }
}