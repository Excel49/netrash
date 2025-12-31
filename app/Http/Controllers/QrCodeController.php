<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class QrCodeController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Generate QR Code data
        $qrData = json_encode([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
        
        $qrCode = QrCode::size(300)->generate($qrData);
        
        return view('warga.qrcode', compact('qrCode', 'user'));
    }
    
    public function scan()
    {
        return view('petugas.scan');
    }
    
    public function scanResult(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);
        
        $qrData = json_decode($request->qr_data, true);
        
        if (!$qrData || !isset($qrData['user_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid'
            ]);
        }
        
        // Cari user berdasarkan ID
        $user = \App\Models\User::find($qrData['user_id']);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }
        
        if (!$user->isWarga()) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code bukan milik warga'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'total_points' => $user->total_points,
                'phone' => $user->phone,
                'address' => $user->address,
            ]
        ]);
    }
}