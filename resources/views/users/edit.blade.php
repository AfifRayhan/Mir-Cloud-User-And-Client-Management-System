<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-lg-8">
                <h1 class="h3 fw-bold mb-0">Edit User</h1>
                <p class="text-muted">Update any part of this user’s profile, including credentials.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    Back to User Management
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('users.update', $user) }}">
                            @csrf
                            @method('PUT')
                            @include('users.partials.form-fields', ['user' => $user, 'roles' => $roles, 'departments' => $departments])

                            <div class="d-flex flex-wrap gap-3 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                                <a href="{{ route('users.index') }}" class="btn btn-link text-decoration-none">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

