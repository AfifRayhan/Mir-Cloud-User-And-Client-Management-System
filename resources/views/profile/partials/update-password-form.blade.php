<section>
    <header class="mb-4">
        <h2 class="h5 fw-bold mb-2">Update Password</h2>
        <p class="text-muted small mb-0">Ensure your account is using a long, random password to stay secure.</p>
    </header>

    <form method="post" action="{{ route('profile.password') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label fw-semibold">Current Password</label>
            <input id="update_password_current_password" 
                   name="current_password" 
                   type="password" 
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                   autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label fw-semibold">New Password</label>
            <input id="update_password_password" 
                   name="password" 
                   type="password" 
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                   autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label fw-semibold">Confirm Password</label>
            <input id="update_password_password_confirmation" 
                   name="password_confirmation" 
                   type="password" 
                   class="form-control" 
                   autocomplete="new-password">
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Save</button>

            @if (session('status') === 'password-updated')
                <span class="text-success small">Saved.</span>
            @endif
        </div>
    </form>
</section>
