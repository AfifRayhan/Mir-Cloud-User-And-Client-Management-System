<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <!-- Logo -->
                            <img src="{{ asset('Mir-Cloud-Logo.png') }}" alt="Logo" class="mb-3" style="width: auto; height: 200px;">
                            <h2 class="fw-bold mb-1">Welcome Back</h2>
                            <p class="text-muted">Sign in to your account</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">Username</label>
                                <input id="username" 
                                       class="form-control form-control-lg @error('username') is-invalid @enderror" 
                                       type="text" 
                                       name="username" 
                                       value="{{ old('username') }}" 
                                       required 
                                       autofocus 
                                       autocomplete="username"
                                       placeholder="Enter your username">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <input id="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                                       type="password"
                                       name="password"
                                       required 
                                       autocomplete="current-password"
                                       placeholder="Enter your password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                                    <label class="form-check-label" for="remember_me">
                                        Remember me
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Sign In
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
