<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriSampah;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    /**
     * Display landing page
     */
    public function index()
    {
        // Data untuk pie chart - hitung total sampah per jenis dari transaksi
        $jenisSampah = ['organik', 'anorganik', 'b3', 'campuran'];
        $pieData = [];
        $pieLabels = [];
        $pieColors = [
            '#10b981', // Green untuk organik
            '#3b82f6', // Blue untuk anorganik
            '#ef4444', // Red untuk B3
            '#f59e0b', // Yellow untuk campuran
        ];
        
        // Hitung total berat per jenis sampah
        foreach ($jenisSampah as $jenis) {
            try {
                $totalBerat = DetailTransaksi::join('kategori_sampah', 'detail_transaksi.kategori_id', '=', 'kategori_sampah.id')
                    ->where('kategori_sampah.jenis_sampah', $jenis)
                    ->sum('detail_transaksi.berat');
                
                // Log untuk debugging
                \Log::info("Total berat {$jenis}: {$totalBerat}");
                
                $pieData[] = (float) $totalBerat; // Pastikan float
            } catch (\Exception $e) {
                \Log::error("Error calculating berat for {$jenis}: " . $e->getMessage());
                $pieData[] = 0;
            }
            $pieLabels[] = ucfirst($jenis);
        }
        
        // Jika tidak ada data, beri contoh data
        if (array_sum($pieData) === 0) {
            $pieData = [45, 30, 5, 20]; // Contoh data: 45% organik, 30% anorganik, 5% B3, 20% campuran
        }
        
        // Daftar tips untuk digunakan di JavaScript (TAMBAHKAN INI)
        $allTips = [
            "♻️ Pisahkan sampah organik dan anorganik untuk memudahkan daur ulang",
            "🌱 Sampah organik bisa dijadikan kompos untuk tanaman",
            "⚡ 1 kg sampah plastik bisa menghasilkan listrik untuk 1 jam",
            "💰 Setiap 1 kg sampah kertas bisa ditukar dengan poin belanja",
            "🌍 Indonesia menghasilkan 64 juta ton sampah per tahun",
            "⏳ Sampah plastik butuh 450 tahun untuk terurai",
            "🔄 Daur ulang 1 ton kertas menyelamatkan 17 pohon",
            "💡 Sampah elektronik mengandung logam berharga yang bisa didaur ulang",
            "🚫 Kurangi penggunaan plastik sekali pakai untuk selamatkan laut",
            "🏆 Warga terbaik bulan ini mengumpulkan 500 kg sampah",
            "📱 Gunakan QR Code NetraTrash untuk transaksi lebih cepat",
            "🎁 Kumpulkan poin dan tukarkan dengan hadiah menarik",
            "👨‍👩‍👧‍👦 Ajak keluarga untuk memilah sampah sejak dini",
            "🗑️ Sampah B3 (baterai, obat) harus dibuang ke tempat khusus",
            "🍃 60% sampah di Indonesia masih bisa didaur ulang",
        ];
        
        // Ambil random tip untuk ditampilkan
        $randomTip = $allTips[array_rand($allTips)];
        
        // Statistik umum
        $stats = [
            'total_users' => User::count(),
            'total_transactions' => Transaksi::count(),
            'total_points' => User::sum('total_points'),
            'total_berat' => Transaksi::sum('total_berat') ?: 12540, // Contoh jika kosong
            'active_warga' => User::where('role_id', 3)->count(),
            'active_petugas' => User::where('role_id', 2)->count(),
        ];
        
        // Fitur utama aplikasi
        $features = [
            [
                'icon' => 'fas fa-qrcode',
                'title' => 'Scan QR Code',
                'description' => 'Scan kode QR untuk transaksi cepat dan akurat',
                'route' => 'login',
                'color' => 'from-blue-500 to-blue-600',
            ],
            [
                'icon' => 'fas fa-exchange-alt',
                'title' => 'Tukar Poin',
                'description' => 'Tukar poin dengan hadiah menarik dari katalog',
                'route' => 'login',
                'color' => 'from-green-500 to-green-600',
            ],
            [
                'icon' => 'fas fa-history',
                'title' => 'Riwayat Transaksi',
                'description' => 'Pantau semua transaksi dan poin Anda',
                'route' => 'login',
                'color' => 'from-purple-500 to-purple-600',
            ],
            [
                'icon' => 'fas fa-chart-line',
                'title' => 'Statistik Real-time',
                'description' => 'Lihat perkembangan pengelolaan sampah',
                'route' => 'login',
                'color' => 'from-yellow-500 to-yellow-600',
            ],
        ];
        
        // Testimonials
        $testimonials = [
            [
                'name' => 'Budi Santoso',
                'role' => 'Warga Aktif',
                'content' => 'Sejak pakai NetraTrash, saya bisa dapat poin dari sampah dan tukar dengan sembako!',
                'points' => 2450,
            ],
            [
                'name' => 'Siti Rahayu',
                'role' => 'Ibu Rumah Tangga',
                'content' => 'Aplikasi ini sangat membantu dalam mengelola sampah rumah tangga.',
                'points' => 1850,
            ],
            [
                'name' => 'Rudi Petugas',
                'role' => 'Petugas Sampah',
                'content' => 'Kerja jadi lebih mudah dengan scan QR, data langsung tercatat otomatis.',
                'points' => 3200,
            ],
        ];
        
        return view('welcome', compact(
            'pieData',
            'pieLabels',
            'pieColors',
            'randomTip',
            'allTips', // <-- TAMBAHKAN INI
            'stats',
            'features',
            'testimonials'
        ));
    }
    
    /**
     * API untuk mendapatkan random tip
     */
    public function getRandomTip()
    {
        $tips = [
            "♻️ Pisahkan sampah organik dan anorganik untuk memudahkan daur ulang",
            "🌱 Sampah organik bisa dijadikan kompos untuk tanaman",
            "⚡ 1 kg sampah plastik bisa menghasilkan listrik untuk 1 jam",
            "💰 Setiap 1 kg sampah kertas bisa ditukar dengan poin belanja",
            "🌍 Indonesia menghasilkan 64 juta ton sampah per tahun",
            "⏳ Sampah plastik butuh 450 tahun untuk terurai",
            "🔄 Daur ulang 1 ton kertas menyelamatkan 17 pohon",
            "💡 Sampah elektronik mengandung logam berharga yang bisa didaur ulang",
            "🚫 Kurangi penggunaan plastik sekali pakai untuk selamatkan laut",
        ];
        
        return response()->json([
            'success' => true,
            'tip' => $tips[array_rand($tips)]
        ]);
    }
}