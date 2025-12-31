<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
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
    
    // Process scan dari QR Code
    public function processScan(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $qrData = $request->input('qr_data');
        $userId = $request->input('user_id');
        
        try {
            // Jika ada QR data, parse JSON
            if ($qrData) {
                $data = json_decode($qrData, true);
                $userId = $data['user_id'] ?? $userId;
            }
            
            // Cari user
            $warga = User::where('id', $userId)
                ->where('role_id', 3) // Pastikan hanya warga
                ->first();
            
            if (!$warga) {
                return response()->json(['error' => 'Warga tidak ditemukan'], 404);
            }
            
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $warga->id,
                    'name' => $warga->name,
                    'email' => $warga->email,
                    'phone' => $warga->phone,
                    'address' => $warga->address,
                    'total_points' => $warga->total_points,
                    'qr_code' => $warga->qr_code
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
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
        $qrCode = QrCode::format('png')
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
    
    // API untuk scan
    public function apiProcessScan(Request $request)
    {
        return $this->processScan($request);
    }
}