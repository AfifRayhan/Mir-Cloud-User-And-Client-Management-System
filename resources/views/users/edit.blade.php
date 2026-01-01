<x-app-layout>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold mb-1">Edit User</h2>
                            <p class="text-muted">Update user account details</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('users.update', $user) }}">
                            @csrf
                            @method('PUT')

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Full Name</label>
                                <input id="name" 
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       type="text" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required 
                                       autofocus 
                                       placeholder="Enter full name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input id="email" 
                                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                       type="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required 
                                       placeholder="Enter email address">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">Username</label>
                                <input id="username" 
                                       class="form-control form-control-lg @error('username') is-invalid @enderror" 
                                       type="text" 
                                       name="username" 
                                       value="{{ old('username', $user->username) }}" 
                                       required 
                                       placeholder="Enter username">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div class="mb-3">
                                <label for="role_id" class="form-label fw-semibold">Role</label>
                                <select id="role_id" 
                                        name="role_id" 
                                        class="form-select form-select-lg @error('role_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select a role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('-', ' ', $role->role_name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div class="mb-3">
                                <label for="department_id" class="form-label fw-semibold">Department</label>
                                <select id="department_id" 
                                        name="department_id" 
                                        class="form-select form-select-lg @error('department_id') is-invalid @enderror">
                                    <option value="">Select a department (Optional)</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3">Change Password <small class="text-muted fw-normal">(Optional)</small></h5>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">New Password</label>
                                <input id="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                                       type="password"
                                       name="password"
                                       placeholder="Leave blank to keep current password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                                <input id="password_confirmation" 
                                       class="form-control form-control-lg"
                                       type="password"
                                       name="password_confirmation" 
                                       placeholder="Confirm new password">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Update User
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const usernameInput = document.getElementById('username');
            const initialEmail = emailInput.value;
            const initialUsername = usernameInput.value;
            let isManuallyEdited = false;

            // If username already matches email, we assume it should stay in sync
            // Unless the user explicitly modifies it.
            if (initialEmail !== initialUsername) {
                isManuallyEdited = true;
            }

            usernameInput.addEventListener('input', function() {
                isManuallyEdited = true;
            });

            emailInput.addEventListener('input', function() {
                if (!isManuallyEdited) {
                    usernameInput.value = emailInput.value;
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
