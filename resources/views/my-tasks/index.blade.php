<x-app-layout>
    <div class="container-fluid custom-my-task-container py-4">
        <!-- Background Elements -->
        <div class="custom-my-task-bg-pattern"></div>
        <div class="custom-my-task-bg-circle circle-1"></div>
        <div class="custom-my-task-bg-circle circle-2"></div>

        <!-- Header -->
        <div class="custom-my-task-header">
            <div>
                <h1 class="custom-my-task-title fw-bold">My Tasks</h1>
                <p class="custom-my-task-subtitle">Tasks assigned to you</p>
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
                                        <i class="fas fa-fingerprint me-2"></i>Task ID
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-user-edit me-2"></i>Inserted By
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-server me-2"></i>Platform
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-exchange-alt me-2"></i>Type
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-calendar-check me-2"></i>Resource Assignment
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-hourglass-end me-2"></i>Resource Deadline
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-check-circle me-2"></i>Completed At
                                    </th>
                                    <th class="custom-my-task-table-header">
                                        <i class="fas fa-info-circle me-2"></i>Status
                                    </th>
                                    <th class="custom-my-task-table-header text-center">
                                        <i class="fas fa-cogs me-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="custom-my-task-table-body">
                                @foreach($tasks as $task)
                                    <tr class="custom-my-task-table-row {{ $task->completed_at ? 'task-completed' : '' }}" data-task-id="{{ $task->id }}">
                                        <td class="custom-my-task-table-cell">
                                            <strong>{{ $task->customer->customer_name }}</strong>
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            @if($task->task_id)
                                                <span class="text-nowrap">{{ $task->task_id }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            {{ $task->insertedBy->name ?? 'System' }}
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            {{ $task->customer->platform ? $task->customer->platform->platform_name : 'N/A' }}
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
                                                    {{ $task->assignment_datetime ? $task->assignment_datetime->format('d') : ($task->activation_date ? $task->activation_date->format('d') : 'N/A') }}
                                                </div>
                                                <div class="custom-my-task-date-details">
                                                    <div class="custom-my-task-date-month fw-bold">
                                                        {{ $task->assignment_datetime ? $task->assignment_datetime->format('F') : ($task->activation_date ? $task->activation_date->format('F') : '') }}
                                                    </div>
                                                    <div class="custom-my-task-date-year">
                                                        {{ $task->assignment_datetime ? $task->assignment_datetime->format('Y') : ($task->activation_date ? $task->activation_date->format('Y') : '') }}
                                                        @if($task->assignment_datetime)
                                                            <span class="small d-block text-muted">{{ $task->assignment_datetime->format('H:i') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="custom-my-task-table-cell">
                                            @if($task->deadline_datetime)
                                                <div class="custom-my-task-date">
                                                    <div class="custom-my-task-date-day-deadline">
                                                        {{ $task->deadline_datetime->format('d') }}
                                                    </div>
                                                    <div class="custom-my-task-date-details">
                                                        <div class="custom-my-task-date-month fw-bold">
                                                            {{ $task->deadline_datetime->format('F') }}
                                                        </div>
                                                        <div class="custom-my-task-date-year">
                                                            {{ $task->deadline_datetime->format('Y') }}
                                                            <span class="small d-block text-muted">{{ $task->deadline_datetime->format('H:i') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
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
                                            <div class="d-flex flex-column gap-1">
                                                <button class="custom-my-task-action-btn custom-my-task-edit-btn view-task-btn" data-task-id="{{ $task->id }}">
                                                    <i class="fas fa-eye me-1"></i> <span class="btn-text">View</span>
                                                </button>
                                                @if(!$task->completed_at)
                                                    @if($task->isEligibleForAction())
                                                        <button type="button" 
                                                            class="custom-my-task-action-btn custom-my-task-edit-btn complete-task-btn"
                                                            data-task-id="{{ $task->id }}"
                                                            data-customer-id="{{ $task->customer_id }}"
                                                            data-customer-name="{{ $task->customer->customer_name }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#vdcModal">
                                                            <i class="fas fa-check me-1"></i> <span class="btn-text">Complete</span>
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                            class="custom-my-task-action-btn custom-my-task-edit-btn btn-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#consistencyWarningModal{{ $task->id }}">
                                                            <i class="fas fa-exclamation-triangle me-1"></i> <span class="btn-text">Complete</span>
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="custom-my-task-action-btn custom-my-task-badge-upgrade">
                                                        <i class="fas fa-check-circle me-1"></i> <span class="btn-text">Completed</span>
                                                    </span>
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

    <!-- Consistency Warning Modals -->
    @foreach($tasks as $task)
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
                            Complete the previous task for this customer to ensure consistency.
                        </p>
                    </div>
                    <div class="modal-footer border-0 bg-light d-flex gap-2">
                        <button type="button" class="btn btn-secondary px-4 flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-warning px-4 flex-grow-1 complete-task-anyway-btn" 
                                data-task-id="{{ $task->id }}"
                                data-customer-id="{{ $task->customer_id }}"
                                data-customer-name="{{ $task->customer->customer_name }}"
                                data-bs-dismiss="modal">
                            Complete Anyway
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- VDC Selection Modal -->
    <div class="modal fade" id="vdcModal" tabindex="-1" aria-labelledby="vdcModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vdcModalLabel">
                        <i class="fas fa-server me-2"></i>Select VDC
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="vdcForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="vdc_select" class="form-label fw-semibold">Select Existing VDC</label>
                            <select id="vdc_select" name="vdc_id" class="form-select">
                                <option value="">-- Select VDC --</option>
                            </select>
                        </div>

                        <div class="text-center my-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="toggleNewVdc">
                                <i class="fas fa-plus me-1"></i> Add New VDC
                            </button>
                        </div>

                        <div id="newVdcContainer" class="mb-3" style="display: none;">
                            <label for="new_vdc_name" class="form-label fw-semibold">New VDC Name</label>
                            <input type="text" id="new_vdc_name" name="new_vdc_name" class="form-control" placeholder="Enter VDC name">
                            <small class="text-muted">Leave blank to use selected VDC above</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Complete Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    @push('styles')
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
                            console.log('Clicking view button');
                            targetBtn.click();
                            targetBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 500);
                    }
                } else if (deepAction === 'complete') {
                    const completeBtn = document.querySelector(`.complete-task-btn[data-task-id="${deepTaskId}"]`);
                    console.log('Target complete button:', completeBtn);
                    if (completeBtn) {
                        setTimeout(() => {
                            console.log('Clicking complete button');
                            completeBtn.click();
                            completeBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
                
                fetch(`/my-tasks/${taskId}/details`)
                    .then(response => response.json())
                    .then(data => {
                        const task = data.task;
                        const resourceDetails = data.resourceDetails;
                        const vdcName = task.vdc ? task.vdc.vdc_name : 'N/A';
                        
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
                                                <th><i class="fas fa-server me-2"></i>VDC</th>
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
                                        <td>
                                            <span class="badge bg-info text-dark">${vdcName}</span>
                                        </td>
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

            // VDC Modal handling
            const vdcModal = document.getElementById('vdcModal');
            const vdcForm = document.getElementById('vdcForm');
            const vdcSelect = document.getElementById('vdc_select');
            const toggleNewVdcBtn = document.getElementById('toggleNewVdc');
            const newVdcContainer = document.getElementById('newVdcContainer');
            const newVdcInput = document.getElementById('new_vdc_name');

            let currentTaskId = null;
            let currentCustomerId = null;

            // Handle Complete button click
            document.querySelectorAll('.complete-task-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    handleCompleteClick(this.dataset.taskId, this.dataset.customerId, this.dataset.customerName);
                });
            });

            // Handle Complete Anyway button click from warning modal
            document.querySelectorAll('.complete-task-anyway-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    handleCompleteClick(this.dataset.taskId, this.dataset.customerId, this.dataset.customerName);
                    
                    // Manually open the VDC modal since we dismissed the warning modal
                    const modal = new bootstrap.Modal(vdcModal);
                    modal.show();
                });
            });

            function handleCompleteClick(taskId, customerId, customerName) {
                currentTaskId = taskId;
                currentCustomerId = customerId;

                // Update modal title
                const modalTitle = document.getElementById('vdcModalLabel');
                if (modalTitle) {
                    modalTitle.innerHTML = `<i class="fas fa-server me-2"></i>${customerName} - Select VDC`;
                }

                // Load VDCs for this customer
                loadCustomerVdcs(currentCustomerId);

                // Reset form
                vdcSelect.value = '';
                newVdcInput.value = '';
                newVdcContainer.style.display = 'none';
            }

            // Toggle new VDC input
            toggleNewVdcBtn.addEventListener('click', function() {
                if (newVdcContainer.style.display === 'none') {
                    newVdcContainer.style.display = 'block';
                    vdcSelect.value = ''; // Clear selection
                    newVdcInput.focus();
                } else {
                    newVdcContainer.style.display = 'none';
                    newVdcInput.value = '';
                }
            });

            // Load VDCs for customer
            function loadCustomerVdcs(customerId) {
                fetch(`/my-tasks/customer/${customerId}/vdcs`)
                    .then(response => response.json())
                    .then(data => {
                        vdcSelect.innerHTML = '<option value="">-- Select VDC --</option>';
                        data.vdcs.forEach(vdc => {
                            const option = document.createElement('option');
                            option.value = vdc.id;
                            option.textContent = vdc.vdc_name;
                            vdcSelect.appendChild(option);
                        });

                        // Select the first VDC by default if available
                        if (data.vdcs.length > 0) {
                            vdcSelect.value = data.vdcs[0].id;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading VDCs:', error);
                    });
            }

            // Handle form submission
            vdcForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i> Processing...';

                // Set the form action to the complete route
                const completeUrl = `/my-tasks/${currentTaskId}/complete`;

                fetch(completeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Server Error'); });
                    }
                    return response.json();
                })
                .then(data => {
                    // Close modal
                    const modalInstance = bootstrap.Modal.getInstance(vdcModal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    // Reload page to show updated task status
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error completing task:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                    alert(error.message || 'Error completing task. Please try again.');
                });
            });
        });


    </script>
    @endpush
</x-app-layout>
