<?php
// database/seeders/DetailTransaksiSeeder.php

namespace Database\Seeders;

use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use App\Models\KategoriSampah;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DetailTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil transaksi setoran yang sudah completed
        $transaksiSetoran = Transaksi::where('jenis_transaksi', 'setoran')
            ->where('status', 'completed')
            ->get();
        
        $kategoris = KategoriSampah::all();
        $detailData = [];
        
        foreach ($transaksiSetoran as $transaksi) {
            // Tentukan jumlah kategori dalam transaksi (1-4 kategori)
            $jumlahKategori = rand(1, min(4, $kategoris->count()));
            $kategoriDipilih = $kategoris->random($jumlahKategori);
            
            $totalBeratTransaksi = $transaksi->total_berat;
            
            // Bagi berat ke masing-masing kategori secara proporsional
            $beratPerKategori = [];
            $sisaBerat = $totalBeratTransaksi;
            
            // Generate pembagian berat yang realistis
            for ($j = 0; $j < $jumlahKategori; $j++) {
                if ($j === $jumlahKategori - 1) {
                    $beratPerKategori[] = round($sisaBerat, 2);
                } else {
                    // Berat antara 0.3kg sampai sisa berat
                    $maxBagian = $sisaBerat - (($jumlahKategori - $j - 1) * 0.1);
                    $bagian = round(rand(30, 70) / 100 * $maxBagian, 2);
                    if ($bagian < 0.1) $bagian = 0.1;
                    
                    $beratPerKategori[] = $bagian;
                    $sisaBerat -= $bagian;
                    if ($sisaBerat < 0) $sisaBerat = 0;
                }
            }
            
            // Hitung poin per kategori dan akumulasi total
            $totalPoinDihitung = 0;
            foreach ($kategoriDipilih as $index => $kategori) {
                $berat = $beratPerKategori[$index];
                $poinPerKg = $kategori->poin_per_kg;
                $poin = round($berat * $poinPerKg);
                $harga = $poin * 100; // 1 poin = Rp 100
                
                $detailData[] = [
                    'transaksi_id' => $transaksi->id,
                    'kategori_id' => $kategori->id,
                    'berat' => $berat,
                    'harga' => $harga,
                    'poin' => $poin,
                    'created_at' => $transaksi->created_at,
                    'updated_at' => $transaksi->updated_at,
                ];
                
                $totalPoinDihitung += $poin;
            }
            
            // Update total poin transaksi jika ada perbedaan
            if (abs($transaksi->total_poin - $totalPoinDihitung) > 1) {
                $transaksi->total_poin = $totalPoinDihitung;
                $transaksi->total_harga = $totalPoinDihitung * 100;
                $transaksi->save();
            }
        }
        
        // Insert data detail transaksi
        DetailTransaksi::insert($detailData);
        
        $this->command->info('Seeder Detail Transaksi berhasil dijalankan!');
        $this->command->info(count($detailData) . ' detail transaksi telah dibuat.');
    }
}