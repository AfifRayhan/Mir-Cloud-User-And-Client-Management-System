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
                    <a href="{{ route('my-tasks.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to My Tasks
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
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="40%">Assigned To:</th>
                                <td>{{ $task->assignedTo->name }} (You)</td>
                            </tr>
                            <tr>
                                <th>Your Role:</th>
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
</x-app-layout>
