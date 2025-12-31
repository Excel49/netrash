<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PenarikanPoinController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;

// Auth Routes
Auth::routes();

// Custom Auth Redirects
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard Route - untuk semua user
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// Profile routes untuk semua user
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notifikasi untuk semua user
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.read-all');
});

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Kategori Sampah Management
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    
    // Penarikan Poin Approval
    Route::get('/penarikan', [PenarikanPoinController::class, 'adminIndex'])->name('penarikan.index');
    Route::get('/penarikan/{penarikan}', [PenarikanPoinController::class, 'show'])->name('penarikan.show');
    Route::post('/penarikan/{penarikan}/approve', [PenarikanPoinController::class, 'approve'])->name('penarikan.approve');
    Route::post('/penarikan/{penarikan}/reject', [PenarikanPoinController::class, 'reject'])->name('penarikan.reject');
    Route::post('/penarikan/{penarikan}/complete', [PenarikanPoinController::class, 'complete'])->name('penarikan.complete');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/transaksi', [ReportController::class, 'transaksiReport'])->name('reports.transaksi');
    Route::get('/reports/penarikan', [ReportController::class, 'penarikanReport'])->name('reports.penarikan');
    Route::get('/reports/users', [ReportController::class, 'usersReport'])->name('reports.users');
    Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    
    // Transaksi (view only untuk admin)
    Route::get('/transaksi', [TransaksiController::class, 'adminIndex'])->name('transaksi.index');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
});

// ==================== PETUGAS ROUTES ====================
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // QR Code Scanner
    Route::get('/scan', [QrCodeController::class, 'scan'])->name('scan');
    Route::post('/scan/process', [QrCodeController::class, 'processScan'])->name('scan.process');
    
    // Transaksi Management
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/{transaksi}/print', [TransaksiController::class, 'print'])->name('transaksi.print');
    Route::post('/transaksi/{transaksi}/complete', [TransaksiController::class, 'complete'])->name('transaksi.complete');
    Route::post('/transaksi/{transaksi}/cancel', [TransaksiController::class, 'cancel'])->name('transaksi.cancel');
    
    // Warga Management
    Route::get('/warga', [PetugasController::class, 'wargaIndex'])->name('warga.index');
    Route::get('/warga/{user}', [PetugasController::class, 'wargaShow'])->name('warga.show');
    
    // Statistik
    Route::get('/statistik', [PetugasController::class, 'statistik'])->name('statistik');
});

// ==================== WARGA ROUTES ====================
Route::middleware(['auth', 'role:warga'])->prefix('warga')->name('warga.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // QR Code Pribadi
    Route::get('/qrcode', [QrCodeController::class, 'show'])->name('qrcode');
    Route::get('/qrcode/download', [QrCodeController::class, 'download'])->name('qrcode.download');
    
    // Transaksi History
    Route::get('/transaksi', [TransaksiController::class, 'wargaIndex'])->name('transaksi.index');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'wargaShow'])->name('transaksi.show');
    
    // Penarikan Poin
    Route::get('/penarikan', [PenarikanPoinController::class, 'index'])->name('penarikan.index');
    Route::get('/penarikan/create', [PenarikanPoinController::class, 'create'])->name('penarikan.create');
    Route::post('/penarikan', [PenarikanPoinController::class, 'store'])->name('penarikan.store');
    Route::get('/penarikan/{penarikan}', [PenarikanPoinController::class, 'show'])->name('penarikan.show');
    Route::delete('/penarikan/{penarikan}', [PenarikanPoinController::class, 'destroy'])->name('penarikan.destroy');
    
    // Kategori Info
    Route::get('/kategori', [WargaController::class, 'kategoriIndex'])->name('kategori.index');
    
    // Poin History
    Route::get('/poin/history', [WargaController::class, 'poinHistory'])->name('poin.history');
});

// ==================== API ROUTES ====================
Route::prefix('api')->middleware('auth')->group(function () {
    // QR Code API
    Route::post('/scan', [QrCodeController::class, 'apiProcessScan']);
    
    // Transaksi API
    Route::get('/transaksi/recent', [TransaksiController::class, 'apiRecent']);
    Route::post('/transaksi/calculate', [TransaksiController::class, 'apiCalculate']);
    
    // Notifikasi API
    Route::get('/notifikasi/unread-count', [NotifikasiController::class, 'apiUnreadCount']);
    Route::post('/notifikasi/mark-read/{id}', [NotifikasiController::class, 'apiMarkAsRead']);
    
    // Poin API
    Route::get('/poin/balance', [WargaController::class, 'apiPoinBalance']);
});

// Fallback untuk halaman tidak ditemukan
Route::fallback(function () {
    return view('errors.404');
});