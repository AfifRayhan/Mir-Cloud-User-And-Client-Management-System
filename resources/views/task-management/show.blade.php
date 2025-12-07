<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2">Task Details</h1>
                        <p class="text-muted mb-0">{{ $task->customer->customer_name }}</p>
                    </div>
                    <a href="{{ route('task-management.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Tasks
                    </a>
                </div>
            </div>
        </div>

        <!-- Task Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Task Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="40%">Customer:</th>
                                <td>{{ $task->customer->customer_name }}</td>
                            </tr>
                            <tr>
                                <th>Allocation Type:</th>
                                <td>
                                    @if($task->allocation_type === 'upgrade')
                                        <span class="badge bg-success">
                                            <i class="fas fa-arrow-up me-1"></i> Upgrade
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-arrow-down me-1"></i> Downgrade
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Activation Date:</th>
                                <td>{{ $task->activation_date->format('F d, Y') }}</td>
                            </tr>
                            @if($task->status)
                                <tr>
                                    <th>Status:</th>
                                    <td><span class="badge bg-info">{{ $task->status->name }}</span></td>
                                </tr>
                            @endif
                            <tr>
                                <th>Created:</th>
                                <td>{{ $task->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Assignment Information</h5>
                    </div>
                    <div class="card-body">
                        @if($task->assignedTo)
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="40%">Assigned To:</th>
                                    <td>{{ $task->assignedTo->name }}</td>
                                </tr>
                                <tr>
                                    <th>Role:</th>
                                    <td><span class="badge bg-primary">{{ $task->assignedTo->role->role_name }}</span></td>
                                </tr>
                                <tr>
                                    <th>Assigned By:</th>
                                    <td>{{ $task->assignedBy ? $task->assignedBy->name : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Assigned At:</th>
                                    <td>{{ $task->assigned_at ? $task->assigned_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                </tr>
                            </table>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-3">This task has not been assigned yet.</p>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assignModal">
                                    <i class="fas fa-user-plus me-1"></i> Assign Task
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Resource Details -->
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-server me-2"></i>
                    Resource {{ $task->allocation_type === 'upgrade' ? 'Increases' : 'Reductions' }}
                </h5>
            </div>
            <div class="card-body">
                @if($task->resourceDetails->count() > 0)
                    <div class="row g-3">
                        @foreach($task->resourceDetails as $detail)
                            <div class="col-md-4">
                                <div class="card border-{{ $task->allocation_type === 'upgrade' ? 'success' : 'warning' }} h-100">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-2">{{ $detail->service->service_name }}</h6>
                                        <div class="display-6 fw-bold text-{{ $task->allocation_type === 'upgrade' ? 'success' : 'warning' }}">
                                            @if($task->allocation_type === 'upgrade')
                                                <i class="fas fa-plus-circle me-2"></i>{{ $detail->upgrade_amount }}
                                            @else
                                                <i class="fas fa-minus-circle me-2"></i>{{ $detail->downgrade_amount }}
                                            @endif
                                        </div>
                                        <p class="text-muted mb-0 mt-2">
                                            {{ $task->allocation_type === 'upgrade' ? 'Increase By' : 'Reduce By' }}
                                        </p>
                                        <small class="text-muted">
                                            New Total: {{ $detail->quantity }} {{ $detail->service->unit }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No resource details available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Assign Modal (if not assigned) -->
    @if(!$task->assignedTo)
        <div class="modal fade" id="assignModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('task-management.assign', $task) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Assign To</label>
                                <select name="assigned_to" class="form-select" required>
                                    <option value="">Select User</option>
                                    @foreach(\App\Models\User::whereHas('role', function($q) { $q->whereIn('role_name', ['tech', 'admin']); })->orderBy('name')->get() as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->role->role_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-1"></i> Assign Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
