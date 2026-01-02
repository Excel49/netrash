<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      data-bs-theme="{{ Cookie::get('theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @hasSection('title')
            @yield('title') - {{ config('app.name', 'NetraTrash') }}
        @else
            {{ config('app.name', 'NetraTrash') }}
        @endif
    </title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --netra-primary: #2E8B57;
            --netra-primary-dark: #1f6f42;
            --netra-secondary: #6c757d;
            --netra-success: #198754;
            --netra-info: #0dcaf0;
            --netra-warning: #ffc107;
            --netra-danger: #dc3545;
            --netra-light: #f8f9fa;
            --netra-dark: #212529;
        }
        
        .bg-netra {
            background-color: var(--netra-primary) !important;
        }
        
        .text-netra {
            color: var(--netra-primary) !important;
        }
        
        .border-netra {
            border-color: var(--netra-primary) !important;
        }
        
        .btn-netra {
            background-color: var(--netra-primary);
            border-color: var(--netra-primary);
            color: white;
        }
        
        .btn-netra:hover {
            background-color: var(--netra-primary-dark);
            border-color: var(--netra-primary-dark);
            color: white;
        }
        
        .btn-netra-outline {
            background-color: transparent;
            border-color: var(--netra-primary);
            color: var(--netra-primary);
        }
        
        .btn-netra-outline:hover {
            background-color: var(--netra-primary);
            border-color: var(--netra-primary);
            color: white;
        }
        
        .badge-netra {
            background-color: var(--netra-primary);
            color: white;
        }
        
        .navbar-netra {
            background-color: var(--netra-primary) !important;
        }
        
        .card-netra {
            border-left: 4px solid var(--netra-primary);
        }
        
        .stat-card {
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--netra-primary);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.7;
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border 0.75s linear infinite;
        }
        
        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
        
        /* Alert customization */
        .alert-netra {
            border-left: 4px solid var(--netra-primary);
        }
        
        /* Table hover effect */
        .table-hover tbody tr:hover {
            background-color: rgba(46, 139, 87, 0.05);
        }
        
        /* Form control focus */
        .form-control:focus, .form-select:focus {
            border-color: var(--netra-primary);
            box-shadow: 0 0 0 0.25rem rgba(46, 139, 87, 0.25);
        }
        
        /* Dark Theme Support */
        [data-bs-theme="dark"] {
            --netra-primary: #3b82f6;
            --netra-primary-dark: #1d4ed8;
        }
        
        [data-bs-theme="dark"] body {
            background-color: #121212;
            color: #e0e0e0;
        }
        
        [data-bs-theme="dark"] .card {
            background-color: #1e1e1e;
            border-color: #2d2d2d;
        }
        
        [data-bs-theme="dark"] .sidebar {
            background-color: #1e1e1e;
            border-right-color: #2d2d2d;
        }
        
        [data-bs-theme="dark"] .sidebar .nav-link {
            color: #e0e0e0;
        }
        
        [data-bs-theme="dark"] .sidebar .nav-link:hover {
            background-color: #2d2d2d;
        }
        
        [data-bs-theme="dark"] .form-control {
            background-color: #2d2d2d;
            border-color: #3d3d3d;
            color: #e0e0e0;
        }
        
        [data-bs-theme="dark"] .table {
            --bs-table-color: #e0e0e0;
            --bs-table-bg: transparent;
            --bs-table-border-color: #2d2d2d;
        }
        
        [data-bs-theme="dark"] .custom-scrollbar::-webkit-scrollbar-track {
            background: #2d2d2d;
        }
        
        [data-bs-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4d4d4d;
        }

        /* Navbar customization */
        .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            margin: 0 0.125rem;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .navbar-brand {
            font-size: 1.5rem;
        }
        
        .navbar-toggler {
            border: none;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        /* Main content */
        .main-content {
            min-height: calc(100vh - 56px - 73px); /* viewport - navbar - footer */
        }
    </style>
    
    <!-- Page Specific Styles -->
    @stack('styles')
    
    <!-- Livewire Styles (if using Livewire) -->

</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-netra shadow-sm">
        <div class="container-fluid">
            <!-- Logo/Brand -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <i class="fas fa-recycle me-2"></i>
                <span class="fw-bold">NetraTrash</span>
                @if(config('app.env') !== 'production')
                    <span class="badge bg-warning ms-2">{{ config('app.env') }}</span>
                @endif
            </a>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main Navigation -->
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                <i class="fas fa-home me-1"></i> Dashboard
                            </a>
                        </li>
                        
                        <!-- Role-based Navigation -->
                        @if(auth()->user()->isAdmin())
                            <!-- Admin Menu -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('admin/*') ? 'active' : '' }}" 
                                   href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-users-cog me-1"></i> Users
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                                           href="{{ route('admin.users.index') }}">
                                            <i class="fas fa-users me-2"></i> Manage Users
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}" 
                                           href="{{ route('admin.kategori.index') }}">
                                            <i class="fas fa-tags me-2"></i> Categories
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('admin/transaksi*') || request()->is('admin/penarikan*') ? 'active' : '' }}" 
                                   href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-exchange-alt me-1"></i> Transactions
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}" 
                                           href="{{ route('admin.transaksi.index') }}">
                                            <i class="fas fa-list me-2"></i> All Transactions
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.penarikan.*') ? 'active' : '' }}" 
                                           href="{{ route('admin.penarikan.index') }}">
                                            <i class="fas fa-money-bill-wave me-2"></i> Withdrawals
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('admin/reports*') ? 'active' : '' }}" 
                                   href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-chart-bar me-1"></i> Reports
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                                           href="{{ route('admin.reports.index') }}">
                                            <i class="fas fa-chart-pie me-2"></i> Overview
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.reports.transaksi') }}">
                                            <i class="fas fa-exchange-alt me-2"></i> Transaction Report
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.reports.users') }}">
                                            <i class="fas fa-users me-2"></i> User Report
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('admin/settings*') || request()->is('admin/logs*') || request()->is('admin/backup*') ? 'active' : '' }}" 
                                   href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog me-1"></i> Settings
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                                            <i class="fas fa-sliders-h me-2"></i> System Settings
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.logs.index') }}">
                                            <i class="fas fa-clipboard-list me-2"></i> Logs
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.backup.index') }}">
                                            <i class="fas fa-database me-2"></i> Backup
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                        @elseif(auth()->user()->isPetugas())
                            <!-- Petugas Menu -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petugas.scan.*') ? 'active' : '' }}" 
                                   href="{{ route('petugas.scan.index') }}">
                                    <i class="fas fa-qrcode me-1"></i> Scan QR
                                </a>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('petugas/transaksi*') ? 'active' : '' }}" 
                                   href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-exchange-alt me-1"></i> Transactions
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('petugas.transaksi.*') ? 'active' : '' }}" 
                                           href="{{ route('petugas.transaksi.index') }}">
                                            <i class="fas fa-list me-2"></i> All Transactions
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('petugas.transaksi.create') }}">
                                            <i class="fas fa-plus-circle me-2"></i> New Transaction
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('petugas.transaksi.today') }}">
                                            <i class="fas fa-calendar-day me-2"></i> Today's Transactions
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('petugas/warga*') ? 'active' : '' }}" 
                                   href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-users me-1"></i> Residents
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('petugas.warga.*') ? 'active' : '' }}" 
                                           href="{{ route('petugas.warga.index') }}">
                                            <i class="fas fa-list me-2"></i> All Residents
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('petugas.warga.create') }}">
                                            <i class="fas fa-user-plus me-2"></i> Add Resident
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petugas.statistik.*') ? 'active' : '' }}" 
                                   href="{{ route('petugas.statistik.index') }}">
                                    <i class="fas fa-chart-line me-1"></i> Statistics
                                </a>
                            </li>
                            
                        @elseif(auth()->user()->isWarga())
                            <!-- Warga Menu -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('warga.qrcode.*') ? 'active' : '' }}" 
                                   href="{{ route('warga.qrcode.index') }}">
                                    <i class="fas fa-qrcode me-1"></i> QR Code
                                </a>
                            </li>
                            
                            <!-- Menu Transaksi tanpa dropdown -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('warga.transaksi.today') ? 'active' : '' }}" 
                                   href="{{ route('warga.transaksi.today') }}">
                                    <i class="fas fa-calendar-day me-1"></i> Transaksi
                                </a>
                            </li>
                            
                            <!-- Menu Semua Transaksi (opsional, jika tetap ingin ada) -->
                            <!--
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('warga.transaksi.index') && !request()->routeIs('warga.transaksi.today') ? 'active' : '' }}" 
                                   href="{{ route('warga.transaksi.index') }}">
                                    <i class="fas fa-history me-1"></i> Semua Transaksi
                                </a>
                            </li>
                            -->
                            
                            <!-- Menu Poin tanpa dropdown -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('warga.penarikan.*') ? 'active' : '' }}" 
                                   href="{{ route('warga.penarikan.index') }}">
                                    <i class="fas fa-money-bill-wave me-1"></i> Poin
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('warga.kategori.*') ? 'active' : '' }}" 
                                   href="{{ route('warga.kategori.index') }}">
                                    <i class="fas fa-tags me-1"></i> Kategori
                                </a>
                            </li>
                        @endif
                        
                        <!-- Common Menu for All Users -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}" 
                               href="{{ route('notifikasi.index') }}">
                                <i class="fas fa-bell me-1"></i> Notifikasi
                            </a>
                        </li>
                        
                    @else
                        <!-- Guest Menu -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                                <i class="fas fa-info-circle me-1"></i> Tentang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('features') ? 'active' : '' }}" href="{{ route('features') }}">
                                <i class="fas fa-star me-1"></i> Fitur
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                                <i class="fas fa-envelope me-1"></i> Kontak
                            </a>
                        </li>
                    @endauth
                </ul>
                
                <!-- Right Navigation -->
                <ul class="navbar-nav ms-auto">
                    @auth
                        <!-- User Info -->
                        <li class="nav-item d-flex align-items-center me-3">
                            @if(auth()->user()->isWarga())
                                <span class="text-white me-2">
                                    <i class="fas fa-coins"></i> 
                                    <strong>{{ number_format(auth()->user()->total_points ?? 0) }}</strong> pts
                                </span>
                            @endif
                            
                            <span class="badge bg-{{ auth()->user()->isAdmin() ? 'danger' : (auth()->user()->isPetugas() ? 'primary' : 'success') }} p-2">
                                {{ ucfirst(auth()->user()->role->name ?? 'User') }}
                            </span>
                        </li>
                        
                        <!-- Notifications Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                @php
                                    $unreadCount = 0;
                                    try {
                                        $unreadCount = auth()->user()->unreadNotifications()->count();
                                    } catch (\Exception $e) {
                                        // Table doesn't exist yet
                                    }
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $unreadCount }}
                                        <span class="visually-hidden">unread notifications</span>
                                    </span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 300px;">
                                <div class="dropdown-header bg-light py-3">
                                    <h6 class="mb-0">Notifikasi</h6>
                                    @if($unreadCount > 0)
                                        <a href="#" class="small text-primary" onclick="markAllAsRead()">
                                            Tandai semua terbaca
                                        </a>
                                    @endif
                                </div>
                                <div class="dropdown-list custom-scrollbar" style="max-height: 300px; overflow-y: auto;">
                                    @php
                                        $notifications = [];
                                        try {
                                            $notifications = auth()->user()->notifications()->take(5)->get();
                                        } catch (\Exception $e) {
                                            // Table doesn't exist yet
                                        }
                                    @endphp
                                    
                                    @forelse($notifications as $notification)
                                        <a href="{{ $notification->data['link'] ?? '#' }}" 
                                        class="dropdown-item d-flex align-items-center py-3 border-bottom"
                                        onclick="markAsRead('{{ $notification->id }}')">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="rounded-circle bg-{{ $notification->data['type'] ?? 'primary' }} p-2">
                                                    <i class="fas fa-{{ $notification->data['icon'] ?? 'bell' }} text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notifikasi' }}</h6>
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-0 text-muted small">{{ Str::limit($notification->data['message'] ?? '', 50) }}</p>
                                            </div>
                                            @if(!$notification->read_at)
                                                <span class="badge bg-primary rounded-pill ms-2">Baru</span>
                                            @endif
                                        </a>
                                    @empty
                                        <div class="dropdown-item text-center py-4">
                                            <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Tidak ada notifikasi</p>
                                        </div>
                                    @endforelse
                                </div>
                                <div class="dropdown-footer text-center py-2">
                                    <a href="{{ route('notifikasi.index') }}" class="text-primary small">
                                        Lihat semua notifikasi <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </li>
                        
                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                @if(auth()->user()->profile_photo_path)
                                    <img src="{{ auth()->user()->profile_photo_url }}" 
                                         alt="{{ auth()->user()->name }}" 
                                         class="rounded-circle me-2" 
                                         style="width: 32px; height: 32px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center me-2" 
                                         style="width: 32px; height: 32px;">
                                        <span class="fw-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <span class="d-none d-lg-inline">{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li class="dropdown-header">
                                    <div class="d-flex align-items-center">
                                        @if(auth()->user()->profile_photo_path)
                                            <img src="{{ auth()->user()->profile_photo_url }}" 
                                                 alt="{{ auth()->user()->name }}" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-netra text-white d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 40px; height: 40px;">
                                                <span class="fw-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                            <small class="text-muted">{{ auth()->user()->email }}</small>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" 
                                       href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user me-2"></i> Profil
                                    </a>
                                </li>
                                
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                                       href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                    </a>
                                </li>
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <!-- Theme Switcher -->
                                <li class="dropdown-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-moon me-2"></i> Mode Gelap</span>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="themeSwitch">
                                        </div>
                                    </div>
                                </li>
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <!-- Guest Navigation -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid main-content">
        <!-- Flash Messages -->
        <div class="container mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <!-- Validation Errors -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Harap perbaiki kesalahan berikut:</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Page Header -->
        @hasSection('header')
            <div class="container-fluid bg-light border-bottom py-3">
                <div class="container">
                    @yield('header')
                </div>
            </div>
        @endif
        
        <!-- Main Content -->
        <main class="container py-4">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light border-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="text-muted">
                        &copy; {{ date('Y') }} NetraTrash. All rights reserved.
                        <span class="ms-2">v{{ config('app.version', '1.0.0') }}</span>
                    </span>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('privacy') }}" class="text-muted me-3">Kebijakan Privasi</a>
                    <a href="{{ route('terms') }}" class="text-muted me-3">Syarat Layanan</a>
                    <a href="{{ route('contact') }}" class="text-muted">Kontak</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (optional, if needed) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Chart.js (if using charts) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // CSRF Token setup for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Theme switcher
        document.addEventListener('DOMContentLoaded', function() {
            const themeSwitch = document.getElementById('themeSwitch');
            const currentTheme = localStorage.getItem('theme') || 
                                 document.documentElement.getAttribute('data-bs-theme') || 
                                 'light';
            
            // Set initial state
            if (currentTheme === 'dark') {
                themeSwitch.checked = true;
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            }
            
            // Theme switch handler
            themeSwitch.addEventListener('change', function() {
                if (this.checked) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                    
                    // Save preference via AJAX
                    saveThemePreference('dark');
                } else {
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                    localStorage.setItem('theme', 'light');
                    
                    // Save preference via AJAX
                    saveThemePreference('light');
                }
            });
            
            // Save theme preference via AJAX
            function saveThemePreference(theme) {
                fetch('{{ route("profile.preferences.update") }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        theme: theme
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Preferensi tema disimpan:', data);
                })
                .catch(error => {
                    console.error('Error menyimpan preferensi tema:', error);
                });
            }
            
            // Form submission loading states
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.classList.contains('no-loading')) {
                        submitBtn.innerHTML = '<span class="loading-spinner"></span> Memproses...';
                        submitBtn.disabled = true;
                    }
                });
            });
            
            // Active nav link highlighting
            const currentPath = window.location.pathname;
            document.querySelectorAll('.navbar-nav .nav-link, .dropdown-item').forEach(link => {
                if (link.href === window.location.href) {
                    link.classList.add('active');
                }
                
                // For dropdown parents
                if (link.classList.contains('dropdown-toggle')) {
                    const dropdownItems = link.parentElement.querySelectorAll('.dropdown-item');
                    let hasActiveChild = false;
                    dropdownItems.forEach(item => {
                        if (item.href === window.location.href) {
                            hasActiveChild = true;
                        }
                    });
                    if (hasActiveChild) {
                        link.classList.add('active');
                    }
                }
            });
        });
        
        // Toast notification function
        function showToast(message, type = 'success') {
            const toastHtml = `
                <div class="toast align-items-center text-bg-${type} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            const toastElement = toastContainer.lastElementChild;
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
            
            // Remove toast after it hides
            toastElement.addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        }
        
        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1060';
            document.body.appendChild(container);
            return container;
        }
        
        // Confirm dialog helper
        function confirmAction(message, callback) {
            if (confirm(message)) {
                if (typeof callback === 'function') {
                    callback();
                }
                return true;
            }
            return false;
        }
        
        // Format number with commas
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }
        
        // Format date time
        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Get cookie helper
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        // Mark all notifications as read
        function markAllAsRead() {
            fetch('{{ route("notifikasi.read-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove badge
                    const badge = document.querySelector('.nav-link .badge');
                    if (badge) badge.remove();
                    
                    // Remove "New" badges from notifications
                    document.querySelectorAll('.dropdown-list .badge').forEach(badge => {
                        badge.remove();
                    });
                    
                    // Show success message
                    showToast('Semua notifikasi ditandai terbaca', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Gagal menandai notifikasi terbaca', 'error');
            });
        }

        // Mark single notification as read
        function markAsRead(notificationId) {
            fetch(`/notifikasi/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove "New" badge from this notification
                    const badge = document.querySelector(`[data-notification="${notificationId}"] .badge`);
                    if (badge) badge.remove();
                    
                    // Update unread count
                    const unreadBadge = document.querySelector('.nav-link .badge');
                    if (unreadBadge) {
                        const currentCount = parseInt(unreadBadge.textContent);
                        if (currentCount > 1) {
                            unreadBadge.textContent = currentCount - 1;
                        } else {
                            unreadBadge.remove();
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
    
    <!-- Page Specific Scripts -->
    @stack('scripts')
    
    <!-- Livewire Scripts (if using Livewire) -->
    @livewireScripts
    
    <!-- SweetAlert2 (optional) -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // SweetAlert2 configuration
        const Swal = window.Swal;
        if (Swal) {
            Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        }
    </script>
</body>
</html>