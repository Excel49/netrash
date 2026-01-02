<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotifikasiController extends Controller
{
    /**
     * Display notifications for authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all notifications for the user
        $notifikasi = Notifikasi::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('notifikasi.index', compact('notifikasi'));
    }
    
    /**
     * Display notification creation form for petugas
     */
    public function create()
    {
        // Only petugas can send notifications
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || $user->role_id != 2) { // 2 = petugas
            abort(403);
        }
        
        // Get all warga for petugas to send notifications to
        $petugasId = Auth::id();
        $warga = User::where('role_id', 3) // role warga
            ->whereHas('transaksiSebagaiWarga', function($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            })
            ->orderBy('name')
            ->get();
        
        return view('petugas.notifikasi.create', compact('warga'));
    }
    
    /**
     * Store new notification from petugas
     */
    public function store(Request $request)
    {
        // Only petugas can send notifications
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || $user->role_id != 2) { // 2 = petugas
            abort(403);
        }
        
        $validated = $request->validate([
            'warga_id' => 'required|array|min:1',
            'warga_id.*' => 'exists:users,id',
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
            'tipe' => 'nullable|in:info,warning,important,transaction',
        ]);
        
        $petugas = Auth::user();
        
        // Create notifications for each selected warga
        foreach ($validated['warga_id'] as $wargaId) {
            Notifikasi::create([
                'user_id' => $wargaId,
                'judul' => $validated['judul'],
                'pesan' => $validated['pesan'],
                'tipe' => $validated['tipe'] ?? 'info',
                'data' => [
                    'sender_id' => $petugas->id,
                    'sender_name' => $petugas->name,
                    'sender_role' => 'petugas',
                ],
                'is_read' => false,
            ]);
        }
        
        return redirect()->route('notifikasi.index')
            ->with('success', 'Notifikasi berhasil dikirim ke ' . count($validated['warga_id']) . ' warga');
    }
    
    /**
     * Display notification settings
     */
    public function settings()
    {
        $user = Auth::user();
        $preferences = $user->notification_preferences ?? [
            'transaction' => true,
            'points' => true,
            'withdrawal' => true,
            'promo' => false,
            'system' => true,
            'petugas_message' => true, // Tambahkan untuk notifikasi dari petugas
        ];
        
        return view('profile.partials.notification-settings', compact('preferences'));
    }
    
    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'transaction' => ['nullable', 'boolean'],
            'points' => ['nullable', 'boolean'],
            'withdrawal' => ['nullable', 'boolean'],
            'promo' => ['nullable', 'boolean'],
            'system' => ['nullable', 'boolean'],
            'petugas_message' => ['nullable', 'boolean'],
        ]);
        
        $user->notification_preferences = $validated;
        $user->save();
        
        return redirect()->back()->with('success', 'Pengaturan notifikasi berhasil diperbarui!');
    }
    
    /**
     * Mark single notification as read
     */
    public function markAsRead($id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        
        // Check if notification belongs to user
        if ($notifikasi->user_id != Auth::id()) {
            abort(403);
        }
        
        // Update using model
        $notifikasi->is_read = true;
        $notifikasi->save();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        // Update all user notifications
        Notifikasi::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Display sent notifications for petugas
     */
    public function sent()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || $user->role_id != 2) { // 2 = petugas
            abort(403);
        }
        
        $petugasId = Auth::id();
        
        // Get notifications sent by this petugas
        $notifikasi = Notifikasi::where('data->sender_id', $petugasId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('petugas.notifikasi.sent', compact('notifikasi'));
    }
    
    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        
        // Check if notification belongs to user
        if ($notifikasi->user_id != Auth::id()) {
            abort(403);
        }
        
        $notifikasi->delete();
        
        return response()->json(['success' => true]);
    }
    
    public function unread()
{
    $user = Auth::user();
    
    // Get unread notifications for the user
    $notifikasi = Notifikasi::where('user_id', $user->id)
        ->where('is_read', false)
        ->orderBy('created_at', 'desc')
        ->paginate(15);
        
    return view('notifikasi.unread', compact('notifikasi'));
}

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        $user = Auth::user();
        
        Notifikasi::where('user_id', $user->id)->delete();
        
        return response()->json(['success' => true]);
    }
    
    // ==================== API METHODS ====================
    
    public function apiUnreadCount()
    {
        $user = Auth::user();
        $count = Notifikasi::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }
    
    public function apiUnread()
    {
        $user = Auth::user();
        $notifications = Notifikasi::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json($notifications);
    }
    
    public function apiMarkAsRead($id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        
        if ($notifikasi->user_id != Auth::id()) {
            return response()->json(['success' => false], 403);
        }
        
        $notifikasi->is_read = true;
        $notifikasi->save();
        
        return response()->json(['success' => true]);
    }
    
    public function apiMarkAllAsRead()
    {
        $user = Auth::user();
        
        Notifikasi::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
    
    public function apiSettings()
    {
        $user = Auth::user();
        $preferences = $user->notification_preferences ?? [
            'transaction' => true,
            'points' => true,
            'withdrawal' => true,
            'promo' => false,
            'system' => true,
            'petugas_message' => true,
        ];
        
        return response()->json($preferences);
    }
    
    public function apiUpdateSettings(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'transaction' => ['nullable', 'boolean'],
            'points' => ['nullable', 'boolean'],
            'withdrawal' => ['nullable', 'boolean'],
            'promo' => ['nullable', 'boolean'],
            'system' => ['nullable', 'boolean'],
            'petugas_message' => ['nullable', 'boolean'],
        ]);
        
        $user->notification_preferences = $validated;
        $user->save();
        
        return response()->json(['success' => true]);
    }
}