@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-4 mb-4">
            <!-- Profile Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <!-- Profile Photo -->
                    <div class="mb-3">
                        @if(auth()->user()->profile_photo_path)
                            <img src="{{ auth()->user()->profile_photo_url }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="rounded-circle border border-3 border-netra"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-netra d-inline-flex align-items-center justify-content-center"
                                 style="width: 100px; height: 100px; border: 3px solid #2E8B57;">
                                <span class="text-white fw-bold" style="font-size: 2rem;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                    
                    @php
                        $roleColor = auth()->user()->isAdmin() ? 'danger' : (auth()->user()->isPetugas() ? 'warning' : 'success');
                    @endphp
                    <span class="badge bg-{{ $roleColor }} mb-3">
                        {{ ucfirst(auth()->user()->role->name) }}
                    </span>
                    
                    <!-- Quick Stats -->
                    <div class="mt-3 small text-muted">
                        <div class="mb-1">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Bergabung {{ auth()->user()->created_at->format('d M Y') }}
                        </div>
                        @if(auth()->user()->hasVerifiedEmail())
                        <div class="text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            Email Terverifikasi
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Navigation -->
                <div class="list-group list-group-flush">
                    <a href="#profile" onclick="showTab('profile')" 
                       class="list-group-item list-group-item-action border-0 active">
                        <i class="fas fa-user me-2"></i>Profile
                    </a>
                    <a href="#password" onclick="showTab('password')" 
                       class="list-group-item list-group-item-action border-0">
                        <i class="fas fa-key me-2"></i>Password
                    </a>
                    @if(auth()->user()->isWarga())
                    <a href="#points" onclick="showTab('points')" 
                       class="list-group-item list-group-item-action border-0">
                        <i class="fas fa-coins me-2"></i>My Points
                    </a>
                    @endif
                    <a href="#preferences" onclick="showTab('preferences')" 
                       class="list-group-item list-group-item-action border-0">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                </div>
            </div>
            
            <!-- Points Summary (Warga only) -->
            @if(auth()->user()->isWarga())
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">Points</h6>
                    <div class="text-center">
                        <div class="display-5 text-netra fw-bold mb-1">
                            {{ number_format(auth()->user()->total_points, 0, ',', '.') }}
                        </div>
                        <p class="text-muted mb-3">Total Points</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            <!-- Tab Contents -->
            
            <!-- Profile Tab -->
            <div id="profile-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            
                            <!-- Photo Upload -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">Profile Photo</label>
                                    <div class="position-relative">
                                        @if(auth()->user()->profile_photo_path)
                                            <img id="profile-preview" 
                                                 src="{{ auth()->user()->profile_photo_url }}" 
                                                 alt="{{ auth()->user()->name }}" 
                                                 class="rounded-circle border border-3 border-netra"
                                                 style="width: 120px; height: 120px; object-fit: cover;">
                                        @else
                                            <div id="profile-initials" 
                                                 class="rounded-circle bg-netra d-flex align-items-center justify-content-center"
                                                 style="width: 120px; height: 120px; border: 3px solid #2E8B57;">
                                                <span class="text-white fw-bold" style="font-size: 2.5rem;">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <img id="profile-preview" src="" alt="Preview" 
                                                 class="rounded-circle border border-3 border-netra d-none"
                                                 style="width: 120px; height: 120px; object-fit: cover;">
                                        @endif
                                        
                                        <label for="photo" class="position-absolute bottom-0 end-0 bg-netra text-white rounded-circle p-2 cursor-pointer"
                                               style="width: 36px; height: 36px; transform: translate(25%, 25%);">
                                            <i class="fas fa-camera"></i>
                                            <input type="file" id="photo" name="photo" class="d-none" accept="image/*" onchange="previewImage(event)">
                                        </label>
                                    </div>
                                    @if(auth()->user()->profile_photo_path)
                                    <button type="button" onclick="removePhoto()" class="btn btn-sm btn-outline-danger mt-2">
                                        <i class="fas fa-trash me-1"></i> Remove
                                    </button>
                                    @endif
                                </div>
                                
                                <div class="col-md-9">
                                    <div class="row">
                                        <!-- Name -->
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" id="name" name="name" 
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   value="{{ old('name', auth()->user()->name ) }}"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Email -->
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" id="email" name="email" 
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   value="{{ old('email', auth()->user()->email) }}"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Phone -->
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="tel" id="phone" name="phone" 
                                                   class="form-control @error('phone') is-invalid @enderror"
                                                   value="{{ old('phone', auth()->user()->phone) }}"
                                                   placeholder="0812 3456 7890">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Role-specific fields -->
                                        @if(auth()->user()->isWarga())
                                        <div class="col-md-6 mb-3">
                                            <label for="nik" class="form-label">NIK</label>
                                            <input type="text" id="nik" name="nik" 
                                                   class="form-control @error('nik') is-invalid @enderror"
                                                   value="{{ old('nik', auth()->user()->nik) }}">
                                            @error('nik')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Address -->
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea id="address" name="address" rows="2"
                                                  class="form-control @error('address') is-invalid @enderror">{{ old('address', auth()->user()->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-netra">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Password Tab -->
            <div id="password-tab" class="tab-content d-none">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('profile.password.update') }}">
                            @csrf
                            @method('patch')
                            
                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <input type="password" id="current_password" name="current_password" 
                                           class="form-control @error('current_password') is-invalid @enderror"
                                           required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('current_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" 
                                           class="form-control @error('password') is-invalid @enderror"
                                           required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Minimal 8 karakter</small>
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" id="password_confirmation" name="password_confirmation" 
                                           class="form-control @error('password_confirmation') is-invalid @enderror"
                                           required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-netra">
                                    <i class="fas fa-key me-2"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Points Tab (Warga only) -->
            @if(auth()->user()->isWarga())
            <div id="points-tab" class="tab-content d-none">
                @php
                    $user = auth()->user();
                    $recentTransactions = $user->transaksiSebagaiWarga()->with('petugas')->orderBy('created_at', 'desc')->take(5)->get();
                @endphp
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">My Points</h5>
                    </div>
                    <div class="card-body">
                        <!-- Points Stats -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="text-netra display-5 fw-bold mb-1">
                                            {{ number_format($user->total_points, 0, ',', '.') }}
                                        </div>
                                        <small class="text-muted">Total Points</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="text-netra display-5 fw-bold mb-1">
                                            {{ $user->transaksiSebagaiWarga()->count() }}
                                        </div>
                                        <small class="text-muted">Total Transactions</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="d-flex gap-2 mb-4">
                            <a href="{{ route('warga.transaksi.index') }}" class="btn btn-outline-netra">
                                <i class="fas fa-history me-2"></i>Transaction History
                            </a>
                        </div>
                        
                        <!-- Recent Transactions -->
                        <h6 class="mb-3">Recent Transactions</h6>
                        
                        @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Petugas</th>
                                        <th>Points</th>
                                        <th>Weight</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('d/m') }}</td>
                                        <td>{{ $transaction->petugas->name ?? '-' }}</td>
                                        <td class="text-success fw-bold">+{{ number_format($transaction->total_poin) }}</td>
                                        <td>{{ number_format($transaction->total_berat, 1) }} kg</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('warga.transaksi.index') }}" class="btn btn-sm btn-outline-netra">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-receipt display-4 text-muted mb-3"></i>
                            <p class="text-muted">No transactions yet</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Preferences Tab -->
            <div id="preferences-tab" class="tab-content d-none">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Settings</h5>
                    </div>
                    <div class="card-body">
                        @if(auth()->user()->preferences ?? false)
                            @include('profile.partials.preferences', ['preferences' => $preferences ?? []])
                        @else
                        <form method="post" action="{{ route('profile.preferences.update') }}">
                            @csrf
                            @method('patch')
                            
                            <div class="mb-3">
                                <label class="form-label">Theme</label>
                                <select name="theme" class="form-select">
                                    <option value="light" {{ (old('theme', 'light') == 'light') ? 'selected' : '' }}>Light</option>
                                    <option value="dark" {{ (old('theme', 'light') == 'dark') ? 'selected' : '' }}>Dark</option>
                                    <option value="auto" {{ (old('theme', 'light') == 'auto') ? 'selected' : '' }}>Auto</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Language</label>
                                <select name="language" class="form-select">
                                    <option value="id" {{ (old('language', 'id') == 'id') ? 'selected' : '' }}>Bahasa Indonesia</option>
                                    <option value="en" {{ (old('language', 'id') == 'en') ? 'selected' : '' }}>English</option>
                                </select>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="email_notifications" class="form-check-input" 
                                       id="email_notifications" {{ (old('email_notifications', true) ? 'checked' : '') }}>
                                <label class="form-check-label" for="email_notifications">
                                    Email Notifications
                                </label>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="push_notifications" class="form-check-input" 
                                       id="push_notifications" {{ (old('push_notifications', true) ? 'checked' : '') }}>
                                <label class="form-check-label" for="push_notifications">
                                    Push Notifications
                                </label>
                            </div>
                            
                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-netra">
                                    <i class="fas fa-save me-2"></i>Save Settings
                                </button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
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
    if (confirm('Hapus foto profil?')) {
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

// Initialize first tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('profile');
});
</script>
@endpush