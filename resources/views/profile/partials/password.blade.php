<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-lock me-2"></i>Update Password</h5>
        <p class="text-muted mb-0">Ensure your account is using a long, random password to stay secure</p>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('profile.password.update') }}">
            @csrf
            @method('put')
            
            <!-- Current Password -->
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <div class="input-group">
                    <input type="password" id="current_password" name="current_password" 
                           class="form-control @error('current_password') is-invalid @enderror"
                           placeholder="Enter current password">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('current_password')">
                        <i class="bi bi-eye"></i>
                    </button>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- New Password -->
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" 
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Enter new password (min 8 characters)">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                        <i class="bi bi-eye"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Password Strength Meter -->
                <div class="mt-3">
                    <div class="progress" style="height: 8px;">
                        <div id="strength-bar-1" class="progress-bar" style="width: 25%"></div>
                        <div id="strength-bar-2" class="progress-bar" style="width: 25%"></div>
                        <div id="strength-bar-3" class="progress-bar" style="width: 25%"></div>
                        <div id="strength-bar-4" class="progress-bar" style="width: 25%"></div>
                    </div>
                    <small id="strength-text" class="mt-2 d-block"></small>
                </div>
            </div>
            
            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <div class="input-group">
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           placeholder="Confirm new password">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                        <i class="bi bi-eye"></i>
                    </button>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Password Requirements -->
            <div class="alert alert-info mb-4">
                <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Password Requirements</h6>
                <ul class="mb-0 ps-3">
                    <li>Minimum 8 characters</li>
                    <li>Include uppercase and lowercase letters</li>
                    <li>Include at least one number</li>
                    <li>Include special characters (optional but recommended)</li>
                </ul>
            </div>
            
            <!-- Submit Button -->
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-netra">
                    <i class="bi bi-key me-2"></i>Update Password
                </button>
            </div>
        </form>
    </div>
</div>