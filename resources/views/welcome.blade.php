<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NetraTrash - @yield('title', 'Sistem Pengelolaan Sampah')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-trash3"></i> NetraTrash
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="bi bi-house"></i> Dashboard
                        </a>
                    </li>
                    
                    @if(auth()->user()->isWarga())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('warga.qr') }}">
                            <i class="bi bi-qr-code"></i> QR Code Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('warga.penarikan') }}">
                            <i class="bi bi-wallet2"></i> Penarikan Poin
                        </a>
                    </li>
                    @endif
                    
                    @if(auth()->user()->isPetugas())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('petugas.scan') }}">
                            <i class="bi bi-camera"></i> Scan QR
                        </a>
                    </li>
                    @endif
                    
                    @if(auth()->user()->isAdmin())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.users') }}">Kelola User</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.kategori') }}">Kategori Sampah</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports') }}">Laporan</a></li>
                        </ul>
                    </li>
                    @endif
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->nama_lengkap }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text">
                                <small>{{ auth()->user()->role->nama_role }}</small>
                            </span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
                @endauth
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
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

    <footer class="footer mt-auto py-3 bg-dark text-white">
        <div class="container text-center">
            <span>&copy; {{ date('Y') }} NetraTrash - Sistem Pengelolaan Sampah Berbasis Poin</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>