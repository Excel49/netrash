<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenukaranController extends Controller
{
    /**
     * Show form untuk penukaran barang
     */
    public function create(Request $request)
    {
        $barangId = $request->query('barang_id');
        $barang = Barang::aktif()->findOrFail($barangId);
        
        return view('warga.penukaran.create', compact('barang'));
    }
    
    /**
     * Proses penukaran barang
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
        ]);
        
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            $barang = Barang::findOrFail($request->barang_id);
            $jumlah = $request->jumlah;
            
            // Cek stok
            if ($barang->stok < $jumlah) {
                return back()->with('error', 'Stok barang tidak mencukupi')->withInput();
            }
            
            // Cek poin user
            $totalPoin = $barang->harga_poin * $jumlah;
            if ($user->total_points < $totalPoin) {
                return back()->with('error', 'Poin Anda tidak cukup')->withInput();
            }
            
            // TIDAK kurangi stok dan poin dulu (pending)
            // Tunggu admin approve
            
            // Generate kode transaksi
            $kodeTransaksi = 'TUKAR-' . date('Ymd') . '-' . str_pad(Transaksi::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
            
            // Buat transaksi penukaran dengan status pending
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'warga_id' => $user->id,
                'petugas_id' => null,
                'total_berat' => 0,
                'total_harga' => 0,
                'total_poin' => -$totalPoin,
                'status' => 'pending', // Status transaksi pending
                'status_penukaran' => 'pending', // Status penukaran pending
                'jenis_transaksi' => 'penukaran',
                'catatan' => 'Penukaran: ' . $barang->nama_barang . ' x' . $jumlah,
                'tanggal_transaksi' => now(),
            ]);
            
            // Buat notifikasi untuk warga
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Pengajuan Penukaran',
                'pesan' => "Pengajuan penukaran {$barang->nama_barang} x{$jumlah} telah dikirim. Menunggu persetujuan admin.",
                'tipe' => 'info',
                'link' => '/warga/transaksi',
            ]);
            
            // Buat notifikasi untuk admin
            $admins = User::where('role_id', 1)->get();
            foreach ($admins as $admin) {
                Notifikasi::create([
                    'user_id' => $admin->id,
                    'judul' => 'Pengajuan Penukaran Baru',
                    'pesan' => "Warga {$user->name} mengajukan penukaran {$barang->nama_barang} x{$jumlah}",
                    'tipe' => 'warning',
                    'link' => '/admin/penukaran/pending',
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('warga.transaksi.show', $transaksi->id)
                ->with('success', 'Pengajuan penukaran berhasil dikirim! Menunggu persetujuan admin.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}