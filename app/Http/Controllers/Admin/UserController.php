<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
    // Query users dengan filter
    $query = User::with('role');
    
    // Filter search (nama/email)
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        });
    }
    
    // Filter role
    if ($request->has('role_id') && $request->role_id != '') {
        $query->where('role_id', $request->role_id);
    }
    
    // Filter verification
    if ($request->has('verification') && $request->verification != '') {
        if ($request->verification == 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($request->verification == 'unverified') {
            $query->whereNull('email_verified_at');
        }
    }
    
    // Order dan pagination
    $users = $query->orderBy('created_at', 'desc')->paginate(10);
    
    // Get total stats (TAMBAHKAN INI)
    $totalStats = [
        'total_users' => User::count(),
        'total_admin' => User::where('role_id', 1)->count(),
        'total_petugas' => User::where('role_id', 2)->count(),
        'total_warga' => User::where('role_id', 3)->count(),
        'verified_users' => User::whereNotNull('email_verified_at')->count(),
        'total_points' => User::sum('total_points'),
    ];
    
    // Get roles untuk dropdown filter
    $roles = Role::all();
    
    return view('admin.users.index', compact('users', 'roles', 'totalStats'));
}   
    
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'qr_code_token' => \Illuminate\Support\Str::random(32),
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat');
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|min:6|confirmed',
            'address' => 'nullable|string',
            'nik' => 'nullable|string|max:20',
            'rt_rw' => 'nullable|string|max:20',
            'area' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'total_points' => 'nullable|integer|min:0',
        ]);
        
        $data = $request->only([
            'name', 'email', 'phone', 'role_id', 'address', 
            'nik', 'rt_rw', 'area', 'bio', 'total_points'
        ]);
        
        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }
}