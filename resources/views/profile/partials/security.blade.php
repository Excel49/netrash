<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Keamanan Akun</h5>
        <p class="text-muted mb-0">Kelola keamanan dan aktivitas akun Anda</p>
    </div>
    <div class="card-body">
        <!-- Password Security -->
        <div class="mb-5">
            <h6 class="mb-3">Keamanan Password</h6>
            
            <div class="list-group">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-key me-3 text-primary"></i>
                        <div>
                            <h6 class="mb-1">Password</h6>
                            <small class="text-muted">Terakhir diubah: {{ $security_data['last_password_change'] ?? 'Belum pernah' }}</small>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="bi bi-pencil me-1"></i>Ubah
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Two-Factor Authentication -->
        <div class="mb-5">
            <h6 class="mb-3">Autentikasi Dua Faktor (2FA)</h6>
            <p class="text-muted mb-3">Tambahkan lapisan keamanan ekstra ke akun Anda</p>
            
            <div class="list-group">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-check me-3 {{ $security_data['two_factor_enabled'] ? 'text-success' : 'text-warning' }}"></i>
                        <div>
                            <h6 class="mb-1">Autentikasi 2 Faktor</h6>
                            <small class="text-muted">
                                {{ $security_data['two_factor_enabled'] ? 'Aktif' : 'Belum Aktif' }} • 
                                {{ $security_data['two_factor_enabled'] ? 'Menambahkan lapisan keamanan ekstra' : 'Aktifkan untuk keamanan maksimal' }}
                            </small>
                        </div>
                    </div>
                    <div>
                        @if($security_data['two_factor_enabled'])
                        <span class="badge bg-success">Aktif</span>
                        @else
                        <button type="button" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-shield-plus me-1"></i>Aktifkan
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            
            @if(!$security_data['two_factor_enabled'])
            <div class="alert alert-info mt-3">
                <h6><i class="bi bi-info-circle me-2"></i>Cara Mengaktifkan 2FA:</h6>
                <ol class="mb-0 ps-3">
                    <li class="mb-1">Unduh aplikasi Google Authenticator di smartphone Anda</li>
                    <li class="mb-1">Scan QR code yang akan ditampilkan setelah Anda klik tombol aktifkan</li>
                    <li>Masukkan kode 6 digit dari aplikasi untuk verifikasi</li>
                </ol>
            </div>
            @endif
        </div>
        
        <!-- Active Sessions -->
        <div class="mb-5">
            <h6 class="mb-3">Sesi Aktif</h6>
            <p class="text-muted mb-3">{{ $security_data['active_sessions'] }} sesi aktif ditemukan</p>
            
            <div class="list-group">
                @foreach($login_history as $session)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi {{ $session['current'] ? 'bi-laptop text-success' : 'bi-phone text-secondary' }} me-3"></i>
                        <div>
                            <h6 class="mb-1">{{ $session['device'] }}</h6>
                            <small class="text-muted">
                                {{ $session['ip'] }} • {{ $session['location'] }} • 
                                {{ $session['time']->format('d M Y, H:i') }}
                            </small>
                        </div>
                    </div>
                    <div>
                        @if($session['current'])
                        <span class="badge bg-success">Saat Ini</span>
                        @else
                        <button type="button" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Hapus
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="d-grid mt-3">
                <button type="button" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout dari Semua Perangkat
                </button>
            </div>
        </div>
        
        <!-- Account Deletion -->
        <div class="border-top pt-4">
            <h6 class="mb-3 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Zona Bahaya</h6>
            
            <div class="alert alert-danger">
                <h6><i class="bi bi-trash me-2"></i>Hapus Akun</h6>
                <p class="mb-2">Setelah menghapus akun Anda, semua data akan dihapus secara permanen. Aksi ini tidak dapat dibatalkan.</p>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="bi bi-trash me-1"></i>Hapus Akun Saya
                </button>
            </div>
        </div>
    </div>
</div>