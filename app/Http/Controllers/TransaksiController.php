<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\KategoriSampah;
use App\Models\User;
use App\Models\DetailTransaksi;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Barang;

class TransaksiController extends Controller
{
    // ================== PETUGAS ==================
    
    // Tambahkan method ini di bagian PETUGAS (setelah method createItem)
    public function createCombined(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }
        
        $wargaId = $request->query('warga_id');
        $warga = null;
        if ($wargaId) {
            $warga = User::where('id', $wargaId)
                ->where('role_id', 3)
                ->first();
        }
        
        // Ambil 4 kategori utama (locked)
        $kategoriUtama = KategoriSampah::where('is_locked', true)
            ->where('status', true)
            ->orderBy('jenis_sampah')
            ->get();
        
        // Ambil semua item spesifik (unlocked)
        $itemSpesifik = KategoriSampah::where('is_locked', false)
            ->orderBy('nama_kategori')
            ->get();
        
        // Jika tidak ada warga, redirect ke scan
        if (!$warga) {
            return redirect()->route('petugas.scan.index')
                ->with('warning', 'Silakan scan QR Code warga terlebih dahulu');
        }
        
        return view('petugas.transaksi.create-combined', compact('kategoriUtama', 'itemSpesifik', 'warga'));
    }

    // Method untuk menyimpan transaksi gabungan (poin only)
    public function storeCombined(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }
        
        $request->validate([
            'warga_id' => 'required|exists:users,id',
            'kategori_utama' => 'required|array',
            'kategori_utama.*.berat' => 'nullable|numeric|min:0',
            'item_spesifik' => 'nullable|array',
            'item_spesifik.*.id' => 'required_with:item_spesifik.*.berat|exists:kategori_sampah,id',
            'item_spesifik.*.berat' => 'nullable|numeric|min:0.1',
            'catatan' => 'nullable|string|max:500',
        ]);
        
        return $this->storeCombinedTransaction($request);
    }

    private function storeCombinedTransaction(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $petugasId = Auth::id();
            $wargaId = $request->warga_id;
            
            // Kumpulkan semua detail
            $details = [];
            $totalBerat = 0;
            $totalPoin = 0;
            
            // Proses kategori utama
            foreach ($request->kategori_utama as $kategoriId => $data) {
                if (!empty($data['berat']) && $data['berat'] > 0) {
                    $kategori = KategoriSampah::findOrFail($kategoriId);
                    $berat = floatval($data['berat']);
                    
                    $poin = $berat * $kategori->poin_per_kg;
                    
                    $totalBerat += $berat;
                    $totalPoin += $poin;
                    
                    $details[] = [
                        'kategori_id' => $kategoriId,
                        'berat' => $berat,
                        'harga' => 0, // Harga di-set 0 karena sistem poin only
                        'poin' => $poin,
                    ];
                }
            }
            
            // Proses item spesifik
            if (!empty($request->item_spesifik)) {
                foreach ($request->item_spesifik as $item) {
                    if (!empty($item['berat']) && $item['berat'] > 0 && !empty($item['id'])) {
                        $kategori = KategoriSampah::findOrFail($item['id']);
                        $berat = floatval($item['berat']);
                        
                        $poin = $berat * $kategori->poin_per_kg;
                        
                        $totalBerat += $berat;
                        $totalPoin += $poin;
                        
                        $details[] = [
                            'kategori_id' => $item['id'],
                            'berat' => $berat,
                            'harga' => 0, // Harga di-set 0 karena sistem poin only
                            'poin' => $poin,
                        ];
                    }
                }
            }
            
            // Jika tidak ada data sama sekali
            if (empty($details)) {
                return back()->with('error', 'Minimal satu kategori atau item harus diisi')
                    ->withInput();
            }
            
            // Generate kode transaksi
            $kodeTransaksi = 'TRX-' . date('Ymd') . '-' . str_pad(Transaksi::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
            
            // Buat transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'warga_id' => $wargaId,
                'petugas_id' => $petugasId,
                'total_berat' => $totalBerat,
                'total_harga' => 0, // Total harga 0 karena sistem poin only
                'total_poin' => $totalPoin,
                'status' => 'completed',
                'catatan' => $request->catatan,
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'setoran',
            ]);
            
            // Buat detail transaksi
            foreach ($details as $detail) {
                $detail['transaksi_id'] = $transaksi->id;
                DetailTransaksi::create($detail);
            }
            
            // Update poin warga
            $warga = User::find($wargaId);
            $warga->total_points += $totalPoin;
            $warga->save();
            
            // Buat notifikasi untuk warga
            Notifikasi::create([
                'user_id' => $wargaId,
                'judul' => 'Transaksi Berhasil',
                'pesan' => "Transaksi {$kodeTransaksi} berhasil. Anda mendapatkan {$totalPoin} poin.",
                'tipe' => 'success',
                'link' => '/warga/transaksi',
            ]);
            
            DB::commit();
            
            return redirect()->route('petugas.transaksi.show', $transaksi->id)
                ->with('success', 'Transaksi berhasil disimpan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function wargaPrint($id)
{
    $transaksi = Transaksi::with(['petugas', 'detailTransaksi.kategori'])
        ->findOrFail($id);
    $user = Auth::user();
        
    // Cek akses
    if ($user->role_id === 3 && $transaksi->warga_id != $user->id) {
        abort(403, 'Anda tidak memiliki akses ke transaksi ini');
    }
        
    return view('warga.transaksi.print', compact('transaksi'));
}

    // Index untuk petugas - TAMBAHKAN REQUEST DAN FILTER
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }
        
        $petugasId = Auth::id();
        
        // Query untuk HANYA transaksi SETOR SAMPAH (bukan penukaran)
        $query = Transaksi::with(['warga'])
            ->where('petugas_id', $petugasId)
            // Filter: hanya transaksi setor sampah, BUKAN penukaran
            ->where(function($q) {
                $q->where('jenis_transaksi', '!=', 'penukaran')
                ->orWhereNull('jenis_transaksi')
                ->orWhere('jenis_transaksi', 'setoran');
            });
        
        // Filter tanggal mulai
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        // Filter tanggal akhir
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Filter status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $transaksi = $query->orderBy('created_at', 'desc')->paginate(15);
            
        return view('petugas.transaksi.index', compact('transaksi'));
    }
    
    // Pilih jenis transaksi (Kategori Utama atau Item Spesifik)
    public function selectType()
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }
        
        return view('petugas.transaksi.select-type');
    }
    
    // Create transaksi baru
    public function create(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }
        
        $type = $request->query('type', 'kategori');
        $jenis = $request->query('jenis', 'organik');
        $wargaId = $request->query('warga_id');
        
        $warga = null;
        if ($wargaId) {
            $warga = User::where('id', $wargaId)
                ->where('role_id', 3)
                ->first();
        }
        
        if ($type === 'kategori') {
            // Ambil kategori utama berdasarkan jenis
            $kategori = KategoriSampah::where('is_locked', true)
                ->where('jenis_sampah', $jenis)
                ->where('status', true)
                ->first();
                
            return view('petugas.transaksi.create-kategori', compact('kategori', 'warga'));
        } else {
            // Ambil semua item spesifik
            $items = KategoriSampah::where('is_locked', false)
                ->get();
                
            return view('petugas.transaksi.create-item', compact('items', 'warga'));
        }
    }
    
    // Create untuk item spesifik langsung
    public function createItem($id)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }
        
        $item = KategoriSampah::where('is_locked', false)
            ->findOrFail($id);
            
        return view('petugas.transaksi.create-item-single', compact('item'));
    }
    
    // Store transaksi baru
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 2) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }
        
        // Validasi berdasarkan type
        if ($request->type === 'multi') {
            // Untuk multiple kategori/item (sistem lama)
            $request->validate([
                'warga_id' => 'required|exists:users,id',
                'kategori' => 'required|array',
                'kategori.*.id' => 'required|exists:kategori_sampah,id',
                'kategori.*.berat' => 'required|numeric|min:0.1',
                'catatan' => 'nullable|string|max:500',
            ]);
            
            return $this->storeMulti($request);
        } else {
            // Untuk single kategori/item (sistem baru)
            $request->validate([
                'type' => 'required|in:kategori,item',
                'warga_id' => 'required|exists:users,id',
                'kategori_id' => 'required|exists:kategori_sampah,id',
                'berat' => 'required|numeric|min:0.1',
                'catatan' => 'nullable|string|max:500',
            ]);
            
            return $this->storeSingle($request);
        }
    }
    
    // Store transaksi single (kategori utama atau item spesifik)
    private function storeSingle(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $petugasId = Auth::id();
            $wargaId = $request->warga_id;
            $kategoriId = $request->kategori_id;
            
            $kategori = KategoriSampah::findOrFail($kategoriId);
            $berat = floatval($request->berat);
            
            // Hitung total
            $harga = $berat * $kategori->harga_per_kg;
            $poin = $berat * $kategori->poin_per_kg;
            
            // Generate kode transaksi
            $kodeTransaksi = 'TRX-' . date('Ymd') . '-' . str_pad(Transaksi::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
            
            // Buat transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'warga_id' => $wargaId,
                'petugas_id' => $petugasId,
                'total_berat' => $berat,
                'total_harga' => $harga,
                'total_poin' => $poin,
                'status' => 'completed',
                'catatan' => $request->catatan,
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => $request->type, // 'kategori' atau 'item'
            ]);
            
            // Buat detail transaksi
            DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'kategori_id' => $kategoriId,
                'berat' => $berat,
                'harga' => $harga,
                'poin' => $poin,
            ]);
            
            // Update poin warga
            $warga = User::find($wargaId);
            $warga->total_points += $poin;
            $warga->save();
            
            // Buat notifikasi untuk warga
            Notifikasi::create([
                'user_id' => $wargaId,
                'judul' => 'Transaksi Berhasil',
                'pesan' => "Transaksi {$kodeTransaksi} berhasil. Anda mendapatkan {$poin} poin.",
                'tipe' => 'success',
                'link' => '/warga/transaksi',
            ]);
            
            DB::commit();
            
            return redirect()->route('petugas.transaksi.show', $transaksi->id)
                ->with('success', 'Transaksi berhasil disimpan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    // Store transaksi multi (sistem lama)
    private function storeMulti(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $petugasId = Auth::id();
            $wargaId = $request->warga_id;
            
            // Generate kode transaksi
            $kodeTransaksi = 'TRX-' . date('Ymd') . '-' . str_pad(Transaksi::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
            
            // Hitung total
            $totalBerat = 0;
            $totalHarga = 0;
            $totalPoin = 0;
            $details = [];
            
            foreach ($request->kategori as $item) {
                $kategori = KategoriSampah::find($item['id']);
                $berat = floatval($item['berat']);
                
                $harga = $berat * $kategori->harga_per_kg;
                $poin = $berat * $kategori->poin_per_kg;
                
                $totalBerat += $berat;
                $totalHarga += $harga;
                $totalPoin += $poin;
                
                $details[] = [
                    'kategori_id' => $kategori->id,
                    'berat' => $berat,
                    'harga' => $harga,
                    'poin' => $poin,
                ];
            }
            
            // Buat transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'warga_id' => $wargaId,
                'petugas_id' => $petugasId,
                'total_berat' => $totalBerat,
                'total_harga' => $totalHarga,
                'total_poin' => $totalPoin,
                'status' => 'completed',
                'catatan' => $request->catatan,
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'multi',
            ]);
            
            // Buat detail transaksi
            foreach ($details as $detail) {
                $detail['transaksi_id'] = $transaksi->id;
                DetailTransaksi::create($detail);
            }
            
            // Update poin warga
            $warga = User::find($wargaId);
            $warga->total_points += $totalPoin;
            $warga->save();
            
            // Buat notifikasi untuk warga
            Notifikasi::create([
                'user_id' => $wargaId,
                'judul' => 'Transaksi Berhasil',
                'pesan' => "Transaksi {$kodeTransaksi} berhasil. Anda mendapatkan {$totalPoin} poin.",
                'tipe' => 'success',
                'link' => '/warga/transaksi',
            ]);
            
            DB::commit();
            
            return redirect()->route('petugas.transaksi.show', $transaksi->id)
                ->with('success', 'Transaksi berhasil disimpan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    // Show detail transaksi untuk petugas
    public function show($id)
    {
        $transaksi = Transaksi::with(['warga', 'petugas', 'detailTransaksi.kategori'])
            ->findOrFail($id);
        $user = Auth::user();
            
        // Cek akses
        if ($user->role_id === 2 && $transaksi->petugas_id != $user->id) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini');
        }
        
        return view('petugas.transaksi.show', compact('transaksi'));
    }
    
    // Print transaksi
    public function print($id)
    {
        $transaksi = Transaksi::with(['warga', 'petugas', 'detailTransaksi.kategori'])
            ->findOrFail($id);
            
        return view('petugas.transaksi.print', compact('transaksi'));
    }
    
    /**
     * Display today's transactions for petugas
     */
    public function today()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        
        // Get today's transactions handled by this petugas
        $transaksi = Transaksi::where('petugas_id', $user->id)
            ->whereDate('tanggal_transaksi', $today)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Cek apakah view ada
        if (view()->exists('petugas.transaksi.today')) {
            return view('petugas.transaksi.today', [
                'transaksi' => $transaksi,
                'today' => $today,
            ]);
        }
        
        // Jika view tidak ada, redirect ke index dengan filter
        return redirect()->route('petugas.transaksi.index', ['filter' => 'today']);
    }
    
    // ================== WARGA ==================
    
    // Index untuk warga
    // app/Http/Controllers/TransaksiController.php

    public function wargaIndex(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role_id !== 3) {
            abort(403, 'Hanya warga yang dapat mengakses halaman ini');
        }
        
        $wargaId = Auth::id();
        $query = Transaksi::with('petugas')
            ->where('warga_id', $wargaId);
        
        // HANYA tampilkan transaksi setoran sampah (BUKAN penukaran)
        $query->where(function($q) {
            $q->where('jenis_transaksi', '!=', 'penukaran')
            ->orWhereNull('jenis_transaksi');
        });
        
        // Filter tanggal
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }
        
        // Filter petugas
        if ($request->has('petugas_id') && $request->petugas_id != '') {
            $query->where('petugas_id', $request->petugas_id);
        }
        
        $transaksi = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('warga.transaksi.index', compact('transaksi'));
    }
    
    // Show detail untuk warga
    public function wargaShow($id)
    {
        $transaksi = Transaksi::with(['petugas', 'detailTransaksi.kategori'])
            ->findOrFail($id);
        $user = Auth::user();
            
        // Cek akses
        if ($user->role_id === 3 && $transaksi->warga_id != $user->id) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini');
        }
        
        return view('warga.transaksi.show', compact('transaksi'));
    }
    
    public function wargaToday()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        
        // Get today's transactions for this warga
        $transaksi = Transaksi::where('warga_id', $user->id)
            ->whereDate('tanggal_transaksi', $today)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Cek apakah view ada
        if (view()->exists('warga.transaksi.today')) {
            return view('warga.transaksi.today', [
                'transaksi' => $transaksi,
                'today' => $today,
            ]);
        }
        
        // Jika view tidak ada, redirect ke index dengan filter
        return redirect()->route('warga.transaksi.index', ['filter' => 'today']);
    }
    
    // ================== ADMIN ==================
    
    // Index untuk admin
    public function adminIndex(Request $request)
    {
        $query = Transaksi::with(['warga', 'petugas', 'admin']);
        
        // Filter pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                ->orWhereHas('warga', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('petugas', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        // Filter jenis transaksi (sesuai dengan view yang baru)
        if ($request->has('jenis_transaksi') && $request->jenis_transaksi != '') {
            $jenis = $request->jenis_transaksi;
            if ($jenis === 'setoran') {
                $query->where('jenis_transaksi', '!=', 'penukaran');
            } elseif ($jenis === 'penukaran') {
                $query->where('jenis_transaksi', 'penukaran');
            }
        }
        
        // Filter status yang digabungkan
        if ($request->has('status') && $request->status != '') {
        $status = $request->status;
        
        if ($status === 'pending') {
            // Status pending: bisa status utama pending atau status penukaran pending
            $query->where(function($q) {
                $q->where('status', 'pending')
                ->orWhere('status_penukaran', 'pending');
            });
        } elseif ($status === 'cancelled') {
            // Status cancelled: bisa status utama cancelled atau status penukaran cancelled
            $query->where(function($q) {
                $q->where('status', 'cancelled')
                ->orWhere('status_penukaran', 'cancelled');
            });
        } elseif ($status === 'completed') {
            // Status completed: bisa status utama completed atau status penukaran completed
            $query->where(function($q) {
                $q->where('status', 'completed')
                ->orWhere('status_penukaran', 'completed');
            });
        }
    }
        
        // Filter tanggal
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('tanggal_transaksi', $request->date);
        }
        
        // Filter tambahan jika masih perlu status_penukaran khusus
        // if ($request->has('status_penukaran') && $request->status_penukaran != '') {
        //     $query->where('status_penukaran', $request->status_penukaran);
        // }
        
        $transaksi = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.transaksi.index', compact('transaksi'));
    }
    
    // Method untuk approve penukaran
public function approvePenukaran(Request $request, $id)
{
    $request->validate([
        'catatan_admin' => 'nullable|string|max:500',
    ]);
    
    DB::beginTransaction();
    
    try {
        $transaksi = Transaksi::where('jenis_transaksi', 'penukaran')
            ->where('status_penukaran', 'pending')
            ->findOrFail($id);
        
        $admin = auth()->user();
        
        // Extract barang info dari catatan
        $barangInfo = $this->extractBarangInfo($transaksi->catatan);
        
        // Cari barang berdasarkan nama
        $barang = Barang::where('nama_barang', 'like', '%' . $barangInfo['nama'] . '%')->first();
        
        if (!$barang) {
            return back()->with('error', 'Barang tidak ditemukan di database');
        }
        
        // Cek stok
        if ($barang->stok < $barangInfo['jumlah']) {
            return back()->with('error', 'Stok barang tidak mencukupi');
        }
        
        // Kurangi stok barang
        $barang->stok -= $barangInfo['jumlah'];
        $barang->save();
        
        // Kurangi poin user
        $warga = User::find($transaksi->warga_id);
        $totalPoin = abs($transaksi->total_poin);
        
        if ($warga->total_points < $totalPoin) {
            return back()->with('error', 'Poin warga tidak mencukupi');
        }
        
        $warga->total_points -= $totalPoin;
        $warga->save();
        
        // Update transaksi
        $transaksi->update([
            'status' => 'completed',
            'status_penukaran' => 'completed',
            'admin_id' => $admin->id,
            'diproses_pada' => now(),
            'catatan' => $transaksi->catatan . (isset($request->catatan_admin) ? "\nCatatan Admin: " . $request->catatan_admin : ''),
        ]);
        
        // Buat notifikasi untuk warga
        Notifikasi::create([
            'user_id' => $transaksi->warga_id,
            'judul' => 'Penukaran Disetujui',
            'pesan' => "Penukaran {$barangInfo['nama']} x{$barangInfo['jumlah']} telah disetujui admin. Poin berkurang {$totalPoin}.",
            'tipe' => 'success',
            'link' => '/warga/transaksi/' . $transaksi->id,
        ]);
        
        DB::commit();
        
        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Penukaran berhasil disetujui!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

// Method untuk reject penukaran
public function rejectPenukaran(Request $request, $id)
{
    $request->validate([
        'alasan_batal' => 'required|string|max:500',
    ]);
    
    DB::beginTransaction();
    
    try {
        $transaksi = Transaksi::where('jenis_transaksi', 'penukaran')
            ->where('status_penukaran', 'pending')
            ->findOrFail($id);
        
        $admin = auth()->user();
        
        // Update transaksi
        $transaksi->update([
            'status' => 'cancelled',
            'status_penukaran' => 'cancelled',
            'alasan_batal' => $request->alasan_batal,
            'admin_id' => $admin->id,
            'diproses_pada' => now(),
        ]);
        
        // Extract barang info
        $barangInfo = $this->extractBarangInfo($transaksi->catatan);
        
        // Buat notifikasi untuk warga
        Notifikasi::create([
            'user_id' => $transaksi->warga_id,
            'judul' => 'Penukaran Ditolak',
            'pesan' => "Penukaran {$barangInfo['nama']} x{$barangInfo['jumlah']} ditolak. Alasan: {$request->alasan_batal}",
            'tipe' => 'error',
            'link' => '/warga/transaksi/' . $transaksi->id,
        ]);
        
        DB::commit();
        
        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Penukaran berhasil ditolak!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

// Method untuk mark as processed
public function processPenukaran($id)
{
    DB::beginTransaction();
    
    try {
        $transaksi = Transaksi::where('jenis_transaksi', 'penukaran')
            ->where('status_penukaran', 'pending')
            ->findOrFail($id);
        
        $admin = auth()->user();
        
        $transaksi->update([
            'status_penukaran' => 'processed',
            'admin_id' => $admin->id,
        ]);
        
        // Extract barang info
        $barangInfo = $this->extractBarangInfo($transaksi->catatan);
        
        // Buat notifikasi untuk warga
        Notifikasi::create([
            'user_id' => $transaksi->warga_id,
            'judul' => 'Penukaran Diproses',
            'pesan' => "Penukaran {$barangInfo['nama']} x{$barangInfo['jumlah']} sedang diproses oleh admin.",
            'tipe' => 'info',
            'link' => '/warga/transaksi/' . $transaksi->id,
        ]);
        
        DB::commit();
        
        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Penukaran ditandai sebagai sedang diproses!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    // Helper function untuk extract barang info
    private function extractBarangInfo($catatan)
    {
        $info = [
            'nama' => 'Barang',
            'jumlah' => 1,
        ];
        
        if (preg_match('/Penukaran:\s*(.+?)\s*x(\d+)/', $catatan, $matches)) {
            $info['nama'] = trim($matches[1]);
            $info['jumlah'] = intval($matches[2]);
        }
        
        return $info;
    }

    /**
     * Display today's transactions for admin
     */
    public function adminToday()
    {
        $today = Carbon::today()->toDateString();
        
        // Get all today's transactions
        $transaksi = Transaksi::whereDate('tanggal_transaksi', $today)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Cek apakah view ada
        if (view()->exists('admin.transaksi.today')) {
            return view('admin.transaksi.today', [
                'transaksi' => $transaksi,
                'today' => $today,
            ]);
        }
        
        // Jika view tidak ada, redirect ke index dengan filter
        return redirect()->route('admin.transaksi.index', ['filter' => 'today']);
    }
    
    // ================== API ==================
    
    // API untuk hitung otomatis
    public function apiCalculate(Request $request)
    {
        try {
            $kategoriId = $request->kategori_id;
            $berat = floatval($request->berat);
            
            $kategori = KategoriSampah::findOrFail($kategoriId);
            
            $harga = $berat * $kategori->harga_per_kg;
            $poin = $berat * $kategori->poin_per_kg;
            
            return response()->json([
                'success' => true,
                'harga' => $harga,
                'poin' => $poin,
                'harga_formatted' => 'Rp ' . number_format($harga, 0, ',', '.'),
                'poin_formatted' => number_format($poin, 0, ',', '.') . ' poin',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // API untuk recent transactions
    public function apiRecent()
    {
        $user = Auth::user();
        
        if ($user->role_id === 2) {
            $transaksi = Transaksi::with('warga')
                ->where('petugas_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } else {
            $transaksi = Transaksi::with('petugas')
                ->where('warga_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'transactions' => $transaksi
        ]);
    }
}