<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Tasks</h1>
                        <p class="text-muted mb-0">View and manage your assigned tasks</p>
                    </div>
                    @if(Auth::user()->isAdmin() || Auth::user()->isProTech())
                        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg>
                            Assign New Task
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                @if($tasks->isEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5 text-center">
                            <p class="text-muted mb-0">No tasks found.</p>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Task Name</th>
                                            <th>Details</th>
                                            @if(Auth::user()->isAdmin() || Auth::user()->isProTech())
                                                <th>Assigned To</th>
                                                <th>Created By</th>
                                            @endif
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tasks as $task)
                                            <tr>
                                                <td class="fw-semibold">{{ $task->task_name }}</td>
                                                <td>
                                                    @if($task->task_details)
                                                        <span class="text-muted">{{ \Illuminate\Support\Str::limit($task->task_details, 50) }}</span>
                                                    @else
                                                        <span class="text-muted fst-italic">No details</span>
                                                    @endif
                                                </td>
                                                @if(Auth::user()->isAdmin() || Auth::user()->isProTech())
                                                    <td>{{ $task->assignedUser->name }}</td>
                                                    <td>{{ $task->creator->name }}</td>
                                                @endif
                                                <td>
                                                    @if($task->assigned_to === Auth::id())
                                                        <form method="POST" action="{{ route('tasks.update', $task->task_id) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <select name="task_status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                                <option value="pending" {{ $task->task_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="in_progress" {{ $task->task_status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                                <option value="completed" {{ $task->task_status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                                <option value="cancelled" {{ $task->task_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                            </select>
                                                        </form>
                                                    @else
                                                        @php
                                                            $statusColors = [
                                                                'pending' => 'warning',
                                                                'in_progress' => 'info',
                                                                'completed' => 'success',
                                                                'cancelled' => 'danger',
                                                            ];
                                                            $color = $statusColors[$task->task_status] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $task->task_status)) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $task->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if(Auth::user()->isProTech())
                                                        <form method="POST" action="{{ route('tasks.destroy', $task->task_id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

