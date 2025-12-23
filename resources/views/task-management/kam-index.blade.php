<x-app-layout>
    @push('styles')
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
                <div>
                    <h1 class="custom-kam-task-management-title fw-bold mb-2">KAM Task Management</h1>
                    <p class="custom-kam-task-management-subtitle text-muted">Manage unassigned tasks and resource requests</p>
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
                                <th class="custom-kam-task-management-table-header">Customer</th>
                                <th class="custom-kam-task-management-table-header">Platform</th>
                                <th class="custom-kam-task-management-table-header">Type</th>
                                <th class="custom-kam-task-management-table-header">Activation Date</th>
                                <th class="custom-kam-task-management-table-header">Status</th>
                                <th class="custom-kam-task-management-table-header">Assigned To</th>
                                <th class="custom-kam-task-management-table-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr class="custom-kam-task-management-table-row">
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
                                        @if($task->customer->platform)
                                            <span class="custom-kam-task-management-badge">{{ $task->customer->platform->platform_name }}</span>
                                        @else
                                            <span class="text-muted">Any</span>
                                        @endif
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        @if($task->allocation_type === 'upgrade')
                                            <span class="custom-kam-task-management-badge custom-kam-task-management-badge-upgrade">
                                                <i class="fas fa-arrow-up me-1"></i> Upgrade
                                            </span>
                                        @else
                                            <span class="custom-kam-task-management-badge custom-kam-task-management-badge-downgrade">
                                                <i class="fas fa-arrow-down me-1"></i> Downgrade
                                            </span>
                                        @endif
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        <div class="custom-kam-task-management-date">
                                            <div class="custom-kam-task-management-date-day">
                                                {{ $task->activation_date->format('d') }}
                                            </div>
                                            <div class="d-flex flex-column ms-2">
                                                <span class="fw-bold">{{ $task->activation_date->format('M') }}</span>
                                                <span class="text-muted small">{{ $task->activation_date->format('Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        @if($task->completed_at)
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($task->assigned_to)
                                            <span class="badge bg-info">Assigned</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="custom-kam-task-management-table-cell">
                                        {{ $task->assignedTo->name ?? 'Unassigned' }}
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
                                    <td colspan="7" class="p-0">
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
                                    <input type="date" name="activation_date" class="form-control" value="{{ $task->activation_date->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <h6 class="mb-3">Resource Details</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="table-light">
                                            <th>Service</th>
                                            <th>{{ $task->allocation_type === 'upgrade' ? 'Upgrade Amount' : 'Downgrade Amount' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($task->resourceDetails as $detail)
                                            <tr>
                                                <td>{{ $detail->service->service_name }}</td>
                                                <td>
                                                    <input type="number" name="services[{{ $detail->id }}]" class="form-control" value="{{ $task->allocation_type === 'upgrade' ? $detail->upgrade_amount : $detail->downgrade_amount }}" min="0" required>
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
                    }, 800);
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
                        const label = isUpgrade ? 'Increase By' : 'Reduce By';
                        const badgeClass = isUpgrade ? 'badge bg-success' : 'badge bg-warning text-dark';
                        
                        html += `
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Service</th>
                                        <th>Current</th>
                                        <th>${label}</th>
                                        <th>New</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        
                        resourceDetails.forEach(detail => {
                            const amount = isUpgrade ? detail.upgrade_amount : detail.downgrade_amount;
                            const prev = isUpgrade ? (detail.quantity - amount) : (detail.quantity + amount);
                            
                            const prevDisplay = prev < 0 
                                ? `<span class="text-danger fw-bold">${prev}</span>` 
                                : prev;
                                
                            const newDisplay = detail.quantity < 0 
                                ? `<span class="text-danger fw-bold">${detail.quantity}</span>` 
                                : `<span class="badge bg-primary">${detail.quantity}</span>`;

                            html += `
                                <tr>
                                    <td>${detail.service.service_name}</td>
                                    <td>${prevDisplay}</td>
                                    <td><span class="${badgeClass}">${amount}</span></td>
                                    <td>${newDisplay}</td>
                                </tr>
                            `;
                        });
                        
                        html += `</tbody></table>`;
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
