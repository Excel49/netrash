@extends('layouts.app')

@section('title', 'Profile - ' . auth()->user()->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">Profile Settings</h2>
        <p class="text-muted">Kelola informasi akun dan pengaturan Anda</p>
    </div>
</div>

<div class="row">
    <!-- Sidebar Navigation -->
    <div class="col-lg-3 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <!-- Profile Photo -->
                <div class="mb-4">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ auth()->user()->profile_photo_url }}" 
                             alt="{{ auth()->user()->name }}" 
                             class="rounded-circle border border-4 border-primary shadow-sm"
                             style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center"
                             style="width: 120px; height: 120px; border: 4px solid var(--bs-primary);">
                            <span class="text-white fw-bold display-6">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>
                
                <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                
                @php
                    $roleColor = '';
                    switch(auth()->user()->role->name) {
                        case 'admin': $roleColor = 'danger'; break;
                        case 'petugas': $roleColor = 'primary'; break;
                        case 'warga': $roleColor = 'success'; break;
                        default: $roleColor = 'secondary';
                    }
                @endphp
                <span class="badge bg-{{ $roleColor }} mb-3">
                    {{ ucfirst(auth()->user()->role->name) }}
                </span>
                
                <!-- Quick Stats -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Bergabung</span>
                        <span class="fw-medium">{{ auth()->user()->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Status</span>
                        <span class="fw-medium text-success">Aktif</span>
                    </div>
                    @if(auth()->user()->hasVerifiedEmail())
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Email</span>
                        <span class="fw-medium text-success">Terverifikasi</span>
                    </div>
                    @else
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Email</span>
                        <span class="fw-medium text-warning">Belum Verifikasi</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Navigation -->
            <div class="list-group list-group-flush">
                <a href="#profile-info" onclick="showTab('profile-info')" 
                   class="list-group-item list-group-item-action border-0 active">
                    <i class="bi bi-person me-2"></i>Profile Information
                </a>
                <a href="#password" onclick="showTab('password')" 
                   class="list-group-item list-group-item-action border-0">
                    <i class="bi bi-lock me-2"></i>Password
                </a>
                <a href="#security" onclick="showTab('security')" 
                   class="list-group-item list-group-item-action border-0">
                    <i class="bi bi-shield-check me-2"></i>Security
                </a>
                @if(auth()->user()->role->name == 'warga')
                <a href="#points" onclick="showTab('points')" 
                   class="list-group-item list-group-item-action border-0">
                    <i class="bi bi-coin me-2"></i>My Points
                </a>
                @endif
                @if(in_array(auth()->user()->role->name, ['petugas', 'admin']))
                <a href="#activity" onclick="showTab('activity')" 
                   class="list-group-item list-group-item-action border-0">
                    <i class="bi bi-activity me-2"></i>Activity Log
                </a>
                @endif
                <a href="#preferences" onclick="showTab('preferences')" 
                   class="list-group-item list-group-item-action border-0">
                    <i class="bi bi-gear me-2"></i>Preferences
                </a>
                <a href="#delete-account" onclick="showTab('delete-account')" 
                   class="list-group-item list-group-item-action border-0 text-danger">
                    <i class="bi bi-trash me-2"></i>Delete Account
                </a>
            </div>
            
            <!-- Logout Button -->
            <div class="card-footer">
                <form method="POST" action="{{ route('logout') }}" class="d-grid">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Points Summary (for warga) -->
        @if(auth()->user()->role->name == 'warga')
        <div class="card mt-4">
            <div class="card-body">
                <h6 class="card-title mb-3">Points Summary</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Points</span>
                    <span class="fw-bold text-success">{{ number_format(auth()->user()->total_points, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Equivalent</span>
                    <span class="fw-medium">Rp {{ number_format(auth()->user()->total_points * 100, 0, ',', '.') }}</span>
                </div>
                <div class="mt-3">
                    <a href="{{ route('warga.penarikan.create') }}" class="btn btn-netra btn-sm w-100">
                        <i class="bi bi-cash-coin me-1"></i> Withdraw Points
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Main Content -->
    <div class="col-lg-9">
        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Validation Errors</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        <!-- Tab Contents -->
        
        <!-- Profile Information -->
        <div id="profile-info-tab" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Profile Information</h5>
                    <p class="text-muted mb-0">Update your account's profile information and email address</p>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profile-form">
                        @csrf
                        @method('patch')
                        
                        <!-- Profile Photo -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Profile Photo</label>
                                <div class="position-relative">
                                    @if(auth()->user()->profile_photo_path)
                                        <img id="profile-preview" 
                                             src="{{ auth()->user()->profile_photo_url }}" 
                                             alt="{{ auth()->user()->name }}" 
                                             class="rounded-circle border border-3 border-primary"
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div id="profile-initials" 
                                             class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                             style="width: 150px; height: 150px; border: 3px solid var(--bs-primary);">
                                            <span class="text-white fw-bold display-5">
                                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <img id="profile-preview" src="" alt="Preview" 
                                             class="rounded-circle border border-3 border-primary d-none"
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    @endif
                                    
                                    <label for="photo" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer"
                                           style="width: 40px; height: 40px; transform: translate(25%, 25%);">
                                        <i class="bi bi-camera"></i>
                                        <input type="file" id="photo" name="photo" class="d-none" accept="image/*" onchange="previewImage(event)">
                                    </label>
                                </div>
                                <div class="mt-2">
                                    @if(auth()->user()->profile_photo_path)
                                    <button type="button" onclick="removePhoto()" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i>Remove
                                    </button>
                                    @endif
                                </div>
                                <small class="text-muted d-block mt-1">Max 2MB. JPG, PNG, GIF</small>
                            </div>
                            
                            <div class="col-md-9">
                                <div class="row">
                                    <!-- Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" 
                                               class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name', auth()->user()->name) }}"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Email -->
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" id="email" name="email" 
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email', auth()->user()->email) }}"
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        
                                        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                                        <div class="alert alert-warning mt-2 mb-0 py-2">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            Your email address is unverified.
                                            <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-link p-0 ms-1">
                                                    Click here to re-send the verification email.
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Phone -->
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" id="phone" name="phone" 
                                               class="form-control @error('phone') is-invalid @enderror"
                                               value="{{ old('phone', auth()->user()->phone) }}"
                                               placeholder="+62 812 3456 7890">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Role-specific fields -->
                                    @if(auth()->user()->role->name == 'warga')
                                    <div class="col-md-6 mb-3">
                                        <label for="nik" class="form-label">NIK</label>
                                        <input type="text" id="nik" name="nik" 
                                               class="form-control @error('nik') is-invalid @enderror"
                                               value="{{ old('nik', auth()->user()->nik) }}"
                                               placeholder="16 digit NIK">
                                        @error('nik')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="rt_rw" class="form-label">RT/RW</label>
                                        <input type="text" id="rt_rw" name="rt_rw" 
                                               class="form-control @error('rt_rw') is-invalid @enderror"
                                               value="{{ old('rt_rw', auth()->user()->rt_rw) }}"
                                               placeholder="001/002">
                                        @error('rt_rw')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @endif
                                    
                                    @if(auth()->user()->role->name == 'petugas')
                                    <div class="col-md-6 mb-3">
                                        <label for="area" class="form-label">Area Tugas</label>
                                        <input type="text" id="area" name="area" 
                                               class="form-control @error('area') is-invalid @enderror"
                                               value="{{ old('area', auth()->user()->area) }}"
                                               placeholder="Area 1, Zone A, etc">
                                        @error('area')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" name="address" rows="3"
                                      class="form-control @error('address') is-invalid @enderror"
                                      placeholder="Enter your complete address">{{ old('address', auth()->user()->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Bio -->
                        <div class="mb-4">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea id="bio" name="bio" rows="3"
                                      class="form-control @error('bio') is-invalid @enderror"
                                      placeholder="Tell us a little about yourself">{{ old('bio', auth()->user()->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end pt-4 border-top">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-netra">
                                <i class="bi bi-check-circle me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Password -->
        <div id="password-tab" class="tab-content d-none">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lock me-2"></i>Update Password</h5>
                    <p class="text-muted mb-0">Ensure your account is using a long, random password to stay secure</p>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.password.update') }}" id="password-form">
                        @csrf
                        @method('patch')
                        
                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="current_password" name="current_password" 
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       placeholder="Enter current password"
                                       required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('current_password')">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" 
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter new password (min 8 characters)"
                                       required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Password Strength Meter -->
                            <div class="mt-3">
                                <div class="progress" style="height: 8px;">
                                    <div id="strength-bar-1" class="progress-bar" style="width: 25%"></div>
                                    <div id="strength-bar-2" class="progress-bar" style="width: 25%"></div>
                                    <div id="strength-bar-3" class="progress-bar" style="width: 25%"></div>
                                    <div id="strength-bar-4" class="progress-bar" style="width: 25%"></div>
                                </div>
                                <small id="strength-text" class="mt-2 d-block"></small>
                            </div>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="password_confirmation" name="password_confirmation" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                       placeholder="Confirm new password"
                                       required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Password Requirements -->
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Password Requirements</h6>
                            <ul class="mb-0 ps-3">
                                <li id="req-length" class="text-muted"><i class="bi bi-circle me-1"></i> Minimum 8 characters</li>
                                <li id="req-uppercase" class="text-muted"><i class="bi bi-circle me-1"></i> Include uppercase and lowercase letters</li>
                                <li id="req-number" class="text-muted"><i class="bi bi-circle me-1"></i> Include at least one number</li>
                                <li id="req-special" class="text-muted"><i class="bi bi-circle me-1"></i> Include special characters (optional but recommended)</li>
                            </ul>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end pt-4 border-top">
                            <button type="submit" class="btn btn-netra">
                                <i class="bi bi-key me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Security -->
        <div id="security-tab" class="tab-content d-none">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Security Settings</h5>
                    <p class="text-muted mb-0">Manage your account security and enable additional security features</p>
                </div>
                <div class="card-body">
                    <!-- Two-Factor Authentication -->
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6>Two-Factor Authentication</h6>
                                <p class="text-muted mb-0">Add an extra layer of security to your account</p>
                            </div>
                            <span class="badge bg-warning">Not Enabled</span>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to log in.
                        </div>
                        
                        <div class="text-center">
                            <button class="btn btn-outline-primary">
                                <i class="bi bi-shield-check me-2"></i>Enable Two-Factor Authentication
                            </button>
                        </div>
                    </div>
                    
                    <!-- Session Management -->
                    <div class="mb-5">
                        <h6 class="mb-3">Browser Sessions</h6>
                        <p class="text-muted">Manage and log out your active sessions on other browsers and devices.</p>
                        
                        <div class="card bg-light border mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Current Session</strong>
                                        <p class="mb-0 text-muted">
                                            {{ request()->ip() }} • {{ request()->header('User-Agent') }}
                                        </p>
                                    </div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                        
                        <button class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Log Out Other Browser Sessions
                        </button>
                    </div>
                    
                    <!-- Login History -->
                    <div class="mb-4">
                        <h6 class="mb-3">Recent Login History</h6>
                        
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>IP Address</th>
                                        <th>Browser</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 0; $i < 3; $i++)
                                    <tr>
                                        <td>{{ now()->subDays($i)->format('d/m/Y H:i') }}</td>
                                        <td>192.168.1.{{ $i + 1 }}</td>
                                        <td>Chrome {{ 120 - $i }}</td>
                                        <td>
                                            <span class="badge bg-success">Success</span>
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Points (Warga only) -->
        @if(auth()->user()->role->name == 'warga')
        <div id="points-tab" class="tab-content d-none">
            @php
                $user = auth()->user();
                $recentTransactions = $user->transaksiSebagaiWarga()->with('petugas')->orderBy('created_at', 'desc')->take(5)->get();
                $withdrawnPoints = $user->penarikanPoin()->where('status', 'completed')->sum('jumlah_poin');
                $pendingPoints = $user->penarikanPoin()->where('status', 'pending')->sum('jumlah_poin');
            @endphp
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-coin me-2"></i>My Points</h5>
                    <p class="text-muted mb-0">Manage and track points earned from waste transactions</p>
                </div>
                <div class="card-body">
                    <!-- Points Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Total Points</h6>
                                            <h3 class="mb-0">{{ number_format($user->total_points, 0, ',', '.') }}</h3>
                                            <small>Equivalent to Rp {{ number_format($user->total_points * 100, 0, ',', '.') }}</small>
                                        </div>
                                        <i class="bi bi-coin display-6 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Total Transactions</h6>
                                            <h3 class="mb-0">{{ $user->transaksiSebagaiWarga()->count() }}</h3>
                                            <small>Waste transactions</small>
                                        </div>
                                        <i class="bi bi-receipt display-6 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Points Withdrawn</h6>
                                            <h3 class="mb-0">{{ number_format($withdrawnPoints, 0, ',', '.') }}</h3>
                                            <small>Successfully withdrawn</small>
                                        </div>
                                        <i class="bi bi-cash-coin display-6 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Pending Points</h6>
                                            <h3 class="mb-0">{{ number_format($pendingPoints, 0, ',', '.') }}</h3>
                                            <small>Awaiting approval</small>
                                        </div>
                                        <i class="bi bi-clock-history display-6 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>Quick Actions</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('warga.penarikan.create') }}" class="btn btn-netra">
                                    <i class="bi bi-cash-coin me-2"></i>Withdraw Points
                                </a>
                                <a href="{{ route('warga.transaksi.index') }}" class="btn btn-outline-netra">
                                    <i class="bi bi-receipt me-2"></i>Transaction History
                                </a>
                                <a href="{{ route('warga.penarikan.index') }}" class="btn btn-outline-netra">
                                    <i class="bi bi-clock-history me-2"></i>Withdrawal History
                                </a>
                                {{-- <a href="{{ route('warga.leaderboard') }}" class="btn btn-outline-netra">
                                    <i class="bi bi-trophy me-2"></i>Leaderboard
                                </a> --}}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Transactions -->
                    <div>
                        <h6 class="mb-3">Recent Transactions</h6>
                        
                        @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Officer</th>
                                        <th>Points</th>
                                        <th>Weight</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $transaction->petugas->name ?? 'System' }}</td>
                                        <td class="text-success">+{{ number_format($transaction->total_poin, 0, ',', '.') }}</td>
                                        <td>{{ number_format($transaction->total_berat, 1) }} kg</td>
                                        <td>
                                            @if($transaction->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                            @elseif($transaction->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                            @else
                                            <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('warga.transaksi.index') }}" class="btn btn-sm btn-outline-netra">
                                View All Transactions <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="bi bi-receipt display-4 text-muted mb-3"></i>
                            <p class="text-muted">No transactions yet</p>
                            <a href="{{ route('warga.qrcode.index') }}" class="btn btn-netra">
                                <i class="bi bi-qr-code-scan me-2"></i>Start Your First Transaction
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Activity Log (Admin/Petugas) -->
        @if(in_array(auth()->user()->role->name, ['petugas', 'admin']))
        <div id="activity-tab" class="tab-content d-none">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Activity Log</h5>
                    <p class="text-muted mb-0">Monitor recent activities on your account</p>
                </div>
                <div class="card-body">
                    <!-- Activity Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase text-muted mb-2">Today</h6>
                                    <h3 class="mb-0">0</h3>
                                    <small>Activities</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase text-muted mb-2">This Month</h6>
                                    <h3 class="mb-0">0</h3>
                                    <small>Activities</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase text-muted mb-2">Total</h6>
                                    <h3 class="mb-0">0</h3>
                                    <small>Activities</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activity Timeline -->
                    <h6 class="mb-3">Recent Activities</h6>
                    
                    <div class="text-center py-4">
                        <i class="bi bi-activity display-4 text-muted mb-3"></i>
                        <p class="text-muted">No recent activities</p>
                    </div>
                    
                    <!-- Export Button -->
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-netra">
                            <i class="bi bi-download me-2"></i>Export Activity Log
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Preferences -->
        <div id="preferences-tab" class="tab-content d-none">
            @include('profile.partials.preferences', ['preferences' => $preferences ?? []])
        </div>
        
        <!-- Delete Account -->
        <div id="delete-account-tab" class="tab-content d-none">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-trash me-2"></i>Delete Account</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Warning: This action cannot be undone</h6>
                        <p class="mb-0">
                            Once your account is deleted, all of your data will be permanently removed. 
                            This includes:
                        </p>
                        <ul class="mb-0 mt-2">
                            <li>Your profile information</li>
                            <li>All transaction history</li>
                            <li>Your points and withdrawal records</li>
                            <li>Any other data associated with your account</li>
                        </ul>
                    </div>
                    
                    <form method="post" action="{{ route('profile.destroy') }}" id="delete-form">
                        @csrf
                        @method('delete')
                        
                        <!-- Password Confirmation -->
                        <div class="mb-4">
                            <label for="delete_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="delete_password" name="password" 
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter your password to confirm"
                                       required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('delete_password')">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Confirmation Checkbox -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm-delete" required>
                            <label class="form-check-label text-danger" for="confirm-delete">
                                I understand that this action cannot be undone and I want to permanently delete my account.
                            </label>
                        </div>
                        
                        <!-- Delete Button -->
                        <div class="d-flex justify-content-end">
                            <button type="button" onclick="confirmDelete()" 
                                    class="btn btn-danger"
                                    id="delete-btn" disabled>
                                <i class="bi bi-trash me-2"></i>Delete Account Permanently
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
    }
    
    .tab-content {
        transition: opacity 0.3s ease;
    }
    
    .progress-bar {
        border-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<script>
// Tab Navigation
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('d-none');
    });
    
    // Remove active class from all nav items
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Show selected tab
    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) {
        selectedTab.classList.remove('d-none');
    }
    
    // Add active class to clicked nav item
    const activeNav = document.querySelector(`[href="#${tabName}"]`);
    if (activeNav) {
        activeNav.classList.add('active');
    }
}

// Password toggle visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Image preview
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('profile-preview');
    const initials = document.getElementById('profile-initials');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            if (initials) initials.classList.add('d-none');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove photo
function removePhoto() {
    if (confirm('Are you sure you want to remove your profile photo?')) {
        // Create a hidden input to indicate photo removal
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_photo';
        input.value = '1';
        document.getElementById('profile-form').appendChild(input);
        
        // Hide current photo and show initials
        document.getElementById('profile-preview').classList.add('d-none');
        document.getElementById('profile-initials').classList.remove('d-none');
    }
}

// Password strength checker
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            updateStrengthMeter(strength);
            updateRequirements(password);
        });
    }
    
    // Delete account confirmation
    const confirmCheckbox = document.getElementById('confirm-delete');
    const deleteBtn = document.getElementById('delete-btn');
    if (confirmCheckbox && deleteBtn) {
        confirmCheckbox.addEventListener('change', function() {
            deleteBtn.disabled = !this.checked;
        });
    }
});

function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

function updateStrengthMeter(strength) {
    const bars = [
        document.getElementById('strength-bar-1'),
        document.getElementById('strength-bar-2'),
        document.getElementById('strength-bar-3'),
        document.getElementById('strength-bar-4')
    ];
    
    const texts = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];
    const colors = ['danger', 'warning', 'info', 'primary', 'success'];
    
    bars.forEach((bar, index) => {
        bar.className = 'progress-bar';
        if (index < strength) {
            bar.classList.add('bg-' + colors[strength - 1]);
        } else {
            bar.classList.add('bg-light');
        }
    });
    
    const textElement = document.getElementById('strength-text');
    if (textElement) {
        textElement.textContent = texts[strength - 1] || '';
        textElement.className = 'mt-2 d-block text-' + (colors[strength - 1] || 'secondary');
    }
}

function updateRequirements(password) {
    const requirements = {
        'req-length': password.length >= 8,
        'req-uppercase': /[A-Z]/.test(password) && /[a-z]/.test(password),
        'req-number': /[0-9]/.test(password),
        'req-special': /[^A-Za-z0-9]/.test(password)
    };
    
    for (const [id, met] of Object.entries(requirements)) {
        const element = document.getElementById(id);
        if (element) {
            if (met) {
                element.classList.remove('text-muted');
                element.classList.add('text-success');
                element.querySelector('i').className = 'bi bi-check-circle-fill me-1';
            } else {
                element.classList.remove('text-success');
                element.classList.add('text-muted');
                element.querySelector('i').className = 'bi bi-circle me-1';
            }
        }
    }
}

// Confirm account deletion
function confirmDelete() {
    if (confirm('Are you absolutely sure? This action cannot be undone!')) {
        document.getElementById('delete-form').submit();
    }
}

// Initialize first tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('profile-info');
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Profile form validation
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
        });
    }
    
    // Password form validation
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return false;
            }
        });
    }
});
</script>
@endpush
@endsection