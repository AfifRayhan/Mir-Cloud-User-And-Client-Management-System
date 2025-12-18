<section>
    <form method="post" action="{{ route('profile.password') }}">
        @csrf
        @method('put')

        <div class="mb-2">
            <label for="update_password_current_password" class="custom-profile-label">Current Password</label>
            <input id="update_password_current_password" 
                   name="current_password" 
                   type="password" 
                   class="form-control custom-profile-input @error('current_password', 'updatePassword') is-invalid @enderror" 
                   autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-2">
            <label for="update_password_password" class="custom-profile-label">New Password</label>
            <input id="update_password_password" 
                   name="password" 
                   type="password" 
                   class="form-control custom-profile-input @error('password', 'updatePassword') is-invalid @enderror" 
                   autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-2">
            <label for="update_password_password_confirmation" class="custom-profile-label">Confirm New Password</label>
            <input id="update_password_password_confirmation" 
                   name="password_confirmation" 
                   type="password" 
                   class="form-control custom-profile-input" 
                   autocomplete="new-password">
        </div>

        <div class="d-flex align-items-center gap-3 mt-3">
            <button type="submit" class="custom-profile-save-btn">
                <i class="fas fa-key me-2"></i> Update Password
            </button>

            @if (session('status') === 'password-updated')
                <div class="text-success small d-flex align-items-center">
                    <i class="fas fa-check-circle me-1"></i> Password updated.
                </div>
            @endif
        </div>
    </form>
</section>
