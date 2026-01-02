<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\Transaksi;
use App\Models\PenarikanPoin;
use App\Models\User;
use App\Models\ActivityLog;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Prepare additional data based on user role
        $data = [
            'user' => $user,
            'stats' => $this->getUserStats($user)
        ];
        
        // Add recent transactions for warga
        if ($user->role->name == 'warga') {
            $data['recentTransactions'] = Transaksi::where('warga_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }
        
        return view('profile.edit', $data);
    }

    /**
     * Display preferences page.
     */
    public function preferences(): View
    {
        $user = Auth::user();
        
        // Get current preferences
        $preferences = $this->getUserPreferences($user);
        
        return view('profile.partials.preferences', compact('preferences'));
    }
    
    /**
     * Get user preferences from database.
     */
    private function getUserPreferences($user): array
    {
        // Default preferences
        $defaultPreferences = [
            'theme' => 'light',
            'language' => 'id',
            'timezone' => 'WIB',
            'notifications' => [
                'transaction' => true,
                'points' => true,
                'withdrawal' => true,
                'promo' => true,
                'system' => true,
            ],
            'privacy' => [
                'public_profile' => false,
                'show_activity' => true,
                'profile_searchable' => true,
            ]
        ];
        
        // Get from database
        $dbPreferences = [
            'notifications' => $user->notification_preferences ?? [],
            'privacy' => $user->privacy_settings ?? [],
        ];
        
        // Get theme and language from cookies (or session)
        $theme = request()->cookie('theme', $defaultPreferences['theme']);
        $language = request()->cookie('language', $defaultPreferences['language']);
        $timezone = request()->cookie('timezone', $defaultPreferences['timezone']);
        
        // Merge preferences
        $preferences = [
            'theme' => $theme,
            'language' => $language,
            'timezone' => $timezone,
            'notifications' => array_merge(
                $defaultPreferences['notifications'],
                $dbPreferences['notifications']
            ),
            'privacy' => array_merge(
                $defaultPreferences['privacy'],
                $dbPreferences['privacy']
            )
        ];
        
        return $preferences;
    }
    
    /**
     * Update user preferences.
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Validate input
        $validated = $request->validate([
            'theme' => ['nullable', 'in:light,dark'],
            'language' => ['nullable', 'in:id,en'],
            'timezone' => ['nullable', 'in:WIB,WITA,WIT'],
            'notifications.transaction' => ['nullable', 'boolean'],
            'notifications.points' => ['nullable', 'boolean'],
            'notifications.withdrawal' => ['nullable', 'boolean'],
            'notifications.promo' => ['nullable', 'boolean'],
            'notifications.system' => ['nullable', 'boolean'],
            'privacy.public_profile' => ['nullable', 'boolean'],
            'privacy.show_activity' => ['nullable', 'boolean'],
            'privacy.profile_searchable' => ['nullable', 'boolean'],
        ]);
        
        // Update notification preferences
        if (isset($validated['notifications'])) {
            $user->notification_preferences = $validated['notifications'];
        }
        
        // Update privacy settings
        if (isset($validated['privacy'])) {
            $user->privacy_settings = $validated['privacy'];
        }
        
        $user->save();
        
        // Prepare redirect response
        $response = Redirect::route('profile.edit')->with([
            'success' => 'Preferensi berhasil diperbarui!',
            'active_tab' => 'preferences'
        ]);
        
        // Set cookies
        if (isset($validated['theme'])) {
            $response->cookie('theme', $validated['theme'], 60 * 24 * 365); // 1 year
            // Update session theme
            session(['theme' => $validated['theme']]);
        }
        
        if (isset($validated['language'])) {
            $response->cookie('language', $validated['language'], 60 * 24 * 365);
            // Update app locale
            session(['locale' => $validated['language']]);
        }
        
        if (isset($validated['timezone'])) {
            $response->cookie('timezone', $validated['timezone'], 60 * 24 * 365);
            session(['timezone' => $validated['timezone']]);
        }
        
        return $response;
    }
    
    /**
     * Apply theme to application.
     */
    public function applyTheme(Request $request)
    {
        $theme = $request->cookie('theme', 'light');
        
        // Remove auto option, only light or dark
        if ($theme === 'auto') {
            $theme = 'light';
        }
        
        return response()->json(['theme' => $theme]);
    }
    
    /**
     * Get user statistics based on role
     */
    private function getUserStats($user): array
    {
        $stats = [
            'join_date' => $user->created_at->diffForHumans(),
            'status' => 'Aktif',
            'email_verified' => $user->hasVerifiedEmail(),
            'total_points' => 0,
            'total_transactions' => 0,
            'withdrawn_points' => 0,
            'pending_points' => 0
        ];
        
        if ($user->role->name == 'warga') {
            $stats['total_points'] = $user->total_points ?? 0;
            $stats['total_transactions'] = Transaksi::where('warga_id', $user->id)->count();
            $stats['withdrawn_points'] = PenarikanPoin::where('warga_id', $user->id)
                ->where('status', 'completed')
                ->sum('jumlah_poin');
            $stats['pending_points'] = PenarikanPoin::where('warga_id', $user->id)
                ->where('status', 'pending')
                ->sum('jumlah_poin');
        }
        
        return $stats;
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Handle profile photo upload
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            // Store new photo
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        // Handle photo removal
        if ($request->has('remove_photo') && $request->remove_photo == '1') {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
                $user->profile_photo_path = null;
            }
        }
        
        // Update other profile fields
        $validated = $request->validated();
        $user->fill($validated);
        
        // Update additional fields
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->bio = $request->bio;
        
        if ($user->role->name == 'warga') {
            $user->nik = $request->nik;
            $user->rt_rw = $request->rt_rw;
        }
        
        if ($user->role->name == 'petugas') {
            $user->area = $request->area;
        }
        
        // Reset email verification if email changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return Redirect::route('profile.edit')->with([
            'success' => 'Password berhasil diubah!',
            'active_tab' => 'security'
        ]);
    }
    
    /**
     * Update profile photo only.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $user = $request->user();
        
        // Delete old photo if exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        
        // Store new photo
        $path = $request->file('photo')->store('profile-photos', 'public');
        $user->profile_photo_path = $path;
        $user->save();
        
        return Redirect::route('profile.edit')->with('success', 'Foto profil berhasil diubah!');
    }
    
    /**
     * Display activity log page.
     */
    public function activity(Request $request): View
    {
        $user = $request->user();
        
        // Simulate activity logs (you can create ActivityLog model later)
        $activities = [];
        
        // Example activities based on user role
        if ($user->role->name == 'warga') {
            $activities = [
                [
                    'icon' => 'bi-receipt',
                    'color' => 'success',
                    'title' => 'Transaksi sampah',
                    'description' => 'Menyerahkan 2.5 kg sampah plastik',
                    'time' => Carbon::now()->subHours(2)->diffForHumans(),
                    'points' => '+250'
                ],
                [
                    'icon' => 'bi-cash-coin',
                    'color' => 'warning',
                    'title' => 'Penarikan poin',
                    'description' => 'Mengajukan penarikan 500 poin',
                    'time' => Carbon::now()->subDays(1)->diffForHumans(),
                    'points' => '-500'
                ],
                [
                    'icon' => 'bi-star',
                    'color' => 'primary',
                    'title' => 'Poin bertambah',
                    'description' => 'Menerima poin dari transaksi',
                    'time' => Carbon::now()->subDays(3)->diffForHumans(),
                    'points' => '+150'
                ],
            ];
        } elseif ($user->role->name == 'petugas') {
            $activities = [
                [
                    'icon' => 'bi-qr-code-scan',
                    'color' => 'primary',
                    'title' => 'Scan QR Code',
                    'description' => 'Memindai QR code warga',
                    'time' => Carbon::now()->subHours(1)->diffForHumans(),
                    'points' => null
                ],
                [
                    'icon' => 'bi-plus-circle',
                    'color' => 'success',
                    'title' => 'Input transaksi',
                    'description' => 'Memasukkan transaksi sampah',
                    'time' => Carbon::now()->subHours(3)->diffForHumans(),
                    'points' => null
                ],
                [
                    'icon' => 'bi-person-check',
                    'color' => 'info',
                    'title' => 'Verifikasi warga',
                    'description' => 'Memverifikasi data warga baru',
                    'time' => Carbon::now()->subDays(2)->diffForHumans(),
                    'points' => null
                ],
            ];
        }
        
        $stats = [
            'today_activities' => count($activities),
            'month_activities' => count($activities) * 7,
            'total_activities' => count($activities) * 30
        ];
        
        return view('profile.partials.activity-log', compact('activities', 'stats'));
    }
    
    /**
     * Display security settings page.
     */
    public function security(Request $request): View
    {
        $user = $request->user();
        
        // Login history simulation
        $login_history = [
            [
                'device' => 'Chrome on Windows',
                'ip' => $request->ip(),
                'location' => 'Jakarta, Indonesia',
                'time' => Carbon::now()->subMinutes(15),
                'current' => true
            ],
            [
                'device' => 'Safari on iPhone',
                'ip' => '192.168.1.100',
                'location' => 'Bandung, Indonesia',
                'time' => Carbon::now()->subDays(1),
                'current' => false
            ],
            [
                'device' => 'Firefox on Android',
                'ip' => '192.168.1.101',
                'location' => 'Surabaya, Indonesia',
                'time' => Carbon::now()->subDays(3),
                'current' => false
            ],
        ];
        
        $security_data = [
            'last_password_change' => $user->updated_at->diffForHumans(),
            'two_factor_enabled' => false,
            'active_sessions' => count($login_history)
        ];
        
        return view('profile.partials.security', compact('login_history', 'security_data'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Akun berhasil dihapus.');
    }
}