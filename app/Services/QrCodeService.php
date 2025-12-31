<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    private $size;
    
    public function __construct()
    {
        $this->size = config('app.qr_code_size', 300);
    }
    
    public function generateForUser($userId, $token, $userName)
    {
        $data = json_encode([
            'user_id' => $userId,
            'token' => $token,
            'nama' => $userName,
            'timestamp' => now()->timestamp,
            'app' => 'NetraTrash',
        ]);
        
        return QrCode::format('png')
            ->size($this->size)
            ->backgroundColor(255, 255, 255)
            ->color(0, 0, 0)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($data);
    }
    
    public function generateForTransaction($transactionId, $totalPoin)
    {
        $data = json_encode([
            'transaction_id' => $transactionId,
            'total_poin' => $totalPoin,
            'timestamp' => now()->timestamp,
        ]);
        
        return QrCode::format('png')
            ->size(200)
            ->generate($data);
    }
}