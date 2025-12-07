<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2">Task Management</h1>
                        <p class="text-muted mb-0">View and assign tasks to team members</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
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
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('task-management.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tasks Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                @if($tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Platform</th>
                                    <th>Type</th>
                                    <th>Activation Date</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                    <tr data-task-id="{{ $task->id }}">
                                        <td>
                                            <strong>{{ $task->customer->customer_name }}</strong>
                                        </td>
                                        <td>
                                            @if($task->customer->platform)
                                                <span class="badge bg-info">{{ $task->customer->platform->platform_name }}</span>
                                            @else
                                                <span class="text-muted">Any</span>
                                            @endif
                                        </td>
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
                                        <td>{{ $task->activation_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($task->status)
                                                <span class="badge bg-info">{{ $task->status->name }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->assignedTo)
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle text-primary me-2"></i>
                                                    {{ $task->assignedTo->name }}
                                                </div>
                                            @else
                                                <span class="badge bg-secondary">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary view-task-btn me-1" data-task-id="{{ $task->id }}">
                                                <i class="fas fa-eye me-1"></i> <span class="btn-text">View</span>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#assignModal{{ $task->id }}">
                                                <i class="fas fa-user-plus me-1"></i> Assign
                                            </button>

                                            <!-- Assign Modal -->
                                            <div class="modal fade" id="assignModal{{ $task->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Assign Task</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="{{ route('task-management.assign', $task) }}">
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
                                        </td>
                                    </tr>
                                    <!-- Expandable details row (hidden by default) -->
                                    <tr class="task-details-row" id="details-{{ $task->id }}" style="display: none;">
                                        <td colspan="7" class="p-0">
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
                    <div class="mt-3">
                        {{ $tasks->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No tasks found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewButtons = document.querySelectorAll('.view-task-btn');
            
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
                            const headerLabel = isUpgrade ? 'Increase By' : 'Reduce By';
                            const headerClass = isUpgrade ? 'text-success' : 'text-warning';
                            
                            html += `
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40%;">Service</th>
                                                <th>Current Value</th>
                                                <th class="${headerClass}">${headerLabel}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                            
                            resourceDetails.forEach(detail => {
                                const amount = isUpgrade ? detail.upgrade_amount : detail.downgrade_amount;
                                const badgeClass = isUpgrade ? 'bg-success' : 'bg-warning';
                                
                                html += `
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">${detail.service.service_name}</span>
                                            ${detail.service.unit ? `<span class="text-muted small"> (${detail.service.unit})</span>` : ''}
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">${detail.quantity - amount} ${detail.service.unit || ''}</span>
                                        </td>
                                        <td>
                                            <span class="badge ${badgeClass}">${amount} ${detail.service.unit || ''}</span>
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
