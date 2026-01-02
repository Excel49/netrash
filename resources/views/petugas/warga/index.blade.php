@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="btn-group" role="group">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="fas fa-sync-alt fa-sm text-white-50"></i> Refresh
            </a>
            <a href="{{ route('profile.edit') }}" class="btn btn-success">
                <i class="fas fa-user fa-sm text-white-50"></i> Profil
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Poin Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Poin
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(Auth::user()->total_points ?? 0) }}</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success mr-2">
                                    <i class="fas fa-arrow-up"></i> Poin Anda
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaksi Hari Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Transaksi Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span>Belum ada transaksi</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Penarikan Pending Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Penarikan Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span>Belum ada penarikan</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifikasi Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Notifikasi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span>Tidak ada notifikasi baru</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Poin Bulan Ini</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-chart-line fa-3x text-gray-300"></i>
                        <p class="mt-3">Belum ada data riwayat poin</p>
                        <p class="text-muted">Mulai dengan menyerahkan sampah kepada petugas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kategori Sampah</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-trash-alt fa-3x text-gray-300"></i>
                        <p class="mt-3">Informasi Kategori</p>
                        <a href="{{ route('warga.kategori.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Lihat Kategori
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        @if(Auth::user()->isWarga())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('warga.qrcode.index') }}" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-qrcode fa-2x mb-2"></i><br>
                                QR Code Saya
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('warga.penarikan.create') }}" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i><br>
                                Tarik Poin
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('warga.transaksi.index') }}" class="btn btn-info btn-lg btn-block">
                                <i class="fas fa-history fa-2x mb-2"></i><br>
                                Riwayat Transaksi
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('warga.profile.index') }}" class="btn btn-warning btn-lg btn-block">
                                <i class="fas fa-user-cog fa-2x mb-2"></i><br>
                                Pengaturan Profil
                            </a>
                        </div>
                        @elseif(Auth::user()->isPetugas())
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('petugas.scan.index') }}" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-camera fa-2x mb-2"></i><br>
                                Scan QR Code
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('petugas.transaksi.create') }}" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                Buat Transaksi
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-info btn-lg btn-block">
                                <i class="fas fa-list fa-2x mb-2"></i><br>
                                Daftar Transaksi
                            </a>
                        </div>
                        @elseif(Auth::user()->isAdmin())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-users fa-2x mb-2"></i><br>
                                Kelola Pengguna
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i><br>
                                Laporan
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.kategori.index') }}" class="btn btn-info btn-lg btn-block">
                                <i class="fas fa-tags fa-2x mb-2"></i><br>
                                Kategori Sampah
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.penarikan.index') }}" class="btn btn-warning btn-lg btn-block">
                                <i class="fas fa-money-check-alt fa-2x mb-2"></i><br>
                                Penarikan Poin
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                    <a href="#" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="fas fa-clock fa-3x text-gray-300"></i>
                        <p class="mt-3">Belum ada aktivitas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto refresh dashboard setiap 30 detik
    $(document).ready(function() {
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    });
</script>
@endsection