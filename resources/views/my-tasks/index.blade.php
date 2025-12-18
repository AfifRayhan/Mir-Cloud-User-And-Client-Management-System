<x-app-layout>
    <div class="container-fluid custom-my-task-container py-4">
        <!-- Background Elements -->
        <div class="custom-my-task-bg-pattern"></div>
        <div class="custom-my-task-bg-circle circle-1"></div>
        <div class="custom-my-task-bg-circle circle-2"></div>

        <!-- Header -->
        <div class="custom-my-task-header">
            <div>
                <h1 class="custom-my-task-title">My Tasks</h1>
                <p class="custom-my-task-subtitle">Tasks assigned to you</p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="custom-my-task-alert" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="custom-my-task-alert" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Tasks Table -->
        <div class="custom-my-task-card">
            <div class="custom-my-task-card-header">
                <h5 class="custom-my-task-card-title">Your Tasks</h5>
            </div>
            <div class="card-body">
                @if($tasks->count() > 0)
                    <div class="custom-my-task-table-responsive">
                        <table class="custom-my-task-table" id="tasksTable">
                            <thead class="custom-my-task-table-head">
                                <tr>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-building me-2"></i>Customer
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-server me-2"></i>Platform
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-exchange-alt me-2"></i>Type
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-calendar-check me-2"></i>Activation Date
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-user-clock me-2"></i>Assigned At
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-check-circle me-2"></i>Completed At
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-info-circle me-2"></i>Status
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-user-tie me-2"></i>Assigned By
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-cogs me-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="custom-my-task-table-body">
                                @foreach($tasks as $task)
                                    <tr class="custom-my-task-table-row" data-task-id="{{ $task->id }}">
                                        <td class="custom-my-task-table-cell">
                                            <strong>{{ $task->customer->customer_name }}</strong>
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            @if($task->customer->platform)
                                                <span class="custom-my-task-badge">{{ $task->customer->platform->platform_name }}</span>
                                            @else
                                                <span class="text-muted">Any</span>
                                            @endif
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            @if($task->allocation_type === 'upgrade')
                                                <span class="custom-my-task-badge custom-my-task-badge-upgrade">
                                                    <i class="fas fa-arrow-up me-1"></i> Upgrade
                                                </span>
                                            @else
                                                <span class="custom-my-task-badge custom-my-task-badge-downgrade">
                                                    <i class="fas fa-arrow-down me-1"></i> Downgrade
                                                </span>
                                            @endif
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            <div class="custom-my-task-date">
                                                <div class="custom-my-task-date-day">
                                                    {{ $task->activation_date->format('d') }}
                                                </div>
                                                <div class="custom-my-task-date-details">
                                                    <div class="custom-my-task-date-month">
                                                        {{ $task->activation_date->format('M') }}
                                                    </div>
                                                    <div class="custom-my-task-date-year">
                                                        {{ $task->activation_date->format('Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            @if($task->assigned_at)
                                                {{ \Carbon\Carbon::parse($task->assigned_at)->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            @if($task->completed_at)
                                                {{ \Carbon\Carbon::parse($task->completed_at)->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-muted">Pending</span>
                                            @endif
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            @if($task->status)
                                                <span class="custom-my-task-badge">{{ $task->status->name }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            @if($task->assignedBy)
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle text-primary me-2"></i>
                                                    {{ $task->assignedBy->name }}
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            <button class="custom-my-task-action-btn custom-my-task-edit-btn view-task-btn me-1" data-task-id="{{ $task->id }}">
                                                <i class="fas fa-eye me-1"></i> <span class="btn-text">View</span>
                                            </button>
                                            @if(!$task->completed_at)
                                                <a href="{{ URL::signedRoute('my-tasks.complete', ['task' => $task->id]) }}"
                                                    class="custom-my-task-action-btn custom-my-task-edit-btn"
                                                    onclick="if(confirm('Are you sure you want to mark this task as complete?')) { this.classList.add('disabled'); this.innerHTML = '<i class=\'fas fa-circle-notch fa-spin me-1\'></i> Processing...'; return true; } else { return false; }">
                                                    <i class="fas fa-check me-1"></i> <span class="btn-text">Complete</span>
                                                </a>
                                            @else
                                                <span class="custom-my-task-action-btn custom-my-task-badge-upgrade">
                                                    <i class="fas fa-check-circle me-1"></i> <span class="btn-text">Completed</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <!-- Expandable details row (hidden by default) -->
                                    <tr class="task-details-row" id="details-{{ $task->id }}" style="display: none;">
                                        <td colspan="9" class="p-0">
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
                    <div class="custom-my-task-card-footer">
                        <div class="custom-my-task-pagination-info">
                            {{ $tasks->links() }}
                        </div>
                    </div>
                @else
                    <div class="custom-my-task-empty-card">
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x custom-my-task-empty-icon mb-3"></i>
                            <h4 class="custom-my-task-empty-title">No Tasks Assigned</h4>
                            <p class="custom-my-task-empty-text">You don't have any tasks assigned yet.</p>
                        </div>
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
                
                fetch(`/my-tasks/${taskId}/details`)
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
                                                <th class="text-primary">New Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                            
                            resourceDetails.forEach(detail => {
                                const amount = isUpgrade ? detail.upgrade_amount : detail.downgrade_amount;
                                const badgeClass = isUpgrade ? 'custom-my-task-badge custom-my-task-badge-upgrade' : 'custom-my-task-badge custom-my-task-badge-downgrade';
                                
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
                                        <td>
                                            <span class="badge bg-primary">${detail.quantity} ${detail.service.unit || ''}</span>
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
