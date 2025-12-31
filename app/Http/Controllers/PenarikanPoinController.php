<?php

namespace App\Http\Controllers;

use App\Models\PenarikanPoin;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenarikanPoinController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $penarikan = PenarikanPoin::where('warga_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('warga.penarikan.index', compact('penarikan'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'jumlah_poin' => 'required|integer|min:100',
        ]);
        
        $user = Auth::user();
        
        // Cek jika poin mencukupi
        if ($user->total_poin < $request->jumlah_poin) {
            return redirect()->back()->withErrors([
                'jumlah_poin' => 'Poin tidak mencukupi. Poin anda: ' . $user->total_poin
            ]);
        }
        
        DB::beginTransaction();
        
        try {
            // Hitung nominal rupiah (1 poin = Rp 100)
            $nominal = $request->jumlah_poin * 100;
            
            // Buat penarikan
            $penarikan = PenarikanPoin::create([
                'warga_id' => $user->id,
                'jumlah_poin' => $request->jumlah_poin,
                'nominal_rupiah' => $nominal,
                'status_pengajuan' => 'pending',
            ]);
            
            // Kurangi poin user
            DB::table('users')
                ->where('id', $user->id)
                ->decrement('total_poin', $request->jumlah_poin);
            
            // Buat notifikasi
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Pengajuan Penarikan Poin',
                'pesan' => "Pengajuan penarikan {$request->jumlah_poin} poin (Rp {$nominal}) telah diajukan.",
                'is_read' => false,
            ]);
            
            // Notifikasi untuk admin (user dengan role admin)
            $admin = User::where('role_id', 1)->first();
            if ($admin) {
                Notifikasi::create([
                    'user_id' => $admin->id,
                    'judul' => 'Pengajuan Penarikan Baru',
                    'pesan' => "Pengajuan penarikan dari {$user->nama_lengkap} sebesar {$request->jumlah_poin} poin.",
                    'is_read' => false,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('warga.penarikan.index')
                ->with('success', 'Pengajuan penarikan berhasil diajukan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengajukan penarikan: ' . $e->getMessage()]);
        }
    }
    
    public function adminIndex()
    {
        $penarikan = PenarikanPoin::with('warga')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.penarikan.index', compact('penarikan'));
    }
    
    public function approve($id)
    {
        $penarikan = PenarikanPoin::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Update status menggunakan DB query
            DB::table('penarikan_poin')
                ->where('id', $id)
                ->update(['status_pengajuan' => 'disetujui']);
            
            // Buat notifikasi untuk warga
            Notifikasi::create([
                'user_id' => $penarikan->warga_id,
                'judul' => 'Penarikan Poin Disetujui',
                'pesan' => "Penarikan poin sebesar {$penarikan->jumlah_poin} poin (Rp {$penarikan->nominal_rupiah}) telah disetujui.",
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.penarikan.index')
                ->with('success', 'Penarikan poin berhasil disetujui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyetujui penarikan: ' . $e->getMessage()]);
        }
    }
    
    public function reject($id)
    {
        $penarikan = PenarikanPoin::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Kembalikan poin ke user
            DB::table('users')
                ->where('id', $penarikan->warga_id)
                ->increment('total_poin', $penarikan->jumlah_poin);
            
            // Update status menggunakan DB query
            DB::table('penarikan_poin')
                ->where('id', $id)
                ->update(['status_pengajuan' => 'ditolak']);
            
            // Buat notifikasi untuk warga
            Notifikasi::create([
                'user_id' => $penarikan->warga_id,
                'judul' => 'Penarikan Poin Ditolak',
                'pesan' => "Penarikan poin sebesar {$penarikan->jumlah_poin} poin telah ditolak. Poin telah dikembalikan.",
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.penarikan.index')
                ->with('success', 'Penarikan poin berhasil ditolak.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menolak penarikan: ' . $e->getMessage()]);
        }
    }
}