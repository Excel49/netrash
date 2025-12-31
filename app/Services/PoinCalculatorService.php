<?php

namespace App\Services;

use App\Models\KategoriSampah;

class PoinCalculatorService
{
    public function calculate($beratOrganik, $beratAnorganik, $beratB3, $beratCampuran)
    {
        $kategoris = KategoriSampah::all();
        $poinPerKg = [];
        
        foreach ($kategoris as $kategori) {
            $poinPerKg[strtolower($kategori->nama_kategori)] = $kategori->poin_per_kg;
        }
        
        $poinOrganik = $beratOrganik * ($poinPerKg['organik'] ?? env('POIN_PER_KG_ORGANIK', 10));
        $poinAnorganik = $beratAnorganik * ($poinPerKg['anorganik'] ?? env('POIN_PER_KG_ANORGANIK', 15));
        $poinB3 = $beratB3 * ($poinPerKg['b3'] ?? env('POIN_PER_KG_B3', 20));
        $poinCampuran = $beratCampuran * ($poinPerKg['campuran'] ?? env('POIN_PER_KG_CAMPURAN', 5));
        
        return [
            'total_poin' => $poinOrganik + $poinAnorganik + $poinB3 + $poinCampuran,
            'detail' => [
                'organik' => $poinOrganik,
                'anorganik' => $poinAnorganik,
                'b3' => $poinB3,
                'campuran' => $poinCampuran,
            ]
        ];
    }
    
    public function calculateRupiah($jumlahPoin)
    {
        // Konversi poin ke rupiah (contoh: 1 poin = Rp 100)
        $konversi = 100;
        return $jumlahPoin * $konversi;
    }
}