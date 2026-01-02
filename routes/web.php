<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ==================== AUTHENTICATION ROUTES ====================
// Default Auth Routes
Auth::routes(['verify' => true]);

// Custom Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Email Verification Routes
Route::get('/email/verify', [VerificationController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');

// ==================== PUBLIC ROUTES ====================
Route::get('/', function () {
    return redirect()->route('login');
});

// About and information pages
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/features', function () {
    return view('pages.features');
})->name('features');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/privacy-policy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/terms-conditions', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/help', function () {
    return view('pages.help');
})->name('help');

// ==================== AUTHENTICATED ROUTES (ALL USERS) ====================
Route::middleware(['auth'])->group(function () {
    // Dashboard Route - untuk semua user berdasarkan role
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Home redirect berdasarkan role
    Route::get('/home', function () {
        /** @var User $user */
        $user = Auth::user();
        
        // Gunakan method dari model User yang sudah ada
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isPetugas()) {
            return redirect()->route('petugas.dashboard');
        } elseif ($user->isWarga()) {
            return redirect()->route('warga.dashboard');
        }
        
        return redirect('/dashboard');
    })->name('home');
    
    // Profile routes untuk semua user
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::patch('/photo', [ProfileController::class, 'updatePhoto'])->name('photo.update');
        Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');
        Route::get('/security', [ProfileController::class, 'security'])->name('security');
        Route::get('/preferences', [ProfileController::class, 'preferences'])->name('preferences');
        Route::patch('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences.update');
        
        // Notification settings
        Route::get('/notification-settings', [NotifikasiController::class, 'settings'])->name('notification.settings');
        Route::post('/notification-settings', [NotifikasiController::class, 'updateSettings'])->name('notification.settings.update');
    });
    
    // Notifikasi untuk semua user
    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/', [NotifikasiController::class, 'index'])->name('index');
        Route::get('/unread', [NotifikasiController::class, 'unread'])->name('unread');
        Route::post('/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotifikasiController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [NotifikasiController::class, 'destroy'])->name('destroy');
        Route::delete('/clear-all', [NotifikasiController::class, 'clearAll'])->name('clear-all');

    });
});

// ==================== NOTIFICATION ROUTES ====================
Route::middleware(['auth'])->prefix('notifikasi')->name('notifikasi.')->group(function () {
    // For all users
    Route::get('/', [NotifikasiController::class, 'index'])->name('index');
    Route::get('/unread', [NotifikasiController::class, 'unread'])->name('unread');
    Route::post('/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('read');
    Route::post('/read-all', [NotifikasiController::class, 'markAllAsRead'])->name('read-all');
    Route::delete('/{id}', [NotifikasiController::class, 'destroy'])->name('destroy');
    Route::delete('/clear-all', [NotifikasiController::class, 'clearAll'])->name('clear-all');
    
    // For petugas only - Gunakan middleware role:2
    Route::middleware('role:2')->group(function () {
        Route::get('/create', [NotifikasiController::class, 'create'])->name('create');
        Route::post('/', [NotifikasiController::class, 'store'])->name('store');
        Route::get('/sent', [NotifikasiController::class, 'sent'])->name('sent');
    });
});

// ==================== ADMIN ROUTES ====================
// Gunakan middleware role:1 untuk admin (role_id = 1)
Route::middleware(['auth', 'role:1'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    
    // ========== USER MANAGEMENT ==========
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/activate', [UserController::class, 'activate'])->name('activate');
        Route::post('/{user}/deactivate', [UserController::class, 'deactivate'])->name('deactivate');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::get('/{user}/activity', [UserController::class, 'activity'])->name('activity');
        Route::post('/import', [UserController::class, 'import'])->name('import');
        Route::get('/export', [UserController::class, 'export'])->name('export');
        Route::get('/trash', [UserController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
    });
    
    // ========== KATEGORI SAMPAH MANAGEMENT ==========
    Route::prefix('kategori')->name('kategori.')->group(function () {
        Route::get('/', [KategoriController::class, 'index'])->name('index');
        Route::get('/create', [KategoriController::class, 'create'])->name('create');
        Route::post('/', [KategoriController::class, 'store'])->name('store');
        Route::get('/{kategori}/edit', [KategoriController::class, 'edit'])->name('edit');
        Route::put('/{kategori}', [KategoriController::class, 'update'])->name('update');
        Route::delete('/{kategori}', [KategoriController::class, 'destroy'])->name('destroy');
        Route::post('/{kategori}/toggle-status', [KategoriController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/import', [KategoriController::class, 'import'])->name('import');
        Route::get('/export', [KategoriController::class, 'export'])->name('export');
        Route::get('/trash', [KategoriController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [KategoriController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [KategoriController::class, 'forceDelete'])->name('force-delete');
    });
    
    // ========== PENARIKAN POIN APPROVAL ==========
    Route::prefix('penarikan')->name('penarikan.')->group(function () {
        Route::get('/', [PenarikanPoinController::class, 'adminIndex'])->name('index');
        Route::get('/pending', [PenarikanPoinController::class, 'pending'])->name('pending');
        Route::get('/approved', [PenarikanPoinController::class, 'approved'])->name('approved');
        Route::get('/completed', [PenarikanPoinController::class, 'completed'])->name('completed');
        Route::get('/rejected', [PenarikanPoinController::class, 'rejected'])->name('rejected');
        Route::get('/{penarikan}', [PenarikanPoinController::class, 'show'])->name('show');
        Route::post('/{penarikan}/approve', [PenarikanPoinController::class, 'approve'])->name('approve');
        Route::post('/{penarikan}/reject', [PenarikanPoinController::class, 'reject'])->name('reject');
        Route::post('/{penarikan}/complete', [PenarikanPoinController::class, 'complete'])->name('complete');
        Route::post('/{penarikan}/cancel', [PenarikanPoinController::class, 'cancel'])->name('cancel');
        Route::post('/{penarikan}/process-payment', [PenarikanPoinController::class, 'processPayment'])->name('process-payment');
        Route::get('/{penarikan}/print', [PenarikanPoinController::class, 'printReceipt'])->name('print');
        Route::get('/export', [PenarikanPoinController::class, 'export'])->name('export');
    });
    
    // ========== REPORTS ==========
    Route::prefix('reports')->name('reports.')->group(function () {
        // Main reports dashboard
        Route::get('/', [ReportController::class, 'index'])->name('index');
        
        // Individual reports
        Route::get('/transaksi', [ReportController::class, 'transaksiReport'])->name('transaksi');
        Route::get('/penarikan', [ReportController::class, 'penarikanReport'])->name('penarikan');
        Route::get('/users', [ReportController::class, 'usersReport'])->name('users');
        Route::get('/kategori', [ReportController::class, 'kategoriReport'])->name('kategori');
        
        // Export functionality
        Route::post('/export', [ReportController::class, 'export'])->name('export');
        
        // Print routes
        Route::get('/print/transaksi/{id}', [ReportController::class, 'printReceipt'])->name('print.transaksi');
        Route::get('/print/penarikan/{id}', [ReportController::class, 'printWithdrawalReceipt'])->name('print.penarikan');
        
        // API routes for charts and stats
        Route::get('/dashboard-stats', [ReportController::class, 'dashboardStats'])->name('dashboard-stats');
        Route::get('/summary', [ReportController::class, 'getSummary'])->name('summary');
        
        // Custom reports
        Route::get('/custom', function () {
            return view('admin.reports.custom');
        })->name('custom');
        Route::post('/custom/generate', [ReportController::class, 'generateCustomReport'])->name('custom.generate');
        Route::get('/analytics', function () {
            return view('admin.reports.analytics');
        })->name('analytics');
        Route::get('/performance', function () {
            return view('admin.reports.performance');
        })->name('performance');
    });
    
    // ========== TRANSAKSI (VIEW ONLY UNTUK ADMIN) ==========
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/', [TransaksiController::class, 'adminIndex'])->name('index');
        Route::get('/pending', [TransaksiController::class, 'adminPending'])->name('pending');
        Route::get('/completed', [TransaksiController::class, 'adminCompleted'])->name('completed');
        Route::get('/cancelled', [TransaksiController::class, 'adminCancelled'])->name('cancelled');
        Route::get('/today', [TransaksiController::class, 'adminToday'])->name('today');
        Route::get('/{transaksi}', [TransaksiController::class, 'show'])->name('show');
        Route::get('/{transaksi}/print', [TransaksiController::class, 'print'])->name('print');
        Route::post('/{transaksi}/verify', [TransaksiController::class, 'verify'])->name('verify');
        Route::post('/{transaksi}/cancel', [TransaksiController::class, 'adminCancel'])->name('cancel');
        Route::get('/export', [TransaksiController::class, 'export'])->name('export');
    });
    
    // ========== SETTINGS ==========
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', function () {
            return view('admin.settings.index');
        })->name('index');
        
        Route::get('/general', function () {
            return view('admin.settings.general');
        })->name('general');
        
        Route::get('/points', function () {
            return view('admin.settings.points');
        })->name('points');
        
        Route::get('/notifications', function () {
            return view('admin.settings.notifications');
        })->name('notifications');
        
        Route::get('/backup', function () {
            return view('admin.settings.backup');
        })->name('backup');
    });
    
    // ========== BACKUP & RESTORE ==========
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', function () {
            return view('admin.backup.index');
        })->name('index');
        
        Route::post('/create', function () {
            // Backup logic
            return back()->with('success', 'Backup berhasil dibuat');
        })->name('create');
        
        Route::get('/download/{filename}', function ($filename) {
            // Download backup
            return response()->download(storage_path('app/backups/' . $filename));
        })->name('download');
        
        Route::delete('/delete/{filename}', function ($filename) {
            // Delete backup
            \Illuminate\Support\Facades\Storage::delete('backups/' . $filename);
            return back()->with('success', 'Backup berhasil dihapus');
        })->name('delete');
        
        Route::post('/restore/{filename}', function ($filename) {
            // Restore backup
            return back()->with('success', 'Backup berhasil dipulihkan');
        })->name('restore');
    });
    
    // ========== LOGS ==========
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', function () {
            return view('admin.logs.index');
        })->name('index');
        
        Route::get('/activity', function () {
            return view('admin.logs.activity');
        })->name('activity');
        
        Route::get('/error', function () {
            return view('admin.logs.error');
        })->name('error');
        
        Route::get('/login', function () {
            return view('admin.logs.login');
        })->name('login');
        
        Route::get('/export', function () {
            // Export logs
            return back()->with('success', 'Logs berhasil diexport');
        })->name('export');
        
        Route::delete('/clear', function () {
            // Clear logs
            return back()->with('success', 'Logs berhasil dibersihkan');
        })->name('clear');
    });
});

// ==================== PETUGAS ROUTES ====================
// Gunakan middleware role:2 untuk petugas (role_id = 2)
Route::middleware(['auth', 'role:2'])->prefix('petugas')->name('petugas.')->group(function () {
    // Dashboard Petugas
    Route::get('/dashboard', [DashboardController::class, 'petugasDashboard'])->name('dashboard');
    
    // ========== QR CODE SCANNER ==========
    Route::prefix('scan')->name('scan.')->group(function () {
        Route::get('/', [QrCodeController::class, 'scan'])->name('index');
        Route::post('/process', [QrCodeController::class, 'processScan'])->name('process');
        Route::get('/manual', [QrCodeController::class, 'manualEntry'])->name('manual');
        Route::post('/manual/process', [QrCodeController::class, 'processManual'])->name('manual.process');
        Route::get('/history', [QrCodeController::class, 'scanHistory'])->name('history');
        Route::get('/settings', [QrCodeController::class, 'scanSettings'])->name('settings');
    });
    
    // ========== TRANSAKSI MANAGEMENT ==========
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('index');
        Route::get('/create', [TransaksiController::class, 'create'])->name('create');
        Route::post('/', [TransaksiController::class, 'store'])->name('store');
        Route::get('/today', [TransaksiController::class, 'today'])->name('today');
        Route::get('/pending', [TransaksiController::class, 'pending'])->name('pending');
        Route::get('/completed', [TransaksiController::class, 'completed'])->name('completed');
        Route::get('/{transaksi}', [TransaksiController::class, 'show'])->name('show');
        Route::get('/{transaksi}/edit', [TransaksiController::class, 'edit'])->name('edit');
        Route::put('/{transaksi}', [TransaksiController::class, 'update'])->name('update');
        Route::get('/{transaksi}/print', [TransaksiController::class, 'print'])->name('print');
        Route::post('/{transaksi}/complete', [TransaksiController::class, 'complete'])->name('complete');
        Route::post('/{transaksi}/cancel', [TransaksiController::class, 'cancel'])->name('cancel');
        Route::post('/{transaksi}/add-item', [TransaksiController::class, 'addItem'])->name('add-item');
        Route::delete('/{transaksi}/remove-item/{item}', [TransaksiController::class, 'removeItem'])->name('remove-item');
        Route::get('/export', [TransaksiController::class, 'export'])->name('export');
    });
    
    // ========== WARGA MANAGEMENT ==========
    Route::prefix('warga')->name('warga.')->group(function () {
        Route::get('/', [PetugasController::class, 'wargaIndex'])->name('index');
        Route::get('/create', [PetugasController::class, 'wargaCreate'])->name('create');
        Route::post('/', [PetugasController::class, 'wargaStore'])->name('store');
        Route::get('/{user}', [PetugasController::class, 'wargaShow'])->name('show');
        Route::get('/{user}/edit', [PetugasController::class, 'wargaEdit'])->name('edit');
        Route::put('/{user}', [PetugasController::class, 'wargaUpdate'])->name('update');
        Route::get('/{user}/transaksi', [PetugasController::class, 'wargaTransaksi'])->name('transaksi');
        Route::get('/{user}/points', [PetugasController::class, 'wargaPoints'])->name('points');
        Route::get('/search', [PetugasController::class, 'wargaSearch'])->name('search');
        Route::get('/export', [PetugasController::class, 'wargaExport'])->name('export');
    });
    
    // ========== STATISTIK ==========
    Route::prefix('statistik')->name('statistik.')->group(function () {
        Route::get('/', [PetugasController::class, 'statistik'])->name('index');
        Route::get('/daily', [PetugasController::class, 'dailyStats'])->name('daily');
        Route::get('/monthly', [PetugasController::class, 'monthlyStats'])->name('monthly');
        Route::get('/performance', [PetugasController::class, 'performance'])->name('performance');
        Route::get('/top-warga', [PetugasController::class, 'topWarga'])->name('top-warga');
        Route::get('/export', [PetugasController::class, 'exportStats'])->name('export');
    });
    
    // ========== PROFILE PETUGAS ==========
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [PetugasController::class, 'profile'])->name('index');
        Route::put('/', [PetugasController::class, 'updateProfile'])->name('update');
        Route::get('/performance', [PetugasController::class, 'myPerformance'])->name('performance');
        Route::get('/transaksi', [PetugasController::class, 'myTransaksi'])->name('transaksi');
        Route::get('/achievements', [PetugasController::class, 'achievements'])->name('achievements');
    });
});

// ==================== WARGA ROUTES ====================
// Gunakan middleware role:3 untuk warga (role_id = 3)
Route::middleware(['auth', 'role:3'])->prefix('warga')->name('warga.')->group(function () {
    // Dashboard Warga
    Route::get('/dashboard', [DashboardController::class, 'wargaDashboard'])->name('dashboard');
    
    // ========== QR CODE PRIBADI ==========
    Route::prefix('qrcode')->name('qrcode.')->group(function () {
        Route::get('/', [QrCodeController::class, 'show'])->name('index');
        Route::get('/download', [QrCodeController::class, 'download'])->name('download');
        Route::get('/print', [QrCodeController::class, 'print'])->name('print');
        Route::get('/share', [QrCodeController::class, 'share'])->name('share');
        Route::post('/regenerate', [QrCodeController::class, 'regenerate'])->name('regenerate');
    });
    
    // ========== TRANSAKSI HISTORY ==========
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/', [TransaksiController::class, 'wargaIndex'])->name('index');
        Route::get('/today', [TransaksiController::class, 'wargaToday'])->name('today');
        Route::get('/monthly', [TransaksiController::class, 'wargaMonthly'])->name('monthly');
        Route::get('/{transaksi}', [TransaksiController::class, 'wargaShow'])->name('show');
        Route::get('/{transaksi}/print', [TransaksiController::class, 'wargaPrint'])->name('print');
        Route::get('/export', [TransaksiController::class, 'wargaExport'])->name('export');
        Route::get('/filter', [TransaksiController::class, 'wargaFilter'])->name('filter');
    });
    
    // ========== PENARIKAN POIN ==========
    Route::prefix('penarikan')->name('penarikan.')->group(function () {
        Route::get('/', [PenarikanPoinController::class, 'index'])->name('index');
        Route::get('/create', [PenarikanPoinController::class, 'create'])->name('create');
        Route::post('/', [PenarikanPoinController::class, 'store'])->name('store');
        
        // Filter routes - tambahkan parameter optional
        Route::get('/history', [PenarikanPoinController::class, 'index'])->name('history');
        Route::get('/pending', function() {
            return app()->make(PenarikanPoinController::class)->index();
        })->name('pending');
        Route::get('/approved', function() {
            return app()->make(PenarikanPoinController::class)->index();
        })->name('approved');
        Route::get('/completed', function() {
            return app()->make(PenarikanPoinController::class)->index();
        })->name('completed');
        Route::get('/rejected', function() {
            return app()->make(PenarikanPoinController::class)->index();
        })->name('rejected');
        
        Route::get('/{penarikan}', [PenarikanPoinController::class, 'show'])->name('show');
        
        // Untuk print receipt, tambahkan route baru di controller
        Route::get('/{penarikan}/print', [PenarikanPoinController::class, 'print'])->name('print');
        
        Route::delete('/{penarikan}', [PenarikanPoinController::class, 'destroy'])->name('destroy');
        
        // Untuk cancel, gunakan route destroy atau buat route baru
        Route::post('/{penarikan}/cancel', function($id) {
            return redirect()->route('warga.penarikan.destroy', $id);
        })->name('cancel');
    });
    
    // ========== KATEGORI INFO ==========
    Route::prefix('kategori')->name('kategori.')->group(function () {
        Route::get('/', [WargaController::class, 'kategoriIndex'])->name('index');
        Route::get('/{kategori}', [WargaController::class, 'kategoriShow'])->name('show');
        Route::get('/price-list', [WargaController::class, 'priceList'])->name('price-list');
        Route::get('/calculator', [WargaController::class, 'calculator'])->name('calculator');
        Route::post('/calculator/calculate', [WargaController::class, 'calculate'])->name('calculate');
    });
    
    // ========== POIN MANAGEMENT ==========
    Route::prefix('poin')->name('poin.')->group(function () {
        Route::get('/', [WargaController::class, 'poinIndex'])->name('index');
        Route::get('/history', [WargaController::class, 'poinHistory'])->name('history');
        Route::get('/detail', [WargaController::class, 'poinDetail'])->name('detail');
        Route::get('/transactions', [WargaController::class, 'poinTransactions'])->name('transactions');
        Route::get('/withdrawals', [WargaController::class, 'poinWithdrawals'])->name('withdrawals');
        Route::get('/leaderboard', [WargaController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/badges', [WargaController::class, 'badges'])->name('badges');
        Route::get('/rewards', [WargaController::class, 'rewards'])->name('rewards');
        Route::post('/redeem/{reward}', [WargaController::class, 'redeemReward'])->name('redeem');
    });
    
    // ========== PROFIL WARGA ==========
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [WargaController::class, 'profile'])->name('index');
        Route::put('/', [WargaController::class, 'updateProfile'])->name('update');
        Route::get('/achievements', [WargaController::class, 'achievements'])->name('achievements');
        Route::get('/stats', [WargaController::class, 'stats'])->name('stats');
        Route::get('/family', [WargaController::class, 'family'])->name('family');
        Route::post('/family/add', [WargaController::class, 'addFamily'])->name('family.add');
        Route::delete('/family/{member}', [WargaController::class, 'removeFamily'])->name('family.remove');
    });
});

// ==================== API ROUTES (AUTHENTICATED) ====================
Route::prefix('api')->middleware('auth')->group(function () {
    // QR Code API
    Route::prefix('scan')->group(function () {
        Route::post('/', [QrCodeController::class, 'apiProcessScan']);
        Route::get('/validate/{code}', [QrCodeController::class, 'apiValidateCode']);
        Route::get('/user/{id}', [QrCodeController::class, 'apiGetUserByQr']);
    });
    
    // Transaksi API
    Route::prefix('transaksi')->group(function () {
        Route::get('/recent', [TransaksiController::class, 'apiRecent']);
        Route::post('/calculate', [TransaksiController::class, 'apiCalculate']);
        Route::get('/stats', [TransaksiController::class, 'apiStats']);
        Route::get('/{id}/items', [TransaksiController::class, 'apiItems']);
        Route::post('/{id}/add-item', [TransaksiController::class, 'apiAddItem']);
        Route::delete('/{id}/remove-item/{itemId}', [TransaksiController::class, 'apiRemoveItem']);
    });
    
    // Notifikasi API
    Route::prefix('notifikasi')->group(function () {
        Route::get('/unread-count', [NotifikasiController::class, 'apiUnreadCount']);
        Route::get('/unread', [NotifikasiController::class, 'apiUnread']);
        Route::post('/mark-read/{id}', [NotifikasiController::class, 'apiMarkAsRead']);
        Route::post('/mark-all-read', [NotifikasiController::class, 'apiMarkAllAsRead']);
        Route::get('/settings', [NotifikasiController::class, 'apiSettings']);
        Route::post('/settings', [NotifikasiController::class, 'apiUpdateSettings']);
    });
    
    // Poin API
    Route::prefix('poin')->group(function () {
        Route::get('/balance', [WargaController::class, 'apiPoinBalance']);
        Route::get('/history', [WargaController::class, 'apiPoinHistory']);
        Route::get('/leaderboard', [WargaController::class, 'apiLeaderboard']);
        Route::get('/stats', [WargaController::class, 'apiPoinStats']);
        Route::post('/withdraw/calculate', [WargaController::class, 'apiCalculateWithdrawal']);
    });
    
    // Kategori API
    Route::prefix('kategori')->group(function () {
        Route::get('/', [KategoriController::class, 'apiIndex']);
        Route::get('/{id}', [KategoriController::class, 'apiShow']);
        Route::post('/calculate', [KategoriController::class, 'apiCalculate']);
        Route::get('/price-list', [KategoriController::class, 'apiPriceList']);
    });
    
    // User API
    Route::prefix('user')->group(function () {
        Route::get('/profile', [ProfileController::class, 'apiProfile']);
        Route::put('/profile', [ProfileController::class, 'apiUpdateProfile']);
        Route::get('/stats', [ProfileController::class, 'apiStats']);
        Route::get('/activity', [ProfileController::class, 'apiActivity']);
    });
    
    // Reports API
    Route::prefix('reports')->group(function () {
        Route::get('/dashboard-stats', [ReportController::class, 'apiDashboardStats']);
        Route::get('/transaksi-stats', [ReportController::class, 'apiTransaksiStats']);
        Route::get('/penarikan-stats', [ReportController::class, 'apiPenarikanStats']);
        Route::get('/user-stats', [ReportController::class, 'apiUserStats']);
        Route::get('/kategori-stats', [ReportController::class, 'apiKategoriStats']);
        Route::post('/export', [ReportController::class, 'apiExport']);
    });
    
    // Dashboard API
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'apiStats']);
        Route::get('/recent-activity', [DashboardController::class, 'apiRecentActivity']);
        Route::get('/charts', [DashboardController::class, 'apiCharts']);
        Route::get('/notifications', [DashboardController::class, 'apiNotifications']);
    });
});

// ==================== PUBLIC API ROUTES ====================
Route::prefix('public')->name('public.')->group(function () {
    // Public information
    Route::get('/kategori', [KategoriController::class, 'publicIndex'])->name('kategori');
    Route::get('/kategori/{kategori}', [KategoriController::class, 'publicShow'])->name('kategori.show');
    
    // Public calculator
    Route::get('/calculator', function () {
        return view('public.calculator');
    })->name('calculator');
    
    Route::post('/calculator/calculate', function (Request $request) {
        // Public calculation logic
        return response()->json(['result' => 'Hasil perhitungan']);
    })->name('calculator.calculate');
    
    // Public statistics
    Route::get('/stats', function () {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_transactions' => \App\Models\Transaksi::count(),
            'total_withdrawals' => \App\Models\PenarikanPoin::count(),
            'total_points' => \App\Models\User::sum('total_points'),
        ];
        
        return response()->json($stats);
    })->name('stats');
    
    // Public contact form
    Route::post('/contact', function (Request $request) {
        // Process contact form
        return response()->json(['message' => 'Pesan berhasil dikirim']);
    })->name('contact.submit');
});

// ==================== ERROR PAGES ====================
Route::get('/401', function () {
    return view('errors.401');
})->name('error.401');

Route::get('/403', function () {
    return view('errors.403');
})->name('error.403');

Route::get('/404', function () {
    return view('errors.404');
})->name('error.404');

Route::get('/419', function () {
    return view('errors.419');
})->name('error.419');

Route::get('/429', function () {
    return view('errors.429');
})->name('error.429');

Route::get('/500', function () {
    return view('errors.500');
})->name('error.500');

Route::get('/503', function () {
    return view('errors.503');
})->name('error.503');

// ==================== MAINTENANCE MODE ====================
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

// ==================== SESSION KEEP-ALIVE ====================
Route::post('/session/keep-alive', function () {
    session()->put('last_activity', time());
    return response()->json(['success' => true]);
})->name('session.keep-alive')->middleware('auth');

// ==================== FALLBACK ROUTE ====================
Route::fallback(function () {
    return view('errors.404');
});

// ==================== DEBUG ROUTES (ONLY LOCAL) ====================
if (app()->environment('local')) {
    Route::get('/debug/routes', function () {
        $routes = Route::getRoutes();
        
        $routeList = [];
        foreach ($routes as $route) {
            $routeList[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ];
        }
        
        return response()->json($routeList);
    })->name('debug.routes');
    
    Route::get('/debug/user', function () {
        return Auth::user();
    })->middleware('auth')->name('debug.user');
    
    Route::get('/debug/session', function () {
        return session()->all();
    })->name('debug.session');
    
    Route::get('/debug/env', function () {
        return [
            'app_env' => env('APP_ENV'),
            'app_debug' => env('APP_DEBUG'),
            'app_url' => env('APP_URL'),
            'db_connection' => env('DB_CONNECTION'),
        ];
    })->name('debug.env');
}