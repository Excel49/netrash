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
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // CSRF Token setup for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // === THEME MANAGEMENT ===
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
            document.cookie = `theme=${theme}; path=/; max-age=31536000`;
            
            // Update switch state if exists
            const themeSwitchDropdown = document.getElementById('themeSwitchDropdown');
            if (themeSwitchDropdown) {
                themeSwitchDropdown.checked = (theme === 'dark');
            }
        }
        
        function getCurrentTheme() {
            // Priority: localStorage > cookie > system preference > light
            const localStorageTheme = localStorage.getItem('theme');
            if (localStorageTheme) return localStorageTheme;
            
            // Check cookie
            const cookieTheme = document.cookie
                .split('; ')
                .find(row => row.startsWith('theme='))
                ?.split('=')[1];
            if (cookieTheme) return cookieTheme;
            
            // Check system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return 'dark';
            }
            
            return 'light';
        }
        
        function initializeTheme() {
            const currentTheme = getCurrentTheme();
            applyTheme(currentTheme);
        }
        
        function setupThemeSwitcher() {
            const themeSwitchDropdown = document.getElementById('themeSwitchDropdown');
            if (themeSwitchDropdown) {
                const currentTheme = getCurrentTheme();
                themeSwitchDropdown.checked = (currentTheme === 'dark');
                
                themeSwitchDropdown.addEventListener('change', function() {
                    const newTheme = this.checked ? 'dark' : 'light';
                    applyTheme(newTheme);
                });
            }
        }
        // === END THEME MANAGEMENT ===
        
        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Apply theme FIRST
            initializeTheme();
            
            // Setup theme switcher
            setupThemeSwitcher();
            
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize Bootstrap popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
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
        });
        
        // Also apply theme when page is shown (for back/forward navigation)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                initializeTheme();
            }
        });
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery (optional, if needed) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
    
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

        /* Fix for navbar on mobile */
        @media (max-width: 992px) {
            .navbar-collapse {
                max-height: 80vh;
                overflow-y: auto;
            }
            
            .navbar-nav .nav-link {
                margin: 0.125rem 0;
            }
        }
    </style>
    
    <!-- Page Specific Styles -->
    @stack('styles')
    
    <!-- Livewire Styles (if using Livewire) -->
        <!-- Inline script for immediate theme application -->
    <script>
        // Apply theme immediately to prevent flash of wrong theme
        (function() {
            try {
                // Check localStorage first (fastest)
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme) {
                    document.documentElement.setAttribute('data-bs-theme', savedTheme);
                    return;
                }
                
                // Check cookie
                const cookieTheme = document.cookie
                    .split('; ')
                    .find(row => row.startsWith('theme='))
                    ?.split('=')[1];
                if (cookieTheme) {
                    document.documentElement.setAttribute('data-bs-theme', cookieTheme);
                    return;
                }
                
                // Default to light
                document.documentElement.setAttribute('data-bs-theme', 'light');
            } catch (e) {
                console.error('Error applying theme:', e);
            }
        })();
    </script>

</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-netra shadow-sm">
        <div class="container-fluid">
            <!-- Logo/Brand -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <i class="fas fa-recycle me-2"></i>
                <span class="fw-bold">NetraTrash</span>
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
                        <!-- Dashboard for all users -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                            href="{{ route('dashboard') }}">
                                <i class="fas fa-home me-1"></i> Dashboard
                            </a>
                        </li>
                        
                        <!-- Role-based Navigation -->
                        @if(auth()->user()->isAdmin())
                            <!-- Admin Menu -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                                href="{{ route('admin.users.index') }}">
                                    <i class="fas fa-users me-1"></i> Users
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}" 
                                href="{{ route('admin.kategori.index') }}">
                                    <i class="fas fa-tags me-1"></i> Categories
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.barang.*') ? 'active' : '' }}" 
                                href="{{ route('admin.barang.index') }}">
                                    <i class="fas fa-box me-1"></i> Products
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}" 
                                href="{{ route('admin.transaksi.index') }}">
                                    <i class="fas fa-exchange-alt me-1"></i> Transactions
                                </a>
                            </li>
                            
                             
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                                href="{{ route('admin.reports.index') }}">
                                    <i class="fas fa-chart-bar me-1"></i> Reports
                                </a>
                            </li>
                            
                        @elseif(auth()->user()->isPetugas())
                            <!-- Petugas Menu -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petugas.scan.*') ? 'active' : '' }}" 
                                href="{{ route('petugas.scan.index') }}">
                                    <i class="fas fa-qrcode me-1"></i> Scan QR
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petugas.transaksi.*') ? 'active' : '' }}" 
                                href="{{ route('petugas.transaksi.index') }}">
                                    <i class="fas fa-exchange-alt me-1"></i> Transactions
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
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('warga.transaksi.*') ? 'active' : '' }}" 
                                href="{{ route('warga.transaksi.index') }}">
                                    <i class="fas fa-exchange-alt me-1"></i> Transaksi
                                </a>
                            </li>
                            
                            
                            <!-- TUKAR POIN MENU -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('warga.barang.*') ? 'active' : '' }}" 
                                href="{{ route('warga.barang.index') }}">
                                    <i class="fas fa-shopping-bag me-1"></i> Tukar Poin
                                </a>
                            </li>
                        @endif
                        
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
                        
                        <!-- Profile Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
                            role="button" data-bs-toggle="dropdown">
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
                                <span class="d-none d-lg-inline">{{ Str::limit(auth()->user()->name, 10) }}</span>
                            </a>
                            
                            <ul class="dropdown-menu dropdown-menu-end">
                                <!-- Header -->
                                <li>
                                    <div class="px-3 py-2">
                                        <div class="fw-bold">{{ auth()->user()->name }}</div>
                                        <small class="text-muted">{{ auth()->user()->email }}</small>
                                        @if(auth()->user()->isWarga())
                                        <div class="mt-1">
                                            <i class="fas fa-coins text-warning me-1"></i>
                                            <strong>{{ number_format(auth()->user()->total_points ?? 0) }}</strong> pts
                                        </div>
                                        @endif
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                
                                <!-- Profile -->
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user me-2"></i> Profile
                                    </a>
                                </li>
                                
                                <!-- Notifications -->
                                <li>
                                    <a class="dropdown-item position-relative" href="{{ route('notifikasi.index') }}">
                                        <i class="fas fa-bell me-2"></i> Notifications
                                        @php
                                            $unreadCount = 0;
                                            try {
                                                $unreadCount = auth()->user()->unreadNotifications()->count();
                                            } catch (\Exception $e) {
                                                // Table doesn't exist yet
                                            }
                                        @endphp
                                        @if($unreadCount > 0)
                                            <span class="position-absolute top-50 end-0 translate-middle-y badge rounded-pill bg-danger me-3" 
                                                style="font-size: 0.6rem; padding: 2px 5px;">
                                                {{ $unreadCount }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                                
                                <!-- Dashboard by Role -->
                                @if(auth()->user()->isAdmin())
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i> Admin Panel
                                    </a>
                                </li>
                                @endif
                                
                                <!-- Theme Switcher -->
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <div class="px-3 py-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="themeSwitchDropdown" 
                                                {{ (Cookie::get('theme', 'light') == 'dark') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="themeSwitchDropdown">
                                                <i class="fas fa-moon me-2"></i> Dark Mode
                                            </label>
                                        </div>
                                    </div>
                                </li>
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <!-- Logout -->
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Log Out
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
            if (themeSwitch) {
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
                    } else {
                        document.documentElement.setAttribute('data-bs-theme', 'light');
                        localStorage.setItem('theme', 'light');
                    }
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
        });
    </script>
    
    <!-- Page Specific Scripts -->
    @stack('scripts')
</body>
</html>