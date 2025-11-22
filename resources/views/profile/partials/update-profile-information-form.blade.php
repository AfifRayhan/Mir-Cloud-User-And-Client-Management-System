<section>
    <header class="mb-4">
        <h2 class="h5 fw-bold mb-2">Profile Information</h2>
        <p class="text-muted small mb-0">Update your account's profile information.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Name</label>
            <input id="name" 
                   name="name" 
                   type="text" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $user->name) }}" 
                   required 
                   autofocus 
                   autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="username" class="form-label fw-semibold">Username</label>
            <input id="username" 
                   name="username" 
                   type="text" 
                   class="form-control @error('username') is-invalid @enderror" 
                   value="{{ old('username', $user->username) }}" 
                   required 
                   autocomplete="username">
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Save</button>
            
            @if (session('status') === 'profile-updated')
                <span class="text-success small">Saved.</span>
            @endif
        </div>
    </form>
</section>
