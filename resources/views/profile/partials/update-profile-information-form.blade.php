<section>
    <form id="send-verification" method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-2">
            <label for="name" class="custom-profile-label">Full Name</label>
            <input id="name" 
                   name="name" 
                   type="text" 
                   class="form-control custom-profile-input @error('name') is-invalid @enderror" 
                   value="{{ old('name', $user->name) }}" 
                   required 
                   autofocus 
                   autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-2">
            <label for="username" class="custom-profile-label">Username</label>
            <input id="username" 
                   name="username" 
                   type="text" 
                   class="form-control custom-profile-input @error('username') is-invalid @enderror" 
                   value="{{ old('username', $user->username) }}" 
                   required 
                   autocomplete="username">
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-2">
            <label for="email" class="custom-profile-label">Email Address</label>
            <input id="email" 
                   name="email" 
                   type="email" 
                   class="form-control custom-profile-input @error('email') is-invalid @enderror" 
                   value="{{ old('email', $user->email) }}" 
                   required 
                   autocomplete="email">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3 mt-3">
            <button type="submit" class="custom-profile-save-btn">
                <i class="fas fa-save me-2"></i> Save Changes
            </button>
            
            @if (session('status') === 'profile-updated')
                <div class="text-success small d-flex align-items-center">
                    <i class="fas fa-check-circle me-1"></i> Changes saved.
                </div>
            @endif
        </div>
    </form>
</section>
