<x-app-layout>
    @push('styles')
    @vite(['resources/css/custom-resource-allocation.css', 'resources/css/custom-kam-task-management.css'])
    <style>
        .kam-task-details-row {
            display: none !important;
            background-color: var(--custom-kam-task-management-bg) !important;
        }
        .kam-task-details-row.show-row {
            display: table-row !important;
        }
        .task-details-container {
            border-top: 1px solid var(--custom-kam-task-management-border);
            border-bottom: 1px solid var(--custom-kam-task-management-border);
        }
    </style>
    @endpush

    <div class="container-fluid custom-kam-task-management-container py-4">
    <!-- Background Elements -->
    <div class="custom-kam-task-management-bg-pattern"></div>
    <div class="custom-kam-task-management-bg-circle circle-1"></div>
    <div class="custom-kam-task-management-bg-circle circle-2"></div>

    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="custom-kam-task-management-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="custom-kam-task-management-title fw-bold mb-2">KAM Task Management</h1>
                        <p class="custom-kam-task-management-subtitle text-muted">Manage unassigned tasks and resource requests</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('kam-task-management.export', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>Export Task Report
                        </a>
                        <a href="{{ route('kam-task-management.export-customers', request()->query()) }}" class="btn btn-primary">
                            <i class="fas fa-users me-2"></i>Export Customer Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fs-4"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Success!</h6>
                        <p class="mb-0">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Error Alert -->
    @if(session('error'))
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Error!</h6>
                        <p class="mb-0">{{ session('error') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="custom-kam-task-management-card mb-4">
        <div class="custom-kam-task-management-card-header">
            <h5 class="custom-kam-task-management-card-title">Filters</h5>
        </div>
        <div class="card-body mt-3">
            <form method="GET" action="{{ route('kam-task-management.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Allocation Type</label>
                    <select name="allocation_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="upgrade" {{ request('allocation_type') == 'upgrade' ? 'selected' : '' }}>Upgrade</option>
                        <option value="downgrade" {{ request('allocation_type') == 'downgrade' ? 'selected' : '' }}>Downgrade</option>
                        <option value="transfer" {{ request('allocation_type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Assignment Status</label>
                    <select name="assigned_status" class="form-select">
                        <option value="">All Tasks</option>
                        <option value="pending" {{ request('assigned_status') == 'pending' ? 'selected' : '' }}>Pending Assignment</option>
                        <option value="assigned" {{ request('assigned_status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Completion Status</label>
                    <select name="completion_status" class="form-select">
                        <option value="">All Tasks</option>
                        <option value="incomplete" {{ request('completion_status') == 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                        <option value="completed" {{ request('completion_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end justify-content-end">
                    <button type="submit" class="custom-kam-task-management-filter-btn me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('kam-task-management.index') }}" class="custom-kam-task-management-reset-btn text-decoration-none">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="custom-kam-task-management-card">
        <div class="custom-kam-task-management-card-header">
            <h5 class="custom-kam-task-management-card-title">Tasks</h5>
        </div>
        <div class="card-body p-0">
            @if($tasks->count() > 0)
                <div class="custom-kam-task-management-table-responsive">
                    <table class="custom-kam-task-management-table">
                        <thead class="custom-kam-task-management-table-head">
                            <tr>
                                <th class="custom-kam-task-management-table-header">
                                    <i class="fas fa-building me-2"></i>Customer
                                </th>
                                <th class="custom-kam-task-management-table-header">
                                    <i class="fas fa-fingerprint me-2"></i>Task ID
                                </th>
                                <th class="custom-kam-task-management-table-header">
                                    <i class="fas fa-server me-2"></i>Platform
                                </th>
                                <th class="custom-kam-task-management-table-header">
                                    <i class="fas fa-info-circle me-2"></i>Status
                                </th>
                                <th class="custom-kam-task-management-table-header">
                                    <i class="fas fa-exchange-alt me-2"></i>Type
                                </th>
                                <th class="custom-kam-task-management-table-header">
                                    <i class="fas fa-calendar-check me-2"></i>Resource Assignment
                                </th>
                                <th class="custom-kam-task-management-table-header">
                                    <i class="fas fa-hourglass-end me-2"></i>Resource Deadline
                                </th>
                                <th class="custom-kam-task-management-table-header">
                                    <i class="fas fa-check-circle me-2"></i>Completion
                                </th>
                                <th class="custom-kam-task-management-table-header">
                                    <i class="fas fa-user-check me-2"></i>Assigned To
                                </th>
                                <th class="custom-kam-task-management-table-header">
                                    <i class="fas fa-cogs me-2"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                @php
                                    $rowClass = '';
                                    if ($task->completed_at) {
                                        $rowClass = 'task-completed';
                                    } elseif ($task->assigned_to) {
                                        $rowClass = 'task-assigned';
                                    }
                                @endphp
                                <tr class="custom-kam-task-management-table-row {{ $rowClass }}">
                                    <td class="custom-kam-task-management-table-cell">
                                        <strong>{{ $task->customer->customer_name }}</strong>
                                        @if($task->has_resource_conflict)
                                            <div class="mt-1">
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i> Resource Conflict
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        @if($task->task_id)
                                            <span class="text-nowrap">{{ $task->task_id }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        @if($task->customer->platform)
                                            <span class="custom-kam-task-management-badge">{{ $task->customer->platform->platform_name }}</span>
                                        @endif
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        @php
                                            $testStatusId = \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 2;
                                            $isTest = $task->status_id == $testStatusId;
                                        @endphp
                                        <span class="custom-kam-task-management-badge">
                                            {{ $task->status->name ?? ($isTest ? 'Test' : 'Billable') }}
                                        </span>
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        @if($task->allocation_type === 'upgrade')
                                            <span class="custom-kam-task-management-badge custom-kam-task-management-badge-upgrade">
                                                <i class="fas fa-arrow-up me-1"></i> Upgrade
                                            </span>
                                        @elseif($task->allocation_type === 'downgrade')
                                            <span class="custom-kam-task-management-badge custom-kam-task-management-badge-downgrade">
                                                <i class="fas fa-arrow-down me-1"></i> Downgrade
                                            </span>
                                        @else
                                            <span class="custom-kam-task-management-badge custom-kam-task-management-badge-transfer">
                                                <i class="fas fa-exchange-alt me-1"></i> Transfer
                                            </span>
                                        @endif
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        <div class="custom-kam-task-management-date">
                                            <div class="custom-kam-task-management-date-day">
                                                {{ $task->assignment_datetime ? $task->assignment_datetime->format('d') : ($task->activation_date ? $task->activation_date->format('d') : 'N/A') }}
                                            </div>
                                            <div class="d-flex flex-column ms-2">
                                                <span class="fw-bold">{{ $task->assignment_datetime ? $task->assignment_datetime->format('F') : ($task->activation_date ? $task->activation_date->format('F') : '') }}</span>
                                                <span class="text-muted small">
                                                    {{ $task->assignment_datetime ? $task->assignment_datetime->format('Y') : ($task->activation_date ? $task->activation_date->format('Y') : '') }}
                                                    @if($task->assignment_datetime)
                                                        <br> {{ $task->assignment_datetime->format('H:i') }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        @if($task->deadline_datetime)
                                            <div class="custom-kam-task-management-date">
                                                <div class="custom-kam-task-management-date-day-deadline">
                                                    {{ $task->deadline_datetime->format('d') }}
                                                </div>
                                                <div class="d-flex flex-column ms-2">
                                                    <span class="fw-bold">{{ $task->deadline_datetime->format('F') }}</span>
                                                    <span class="text-muted small">
                                                        {{ $task->deadline_datetime->format('Y') }}
                                                        <br> {{ $task->deadline_datetime->format('H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        @if($task->completed_at)
                                            <span class="custom-kam-task-management-badge custom-kam-task-management-badge-completed">
                                                <i class="fas fa-check-double me-1"></i> Completed
                                            </span>
                                        @elseif($task->assigned_to)
                                            <span class="custom-kam-task-management-badge custom-kam-task-management-badge-assigned">
                                                <i class="fas fa-user-check me-1"></i> Assigned
                                            </span>
                                        @else
                                            <span class="custom-kam-task-management-badge custom-kam-task-management-badge-pending">
                                                <i class="fas fa-clock me-1"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        @if($task->assignedTo)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                {{ $task->assignedTo->name }}
                                            </div>
                                        @else
                                            <span class="custom-kam-task-management-badge">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        <div class="d-flex gap-2">
                                            <button class="custom-kam-task-management-action-btn custom-kam-task-management-view-btn view-task-btn" data-task-id="{{ $task->id }}">
                                                <i class="fas fa-eye me-1"></i> View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Expandable details row -->
                                <tr class="kam-task-details-row" id="details-{{ $task->id }}">
                                    <td colspan="8" class="p-0">
                                        <div class="task-details-container p-4">
                                            <!-- This div will be populated by AJAX -->
                                            <div class="task-details-content"></div>
                                            
                                            <!-- These buttons will be revealed after successful AJAX load -->
                                            @if(!$task->assigned_to && !$task->completed_at)
                                                <div class="task-actions-wrapper mt-4 pt-3 border-top d-flex gap-2 justify-content-end" id="actions-{{ $task->id }}" style="display: none;">
                                                    <button class="custom-kam-task-management-action-btn custom-kam-task-management-edit-btn" data-bs-toggle="modal" data-bs-target="#editModal{{ $task->id }}">
                                                        <i class="fas fa-edit me-1"></i> Edit Task
                                                    </button>
                                                    <button class="custom-kam-task-management-action-btn custom-kam-task-management-delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $task->id }}">
                                                        <i class="fas fa-trash-alt me-1"></i> Delete Task
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($tasks->count() > 0)
                    <div class="p-4 border-top">
                        {{ $tasks->links() }}
                    </div>
                @endif
            @else
                <div class="custom-kam-task-management-empty-card m-4 text-center py-5">
                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                    <h4>No Tasks Found</h4>
                    <p class="text-muted">No tasks match your current criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@foreach($tasks as $task)
    @if(!$task->assigned_to && !$task->completed_at)
        <!-- Edit Modal -->
        <div class="modal fade" id="editModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('kam-task-management.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Task Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Customer</label>
                                    <input type="text" class="form-control" value="{{ $task->customer->customer_name }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Activation Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="text" name="activation_date" class="form-control flatpickr-date" value="{{ $task->activation_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Resource Details</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="table-light">
                                            <th class="resource-alloc-service-cell"><i class="fas fa-tools me-2"></i>Service</th>
                                            <th><i class="fas fa-chart-line me-2"></i>Current</th>
                                            <th>
                                                @if($task->allocation_type === 'upgrade')
                                                    <i class="fas fa-arrow-up me-2"></i>Increase By
                                                @else
                                                    <i class="fas fa-arrow-down me-2"></i>Reduce By
                                                @endif
                                            </th>
                                            <th><i class="fas fa-equals me-2"></i>New Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // Create a map of existing details by service_id
                                            $detailsMap = $task->resourceDetails->keyBy('service_id');
                                        @endphp
                                        @foreach($services->where('platform_id', $task->customer->platform_id) as $service)
                                            @php
                                                $existingDetail = $detailsMap->get($service->id);
                                                $amount = 0;
                                                $detailId = null;
                                                
                                                    if ($existingDetail) {
                                                        $amount = $task->allocation_type === 'upgrade' 
                                                            ? $existingDetail->upgrade_amount 
                                                            : $existingDetail->downgrade_amount;
                                                        $detailId = $existingDetail->id;
                                                    }

                                                    // Calculate current value relative to this task
                                                    // For upgrades: current = quantity - upgrade_amount
                                                    // For downgrades: current = quantity + downgrade_amount
                                                    // But wait, $task->customer->getResourceQuantity($service->service_name) gives the LIVE current quantity
                                                    // which might include this task's changes if it's already active? 
                                                    // No, getResourceQuantity usually returns the CURRENT TOTAL from the customer_services table.
                                                    // If the task is PENDING, the changes haven't been applied to the live validation? 
                                                    // Actually, let's use the logic we used in the controller or assume getResourceQuantity is the baseline.
                                                    // In ResourceAllocation, we use $customer->getResourceQuantity().
                                                    
                                                    // However, for an existing task edit, we want the "Baseline" value BEFORE this task's effect.
                                                    // If the task is already "active" or "completed", the logic changes.
                                                    // But here we are editing a task. Usually this is for tasks that are "Applied" or "Pending"?
                                                    // Let's assume the baseline is:
                                                    // If detail exists: 
                                                    //    Upgrade: current = detail->quantity - detail->upgrade_amount
                                                    //    Downgrade: current = detail->quantity + detail->downgrade_amount
                                                    // If detail doesn't exist:
                                                    //    It's a new service being added. Current is whatever the customer has now.
                                                    
                                                    $currentValue = 0;
                                                    if ($existingDetail) {
                                                        $currentValue = $task->allocation_type === 'upgrade'
                                                            ? $existingDetail->quantity - $existingDetail->upgrade_amount
                                                            : $existingDetail->quantity + $existingDetail->downgrade_amount;
                                                    } else {
                                                        // Fallback to customer's current quantity for this specific pool
                                                        $currentValue = $task->status_id == 1
                                                            ? $task->customer->getResourceTestQuantity($service->service_name)
                                                            : $task->customer->getResourceBillableQuantity($service->service_name);
                                                    }
                                                @endphp
                                            <tr>
                                                <td class="resource-alloc-service-cell">
                                                    <span class="resource-alloc-service-name">
                                                        {{ $service->service_name }}
                                                        @if($service->unit)
                                                            <span class="resource-alloc-service-unit">({{ $service->unit }})</span>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $currentValue }} {{ $service->unit }}</span>
                                                </td>
                                                <td>
                                                    <div class="resource-alloc-stepper-group">
                                                        <button type="button" class="resource-alloc-stepper-btn" onclick="decrementValue(this)">−</button>
                                                        <input 
                                                            type="number" 
                                                            name="services[{{ $service->id }}]" 
                                                            data-detail-id="{{ $detailId }}"
                                                            data-current="{{ $currentValue }}"
                                                            data-service-id="{{ $service->id }}"
                                                            data-task-id="{{ $task->id }}"
                                                            class="form-control resource-alloc-stepper-input" 
                                                            value="{{ $amount }}" 
                                                            min="0"
                                                            oninput="{{ $task->allocation_type === 'upgrade' ? 'updateNewTotal(this)' : 'updateNewTotalDowngrade(this)' }}"
                                                        >
                                                        <button type="button" class="resource-alloc-stepper-btn" onclick="incrementValue(this)">+</button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="resource-alloc-new-total">
                                                        <span class="resource-alloc-new-total-arrow">→</span>
                                                        @php
                                                            $newTotal = $task->allocation_type === 'upgrade' 
                                                                ? $currentValue + $amount 
                                                                : max(0, $currentValue - $amount);
                                                        @endphp
                                                        <span class="resource-alloc-new-total-value" data-new-total-for="{{ $service->id }}-{{ $task->id }}">{{ $newTotal }}</span>
                                                        <span class="resource-alloc-new-total-unit">{{ $service->unit }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('kam-task-management.destroy', $task) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">Delete Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger opacity-50"></i>
                            </div>
                            <h5>Are you sure?</h5>
                            <p class="text-muted">
                                This will delete the task for <span class="fw-bold">{{ $task->customer->customer_name }}</span> and its associated resource request. This action cannot be undone.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Delete Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@push('scripts')
<script>
    // Stepper functions for resource allocation inputs
    window.incrementValue = function(button) {
        const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
        const currentVal = parseInt(input.value) || 0;
        const max = input.hasAttribute('max') ? parseInt(input.max) : Infinity;
        
        if (currentVal < max) {
            input.value = currentVal + 1;
            // Trigger input event for real-time updates
            const event = new Event('input', { bubbles: true });
            input.dispatchEvent(event);
        }
    };

    window.decrementValue = function(button) {
        const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
        const currentVal = parseInt(input.value) || 0;
        const min = parseInt(input.min) || 0;
        
        if (currentVal > min) {
            input.value = currentVal - 1;
            // Trigger input event for real-time updates
            const event = new Event('input', { bubbles: true });
            input.dispatchEvent(event);
        }
    };

    // Update new total for upgrade
    window.updateNewTotal = function(input) {
        const serviceId = input.dataset.serviceId;
        const taskId = input.dataset.taskId;
        const currentValue = parseInt(input.dataset.current) || 0;
        const increaseBy = parseInt(input.value) || 0;
        const newTotal = currentValue + increaseBy;
        
        // Scope to the specific modal/task using service-id and task-id
        const newTotalElement = document.querySelector(`[data-new-total-for="${serviceId}-${taskId}"]`);
        if (newTotalElement) {
            newTotalElement.textContent = newTotal;
        }
    };

    // Update new total for downgrade
    window.updateNewTotalDowngrade = function(input) {
        const serviceId = input.dataset.serviceId;
        const taskId = input.dataset.taskId;
        const currentValue = parseInt(input.dataset.current) || 0;
        const reduceBy = parseInt(input.value) || 0;
        const newTotal = Math.max(0, currentValue - reduceBy);
        
        const newTotalElement = document.querySelector(`[data-new-total-for="${serviceId}-${taskId}"]`);
        if (newTotalElement) {
            newTotalElement.textContent = newTotal;
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('.view-task-btn');
        const params = new URLSearchParams(window.location.search);
        const deepTaskId = params.get('dtid');
        const deepAction = params.get('da');

        // Clean up any stray open rows on load
        document.querySelectorAll('.kam-task-details-row').forEach(row => row.classList.remove('show-row'));

        if (deepTaskId) {
            if (deepAction === 'view') {
                const targetBtn = document.querySelector(`.view-task-btn[data-task-id="${deepTaskId}"]`);
                if (targetBtn) {
                    setTimeout(() => {
                        targetBtn.click();
                        targetBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 500);
                }
            } else if (deepAction === 'edit') {
                const editBtn = document.querySelector(`button[data-bs-target="#editModal${deepTaskId}"]`);
                if (editBtn) {
                    // We need to expand the row first to see the edit button if it's inside
                    const viewBtn = document.querySelector(`.view-task-btn[data-task-id="${deepTaskId}"]`);
                    if (viewBtn) {
                        viewBtn.click();
                        setTimeout(() => {
                            editBtn.click();
                        }, 500);
                    } else {
                        // If button exists but view doesn't, just try clicking edit
                        editBtn.click();
                    }
                }
            }
        }
        
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const taskId = this.dataset.taskId;
                const detailsRow = document.getElementById('details-' + taskId);
                const btnIcon = this.querySelector('i');
                
                if (!detailsRow.classList.contains('show-row')) {
                    // Close other details
                    document.querySelectorAll('.kam-task-details-row').forEach(row => row.classList.remove('show-row'));
                    document.querySelectorAll('.view-task-btn i').forEach(icon => {
                        icon.className = 'fas fa-eye me-1';
                    });

                    detailsRow.classList.add('show-row');
                    btnIcon.className = 'fas fa-eye-slash me-1';
                    
                    if (!detailsRow.dataset.loaded) {
                        loadTaskDetails(taskId);
                    }
                } else {
                    detailsRow.classList.remove('show-row');
                    btnIcon.className = 'fas fa-eye me-1';
                }
            });
        });

        function loadTaskDetails(taskId) {
            const row = document.getElementById('details-' + taskId);
            const container = row.querySelector('.task-details-content');
            const actions = document.getElementById('actions-' + taskId);
            
            // Show spinner inside container while loading
            container.innerHTML = `
                <div class="text-center py-3">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            fetch(`/kam-task-management/${taskId}/details`)
                .then(response => response.json())
                .then(data => {
                    const task = data.task;
                    const resourceDetails = data.resourceDetails;
                    
                    let html = '';
                    if (resourceDetails && resourceDetails.length > 0) {
                        const isUpgrade = task.allocation_type === 'upgrade';
                        const isTransfer = task.allocation_type === 'transfer';
                        
                        let label = isUpgrade ? 'Increase By' : 'Reduce By';
                        if (isTransfer) label = 'Transfer Amount';
                        
                        let currentHeader = 'Current';
                        let newHeader = 'New Total';

                        if (isTransfer) {
                            if (task.status && task.status.name === 'Billable to Test') {
                                currentHeader = 'Billable';
                                newHeader = 'Test';
                            } else if (task.status && task.status.name === 'Test to Billable') {
                                currentHeader = 'Test';
                                newHeader = 'Billable';
                            }
                        }

                        const badgeClass = isTransfer ? 'custom-kam-task-management-value-badge-transfer' : (isUpgrade ? 'badge bg-success' : 'badge bg-warning text-dark');
                        const arrowIcon = isTransfer ? '<i class="fas fa-exchange-alt me-2"></i>' : (isUpgrade ? '<i class="fas fa-arrow-up me-2"></i>' : '<i class="fas fa-arrow-down me-2"></i>');
                        
                        html += `
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="resource-alloc-service-cell"><i class="fas fa-tools me-2"></i>Service</th>
                                            <th><i class="fas fa-chart-line me-2"></i>${currentHeader}</th>
                                            <th>${arrowIcon}${label}</th>
                                            <th><i class="fas fa-equals me-2"></i>${newHeader}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        
                        resourceDetails.forEach(detail => {
                            let amount, prev, next;
                            if (isTransfer) {
                                amount = detail.transfer_amount || 0;
                                prev = detail.current_source_quantity || 0;
                                next = detail.new_target_quantity || 0;
                            } else {
                                amount = isUpgrade ? (detail.upgrade_amount || 0) : (detail.downgrade_amount || 0);
                                next = detail.quantity || 0;
                                prev = isUpgrade ? (next - amount) : (next + amount);
                            }
                            
                            const prevDisplay = prev < 0 
                                ? `<span class="text-danger fw-bold">${prev}</span>` 
                                : `<span class="badge bg-secondary">${prev}</span>`;
                                
                            const newDisplay = next < 0 
                                ? `<span class="text-danger fw-bold">${next}</span>` 
                                : `<span class="resource-alloc-new-total-value">${next}</span>`;

                            // Display service name with unit inline
                            const serviceName = detail.service.service_name;
                            const serviceUnit = detail.service.unit ? ` <span class="resource-alloc-service-unit">(${detail.service.unit})</span>` : '';
                            const serviceDisplay = `<span class="resource-alloc-service-name">${serviceName}${serviceUnit}</span>`;

                            html += `
                                <tr>
                                    <td class="resource-alloc-service-cell">${serviceDisplay}</td>
                                    <td>${prevDisplay} ${detail.service.unit || ''}</td>
                                    <td>
                                        <span class="${badgeClass}">
                                            ${isUpgrade ? '+' : (isTransfer ? '' : '-')}${amount}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="resource-alloc-new-total">
                                            <span class="resource-alloc-new-total-arrow">→</span>
                                            ${newDisplay}
                                            <span class="resource-alloc-new-total-unit">${detail.service.unit || ''}</span>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                    } else {
                        html = '<p class="text-center text-muted mb-0">No resource details found.</p>';
                    }
                    
                    container.innerHTML = html;
                    row.dataset.loaded = 'true';
                    
                    // Show actions if they exist
                    if (actions) {
                        actions.style.display = 'flex';
                    }
                })
                .catch(err => {
                    container.innerHTML = '<div class="alert alert-danger mb-0">Error loading details.</div>';
                });
        }
    });
</script>
@endpush
</x-app-layout>
