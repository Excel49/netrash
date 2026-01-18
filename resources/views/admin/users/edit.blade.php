@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Edit User</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Edit informasi user</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Form Edit User</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Password Section (Optional) -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <span class="text-muted">(Opsional)</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password <span class="text-muted">(Opsional)</span></label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_id') is-invalid @enderror" name="role_id" required>
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                        {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }} - {{ $role->description }}
                                </option>
                                @endforeach
                            </select>
                            @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Additional Fields -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" class="form-control @error('nik') is-invalid @enderror" 
                                   name="nik" value="{{ old('nik', $user->nik) }}">
                            @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">RT/RW</label>
                            <input type="text" class="form-control @error('rt_rw') is-invalid @enderror" 
                                   name="rt_rw" value="{{ old('rt_rw', $user->rt_rw) }}">
                            @error('rt_rw')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Area</label>
                            <input type="text" class="form-control @error('area') is-invalid @enderror" 
                                   name="area" value="{{ old('area', $user->area) }}">
                            @error('area')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Poin</label>
                            <input type="number" class="form-control @error('total_points') is-invalid @enderror" 
                                   name="total_points" value="{{ old('total_points', $user->total_points) }}" min="0">
                            @error('total_points')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Bio/Deskripsi</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror" 
                                  name="bio" rows="2">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-netra btn-lg">
                            <i class="bi bi-save me-2"></i>Update User
                        </button>
                    </div>
                </form>
                
                <!-- Delete Button (Optional) -->
                <hr class="my-4">
                <div class="text-center">
                    <button type="button" class="btn btn-outline-danger" 
                            onclick="if(confirm('Apakah Anda yakin ingin menghapus user ini?')) { 
                                document.getElementById('delete-form').submit(); 
                            }">
                        <i class="bi bi-trash me-2"></i>Hapus User
                    </button>
                    <form id="delete-form" action="{{ route('admin.users.destroy', $user->id) }}" 
                          method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection