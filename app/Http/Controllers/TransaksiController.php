<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\KategoriSampah;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'warga_id' => 'required|exists:users,id',
            'berat_organik' => 'nullable|numeric|min:0',
            'berat_anorganik' => 'nullable|numeric|min:0',
            'berat_b3' => 'nullable|numeric|min:0',
            'berat_campuran' => 'nullable|numeric|min:0',
        ]);
        
        // Mulai database transaction
        DB::beginTransaction();
        
        try {
            // 1. Hitung total berat
            $totalBerat = $request->berat_organik + $request->berat_anorganik + 
                         $request->berat_b3 + $request->berat_campuran;
            
            // 2. Buat transaksi
            $transaksi = Transaksi::create([
                'warga_id' => $request->warga_id,
                'petugas_id' => Auth::id(),
                'tgl_transaksi' => now(),
                'status' => 'menunggu_konfirmasi',
                'total_berat_kg' => $totalBerat,
            ]);
            
            // 3. Simpan detail transaksi
            $kategoris = KategoriSampah::all();
            $totalPoin = 0;
            
            foreach ($kategoris as $kategori) {
                $namaKategori = strtolower($kategori->nama_kategori);
                $berat = $request->input("berat_{$namaKategori}", 0);
                
                if ($berat > 0) {
                    $subtotalPoin = $berat * $kategori->poin_per_kg;
                    
                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'kategori_id' => $kategori->id,
                        'berat' => $berat,
                        'subtotal_poin' => $subtotalPoin,
                    ]);
                    
                    $totalPoin += $subtotalPoin;
                }
            }
            
            // 4. Buat notifikasi untuk warga
            Notifikasi::create([
                'user_id' => $request->warga_id,
                'judul' => 'Transaksi Sampah Baru',
                'pesan' => "Transaksi sampah seberat {$totalBerat} kg telah dicatat. Total poin: {$totalPoin}. Silahkan konfirmasi transaksi.",
                'is_read' => false,
            ]);
            
            // Commit transaction
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => [
                    'transaksi_id' => $transaksi->id,
                    'total_berat' => $totalBerat,
                    'total_poin' => $totalPoin,
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function confirm($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        
        // Cek jika transaksi milik user yang login
        if ($transaksi->warga_id != Auth::id()) {
            abort(403);
        }
        
        DB::beginTransaction();
        
        try {
            // Update status transaksi
            $transaksi->status = 'selesai';
            $transaksi->save();
            
            // Tambahkan poin ke user
            $user = User::find($transaksi->warga_id);
            $totalPoin = $transaksi->total_poin;
            $user->total_poin += $totalPoin;
            $user->save();
            
            // Buat notifikasi konfirmasi
            Notifikasi::create([
                'user_id' => $transaksi->warga_id,
                'judul' => 'Transaksi Dikonfirmasi',
                'pesan' => "Transaksi #{$transaksi->id} telah dikonfirmasi. Anda mendapatkan {$totalPoin} poin.",
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dikonfirmasi',
                'poin_tambahan' => $totalPoin,
                'total_poin_sekarang' => $user->total_poin,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi transaksi'
            ], 500);
        }
    }
    
    public function history(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);
        
        $transaksis = Transaksi::where('warga_id', $user->id)
            ->orWhere('petugas_id', $user->id)
            ->with(['warga', 'petugas', 'detailTransaksi.kategori'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
            
        return response()->json([
            'success' => true,
            'data' => $transaksis,
        ]);
    }
}