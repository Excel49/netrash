@extends('layouts.app')

@section('title', 'Users Reports')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Users Reports</h2>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Laporan data pengguna sistem</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Role</label>
                <select name="role_id" class="form-select">
                    <option value="">All Roles</option>
                    <option value="1" {{ request('role_id') == '1' ? 'selected' : '' }}>Admin</option>
                    <option value="2" {{ request('role_id') == '2' ? 'selected' : '' }}>Petugas</option>
                    <option value="3" {{ request('role_id') == '3' ? 'selected' : '' }}>Warga</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status Email</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-netra">
                    <i class="bi bi-filter me-2"></i>Filter
                </button>
                <a href="{{ route('admin.reports.users') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
                
                <!-- Export Buttons -->
                <div class="btn-group ms-2">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <form action="{{ route('admin.reports.export') }}" method="POST" target="_blank">
                                @csrf
                                <input type="hidden" name="report" value="users">
                                <input type="hidden" name="type" value="excel">
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-file-earmark-excel me-2"></i>Excel
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Users</h6>
                <h3>{{ number_format($summary['total']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Admin</h6>
                <h3>{{ number_format($summary['admin']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Petugas</h6>
                <h3>{{ number_format($summary['petugas']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Warga</h6>
                <h3>{{ number_format($summary['warga']) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Poin Semua User</h6>
                <h3 class="text-primary">{{ number_format($summary['total_poin']) }}</h3>
                <p class="text-muted mb-0">Rata-rata: {{ number_format($summary['avg_poin'], 0) }} poin/user</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Email Terverifikasi</h6>
                <h3 class="text-success">{{ number_format($summary['verified']) }}</h3>
                @if($summary['total'] > 0)
                <p class="text-muted mb-0">{{ round(($summary['verified']/$summary['total'])*100, 1) }}% dari total</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Aktivitas Terakhir</h6>
                @if($users->count() > 0)
                <h5 class="mb-0">{{ $users->sortByDesc('updated_at')->first()->name }}</h5>
                <p class="text-muted mb-0">
                    {{ $users->sortByDesc('updated_at')->first()->updated_at->diffForHumans() }}
                </p>
                @else
                <p class="mb-0">Tidak ada data</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="usersTable">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Role</th>
                        <th>Total Poin</th>
                        <th>Bergabung</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-primary text-white rounded-circle">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?: '-' }}</td>
                        <td>
                            @if($user->role_id == 1)
                                <span class="badge bg-danger">Admin</span>
                            @elseif($user->role_id == 2)
                                <span class="badge bg-success">Petugas</span>
                            @else
                                <span class="badge bg-info">Warga</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-warning">{{ number_format($user->total_points) }}</span>
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($user->email_verified_at)
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-secondary">Unverified</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.show', $user->id) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data user</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($users->count() > 0)
                <tfoot>
                    <tr class="table-light">
                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                        <td><strong>{{ number_format($summary['total_poin']) }}</strong></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
        @if($users->count() > 0)
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="mb-0">Menampilkan {{ $users->count() }} user</p>
            </div>
            <div>
                <small class="text-muted">Diperbarui: {{ now()->format('d/m/Y H:i') }}</small>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Role Distribution -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Distribusi Per Role</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="badge bg-danger me-3" style="width: 20px; height: 20px;"></div>
                            <div>
                                <h6 class="mb-0">Admin</h6>
                                <p class="mb-0">{{ $summary['admin'] }} user ({{ $summary['total'] > 0 ? round(($summary['admin']/$summary['total'])*100, 1) : 0 }}%)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="badge bg-success me-3" style="width: 20px; height: 20px;"></div>
                            <div>
                                <h6 class="mb-0">Petugas</h6>
                                <p class="mb-0">{{ $summary['petugas'] }} user ({{ $summary['total'] > 0 ? round(($summary['petugas']/$summary['total'])*100, 1) : 0 }}%)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="badge bg-info me-3" style="width: 20px; height: 20px;"></div>
                            <div>
                                <h6 class="mb-0">Warga</h6>
                                <p class="mb-0">{{ $summary['warga'] }} user ({{ $summary['total'] > 0 ? round(($summary['warga']/$summary['total'])*100, 1) : 0 }}%)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection