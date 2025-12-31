<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NetraTrash - {{ $title ?? 'Dashboard' }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2E8B57;
            --secondary-color: #3CB371;
            --accent-color: #20B2AA;
            --dark-color: #228B22;
            --light-color: #F0FFF0;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color), var(--dark-color));
            min-height: 100vh;
            color: white;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
        }
        
        .sidebar .nav-link i {
            width: 25px;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-primary {
            border-left: 5px solid var(--primary-color);
        }
        
        .card-success {
            border-left: 5px solid #28a745;
        }
        
        .card-warning {
            border-left: 5px solid #ffc107;
        }
        
        .card-info {
            border-left: 5px solid var(--accent-color);
        }
        
        .stat-card {
            padding: 20px;
        }
        
        .stat-card .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        .stat-card .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .badge-netra {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-netra {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-netra:hover {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
            color: white;
        }
        
        .btn-netra-outline {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-netra-outline:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .table th {
            background-color: #f1f8e9;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(25%, -25%);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @if(auth()->check())
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=ffffff&color=2E8B57&size=100" 
                             alt="Avatar" class="rounded-circle mb-2" width="80">
                        <h6 class="mb-1">{{ auth()->user()->name }}</h6>
                        <small class="text-white-50">
                            @if(auth()->user()->isAdmin())
                                <span class="badge bg-danger">Administrator</span>
                            @elseif(auth()->user()->isPetugas())
                                <span class="badge bg-warning">Petugas</span>
                            @else
                                <span class="badge bg-info">Warga</span>
                            @endif
                        </small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('*/dashboard') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        @if(auth()->user()->isAdmin())
                            <!-- Admin Menu -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}" 
                                   href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-people"></i>
                                    Management Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/kategori*') ? 'active' : '' }}" 
                                   href="{{ route('admin.kategori.index') }}">
                                    <i class="bi bi-tags"></i>
                                    Kategori Sampah
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/penarikan*') ? 'active' : '' }}" 
                                   href="{{ route('admin.penarikan.index') }}">
                                    <i class="bi bi-cash-coin"></i>
                                    Approval Penarikan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/reports*') ? 'active' : '' }}" 
                                   href="{{ route('admin.reports.index') }}">
                                    <i class="bi bi-bar-chart"></i>
                                    Reports
                                </a>
                            </li>
                            
                        @elseif(auth()->user()->isPetugas())
                            <!-- Petugas Menu -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('petugas/scan*') ? 'active' : '' }}" 
                                   href="{{ route('petugas.scan') }}">
                                    <i class="bi bi-qr-code-scan"></i>
                                    Scan QR Code
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('petugas/transaksi*') ? 'active' : '' }}" 
                                   href="{{ route('petugas.transaksi.create') }}">
                                    <i class="bi bi-plus-circle"></i>
                                    Input Transaksi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('petugas/riwayat*') ? 'active' : '' }}" 
                                   href="{{ route('petugas.transaksi.index') }}">
                                    <i class="bi bi-clock-history"></i>
                                    Riwayat Transaksi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('petugas/warga*') ? 'active' : '' }}" 
                                   href="{{ route('petugas.warga.index') }}">
                                    <i class="bi bi-people"></i>
                                    Data Warga
                                </a>
                            </li>
                            
                        @elseif(auth()->user()->isWarga())
                            <!-- Warga Menu -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('warga/qrcode*') ? 'active' : '' }}" 
                                   href="{{ route('warga.qrcode') }}">
                                    <i class="bi bi-qr-code"></i>
                                    QR Code Saya
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('warga/transaksi*') ? 'active' : '' }}" 
                                   href="{{ route('warga.transaksi.index') }}">
                                    <i class="bi bi-receipt"></i>
                                    Riwayat Transaksi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('warga/penarikan*') ? 'active' : '' }}" 
                                   href="{{ route('warga.penarikan.index') }}">
                                    <i class="bi bi-cash-stack"></i>
                                    Penarikan Poin
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('warga/kategori*') ? 'active' : '' }}" 
                                   href="{{ route('warga.kategori.index') }}">
                                    <i class="bi bi-info-circle"></i>
                                    Informasi Kategori
                                </a>
                            </li>
                        @endif
                        
                        <!-- Common Menu -->
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('*/notifikasi*') ? 'active' : '' }}" 
                               href="{{ route('notifikasi.index') }}">
                                <i class="bi bi-bell"></i>
                                Notifikasi
                                @php
                                    $unreadCount = auth()->user()->notifikasi()->where('dibaca', false)->count();
                                @endphp
                                @if($unreadCount > 0)
                                <span class="badge bg-danger notification-badge">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        </li>
                        
                        <li class="nav-item mt-4">
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
            @endif
            
            <!-- Main Content -->
            <main class="{{ auth()->check() ? 'col-md-9 ms-sm-auto col-lg-10 px-md-4' : 'col-12' }}">
                <!-- Navbar -->
                @if(auth()->check())
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#sidebarToggle" aria-controls="sidebarToggle">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <a class="navbar-brand" href="{{ route('dashboard') }}">
                            <i class="bi bi-recycle text-success"></i>
                            NetraTrash
                        </a>
                        
                        <div class="d-flex align-items-center">
                            <div class="dropdown">
                                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
                                   id="userDropdown" data-bs-toggle="dropdown">
                                    <span class="me-2">{{ auth()->user()->name }}</span>
                                    <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=2E8B57&color=fff&size=32" 
                                         alt="Avatar" class="rounded-circle">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person me-2"></i>Profile
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="#" 
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                @endif
                
                <!-- Content -->
                <div class="container-fluid">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif
                    
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif
                    
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapse');
        });
        
        // Auto-dismiss alerts
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // SweetAlert2 configuration
        window.Swal = Swal;
        
        // Delete confirmation
        function confirmDelete(event, formId) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
        
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>