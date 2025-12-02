<section>
    <header class="mb-4">
        <h2 class="h5 fw-bold mb-2">Profile Information</h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-sm text-gray-700">Role</label>
                <div class="mt-1 text-gray-900 font-semibold">
                    {{ ucfirst(str_replace('-', ' ', $user->role->role_name)) }}
                </div>
            </div>
            <div>
                <label class="block font-medium text-sm text-gray-700">Department</label>
                <div class="mt-1 text-gray-900 font-semibold">
                    {{ $user->department ? $user->department->department_name : 'Not Assigned' }}
                </div>
            </div>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
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
