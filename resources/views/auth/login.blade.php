<x-guest-layout>
    <div class="container-fluid custom-login-container p-0">
        <div class="row g-0 min-vh-100">
            <!-- Left Side - Logo & Branding -->
            <div class="col-lg-6 d-flex custom-login-left">
                <div class="custom-login-brand-wrapper w-100 d-flex flex-column justify-content-center align-items-center p-4 p-lg-5">
                    
                    <h1 class="custom-login-brand-title fw-bold text-white mb-3 mb-lg-4 text-center">MCloud Client Management System</h1>
                    <p class="custom-login-brand-subtitle text-light text-center px-3 px-lg-0">
                        Secure Cloud Platform for Modern Businesses
                    </p>
                    
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center custom-login-right">
                <div class="w-100 px-3 px-md-4 px-lg-5 py-4 py-lg-5">
                    <div class="custom-login-form-container mx-auto">
                        <!-- Form Header -->
                        <div class="text-center mb-4 mb-lg-5">
                            <h2 class="custom-login-form-title fw-bold mb-2">Welcome Back</h2>
                            <p class="custom-login-form-subtitle text-muted">
                                Sign in to continue to your account
                            </p>
                        </div>

                        <!-- Status Messages -->
                        @if (session('status'))
                            <div class="custom-login-alert alert alert-success alert-dismissible fade show mb-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span>{{ session('status') }}</span>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <div class="custom-login-card card border-0 shadow-sm rounded-4">
                            <div class="card-body p-3 p-md-4 p-lg-5">
                                <form method="POST" action="{{ route('login') }}" id="loginForm">
                                    @csrf

                                    <!-- Username Field -->
                                    <div class="mb-3 mb-lg-4">
                                        <label for="username" class="form-label fw-semibold custom-login-label">
                                            <i class="fas fa-user me-2"></i>Username
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text custom-login-input-icon">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input id="username"
                                                   class="form-control form-control-lg custom-login-input @error('username') is-invalid @enderror"
                                                   type="text"
                                                   name="username"
                                                   value="{{ old('username') }}"
                                                   required
                                                   autofocus
                                                   autocomplete="username"
                                                   placeholder="Enter your username">
                                        </div>
                                        @error('username')
                                            <div class="custom-login-error invalid-feedback d-block">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Password Field -->
                                    <div class="mb-3 mb-lg-4">
                                        <label for="password" class="form-label fw-semibold custom-login-label">
                                            <i class="fas fa-lock me-2"></i>Password
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text custom-login-input-icon">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input id="password"
                                                   class="form-control form-control-lg custom-login-input @error('password') is-invalid @enderror"
                                                   type="password"
                                                   name="password"
                                                   required
                                                   autocomplete="current-password"
                                                   placeholder="Enter your password">
                                            <button class="btn custom-login-password-toggle" type="button" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="custom-login-error invalid-feedback d-block">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Remember Me & Forgot Password -->
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mb-3 mb-lg-4 gap-2">
                                        <div class="form-check">
                                            <input class="form-check-input custom-login-checkbox" 
                                                   type="checkbox" 
                                                   id="remember_me" 
                                                   name="remember">
                                            <label class="form-check-label custom-login-checkbox-label" for="remember_me">
                                                Remember me
                                            </label>
                                        </div>
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="custom-login-forgot-link text-decoration-none">
                                                Forgot password?
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-lg custom-login-submit-btn">
                                            <span class="custom-login-btn-text">Sign In</span>
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="text-center mt-4">
                            <p class="custom-login-footer-text text-muted small">
                                © {{ date('Y') }} Mir Cloud. All rights reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>