@push('styles')
    @vite(['resources/css/custom-billing-task-management.css'])
@endpush

<x-app-layout>
    <div class="container-fluid custom-billing-task-management-container py-4">
        <div class="custom-billing-task-management-bg-pattern"></div>
        <div class="custom-billing-task-management-bg-circle circle-1"></div>
        <div class="custom-billing-task-management-bg-circle circle-2"></div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="custom-billing-task-management-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h1 class="custom-billing-task-management-title fw-bold mb-2">Billing Task Management</h1>
                        <p class="custom-billing-task-management-subtitle text-muted">View completed tasks and export reports</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('billing-task-management.export', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>Export Task Report
                        </a>
                        <a href="{{ route('billing-task-management.export-customers', request()->query()) }}" class="btn btn-primary">
                            <i class="fas fa-users me-2"></i>Export Customer Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="custom-billing-task-management-card mb-4 shadow-sm border-0">
            <div class="custom-billing-task-management-card-header bg-white border-bottom py-3">
                <h5 class="custom-billing-task-management-card-title mb-0 fw-bold">
                    <i class="fas fa-filter text-primary me-2"></i>Filters
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('billing-task-management.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Allocation Type</label>
                        <select name="allocation_type" class="form-select border-0 bg-light shadow-none">
                            <option value="">All Types</option>
                            <option value="upgrade" {{ request('allocation_type') == 'upgrade' ? 'selected' : '' }}>Upgrade</option>
                            <option value="downgrade" {{ request('allocation_type') == 'downgrade' ? 'selected' : '' }}>Downgrade</option>
                            <option value="transfer" {{ request('allocation_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Inserted By</label>
                        <select name="inserted_by" class="form-select border-0 bg-light shadow-none">
                            <option value="">All KAMs</option>
                            @foreach($kams as $kam)
                                <option value="{{ $kam->id }}" {{ request('inserted_by') == $kam->id ? 'selected' : '' }}>
                                    {{ $kam->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Assigned To</label>
                        <select name="assigned_to" class="form-select border-0 bg-light shadow-none">
                            <option value="">All Users</option>
                            @foreach($techs as $tech)
                                <option value="{{ $tech->id }}" {{ request('assigned_to') == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status_id" class="form-select border-0 bg-light shadow-none">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 d-flex align-items-end justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm custom-billing-task-management-filter-btn">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                        <a href="{{ route('billing-task-management.index') }}" class="btn btn-light px-4 border custom-billing-task-management-reset-btn">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tasks Table -->
        <div class="custom-billing-task-management-card shadow-sm border-0">
            <div class="custom-billing-task-management-card-header bg-white border-bottom py-3">
                <h5 class="custom-billing-task-management-card-title mb-0 fw-bold">
                    <i class="fas fa-tasks text-primary me-2"></i>Completed Tasks
                </h5>
            </div>
            <div class="card-body">
                @if($tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle custom-billing-task-management-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-4 border-0">
                                        <i class="fas fa-building me-2"></i>Customer
                                    </th>
                                    <th class="py-3 px-4 border-0">
                                        <i class="fas fa-fingerprint me-2"></i>Task ID
                                    </th>
                                    <th class="py-3 px-4 border-0">
                                        <i class="fas fa-user-edit me-2"></i>Inserted By
                                    </th>
                                    <th class="py-3 px-4 border-0">
                                        <i class="fas fa-server me-2"></i>Platform
                                    </th>
                                    <th class="py-3 px-4 border-0">
                                        <i class="fas fa-exchange-alt me-2"></i>Type
                                    </th>
                                    <th class="py-3 px-4 border-0">
                                        <i class="fas fa-calendar-check me-2"></i>Completed At
                                    </th>
                                    <th class="py-3 px-4 border-0">
                                        <i class="fas fa-info-circle me-2"></i>Status
                                    </th>
                                    <th class="py-3 px-4 border-0">
                                        <i class="fas fa-user-check me-2"></i>Assigned To
                                    </th>
                                    <th class="py-3 px-4 border-0 text-center">
                                        <i class="fas fa-cogs me-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                    <tr class="border-bottom custom-billing-task-management-table-row {{ $task->billed_at ? 'task-billed' : 'task-completed' }}">
                                        <td class="py-4 px-4 fw-bold">{{ $task->customer->customer_name }}</td>
                                        <td class="py-4 px-4">
                                            @if($task->task_id)
                                                <span class="text-nowrap">{{ $task->task_id }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">{{ $task->insertedBy->name ?? 'System' }}</td>
                                        <td class="py-4 px-4">
                                            @if($task->customer->platform)
                                                <span>{{ $task->customer->platform->platform_name }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($task->allocation_type === 'upgrade')
                                                <span class="custom-billing-task-management-badge custom-billing-task-management-badge-upgrade">
                                                    <i class="fas fa-arrow-up me-1"></i> Upgrade
                                                </span>
                                            @elseif($task->allocation_type === 'downgrade')
                                                <span class="custom-billing-task-management-badge custom-billing-task-management-badge-downgrade">
                                                    <i class="fas fa-arrow-down me-1"></i> Downgrade
                                                </span>
                                            @else
                                                <span class="custom-billing-task-management-badge custom-billing-task-management-badge-transfer">
                                                    <i class="fas fa-exchange-alt me-1"></i> Transfer
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="custom-billing-task-management-date">
                                                <div class="custom-billing-task-management-date-day">
                                                    {{ $task->completed_at ? $task->completed_at->format('d') : 'N/A' }}
                                                </div>
                                                <div class="d-flex flex-column ms-2">
                                                    <span class="fw-bold">{{ $task->completed_at ? $task->completed_at->format('F') : '' }}</span>
                                                    <span class="text-muted small">
                                                        {{ $task->completed_at ? $task->completed_at->format('Y') : '' }}
                                                        @if($task->completed_at)
                                                            <br> {{ $task->completed_at->format('H:i') }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($task->status)
                                                <span class="text-secondary">{{ $task->status->name }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">{{ $task->assignedTo->name ?? 'Unassigned' }}</td>
                                        <td class="py-4 px-4 text-center">
                                            <button class="btn btn-sm btn-outline-primary view-task-btn custom-billing-task-management-action-btn custom-billing-task-management-view-btn" data-task-id="{{ $task->id }}">
                                                <i class="fas fa-eye me-1"></i> <span class="btn-text">View</span>
                                            </button>
                                            
                                            @if(!$task->billed_at)
                                                <button type="button" 
                                                        class="btn btn-sm btn-success custom-billing-task-management-action-btn bill-task-btn"
                                                        data-task-id="{{ $task->id }}"
                                                        data-customer-name="{{ $task->customer->customer_name }}"
                                                        data-action-url="{{ route('billing-task-management.bill', $task->id) }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#billingConfirmModal">
                                                    <i class="fas fa-check-double me-1"></i> Bill
                                                </button>
                                            @else
                                                <span class="badge bg-success py-2 px-3">
                                                    <i class="fas fa-file-invoice-dollar me-1"></i> Billed
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <!-- Details Row -->
                                    <tr class="task-details-row" id="details-{{ $task->id }}" style="display: none;">
                                        <td colspan="9" class="p-0 border-0">
                                            <div class="task-details-container p-4 bg-light shadow-inner">
                                                <div class="text-center py-3">
                                                    <div class="spinner-border text-primary spinner-border-sm" role="status">
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
                    <div class="mt-4">
                        {{ $tasks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Billing Confirmation Modal -->
    <div class="modal fade" id="billingConfirmModal" tabindex="-1" aria-labelledby="billingConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold" id="billingConfirmModalLabel">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Billing Confirmation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-3x text-success opacity-50"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Confirm Billing</h5>
                    <p class="text-muted mb-0">
                        Are you sure you want to mark the task for <span id="modalCustomerName" class="fw-bold text-dark"></span> as billed?
                    </p>
                    <p class="text-muted small mt-2">
                        This action will record the current timestamp as the billing time.
                    </p>
                </div>
                <div class="modal-footer border-0 bg-light d-flex gap-2">
                    <button type="button" class="btn btn-secondary px-4 flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                    <form id="billingConfirmForm" method="POST" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 w-100">
                            <i class="fas fa-check me-1"></i> Confirm Bill
                        </button>
                    </form>
                </div>
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
                    
                    if (detailsRow.style.display === 'none') {
                        // Close others
                        document.querySelectorAll('.task-details-row').forEach(row => row.style.display = 'none');
                        document.querySelectorAll('.view-task-btn').forEach(btn => {
                            btn.querySelector('.btn-text').textContent = 'View';
                            btn.querySelector('i').className = 'fas fa-eye me-1';
                        });

                        detailsRow.style.display = 'table-row';
                        btnText.textContent = 'Hide';
                        btnIcon.className = 'fas fa-eye-slash me-1';
                        
                        if (!detailsRow.dataset.loaded) {
                            loadTaskDetails(taskId);
                        }
                    } else {
                        detailsRow.style.display = 'none';
                        btnText.textContent = 'View';
                        btnIcon.className = 'fas fa-eye me-1';
                    }
                });
            });
            
            function loadTaskDetails(taskId) {
                const detailsRow = document.getElementById('details-' + taskId);
                const container = detailsRow.querySelector('.task-details-container');
                
                fetch(`/billing-task-management/${taskId}/details`)
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

                            const badgeClass = isTransfer ? 'custom-billing-task-management-value-badge-transfer' : (isUpgrade ? 'badge bg-success' : 'badge bg-warning text-dark');
                            
                            html += `
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle bg-white mb-0 shadow-sm">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Service</th>
                                                <th>${currentHeader}</th>
                                                <th>${label}</th>
                                                <th>${newHeader}</th>
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
                                
                                html += `
                                    <tr>
                                        <td>${detail.service.service_name} ${detail.service.unit ? `(${detail.service.unit})` : ''}</td>
                                        <td><span class="badge bg-light text-dark border">${prev}</span></td>
                                        <td><span class="${badgeClass}">${isUpgrade ? '+' : (isTransfer ? '' : '-')}${amount}</span></td>
                                        <td><span class="fw-bold text-primary">→ ${next}</span></td>
                                    </tr>
                                `;
                            });
                            
                            html += `</tbody></table></div>`;
                        } else {
                            html = '<div class="text-center text-muted py-3">No details found</div>';
                        }
                        
                        container.innerHTML = html;
                        detailsRow.dataset.loaded = 'true';
                    });
            }

            // Billing Modal handling
            const billingConfirmModal = document.getElementById('billingConfirmModal');
            if (billingConfirmModal) {
                billingConfirmModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget.closest('.bill-task-btn');
                    if (!button) return;
                    
                    const actionUrl = button.getAttribute('data-action-url');
                    const customerName = button.getAttribute('data-customer-name');
                    
                    const form = this.querySelector('#billingConfirmForm');
                    const customerNameSpan = this.querySelector('#modalCustomerName');
                    
                    form.action = actionUrl;
                    customerNameSpan.textContent = customerName;
                });
            }

            // Auto-open task details if query params exist (e.g. from email link)
            const urlParams = new URLSearchParams(window.location.search);
            const dtid = urlParams.get('dtid');
            const da = urlParams.get('da');

            if (dtid && da === 'view') {
                const targetBtn = document.querySelector(`.view-task-btn[data-task-id="${dtid}"]`);
                if (targetBtn) {
                    // Slight delay to ensure DOM is fully ready / other scripts initialized
                    setTimeout(() => {
                        targetBtn.click();
                        targetBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 500);
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
