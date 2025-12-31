@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Edit Profile</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Update informasi profil Anda</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <!-- Update Profile Information -->
                @include('profile.partials.update-profile-information-form')
                
                <!-- Update Password -->
                <div class="mt-6">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Update Password') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Ensure your account is using a long, random password to stay secure.') }}
                        </p>
                    </header>

                    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('put')

                        <div>
                            <label class="form-label">{{ __('Current Password') }}</label>
                            <input id="current_password" name="current_password" type="password" 
                                   class="form-control" autocomplete="current-password">
                            @error('current_password')
                            <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">{{ __('New Password') }}</label>
                            <input id="password" name="password" type="password" 
                                   class="form-control" autocomplete="new-password">
                            @error('password')
                            <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">{{ __('Confirm Password') }}</label>
                            <input id="password_confirmation" name="password_confirmation" 
                                   type="password" class="form-control" autocomplete="new-password">
                            @error('password_confirmation')
                            <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="btn btn-netra">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
                
                <!-- Delete Account -->
                <div class="mt-6">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Delete Account') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                        </p>
                    </header>

                    <button class="btn btn-danger mt-3" 
                            onclick="confirmDelete()">
                        {{ __('Delete Account') }}
                    </button>
                    
                    <!-- Delete Account Form -->
                    <form id="delete-form" method="post" action="{{ route('profile.destroy') }}" class="d-none">
                        @csrf
                        @method('delete')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Akun Anda akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form').submit();
        }
    });
}
</script>
@endpush
@endsection