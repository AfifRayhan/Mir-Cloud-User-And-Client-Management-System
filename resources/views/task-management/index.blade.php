<x-app-layout>
    <div class="container-fluid custom-task-management-container py-4">
        <!-- Background Elements -->
        <div class="custom-task-management-bg-pattern"></div>
        <div class="custom-task-management-bg-circle circle-1"></div>
        <div class="custom-task-management-bg-circle circle-2"></div>

        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="custom-task-management-header">
                    <div>
                        <h1 class="custom-task-management-title fw-bold mb-2">Task Management</h1>
                        <p class="custom-task-management-subtitle text-muted">View and assign tasks to team members</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="custom-user-management-alert alert alert-success alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="me-3" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                        <div class="flex-grow-1">
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
                <div class="custom-user-management-alert alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="me-3" viewBox="0 0 16 16">
                            <path d="M8 8a1 1 0 0 1 1 1v.01a1 1 0 1 1-2 0V9a1 1 0 0 1 1-1zm.25-2.25a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0v-1.5z" />
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 0 1.5 0v-.25a.75.75 0 0 0-.75-.75h-.25a.75.75 0 0 0-.75.75V9a2 2 0 1 1-4 0v-.25a.75.75 0 0 0-.75-.75h-.25a.75.75 0 0 0-.75.75v.25a.75.75 0 0 0 1.5 0v-4.5A.75.75 0 0 1 8 4z" />
                        </svg>
                        <div class="flex-grow-1">
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
        <div class="custom-task-management-card mb-4">
            <div class="custom-task-management-card-header">
                <h5 class="custom-task-management-card-title">Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('task-management.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Allocation Type</label>
                        <select name="allocation_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="upgrade" {{ request('allocation_type') == 'upgrade' ? 'selected' : '' }}>Upgrade</option>
                            <option value="downgrade" {{ request('allocation_type') == 'downgrade' ? 'selected' : '' }}>Downgrade</option>
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
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
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
                    <div class="col-md-12 d-flex align-items-end justify-content-end">
                        <button type="submit" class="custom-task-management-add-btn me-2">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <button 
                            type="button" 
                            class="custom-task-management-reset-btn" 
                            data-url="{{ route('task-management.index') }}"
                            onclick="window.location.href=this.getAttribute('data-url')">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tasks Table -->
        <div class="custom-task-management-card">
            <div class="custom-task-management-card-header">
                <h5 class="custom-task-management-card-title">Tasks</h5>
            </div>
            <div class="card-body">
                @if($tasks->count() > 0)
                    <div class="custom-task-management-table-responsive">
                        <table class="custom-task-management-table">
                            <thead class="custom-task-management-table-head">
                                <tr>
                                    <th class="custom-task-management-table-header">
                                        <i class="fas fa-building me-2"></i>Customer
                                    </th>
                                    <th class="custom-task-management-table-header">
                                        <i class="fas fa-user-edit me-2"></i>Inserted By
                                    </th>
                                    <th class="custom-task-management-table-header">
                                        <i class="fas fa-server me-2"></i>Platform
                                    </th>
                                    <th class="custom-task-management-table-header">
                                        <i class="fas fa-exchange-alt me-2"></i>Type
                                    </th>
                                    <th class="custom-task-management-table-header">
                                        <i class="fas fa-calendar-check me-2"></i>Resource Activation
                                    </th>
                                    <!-- <th class="custom-task-management-table-header">
                                        <i class="fas fa-clock me-2"></i>Created At
                                    </th> -->
                                    <th class="custom-task-management-table-header">
                                        <i class="fas fa-info-circle me-2"></i>Status
                                    </th>
                                    <th class="custom-task-management-table-header">
                                        <i class="fas fa-check-circle me-2"></i>Completion Status
                                    </th>
                                    <th class="custom-task-management-table-header">
                                        <i class="fas fa-user-check me-2"></i>Assigned To
                                    </th>
                                    <th class="custom-task-management-table-header">
                                        <i class="fas fa-user-tie me-2"></i>Assigned By
                                    </th>
                                    <th class="custom-task-management-table-header">
                                        <i class="fas fa-cogs me-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="custom-task-management-table-body">
                                @foreach($tasks as $task)
                                    @php
                                        $rowClass = '';
                                        if ($task->completed_at) {
                                            $rowClass = 'task-completed';
                                        } elseif ($task->assigned_to) {
                                            $rowClass = 'task-assigned';
                                        }
                                    @endphp
                                    <tr class="custom-task-management-table-row {{ $rowClass }}">
                                        <td class="custom-task-management-table-cell">
                                            <strong>{{ $task->customer->customer_name }}</strong>
                                        </td>
                                        <td class="custom-task-management-table-cell">
                                            @if($task->insertedBy)
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle text-secondary me-2"></i>
                                                    {{ $task->insertedBy->name }}
                                                </div>
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td class="custom-task-management-table-cell">
                                            @if($task->customer->platform)
                                                <span class="custom-task-management-badge">{{ $task->customer->platform->platform_name }}</span>
                                            @endif
                                        </td>
                                        <td class="custom-task-management-table-cell">
                                            @if($task->allocation_type === 'upgrade')
                                                <span class="custom-task-management-badge custom-task-management-badge-upgrade">
                                                    <i class="fas fa-arrow-up me-1"></i> Upgrade
                                                </span>
                                            @else
                                                <span class="custom-task-management-badge custom-task-management-badge-downgrade">
                                                    <i class="fas fa-arrow-down me-1"></i> Downgrade
                                                </span>
                                            @endif
                                        </td>
                                        <td class="custom-task-management-table-cell">
                                            <div class="custom-task-management-date">
                                                <div class="custom-task-management-date-day">
                                                    {{ $task->activation_date->format('d') }}
                                                </div>
                                                <div class="custom-task-management-date-details">
                                                    <div class="custom-task-management-date-month fw-bold">
                                                        {{ $task->activation_date->format('F') }}
                                                    </div>
                                                    <div class="custom-task-management-date-year">
                                                        {{ $task->activation_date->format('Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <!-- <td class="custom-task-management-table-cell">{{ $task->created_at->format('M d, Y H:i:s') }}</td> -->
                                        <td class="custom-task-management-table-cell">
                                            @if($task->status)
                                                <span class="custom-task-management-badge">{{ $task->status->name }}</span>
                                            @endif
                                        </td>
                                        <td class="custom-task-management-table-cell">
                                            @if($task->completed_at)
                                                <span class="custom-task-management-badge custom-task-management-badge-completed">
                                                    <i class="fas fa-check-double me-1"></i> Completed
                                                </span>
                                            @elseif($task->assigned_to)
                                                <span class="custom-task-management-badge custom-task-management-badge-assigned">
                                                    <i class="fas fa-user-check me-1"></i> Assigned
                                                </span>
                                            @else
                                                <span class="custom-task-management-badge custom-task-management-badge-pending">
                                                    <i class="fas fa-clock me-1"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="custom-task-management-table-cell">
                                            @if($task->assignedTo)
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle text-primary me-2"></i>
                                                    {{ $task->assignedTo->name }}
                                                </div>
                                            @else
                                                <span class="custom-task-management-badge">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="custom-task-management-table-cell">
                                            @if($task->assignedBy)
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle text-primary me-2"></i>
                                                    {{ $task->assignedBy->name }}
                                                </div>
                                            @else
                                                <span class="custom-task-management-badge">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="custom-task-management-table-cell">
                                            <div class="d-flex flex-column gap-1">
                                                <button class="custom-task-management-action-btn custom-task-management-edit-btn view-task-btn" data-task-id="{{ $task->id }}">
                                                    <i class="fas fa-eye me-1"></i> <span class="btn-text">View</span>
                                                </button>
                                                
                                                @if($task->completed_at)
                                                    <span class="custom-task-management-action-btn btn-complete-status">
                                                        <i class="fas fa-check-double me-1"></i> <span class="btn-text">Complete</span>
                                                    </span>
                                                @elseif($task->assigned_to)
                                                    <span class="custom-task-management-action-btn btn-assigned-status">
                                                        <i class="fas fa-user-check me-1"></i> <span class="btn-text">Assigned</span>
                                                    </span>
                                                @else
                                                    @if($task->isEligibleForAction())
                                                        <button type="button" class="custom-task-management-action-btn custom-task-management-edit-btn" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#assignModal{{ $task->id }}">
                                                            <i class="fas fa-user-plus me-1"></i> <span class="btn-text">Assign</span>
                                                        </button>
                                                    @else
                                                        <button type="button" class="custom-task-management-action-btn custom-task-management-edit-btn btn-warning" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#consistencyWarningModal{{ $task->id }}">
                                                            <i class="fas fa-exclamation-triangle me-1"></i> <span class="btn-text">Assign</span>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Expandable details row (hidden by default) -->
                                    <tr class="task-details-row" id="details-{{ $task->id }}" style="display: none;">
                                        <td colspan="10" class="p-0">
                                            <div class="task-details-container p-4 bg-light">
                                                <div class="text-center py-3">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="custom-task-management-card-footer">
                        <div class="custom-task-management-pagination-info">
                            {{ $tasks->links() }}
                        </div>
                    </div>
                @else
                    <div class="custom-task-management-empty-card">
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x custom-task-management-empty-icon mb-3"></i>
                            <h4 class="custom-task-management-empty-title">No Tasks Found</h4>
                            <p class="custom-task-management-empty-text">There are currently no tasks to display.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Assign Modals -->
    @foreach($tasks as $task)
    <div class="modal fade" id="assignModal{{ $task->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('task-management.assign', $task) }}" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'fas fa-circle-notch fa-spin me-1\'></i> Processing...';">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">
                            <strong>Customer:</strong> {{ $task->customer->customer_name }}<br>
                            <strong>Type:</strong> {{ ucfirst($task->allocation_type) }}
                        </p>
                        <div class="mb-3">
                            <label class="form-label">Assign To</label>
                            <select name="assigned_to" class="form-select" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ $task->assigned_to == $user->id ? 'selected' : '' }}>
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

    <!-- Consistency Warning Modals -->
    <div class="modal fade" id="consistencyWarningModal{{ $task->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Consistency Check
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-clock fa-3x text-warning opacity-50"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Order of Operations</h5>
                    <p class="text-muted mb-0">
                        Assign the first task to ensure consistency.
                    </p>
                </div>
                <div class="modal-footer border-0 bg-light d-flex gap-2">
                    <button type="button" class="btn btn-secondary px-4 flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning px-4 flex-grow-1" 
                            data-bs-dismiss="modal" 
                            data-bs-toggle="modal" 
                            data-bs-target="#assignModal{{ $task->id }}">
                        Assign Anyway
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @push('styles')
    <style>
        /* Row details loading state */
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewButtons = document.querySelectorAll('.view-task-btn');
            const params = new URLSearchParams(window.location.search);
            const deepTaskId = params.get('dtid');
            const deepAction = params.get('da');

            if (deepTaskId) {
                console.log('Deep link detected:', { dtid: deepTaskId, da: deepAction });
                if (deepAction === 'view') {
                    const targetBtn = document.querySelector(`.view-task-btn[data-task-id="${deepTaskId}"]`);
                    console.log('Target view button:', targetBtn);
                    if (targetBtn) {
                        setTimeout(() => {
                            console.log('Clicking target button');
                            targetBtn.click();
                            targetBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 500);
                    }
                } else if (deepAction === 'assign') {
                    const assignBtn = document.querySelector(`button[data-bs-target="#assignModal${deepTaskId}"]`);
                    console.log('Target assign button:', assignBtn);
                    if (assignBtn) {
                        setTimeout(() => {
                            console.log('Clicking assign button');
                            assignBtn.click();
                        }, 500);
                    }
                }
            }
            
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.dataset.taskId;
                    const detailsRow = document.getElementById('details-' + taskId);
                    const btnText = this.querySelector('.btn-text');
                    const btnIcon = this.querySelector('i');
                    
                    // Toggle visibility
                    if (detailsRow.style.display === 'none') {
                        // Close all other open details
                        document.querySelectorAll('.task-details-row').forEach(row => {
                            row.style.display = 'none';
                        });
                        
                        // Reset all other buttons
                        document.querySelectorAll('.view-task-btn').forEach(btn => {
                            btn.querySelector('.btn-text').textContent = 'View';
                            btn.querySelector('i').className = 'fas fa-eye me-1';
                            btn.classList.remove('btn-primary');
                            btn.classList.add('btn-outline-primary');
                        });
                        
                        // Show this row
                        detailsRow.style.display = 'table-row';
                        btnText.textContent = 'Hide';
                        btnIcon.className = 'fas fa-eye-slash me-1';
                        this.classList.remove('btn-outline-primary');
                        this.classList.add('btn-primary');
                        
                        // Load details if not already loaded
                        if (!detailsRow.dataset.loaded) {
                            loadTaskDetails(taskId);
                        }
                    } else {
                        // Hide this row
                        detailsRow.style.display = 'none';
                        btnText.textContent = 'View';
                        btnIcon.className = 'fas fa-eye me-1';
                        this.classList.remove('btn-primary');
                        this.classList.add('btn-outline-primary');
                    }
                });
            });
            
            function loadTaskDetails(taskId) {
                const detailsRow = document.getElementById('details-' + taskId);
                const container = detailsRow.querySelector('.task-details-container');
                
                fetch(`/task-management/${taskId}/details`)
                    .then(response => response.json())
                    .then(data => {
                        const task = data.task;
                        const resourceDetails = data.resourceDetails;
                        
                        let html = '';
                        
                        if (resourceDetails && resourceDetails.length > 0) {
                            const isUpgrade = task.allocation_type === 'upgrade';
                            const label = isUpgrade ? 'Increase By' : 'Reduce By';
                            const badgeClass = isUpgrade ? 'badge bg-success' : 'badge bg-warning text-dark';
                            const arrowIcon = isUpgrade ? '<i class="fas fa-arrow-up me-2"></i>' : '<i class="fas fa-arrow-down me-2"></i>';
                            
                            html += `
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="resource-alloc-service-cell"><i class="fas fa-tools me-2"></i>Service</th>
                                                <th><i class="fas fa-chart-line me-2"></i>Current</th>
                                                <th>${arrowIcon}${label}</th>
                                                <th><i class="fas fa-equals me-2"></i>New Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                            
                            resourceDetails.forEach(detail => {
                                const amount = isUpgrade ? detail.upgrade_amount : detail.downgrade_amount;
                                const prev = isUpgrade ? (detail.quantity - amount) : (detail.quantity + amount);
                                
                                const prevDisplay = prev < 0 
                                    ? `<span class="text-danger fw-bold">${prev}</span>` 
                                    : `<span class="badge bg-secondary">${prev}</span>`;
                                    
                                const newDisplay = detail.quantity < 0 
                                    ? `<span class="text-danger fw-bold">${detail.quantity}</span>` 
                                    : `<span class="resource-alloc-new-total-value">${detail.quantity}</span>`;

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
                                                ${isUpgrade ? '+' : '-'}${amount}
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
                            html = `
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No resource details available.</p>
                                </div>
                            `;
                        }
                        
                        container.innerHTML = html;
                        detailsRow.dataset.loaded = 'true';
                    })
                    .catch(error => {
                        console.error('Error loading task details:', error);
                        container.innerHTML = `
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error loading task details. Please try again.
                            </div>
                        `;
                    });
            }
        });
    </script>
    @endpush
</x-app-layout>
