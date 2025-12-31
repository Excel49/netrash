@extends('layouts.app')

@section('title', 'Management Users')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Management Users</h2>
            <a href="{{ route('admin.users.create') }}" class="btn btn-netra">
                <i class="bi bi-person-plus me-2"></i>Tambah User
            </a>
        </div>
        <p class="text-muted">Kelola semua user sistem</p>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Users</h6>
                <h3>{{ $users->total() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Admin</h6>
                <h3>{{ $users->where('role_id', 1)->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Petugas</h6>
                <h3>{{ $users->where('role_id', 2)->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Warga</h6>
                <h3>{{ $users->where('role_id', 3)->count() }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Poin</th>
                        <th>Bergabung</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <div style="width: 32px; height: 32px; background-color: #2E8B57; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->phone ?: '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role_id == 1)
                            <span class="badge bg-danger">Admin</span>
                            @elseif($user->role_id == 2)
                            <span class="badge bg-warning">Petugas</span>
                            @else
                            <span class="badge bg-info">Warga</span>
                            @endif
                        </td>
                        <td>{{ number_format($user->total_points, 0, ',', '.') }}</td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($user->email_verified_at)
                            <span class="badge bg-success">Verified</span>
                            @else
                            <span class="badge bg-secondary">Unverified</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    @if($users->hasPages())
    <div class="card-footer">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection