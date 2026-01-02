<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Profile Information</h5>
        <p class="text-muted mb-0">Update your account's profile information and email address</p>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('patch')
            
            <!-- Profile Photo -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Profile Photo</label>
                    <div class="position-relative">
                        @if(auth()->user()->profile_photo_path)
                            <img id="profile-preview" 
                                 src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="rounded-circle border border-3 border-primary"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div id="profile-initials" 
                                 class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                 style="width: 150px; height: 150px; border: 3px solid var(--bs-primary);">
                                <span class="text-white fw-bold display-5">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <img id="profile-preview" src="" alt="Preview" 
                                 class="rounded-circle border border-3 border-primary d-none"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @endif
                        
                        <label for="photo" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer"
                               style="width: 40px; height: 40px;">
                            <i class="bi bi-camera"></i>
                            <input type="file" id="photo" name="photo" class="d-none" accept="image/*" onchange="previewImage(event)">
                        </label>
                    </div>
                    <div class="mt-2">
                        @if(auth()->user()->profile_photo_path)
                        <button type="button" onclick="removePhoto()" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i>Remove
                        </button>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-9">
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" 
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', auth()->user()->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" 
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', auth()->user()->email )}}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                            <div class="alert alert-warning mt-2 mb-0 py-2">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Your email address is unverified.
                                <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 ms-1">
                                        Click here to re-send the verification email.
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Phone -->
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', auth()->user()->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Role-specific fields -->
                        @if(auth()->user()->role->name == 'warga')
                        <div class="col-md-6 mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" id="nik" name="nik" 
                                   class="form-control @error('nik') is-invalid @enderror"
                                   value="{{ old('nik', auth()->user()->nik) }}">
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="rt_rw" class="form-label">RT/RW</label>
                            <input type="text" id="rt_rw" name="rt_rw" 
                                   class="form-control @error('rt_rw') is-invalid @enderror"
                                   value="{{ old('rt_rw', auth()->user()->rt_rw) }}">
                            @error('rt_rw')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                        
                        @if(auth()->user()->role->name == 'petugas')
                        <div class="col-md-6 mb-3">
                            <label for="area" class="form-label">Area Tugas</label>
                            <input type="text" id="area" name="area" 
                                   class="form-control @error('area') is-invalid @enderror"
                                   value="{{ old('area', auth()->user()->area) }}">
                            @error('area')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Address -->
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea id="address" name="address" rows="3"
                          class="form-control @error('address') is-invalid @enderror">{{ old('address', auth()->user()->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Bio -->
            <div class="mb-4">
                <label for="bio" class="form-label">Bio</label>
                <textarea id="bio" name="bio" rows="3"
                          class="form-control @error('bio') is-invalid @enderror"
                          placeholder="Tell us a little about yourself">{{ old('bio', auth()->user()->bio) }}</textarea>
                @error('bio')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Submit Button -->
            <div class="d-flex justify-content-end">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-netra">
                    <i class="bi bi-check-circle me-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function removePhoto() {
    if (confirm('Are you sure you want to remove your profile photo?')) {
        // Create a hidden input to indicate photo removal
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_photo';
        input.value = '1';
        document.querySelector('form').appendChild(input);
        
        // Hide current photo and show initials
        document.getElementById('profile-preview').classList.add('d-none');
        document.getElementById('profile-initials').classList.remove('d-none');
    }
}
</script>