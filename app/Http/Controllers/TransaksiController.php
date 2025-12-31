<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\KategoriSampah;
use App\Models\User;
use App\Models\DetailTransaksi;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // Index untuk petugas
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }
        
        $petugasId = Auth::id();
        $transaksi = Transaksi::with(['warga', 'petugas'])
            ->where('petugas_id', $petugasId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('petugas.transaksi.index', compact('transaksi'));
    }
    
    // Create transaksi baru
    public function create(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }
        
        $wargaId = $request->query('warga_id');
        $warga = null;
        
        if ($wargaId) {
            $warga = User::where('id', $wargaId)
                ->where('role_id', 3)
                ->first();
        }
        
        $kategori = KategoriSampah::where('status', true)->get();
        
        return view('petugas.transaksi.create', compact('warga', 'kategori'));
    }
    
    // Store transaksi baru
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }
        
        $request->validate([
            'warga_id' => 'required|exists:users,id',
            'kategori' => 'required|array',
            'kategori.*.id' => 'required|exists:kategori_sampah,id',
            'kategori.*.berat' => 'required|numeric|min:0.1',
            'catatan' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $petugasId = Auth::id();
            $wargaId = $request->warga_id;
            
            // Generate kode transaksi
            $kodeTransaksi = 'TRX-' . date('Ymd') . '-' . str_pad(Transaksi::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
            
            // Hitung total
            $totalBerat = 0;
            $totalHarga = 0;
            $totalPoin = 0;
            $details = [];
            
            foreach ($request->kategori as $item) {
                $kategori = KategoriSampah::find($item['id']);
                $berat = floatval($item['berat']);
                
                $harga = $berat * $kategori->harga_per_kg;
                $poin = $berat * $kategori->poin_per_kg;
                
                $totalBerat += $berat;
                $totalHarga += $harga;
                $totalPoin += $poin;
                
                $details[] = [
                    'kategori_id' => $kategori->id,
                    'berat' => $berat,
                    'harga' => $harga,
                    'poin' => $poin,
                ];
            }
            
            // Buat transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'warga_id' => $wargaId,
                'petugas_id' => $petugasId,
                'total_berat' => $totalBerat,
                'total_harga' => $totalHarga,
                'total_poin' => $totalPoin,
                'status' => 'completed',
                'catatan' => $request->catatan,
                'tanggal_transaksi' => now(),
            ]);
            
            // Buat detail transaksi
            foreach ($details as $detail) {
                $detail['transaksi_id'] = $transaksi->id;
                DetailTransaksi::create($detail);
            }
            
            // Update poin warga
            $warga = User::find($wargaId);
            $warga->total_points += $totalPoin;
            $warga->save();
            
            // Buat notifikasi untuk warga
            Notifikasi::create([
                'user_id' => $wargaId,
                'judul' => 'Transaksi Berhasil',
                'pesan' => "Transaksi {$kodeTransaksi} berhasil. Anda mendapatkan {$totalPoin} poin.",
                'tipe' => 'success',
                'link' => '/warga/transaksi',
            ]);
            
            DB::commit();
            
            return redirect()->route('petugas.transaksi.show', $transaksi->id)
                ->with('success', 'Transaksi berhasil disimpan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    // Show detail transaksi untuk petugas
    public function show($id)
    {
        $transaksi = Transaksi::with(['warga', 'petugas', 'detailTransaksi.kategori'])
            ->findOrFail($id);
        $user = Auth::user();
            
        // Cek akses
        if ($user->role_id === 2 && $transaksi->petugas_id != $user->id) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini');
        }
        
        return view('petugas.transaksi.show', compact('transaksi'));
    }
    
    // Print transaksi
    public function print($id)
    {
        $transaksi = Transaksi::with(['warga', 'petugas', 'detailTransaksi.kategori'])
            ->findOrFail($id);
            
        return view('petugas.transaksi.print', compact('transaksi'));
    }
    
    // Index untuk warga
    public function wargaIndex()
    {
        $user = Auth::user();
        
        if ($user->role_id !== 3) {
            abort(403, 'Hanya warga yang dapat mengakses halaman ini');
        }
        
        $wargaId = Auth::id();
        $transaksi = Transaksi::with('petugas')
            ->where('warga_id', $wargaId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('warga.transaksi.index', compact('transaksi'));
    }
    
    // Show detail untuk warga
    public function wargaShow($id)
    {
        $transaksi = Transaksi::with(['petugas', 'detailTransaksi.kategori'])
            ->findOrFail($id);
        $user = Auth::user();
            
        // Cek akses
        if ($user->role_id === 3 && $transaksi->warga_id != $user->id) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini');
        }
        
        return view('warga.transaksi.show', compact('transaksi'));
    }
    
    // Index untuk admin
    public function adminIndex()
    {
        $user = Auth::user();
        
        if ($user->role_id !== 1) {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini');
        }
        
        $transaksi = Transaksi::with(['warga', 'petugas'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.transaksi.index', compact('transaksi'));
    }
    
    // API untuk hitung otomatis
    public function apiCalculate(Request $request)
    {
        try {
            $kategoriId = $request->kategori_id;
            $berat = floatval($request->berat);
            
            $kategori = KategoriSampah::findOrFail($kategoriId);
            
            $harga = $berat * $kategori->harga_per_kg;
            $poin = $berat * $kategori->poin_per_kg;
            
            return response()->json([
                'success' => true,
                'harga' => $harga,
                'poin' => $poin,
                'harga_formatted' => 'Rp ' . number_format($harga, 0, ',', '.'),
                'poin_formatted' => number_format($poin, 0, ',', '.') . ' poin',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // API untuk recent transactions
    public function apiRecent()
    {
        $user = Auth::user();
        
        if ($user->role_id === 2) {
            $transaksi = Transaksi::with('warga')
                ->where('petugas_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } else {
            $transaksi = Transaksi::with('petugas')
                ->where('warga_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'transactions' => $transaksi
        ]);
    }
}