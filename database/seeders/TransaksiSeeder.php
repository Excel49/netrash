<?php
// database/seeders/TransaksiSeeder.php

namespace Database\Seeders;

use App\Models\Transaksi;
use App\Models\User;
use App\Models\KategoriSampah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil data petugas dan warga
        $petugas = User::where('role_id', 2)->get();
        $warga = User::where('role_id', 3)->get();
        $kategoris = KategoriSampah::all();
        
        $jenisTransaksi = ['setoran', 'penukaran', 'transfer'];
        $statusTransaksi = ['pending', 'completed', 'cancelled'];
        
        $transaksiData = [];
        $detailTransaksiData = [];
        
        // Data untuk grafik - buat transaksi dalam 3 bulan terakhir
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();
        
        // Untuk setiap warga, buat 10-15 transaksi
        foreach ($warga as $wargaItem) {
            // Tentukan berapa transaksi untuk warga ini (10-15 transaksi)
            $jumlahTransaksiWarga = rand(10, 15);
            
            for ($i = 1; $i <= $jumlahTransaksiWarga; $i++) {
                // Pilih jenis transaksi (70% setoran, 20% penukaran, 10% transfer)
                $randomPercent = rand(1, 100);
                if ($randomPercent <= 70) {
                    $jenis = 'setoran';
                } elseif ($randomPercent <= 90) {
                    $jenis = 'penukaran';
                } else {
                    $jenis = 'transfer';
                }
                
                // Tentukan status berdasarkan jenis transaksi
                if ($jenis === 'setoran') {
                    $status = (rand(1, 100) <= 85) ? 'completed' : (rand(1, 100) <= 95 ? 'pending' : 'cancelled');
                } else {
                    $status = $statusTransaksi[array_rand($statusTransaksi)];
                }
                
                // Tentukan petugas untuk transaksi setoran yang completed
                $petugasSelected = null;
                if ($jenis === 'setoran' && $status === 'completed') {
                    $petugasSelected = $petugas->random();
                }
                
                // Tentukan tanggal acak dalam 3 bulan terakhir (untuk grafik)
                $tanggal = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                );
                
                // Tentukan nilai transaksi berdasarkan jenis
                if ($jenis === 'setoran') {
                    $totalBerat = rand(5, 500) / 10; // 0.5 - 50 kg
                    $poinPerKg = $kategoris->random()->poin_per_kg;
                    $totalPoin = round($totalBerat * $poinPerKg);
                    $totalHarga = $totalPoin * 100; // 1 poin = Rp 100
                    
                    $catatanOptions = [
                        "Setoran sampah harian",
                        "Setoran sampah mingguan dari rumah tangga",
                        "Setoran plastik dan kertas",
                        "Setoran organik dari dapur",
                        "Setoran sampah anorganik",
                        "Setoran khusus B3 (baterai bekas)",
                        "Setoran campuran berbagai jenis"
                    ];
                    $catatan = $catatanOptions[array_rand($catatanOptions)];
                    
                } elseif ($jenis === 'penukaran') {
                    $totalBerat = 0;
                    $totalPoin = -rand(500, 5000); // Poin berkurang (negatif)
                    $totalHarga = 0;
                    
                    $catatanOptions = [
                        "Penukaran poin untuk beras 5kg",
                        "Penukaran poin untuk minyak goreng",
                        "Penukaran poin untuk sembako",
                        "Penukaran poin untuk sabun dan shampoo",
                        "Penukaran poin untuk kebutuhan rumah tangga"
                    ];
                    $catatan = $catatanOptions[array_rand($catatanOptions)];
                    
                } else { // transfer
                    $totalBerat = 0;
                    $totalPoin = rand(-2000, 2000);
                    $totalHarga = abs($totalPoin) * 100;
                    
                    $catatanOptions = [
                        "Transfer poin ke keluarga",
                        "Transfer poin ke teman",
                        "Berbagi poin bantuan",
                        "Transfer poin bersama"
                    ];
                    $catatan = $catatanOptions[array_rand($catatanOptions)];
                }
                
                // Buat kode transaksi unik
                $kodeTransaksi = 'TRX' . $tanggal->format('Ymd') . strtoupper(Str::random(6));
                
                $transaksiData[] = [
                    'kode_transaksi' => $kodeTransaksi,
                    'warga_id' => $wargaItem->id,
                    'petugas_id' => $petugasSelected ? $petugasSelected->id : null,
                    'total_berat' => $totalBerat,
                    'total_harga' => $totalHarga,
                    'total_poin' => $totalPoin,
                    'status' => $status,
                    'jenis_transaksi' => $jenis,
                    'catatan' => $catatan,
                    'tanggal_transaksi' => $tanggal,
                    'created_at' => $tanggal,
                    'updated_at' => $tanggal,
                ];
                
                // Update total points warga jika transaksi completed
                if ($status === 'completed') {
                    $wargaItem->total_points += $totalPoin;
                }
            }
            
            // Simpan perubahan total points warga
            $wargaItem->save();
        }
        
        // Insert data transaksi
        Transaksi::insert($transaksiData);
        
        $this->command->info('Seeder Transaksi berhasil dijalankan!');
        $this->command->info(count($transaksiData) . ' transaksi telah dibuat.');
        
        // Sekarang buat detail transaksi untuk transaksi setoran yang completed
        $this->createDetailTransaksi($transaksiData, $kategoris);
    }
    
    private function createDetailTransaksi($transaksiData, $kategoris)
    {
        $detailTransaksiData = [];
        
        // Ambil ID transaksi setoran yang sudah di-insert
        $transaksiSetoran = Transaksi::where('jenis_transaksi', 'setoran')
            ->where('status', 'completed')
            ->get();
        
        foreach ($transaksiSetoran as $transaksi) {
            // Tentukan jumlah kategori dalam transaksi (1-3 kategori)
            $jumlahKategori = rand(1, min(3, $kategoris->count()));
            $kategoriDipilih = $kategoris->random($jumlahKategori);
            
            $totalBeratTransaksi = $transaksi->total_berat;
            
            // Bagi berat ke masing-masing kategori secara proporsional
            $beratPerKategori = [];
            $sisaBerat = $totalBeratTransaksi;
            
            for ($j = 0; $j < $jumlahKategori; $j++) {
                if ($j === $jumlahKategori - 1) {
                    $berat = round($sisaBerat, 2);
                } else {
                    $bagian = round($sisaBerat * (rand(30, 70) / 100), 2);
                    $berat = max(0.1, $bagian); // Minimal 0.1 kg
                    $sisaBerat -= $berat;
                }
                $beratPerKategori[] = $berat;
            }
            
            // Hitung poin per kategori dan akumulasi total
            $totalPoinDihitung = 0;
            foreach ($kategoriDipilih as $index => $kategori) {
                $berat = $beratPerKategori[$index];
                $poinPerKg = $kategori->poin_per_kg;
                $poin = round($berat * $poinPerKg);
                $harga = $poin * 100;
                
                $detailTransaksiData[] = [
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
            if (abs($transaksi->total_poin - $totalPoinDihitung) > 10) {
                $transaksi->total_poin = $totalPoinDihitung;
                $transaksi->total_harga = $totalPoinDihitung * 100;
                $transaksi->save();
            }
        }
        
        // Insert data detail transaksi
        if (!empty($detailTransaksiData)) {
            \DB::table('detail_transaksi')->insert($detailTransaksiData);
            $this->command->info(count($detailTransaksiData) . ' detail transaksi telah dibuat.');
        }
    }
}