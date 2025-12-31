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
    // Index untuk warga
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->role_id !== 3) {
            abort(403, 'Hanya warga yang dapat mengakses halaman ini');
        }
        
        $wargaId = Auth::id();
        $penarikan = PenarikanPoin::where('warga_id', $wargaId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('warga.penarikan.index', compact('penarikan'));
    }
    
    // Create penarikan baru
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->role_id !== 3) {
            abort(403, 'Hanya warga yang dapat mengakses halaman ini');
        }
        
        return view('warga.penarikan.create', compact('user'));
    }
    
    // Store penarikan baru
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->role_id !== 3) {
            abort(403, 'Hanya warga yang dapat mengakses halaman ini');
        }
        
        $request->validate([
            'jumlah_poin' => 'required|integer|min:100',
            'alasan_penarikan' => 'required|string|max:500',
        ]);
        
        // Cek poin cukup
        if ($user->total_points < $request->jumlah_poin) {
            return back()->withErrors([
                'jumlah_poin' => 'Poin tidak cukup. Poin Anda: ' . number_format($user->total_points, 0, ',', '.')
            ])->withInput();
        }
        
        // Cek ada penarikan pending
        $pendingCount = PenarikanPoin::where('warga_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->count();
            
        if ($pendingCount > 0) {
            return back()->withErrors([
                'jumlah_poin' => 'Anda masih memiliki penarikan yang belum diproses'
            ])->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Kurangi poin sementara
            $user->total_points -= $request->jumlah_poin;
            $user->save();
            
            // Hitung rupiah (100 poin = Rp 10,000)
            $jumlahRupiah = $request->jumlah_poin * 100;
            
            // Buat penarikan
            $penarikan = PenarikanPoin::create([
                'warga_id' => $user->id,
                'jumlah_poin' => $request->jumlah_poin,
                'jumlah_rupiah' => $jumlahRupiah,
                'status' => 'pending',
                'alasan_penarikan' => $request->alasan_penarikan,
                'tanggal_pengajuan' => now(),
            ]);
            
            // Buat notifikasi untuk admin
            $admins = User::where('role_id', 1)->get();
            foreach ($admins as $admin) {
                Notifikasi::create([
                    'user_id' => $admin->id,
                    'judul' => 'Pengajuan Penarikan Poin Baru',
                    'pesan' => "{$user->name} mengajukan penarikan {$request->jumlah_poin} poin (Rp " . number_format($jumlahRupiah, 0, ',', '.') . ")",
                    'tipe' => 'warning',
                    'link' => '/admin/penarikan',
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('warga.penarikan.index')
                ->with('success', 'Pengajuan penarikan berhasil dikirim! Menunggu approval admin.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    // Show detail untuk warga
    public function show($id)
    {
        $penarikan = PenarikanPoin::with(['warga', 'admin'])->findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Cek akses
        if ($user->role_id === 3 && $penarikan->warga_id != $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }
        
        return view('warga.penarikan.show', compact('penarikan'));
    }
    
    // Cancel penarikan (warga)
    public function destroy($id)
    {
        $penarikan = PenarikanPoin::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Cek akses
        if ($penarikan->warga_id != $user->id) {
            abort(403, 'Anda tidak memiliki akses');
        }
        
        // Hanya bisa cancel jika status pending
        if ($penarikan->status != 'pending') {
            return back()->with('error', 'Penarikan tidak dapat dibatalkan karena status sudah ' . $penarikan->status);
        }
        
        DB::beginTransaction();
        
        try {
            /** @var \App\Models\User $warga */
            $warga = $penarikan->warga;
            $warga->total_points += $penarikan->jumlah_poin;
            $warga->save();
            
            // Hapus penarikan
            $penarikan->delete();
            
            DB::commit();
            
            return redirect()->route('warga.penarikan.index')
                ->with('success', 'Penarikan berhasil dibatalkan. Poin telah dikembalikan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    // ==================== ADMIN FUNCTIONS ====================
    
    // Index untuk admin
    public function adminIndex()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->role_id !== 1) {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini');
        }
        
        $penarikan = PenarikanPoin::with(['warga', 'admin'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'completed', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.penarikan.index', compact('penarikan'));
    }
    
    // Show detail untuk admin
    public function adminShow($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->role_id !== 1) {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini');
        }
        
        $penarikan = PenarikanPoin::with(['warga', 'admin'])->findOrFail($id);
        
        return view('admin.penarikan.show', compact('penarikan'));
    }
    
    // Approve penarikan
    public function approve(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->role_id !== 1) {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini');
        }
        
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);
        
        $penarikan = PenarikanPoin::findOrFail($id);
        
        if ($penarikan->status != 'pending') {
            return back()->with('error', 'Penarikan sudah diproses');
        }
        
        DB::beginTransaction();
        
        try {
            // Update penarikan
            $penarikan->status = 'approved';
            $penarikan->admin_id = $user->id;
            $penarikan->catatan_admin = $request->catatan_admin;
            $penarikan->tanggal_approval = now();
            $penarikan->save();
            
            // Buat notifikasi untuk warga
            Notifikasi::create([
                'user_id' => $penarikan->warga_id,
                'judul' => 'Penarikan Poin Disetujui',
                'pesan' => "Pengajuan penarikan {$penarikan->jumlah_poin} poin (Rp " . number_format($penarikan->jumlah_rupiah, 0, ',', '.') . ") telah disetujui.",
                'tipe' => 'success',
                'link' => '/warga/penarikan',
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Penarikan berhasil disetujui!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    // Reject penarikan
    public function reject(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->role_id !== 1) {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini');
        }
        
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ]);
        
        $penarikan = PenarikanPoin::findOrFail($id);
        
        if ($penarikan->status != 'pending') {
            return back()->with('error', 'Penarikan sudah diproses');
        }
        
        DB::beginTransaction();
        
        try {
            /** @var \App\Models\User $warga */
            $warga = $penarikan->warga;
            $warga->total_points += $penarikan->jumlah_poin;
            $warga->save();
            
            // Update penarikan
            $penarikan->status = 'rejected';
            $penarikan->admin_id = $user->id;
            $penarikan->catatan_admin = $request->catatan_admin;
            $penarikan->tanggal_approval = now();
            $penarikan->save();
            
            // Buat notifikasi untuk warga
            Notifikasi::create([
                'user_id' => $penarikan->warga_id,
                'judul' => 'Penarikan Poin Ditolak',
                'pesan' => "Pengajuan penarikan {$penarikan->jumlah_poin} poin ditolak. Alasan: {$request->catatan_admin}",
                'tipe' => 'error',
                'link' => '/warga/penarikan',
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Penarikan berhasil ditolak. Poin telah dikembalikan ke warga.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    // Complete penarikan (setelah transfer)
    public function complete(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->role_id !== 1) {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini');
        }
        
        $penarikan = PenarikanPoin::findOrFail($id);
        
        if ($penarikan->status != 'approved') {
            return back()->with('error', 'Hanya penarikan dengan status approved yang bisa diselesaikan');
        }
        
        $penarikan->status = 'completed';
        $penarikan->save();
        
        // Buat notifikasi untuk warga
        Notifikasi::create([
            'user_id' => $penarikan->warga_id,
            'judul' => 'Penarikan Poin Selesai',
            'pesan' => "Penarikan {$penarikan->jumlah_poin} poin (Rp " . number_format($penarikan->jumlah_rupiah, 0, ',', '.') . ") telah selesai dan dana telah ditransfer.",
            'tipe' => 'success',
            'link' => '/warga/penarikan',
        ]);
        
        return back()->with('success', 'Penarikan telah diselesaikan!');
    }
}