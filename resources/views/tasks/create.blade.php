<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 fw-bold mb-1">Assign New Task</h1>
                <p class="text-muted mb-0">Create and assign a task to a pro-tech or tech user</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-xl-6">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <p class="mb-2 fw-semibold">Please review the highlighted fields.</p>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('tasks.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="task_name" class="form-label fw-semibold">Task Name</label>
                                <input type="text" class="form-control @error('task_name') is-invalid @enderror" id="task_name" name="task_name" value="{{ old('task_name') }}" required>
                                @error('task_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="task_details" class="form-label fw-semibold">Task Details</label>
                                <textarea class="form-control @error('task_details') is-invalid @enderror" id="task_details" name="task_details" rows="4">{{ old('task_details') }}</textarea>
                                @error('task_details')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="assigned_to" class="form-label fw-semibold">Assign To</label>
                                <select id="assigned_to" name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('assigned_to') ? '' : 'selected' }}>Select a user</option>
                                    @foreach($assignableUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ ucfirst(str_replace('-', ' ', $user->role->role_name ?? '')) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Only pro-tech and tech users can be assigned tasks.</small>
                            </div>

                            <div class="mb-4">
                                <label for="task_status" class="form-label fw-semibold">Status</label>
                                <select id="task_status" name="task_status" class="form-select @error('task_status') is-invalid @enderror">
                                    <option value="pending" {{ old('task_status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ old('task_status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('task_status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('task_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('task_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    Assign Task
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

