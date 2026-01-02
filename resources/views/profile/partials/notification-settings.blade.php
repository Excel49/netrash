<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">Pengaturan Notifikasi</h6>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form method="POST" action="{{ route('profile.notification.settings.update') }}">
            @csrf
            @method('POST')

            <h6 class="mb-3">Jenis Notifikasi yang Diterima:</h6>
            
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="transaction" 
                           id="transaction" value="1" {{ ($preferences['transaction'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="transaction">
                        Transaksi
                    </label>
                    <small class="form-text text-muted d-block">
                        Notifikasi untuk transaksi sampah berhasil
                    </small>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="points" 
                           id="points" value="1" {{ ($preferences['points'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="points">
                        Update Poin
                    </label>
                    <small class="form-text text-muted d-block">
                        Notifikasi untuk perubahan jumlah poin
                    </small>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="withdrawal" 
                           id="withdrawal" value="1" {{ ($preferences['withdrawal'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="withdrawal">
                        Penarikan Poin
                    </label>
                    <small class="form-text text-muted d-block">
                        Notifikasi untuk status penarikan poin
                    </small>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="promo" 
                           id="promo" value="1" {{ ($preferences['promo'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="promo">
                        Promo & Diskon
                    </label>
                    <small class="form-text text-muted d-block">
                        Notifikasi promo dan penawaran khusus
                    </small>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="system" 
                           id="system" value="1" {{ ($preferences['system'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="system">
                        Sistem
                    </label>
                    <small class="form-text text-muted d-block">
                        Notifikasi update dan maintenance sistem
                    </small>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="petugas_message" 
                           id="petugas_message" value="1" {{ ($preferences['petugas_message'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="petugas_message">
                        Pesan dari Petugas
                    </label>
                    <small class="form-text text-muted d-block">
                        Notifikasi pesan dari petugas sampah
                    </small>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="reset" class="btn btn-outline-secondary btn-sm">
                    Reset
                </button>
                <button type="submit" class="btn btn-netra btn-sm">
                    <i class="bi bi-save me-1"></i>Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>