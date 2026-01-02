@extends('layouts.app')

@section('title', 'Pengaturan Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar (opsional) -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Menu Notifikasi</h6>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('notifikasi.index') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-bell me-2"></i> Semua Notifikasi
                        </a>
                        <a href="{{ route('notifikasi.unread') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-bell-fill me-2"></i> Belum Dibaca
                        </a>
                        <a href="{{ route('notifikasi.settings') }}" class="list-group-item list-group-item-action active">
                            <i class="bi bi-gear me-2"></i> Pengaturan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Pengaturan Notifikasi</h5>
                    <p class="text-muted mb-0">Kelola jenis notifikasi yang ingin Anda terima</p>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('notifikasi.settings.update') }}">
                        @csrf
                        @method('POST')

                        <!-- Notifikasi Email -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Notifikasi Email</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_transactions" 
                                           id="email_transactions" value="1" 
                                           {{ ($settings['email_transactions'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_transactions">
                                        Transaksi
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Kirim notifikasi email untuk setiap transaksi
                                    </small>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_withdrawals" 
                                           id="email_withdrawals" value="1" 
                                           {{ ($settings['email_withdrawals'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_withdrawals">
                                        Penarikan Poin
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Kirim notifikasi email untuk status penarikan
                                    </small>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="email_promotions" 
                                           id="email_promotions" value="1" 
                                           {{ ($settings['email_promotions'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_promotions">
                                        Promosi & Penawaran
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Kirim email promosi dan penawaran khusus
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Notifikasi Sistem -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Notifikasi Sistem</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="system_transactions" 
                                           id="system_transactions" value="1" 
                                           {{ ($settings['system_transactions'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="system_transactions">
                                        Transaksi Berhasil
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Tampilkan notifikasi di sistem untuk transaksi berhasil
                                    </small>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="system_withdrawals" 
                                           id="system_withdrawals" value="1" 
                                           {{ ($settings['system_withdrawals'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="system_withdrawals">
                                        Status Penarikan
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Tampilkan notifikasi untuk update status penarikan
                                    </small>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="system_announcements" 
                                           id="system_announcements" value="1" 
                                           {{ ($settings['system_announcements'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="system_announcements">
                                        Pengumuman
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Tampilkan pengumuman penting dari sistem
                                    </small>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="system_maintenance" 
                                           id="system_maintenance" value="1" 
                                           {{ ($settings['system_maintenance'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="system_maintenance">
                                        Maintenance
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Beri tahu tentang jadwal maintenance sistem
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Notifikasi Browser -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Notifikasi Browser</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Notifikasi browser akan muncul di desktop Anda saat ada update penting.
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="browser_important" 
                                           id="browser_important" value="1" 
                                           {{ ($settings['browser_important'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="browser_important">
                                        Notifikasi Penting
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Tampilkan notifikasi browser untuk update penting
                                    </small>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="browser_sound" 
                                           id="browser_sound" value="1" 
                                           {{ ($settings['browser_sound'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="browser_sound">
                                        Suara Notifikasi
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Putar suara saat ada notifikasi baru
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </button>
                            <div>
                                <button type="reset" class="btn btn-outline-danger me-2">
                                    <i class="bi bi-x-circle me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Simpan Pengaturan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ringkasan -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Ringkasan Notifikasi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h2 class="text-primary">{{ auth()->user()->unreadNotifications->count() }}</h2>
                                    <p class="text-muted mb-0">Belum Dibaca</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h2 class="text-success">{{ auth()->user()->notifications->count() }}</h2>
                                    <p class="text-muted mb-0">Total Notifikasi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h2 class="text-warning">7</h2>
                                    <p class="text-muted mb-0">7 Hari Terakhir</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection