<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Preferensi</h5>
        <p class="text-muted mb-0">Sesuaikan pengalaman aplikasi Anda</p>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('profile.preferences.update') }}" id="preferences-form">
            @csrf
            @method('patch')
            
            <!-- Theme Settings -->
            <div class="mb-5">
                <h6 class="mb-3">Pengaturan Tema</h6>
                <p class="text-muted mb-3">Pilih tema yang Anda sukai untuk aplikasi.</p>
                
                <div class="row">
                    @foreach([
                        ['value' => 'light', 'icon' => 'bi-sun', 'label' => 'Tema Terang', 'desc' => 'Tema default terang'],
                        ['value' => 'dark', 'icon' => 'bi-moon', 'label' => 'Tema Gelap', 'desc' => 'Mode gelap untuk kenyamanan mata'],
                    ] as $theme)
                    <div class="col-md-6 mb-3">
                        <label class="form-check-label d-block cursor-pointer">
                            <input type="radio" name="theme" value="{{ $theme['value'] }}" 
                                   class="form-check-input" 
                                   {{ (old('theme', $preferences['theme'] ?? 'light') == $theme['value']) ? 'checked' : '' }}
                                   onchange="updateThemePreview('{{ $theme['value'] }}')">
                            <div class="card border theme-card {{ (old('theme', $preferences['theme'] ?? 'light') == $theme['value']) ? 'border-2 border-primary' : 'border-secondary' }}">
                                <div class="card-body text-center py-4">
                                    <i class="bi {{ $theme['icon'] }} display-5 mb-3 
                                        {{ $theme['value'] == 'dark' ? 'text-dark' : '' }}
                                        {{ $theme['value'] == 'light' ? 'text-warning' : '' }}"></i>
                                    <h6 class="mb-1">{{ $theme['label'] }}</h6>
                                    <small class="text-muted">{{ $theme['desc'] }}</small>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
                
                <!-- Theme Preview -->
                <div class="mt-4">
                    <h6 class="mb-2">Pratinjau</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light mb-3" id="light-preview">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                                        <small class="text-muted">Tema Terang</small>
                                    </div>
                                    <div class="form-control mb-2">Contoh input</div>
                                    <button class="btn btn-primary btn-sm">Tombol</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-dark text-white mb-3 d-none" id="dark-preview">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-info rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                                        <small>Tema Gelap</small>
                                    </div>
                                    <div class="form-control bg-secondary text-white border-dark mb-2">Contoh input</div>
                                    <button class="btn btn-info btn-sm">Tombol</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Language Settings -->
            <div class="mb-5">
                <h6 class="mb-3">Bahasa & Wilayah</h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bahasa</label>
                        <select name="language" class="form-select">
                            <option value="id" {{ (old('language', $preferences['language'] ?? 'id') == 'id') ? 'selected' : '' }}>
                                🇮🇩 Bahasa Indonesia
                            </option>
                            <option value="en" {{ (old('language', $preferences['language'] ?? 'id') == 'en') ? 'selected' : '' }}>
                                🇬🇧 English
                            </option>
                        </select>
                        <small class="text-muted">Perubahan akan berlaku setelah refresh halaman</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Zona Waktu</label>
                        <select name="timezone" class="form-select">
                            <option value="WIB" {{ (old('timezone', $preferences['timezone'] ?? 'WIB') == 'WIB') ? 'selected' : '' }}>
                                WIB (Waktu Indonesia Barat) GMT+7
                            </option>
                            <option value="WITA" {{ (old('timezone', $preferences['timezone'] ?? 'WIB') == 'WITA') ? 'selected' : '' }}>
                                WITA (Waktu Indonesia Tengah) GMT+8
                            </option>
                            <option value="WIT" {{ (old('timezone', $preferences['timezone'] ?? 'WIB') == 'WIT') ? 'selected' : '' }}>
                                WIT (Waktu Indonesia Timur) GMT+9
                            </option>
                        </select>
                        <small class="text-muted">Mempengaruhi tampilan waktu di seluruh aplikasi</small>
                    </div>
                </div>
            </div>
            
            <!-- Notification Settings -->
            <div class="mb-5">
                <h6 class="mb-3">Preferensi Notifikasi</h6>
                <p class="text-muted mb-3">Pilih notifikasi yang ingin Anda terima.</p>
                
                <div class="list-group">
                    @foreach([
                        ['key' => 'transaction', 'icon' => 'bi-receipt', 'title' => 'Notifikasi Transaksi', 'desc' => 'Dapatkan notifikasi tentang transaksi baru'],
                        ['key' => 'points', 'icon' => 'bi-coin', 'title' => 'Update Poin', 'desc' => 'Terima update tentang perubahan poin'],
                        ['key' => 'withdrawal', 'icon' => 'bi-cash-coin', 'title' => 'Alert Penarikan', 'desc' => 'Notifikasi untuk permintaan penarikan'],
                        ['key' => 'promo', 'icon' => 'bi-gift', 'title' => 'Email Promosi', 'desc' => 'Terima penawaran dan promosi'],
                        ['key' => 'system', 'icon' => 'bi-bell', 'title' => 'Update Sistem', 'desc' => 'Notifikasi sistem penting'],
                    ] as $notification)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi {{ $notification['icon'] }} me-3 text-primary"></i>
                            <div>
                                <h6 class="mb-1">{{ $notification['title'] }}</h6>
                                <small class="text-muted">{{ $notification['desc'] }}</small>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   name="notifications[{{ $notification['key'] }}]"
                                   value="1"
                                   {{ (old('notifications.' . $notification['key'], $preferences['notifications'][$notification['key']] ?? true)) ? 'checked' : '' }}>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Privacy Settings -->
            <div class="mb-4">
                <h6 class="mb-3">Pengaturan Privasi</h6>
                <p class="text-muted mb-3">Kontrol privasi dan visibilitas Anda di platform.</p>
                
                <div class="list-group">
                    @foreach([
                        ['key' => 'public_profile', 'icon' => 'bi-eye', 'title' => 'Profil Publik', 'desc' => 'Izinkan orang lain melihat profil Anda'],
                        ['key' => 'show_activity', 'icon' => 'bi-activity', 'title' => 'Tampilkan Aktivitas', 'desc' => 'Tampilkan aktivitas Anda ke orang lain'],
                        ['key' => 'profile_searchable', 'icon' => 'bi-search', 'title' => 'Profil Dapat Dicari', 'desc' => 'Izinkan profil Anda muncul di hasil pencarian'],
                    ] as $privacy)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi {{ $privacy['icon'] }} me-3 text-primary"></i>
                            <div>
                                <h6 class="mb-1">{{ $privacy['title'] }}</h6>
                                <small class="text-muted">{{ $privacy['desc'] }}</small>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   name="privacy[{{ $privacy['key'] }}]"
                                   value="1"
                                   {{ (old('privacy.' . $privacy['key'], $preferences['privacy'][$privacy['key']] ?? ($privacy['key'] == 'public_profile' ? false : true))) ? 'checked' : '' }}>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Save Button -->
            <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                <div>
                    <button type="button" class="btn btn-outline-secondary" onclick="resetPreferences()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset ke Default
                    </button>
                </div>
                <div>
                    <button type="submit" class="btn btn-netra">
                        <i class="bi bi-check-circle me-2"></i>Simpan Preferensi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Theme preview
function updateThemePreview(theme) {
    const lightPreview = document.getElementById('light-preview');
    const darkPreview = document.getElementById('dark-preview');
    
    if (theme === 'light') {
        lightPreview.classList.remove('d-none');
        darkPreview.classList.add('d-none');
    } else if (theme === 'dark') {
        lightPreview.classList.add('d-none');
        darkPreview.classList.remove('d-none');
    }
}

// Reset preferences to default
function resetPreferences() {
    if (confirm('Apakah Anda yakin ingin mengatur ulang semua preferensi ke default?')) {
        // Reset theme
        document.querySelectorAll('input[name="theme"]').forEach(radio => {
            radio.checked = radio.value === 'light';
        });
        
        // Reset language
        document.querySelector('select[name="language"]').value = 'id';
        
        // Reset timezone
        document.querySelector('select[name="timezone"]').value = 'WIB';
        
        // Reset notifications (all checked)
        document.querySelectorAll('input[name^="notifications"]').forEach(checkbox => {
            checkbox.checked = true;
        });
        
        // Reset privacy
        document.querySelectorAll('input[name^="privacy"]').forEach((checkbox, index) => {
            checkbox.checked = index === 0 ? false : true;
        });
        
        // Update theme preview
        updateThemePreview('light');
        
        alert('Preferensi telah direset ke default. Klik "Simpan Preferensi" untuk menerapkan.');
    }
}

// Initialize theme preview
document.addEventListener('DOMContentLoaded', function() {
    const selectedTheme = document.querySelector('input[name="theme"]:checked')?.value || 'light';
    updateThemePreview(selectedTheme);
    
    document.querySelectorAll('input[name="theme"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateThemePreview(this.value);
        });
    });
});
</script>
@endpush