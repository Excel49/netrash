<?php
// database/seeders/NotifikasiSeeder.php

namespace Database\Seeders;

use App\Models\Notifikasi;
use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NotifikasiSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $transaksis = Transaksi::all();
        
        // Sesuaikan dengan enum di migration: ['info', 'success', 'warning', 'error']
        $tipeNotifikasi = ['info', 'success', 'warning', 'error', 'info', 'info']; // info lebih banyak
        $dibacaStatus = [true, false]; // boolean true/false untuk dibaca
        
        $notifikasiData = [];
        
        // Buat notifikasi untuk setiap user (minimal 2-3 notifikasi per user)
        foreach ($users as $user) {
            $jumlahNotifikasi = rand(2, 5);
            
            for ($i = 0; $i < $jumlahNotifikasi; $i++) {
                $tipe = $tipeNotifikasi[array_rand($tipeNotifikasi)];
                $dibaca = $dibacaStatus[array_rand($dibacaStatus)];
                
                // Tentukan konten berdasarkan tipe dan role user
                if ($user->role_id == 3) { // Warga
                    $judulPesan = $this->getNotifikasiWarga($user, $transaksis, $tipe);
                    $judul = $judulPesan['judul'];
                    $pesan = $judulPesan['pesan'];
                    $link = $judulPesan['link'];
                } elseif ($user->role_id == 2) { // Petugas
                    $judulPesan = $this->getNotifikasiPetugas($tipe);
                    $judul = $judulPesan['judul'];
                    $pesan = $judulPesan['pesan'];
                    $link = $judulPesan['link'];
                } else { // Admin
                    $judulPesan = $this->getNotifikasiAdmin($tipe);
                    $judul = $judulPesan['judul'];
                    $pesan = $judulPesan['pesan'];
                    $link = $judulPesan['link'];
                }
                
                $waktu = Carbon::now()
                    ->subDays(rand(0, 30))
                    ->subHours(rand(0, 23))
                    ->subMinutes(rand(0, 59));
                
                $notifikasiData[] = [
                    'user_id' => $user->id,
                    'judul' => $judul,
                    'pesan' => $pesan,
                    'tipe' => $tipe,
                    'dibaca' => $dibaca, // Gunakan 'dibaca' bukan 'status'
                    'link' => $link,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ];
            }
        }
        
        // Insert data notifikasi
        Notifikasi::insert($notifikasiData);
        
        $this->command->info('Seeder Notifikasi berhasil dijalankan!');
        $this->command->info(count($notifikasiData) . ' notifikasi telah dibuat.');
    }
    
    private function getNotifikasiWarga($user, $transaksis, $tipe)
    {
        $transaksiWarga = $transaksis->where('warga_id', $user->id)->first();
        
        switch ($tipe) {
            case 'success':
                if ($transaksiWarga) {
                    $judul = "Transaksi Berhasil";
                    $pesan = "Transaksi #" . $transaksiWarga->kode_transaksi . " telah berhasil diproses dengan " . 
                            abs($transaksiWarga->total_poin) . " poin";
                    $link = "/transaksi/" . $transaksiWarga->id;
                } else {
                    $judul = "Selamat Datang";
                    $pesan = "Selamat bergabung di NetraTrash! Mulai setor sampah dan dapatkan poin.";
                    $link = "/dashboard";
                }
                break;
                
            case 'warning':
                $judul = "Peringatan";
                $pesan = "Poin Anda hampir habis. Segera lakukan setoran sampah.";
                $link = "/transaksi/buat";
                break;
                
            case 'error':
                $judul = "Transaksi Gagal";
                $pesan = "Maaf, transaksi terakhir Anda gagal diproses. Silakan coba lagi.";
                $link = "/transaksi/buat";
                break;
                
            default: // info
                $judul = "Informasi Penting";
                $pesan = "Jadwal pengambilan sampah: Senin & Kamis pukul 08:00-12:00";
                $link = "/info/jadwal";
                break;
        }
        
        return ['judul' => $judul, 'pesan' => $pesan, 'link' => $link];
    }
    
    private function getNotifikasiPetugas($tipe)
    {
        switch ($tipe) {
            case 'success':
                $judul = "Laporan Berhasil";
                $pesan = "Anda telah menyelesaikan " . rand(5, 20) . " transaksi hari ini";
                $link = "/petugas/laporan";
                break;
                
            case 'warning':
                $judul = "Peringatan Jadwal";
                $pesan = "Ingat! Besok jadwal pengambilan sampah wilayah Anda.";
                $link = "/petugas/jadwal";
                break;
                
            case 'error':
                $judul = "Data Bermasalah";
                $pesan = "Terdapat " . rand(1, 3) . " transaksi yang perlu verifikasi ulang.";
                $link = "/petugas/verifikasi";
                break;
                
            default: // info
                $judul = "Pengumuman";
                $pesan = "Rapat rutin petugas akan diadakan hari Jumat pukul 14:00";
                $link = "/petugas/pengumuman";
                break;
        }
        
        return ['judul' => $judul, 'pesan' => $pesan, 'link' => $link];
    }
    
    private function getNotifikasiAdmin($tipe)
    {
        switch ($tipe) {
            case 'success':
                $judul = "Sistem Berjalan Baik";
                $pesan = "Semua sistem berfungsi normal. " . rand(50, 100) . " transaksi hari ini.";
                $link = "/admin/dashboard";
                break;
                
            case 'warning':
                $judul = "Monitor Stok";
                $pesan = "Stok beberapa barang hampir habis. Segera lakukan restok.";
                $link = "/admin/stok";
                break;
                
            case 'error':
                $judul = "Laporan Error";
                $pesan = "Terdapat " . rand(1, 3) . " laporan error sistem yang perlu ditangani.";
                $link = "/admin/error-log";
                break;
                
            default: // info
                $judul = "Laporan Bulanan";
                $pesan = "Laporan bulanan sudah tersedia. Total " . rand(1000, 5000) . " transaksi.";
                $link = "/admin/laporan";
                break;
        }
        
        return ['judul' => $judul, 'pesan' => $pesan, 'link' => $link];
    }
}