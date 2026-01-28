<x-app-layout>
    <div class="custom-user-add-container">
        <!-- Background Elements -->
        <div class="custom-user-add-bg-pattern"></div>
        <div class="custom-user-add-bg-circle circle-1"></div>
        <div class="custom-user-add-bg-circle circle-2"></div>
        <div class="custom-user-add-bg-circle circle-3"></div>

        <div class="custom-user-add-header text-center">
            <h1 class="custom-user-add-title fw-bold">Add New User</h1>
            <p class="custom-user-add-subtitle">Create a new user account</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="custom-user-add-card">
                    <div class="card-body p-5">

                        @if ($errors->any())
                        <div class="custom-user-add-alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf

                            <!-- Name -->
                            <div class="custom-user-add-section">
                                <div class="custom-user-add-section-header">
                                    <div class="custom-user-add-section-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h5 class="custom-user-add-section-title">User Information</h5>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="custom-user-add-label">Full Name</label>
                                    <input id="name"
                                        class="custom-user-add-input @error('name') is-invalid @enderror"
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                        autofocus
                                        placeholder="Enter full name">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="custom-user-add-label">Email</label>
                                    <input id="email"
                                        class="custom-user-add-input @error('email') is-invalid @enderror"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        placeholder="Enter email address">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="custom-user-add-label">Username</label>
                                <input id="username"
                                    class="custom-user-add-input @error('username') is-invalid @enderror"
                                    type="text"
                                    name="username"
                                    value="{{ old('username') }}"
                                    required
                                    placeholder="Enter username">
                                @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role and Department -->
                            <div class="custom-user-add-section">
                                <div class="custom-user-add-section-header">
                                    <div class="custom-user-add-section-icon">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div>
                                        <h5 class="custom-user-add-section-title">Role & Department</h5>
                                    </div>
                                </div>

                                <!-- Role -->
                                <div class="mb-3">
                                    <label for="role_id" class="custom-user-add-label">Role</label>
                                    <select id="role_id"
                                        name="role_id"
                                        class="custom-user-add-select @error('role_id') is-invalid @enderror"
                                        required>
                                        <option value="">Select a role</option>
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
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
                                    <label for="department_id" class="custom-user-add-label">Department</label>
                                    <select id="department_id"
                                        name="department_id"
                                        class="custom-user-add-select @error('department_id') is-invalid @enderror">
                                        <option value="">Select a department (Optional)</option>
                                        @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->department_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="custom-user-add-section">
                                <div class="custom-user-add-section-header">
                                    <div class="custom-user-add-section-icon">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div>
                                        <h5 class="custom-user-add-section-title">Security</h5>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="custom-user-add-label">Password</label>
                                    <input id="password"
                                        class="custom-user-add-input @error('password') is-invalid @enderror"
                                        type="password"
                                        name="password"
                                        required
                                        placeholder="Enter password">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-4">
                                    <label for="password_confirmation" class="custom-user-add-label">Confirm Password</label>
                                    <input id="password_confirmation"
                                        class="custom-user-add-input"
                                        type="password"
                                        name="password_confirmation"
                                        required
                                        placeholder="Confirm password">
                                </div>
                            </div>

                            <div class="custom-user-add-actions">
                                <div class="d-flex justify-content-center gap-3">
                                    <button type="submit" class="custom-user-add-submit-btn">
                                        Create User
                                    </button>
                                    <button type="button" data-url="{{ route('users.index') }}" onclick="window.location.href=this.getAttribute('data-url')" class="custom-user-add-cancel-btn">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    @vite(['resources/views/users/user-form.js'])
    @endpush
</x-app-layout>