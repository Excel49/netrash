@extends('layouts.app')

@section('title', 'Management Users')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">
                <i class="fas fa-users-cog me-2"></i>Management Users
            </h2>
            <a href="{{ route('admin.users.create') }}" class="btn btn-netra">
                <i class="fas fa-user-plus me-2"></i>Tambah User
            </a>
        </div>
        <p class="text-muted">Kelola semua user sistem</p>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-primary bg-gradient text-white shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Total Users
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            {{ number_format($totalStats['total_users']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-white-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-danger bg-gradient text-white shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Admin
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            {{ number_format($totalStats['total_admin']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-white-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-warning bg-gradient text-white shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Petugas
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            {{ number_format($totalStats['total_petugas']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-white-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-info bg-gradient text-white shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Warga
                        </div>
                        <div class="h5 mb-0 font-weight-bold">
                            {{ number_format($totalStats['total_warga']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-white-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i> Filter Users
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Cari Nama/Email</label>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" placeholder="Cari user...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Role</label>
                <select name="role_id" class="form-select">
                    <option value="">Semua Role</option>
                    <option value="1" {{ request('role_id') == '1' ? 'selected' : '' }}>Admin</option>
                    <option value="2" {{ request('role_id') == '2' ? 'selected' : '' }}>Petugas</option>
                    <option value="3" {{ request('role_id') == '3' ? 'selected' : '' }}>Warga</option>
                </select>
            </div>
    
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-netra w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100" title="Reset Filter">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-table me-2"></i> Daftar Users
        </h5>
        <small class="text-muted">
            Menampilkan {{ $users->count() }} dari {{ $users->total() }} users
        </small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th style="width: 25%;">User</th>
                        <th style="width: 20%;">Email</th>
                        <th style="width: 10%;">Role</th>
                        <th class="text-center" style="width: 10%;">Poin</th>
                        <th style="width: 15%;">Bergabung</th>
                        <th class="text-center" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + (($users->currentPage() - 1) * $users->perPage()) }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3">
                                    <img src="{{ $user->profile_photo_url }}" 
                                         alt="{{ $user->name }}" 
                                         class="rounded-circle"
                                         style="width: 36px; height: 36px; object-fit: cover; border: 2px solid #dee2e6;">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->phone ?: 'No phone' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role_id == 1)
                            <span class="badge bg-danger px-2 py-1">
                                <i class="fas fa-user-shield me-1"></i>Admin
                            </span>
                            @elseif($user->role_id == 2)
                            <span class="badge bg-warning px-2 py-1">
                                <i class="fas fa-user-tie me-1"></i>Petugas
                            </span>
                            @else
                            <span class="badge bg-info px-2 py-1">
                                <i class="fas fa-user me-1"></i>Warga
                            </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success rounded-pill px-3 py-1">
                                {{ number_format($user->total_points) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted d-block">{{ $user->created_at->format('d/m/Y') }}</small>
                            <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                        </td>
                
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="btn btn-outline-primary" 
                                   title="Edit User" data-bs-toggle="tooltip">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger" 
                                            title="Hapus User" 
                                            data-bs-toggle="tooltip"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }}?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-users fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada data user</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination Custom -->
@if($users->hasPages())
<div class="card-footer">
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mb-0">
            {{-- Previous Page Link --}}
            @if($users->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $users->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" aria-label="Previous">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @php
                $current = $users->currentPage();
                $last = $users->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
                
                if($end - $start < 4) {
                    if($start == 1) {
                        $end = min($last, $start + 4);
                    } else {
                        $start = max(1, $end - 4);
                    }
                }
            @endphp

            {{-- First Page Link --}}
            @if($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $users->url(1) . '&' . http_build_query(request()->except('page')) }}">1</a>
                </li>
                @if($start > 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            {{-- Page Number Links --}}
            @for($i = $start; $i <= $end; $i++)
                <li class="page-item {{ ($i == $current) ? 'active' : '' }}">
                    <a class="page-link" href="{{ $users->url($i) . '&' . http_build_query(request()->except('page')) }}">{{ $i }}</a>
                </li>
            @endfor

            {{-- Last Page Link --}}
            @if($end < $last)
                @if($end < $last - 1)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $users->url($last) . '&' . http_build_query(request()->except('page')) }}">{{ $last }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if($users->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $users->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" aria-label="Next">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
</div>
    @endif
</div>

<!-- Quick Stats -->
@if($users->isNotEmpty())
<div class="row mt-4">
    <div class="col-md-6">
        
    </div>

</div>
@endif
@endsection

@push('styles')
<style>
    .page-link {
        color: #2E8B57;
        border: 1px solid #dee2e6;
        margin: 0 2px;
        border-radius: 4px;
    }
    
    .page-item.active .page-link {
        background-color: #2E8B57;
        border-color: #2E8B57;
        color: white;
    }
    
    .page-link:hover {
        background-color: #f8f9fa;
        border-color: #2E8B57;
        color: #2E8B57;
    }
    
    .avatar-sm img {
        transition: transform 0.2s;
    }
    
    .avatar-sm img:hover {
        transform: scale(1.1);
    }
    
    .badge {
        font-size: 0.8em;
        font-weight: 500;
    }
    
    .table > :not(caption) > * > * {
        padding: 0.75rem 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush