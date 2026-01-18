<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // <-- Tambahkan ini

class QrCodeController extends Controller
{
    // API Process Scan - method utama
    public function processScan(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }
        
        $qrData = $request->input('qr_data');
        $userId = $request->input('user_id');
        
        Log::info('Scan Request:', [
            'qr_data' => $qrData,
            'user_id' => $userId,
            'petugas' => $user->name
        ]);
        
        try {
            // Priority: Jika ada user_id langsung, gunakan itu
            if (!$userId && $qrData) {
                // Coba parse QR data
                if (is_string($qrData) && strpos($qrData, '{') === 0) {
                    $data = json_decode($qrData, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($data['user_id'])) {
                        $userId = $data['user_id'];
                        Log::info('Parsed JSON QR data:', $data);
                    }
                }
                
                // Jika masih belum dapat, coba format lain
                if (!$userId && strpos($qrData, 'user:') === 0) {
                    $parts = explode(':', $qrData);
                    if (count($parts) >= 2 && is_numeric($parts[1])) {
                        $userId = $parts[1];
                        Log::info('Parsed user: format, user_id: ' . $userId);
                    }
                }
            }
            
            // Validasi userId
            if (!$userId || !is_numeric($userId)) {
                Log::warning('Invalid user_id: ' . $userId);
                return response()->json([
                    'success' => false,
                    'error' => 'User ID tidak valid dalam QR code'
                ], 400);
            }
            
            // Cari user dengan role warga - PERBAIKAN: Hapus where('role_id', 3) sementara
            $warga = User::find($userId);
            
            if (!$warga) {
                Log::warning('User not found: ' . $userId);
                return response()->json([
                    'success' => false,
                    'error' => 'Warga tidak ditemukan'
                ], 404);
            }
            
            // Verifikasi bahwa user adalah warga
            if ($warga->role_id != 3) {
                Log::warning('User is not warga, role_id: ' . $warga->role_id);
                return response()->json([
                    'success' => false,
                    'error' => 'User bukan warga. Role: ' . ($warga->role->name ?? 'Unknown')
                ], 400);
            }
            
            // Hitung total transaksi
            $totalTransactions = $warga->transaksiSebagaiWarga()->count();
            
            Log::info('Warga found:', [
                'id' => $warga->id,
                'name' => $warga->name,
                'role' => $warga->role->name ?? 'Unknown'
            ]);
            
            // Data warga ditemukan
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $warga->id,
                    'name' => $warga->name,
                    'email' => $warga->email,
                    'phone' => $warga->phone,
                    'address' => $warga->address,
                    'total_points' => $warga->total_points ?? 0,
                    'profile_photo_url' => $warga->profile_photo_url ?? null,
                    'qr_code' => $warga->qr_code ?? null,
                    'total_transactions' => $totalTransactions
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('QR Scan Error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Tampilkan scanner untuk petugas
    public function scan()
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }
        
        return view('petugas.scan');
    }
    
    // Tampilkan QR Code untuk warga
    public function show()
    {
        $user = Auth::user();
        
        if ($user->role_id !== 3) {
            abort(403, 'Hanya warga yang dapat mengakses halaman ini');
        }
        
        // Generate QR Code jika belum ada
        if (!$user->qr_code) {
            $this->generateQrCode($user);
        }
        
        return view('warga.qrcode', compact('user'));
    }
    
    // Download QR Code
    public function download()
    {
        $user = Auth::user();
        
        if (!$user->qr_code) {
            return back()->with('error', 'QR Code belum tersedia');
        }
        
        $path = storage_path('app/public/' . $user->qr_code);
        
        if (!file_exists($path)) {
            $this->generateQrCode($user);
        }
        
        return response()->download($path, 'qrcode-' . $user->name . '.png');
    }
    
    // Generate QR Code
    private function generateQrCode($user)
    {
        $qrData = json_encode([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'timestamp' => now()->timestamp
        ]);
        
        // Generate QR Code
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($qrData);
        
        // Simpan ke storage
        $fileName = 'qrcodes/user-' . $user->id . '-' . time() . '.png';
        Storage::disk('public')->put($fileName, $qrCode);
        
        // Update user
        $user->qr_code = $fileName;
        $user->save();
        
        return $fileName;
    }
    
    // Search by Email (optional - bisa dihapus jika tidak digunakan)
    public function searchByEmail(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }
        
        $request->validate([
            'email' => 'required|email'
        ]);
        
        try {
            // AMBIL LANGSUNG DARI DATABASE
            $warga = User::where('email', $request->email)
                ->where('role_id', 3) // Pastikan hanya warga
                ->first();
            
            if (!$warga) {
                return response()->json([
                    'success' => false,
                    'error' => 'Warga tidak ditemukan'
                ], 404);
            }
            
            // Data warga ditemukan dari database
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $warga->id,
                    'name' => $warga->name,
                    'email' => $warga->email,
                    'phone' => $warga->phone,
                    'address' => $warga->address,
                    'total_points' => $warga->total_points ?? 0,
                    'profile_photo_url' => $warga->profile_photo_url ?? null,
                    'qr_code' => $warga->qr_code ?? null,
                    'created_at' => $warga->created_at->format('d-m-Y'),
                    'total_transactions' => $warga->transaksiSebagaiWarga()->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Search Email Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // API Process Scan - alias untuk compatibility
    public function apiProcessScan(Request $request)
    {
        return $this->processScan($request);
    }
}