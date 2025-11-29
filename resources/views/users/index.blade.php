<x-app-layout>
    <div class="container-fluid py-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">User Management</h1>
                <p class="text-muted mb-0">Add, edit, or remove platform users and reset their credentials.</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-lg">
                + Add User
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">User</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Role</th>
                            <th scope="col">Department</th>
                            <th scope="col">Created By</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="text-muted small">
                                        {{ $user->designation ?? '—' }}
                                    </div>
                                    <div class="text-muted small">
                                        Username: {{ $user->username }}
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $user->email ?? '—' }}</div>
                                    <div class="text-muted small">{{ $user->phone ?? '—' }}</div>
                                </td>
                                <td>{{ ucfirst(str_replace('-', ' ', $user->role->role_name ?? '')) ?: '—' }}</td>
                                <td>{{ $user->department->name ?? '—' }}</td>
                                <td>{{ $user->creator->name ?? 'System' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No users found. Start by adding one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="card-footer">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

