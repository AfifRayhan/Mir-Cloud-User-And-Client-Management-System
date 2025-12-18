@if($actionType === 'upgrade')
    <form id="allocation-form" method="POST" data-customer-id="{{ $customer->id }}" data-action-type="{{ $actionType }}" data-status-id="{{ $statusId }}" onsubmit="return window.handleAllocationSubmit(event, this)">
        @csrf
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-success">
                    @if($isFirstAllocation ?? false)
                        Initial Resource Allocation for {{ $customer->customer_name }}
                    @else
                        Upgrade Resources for {{ $customer->customer_name }}
                    @endif
                </h5>
            </div>
            <div class="card-body p-4">
                <!-- Success Message Container -->
                <div id="allocation-success-message" class="alert alert-success alert-dismissible fade d-none" role="alert">
                    <strong>Success!</strong> <span id="success-message-text"></span>
                    <button type="button" class="btn-close" onclick="this.parentElement.classList.add('d-none')"></button>
                </div>
                
                @if($isFirstAllocation ?? false)
                    <div class="alert alert-info mb-4">
                        <small><strong>Initial Allocation:</strong> This is the first resource allocation for this customer. Specify the initial quantity for each resource.</small>
                    </div>
                @else
                    <div class="alert alert-success mb-4">
                        <small><strong>Upgrade Mode:</strong> Specify the amount to increase for each resource.</small>
                    </div>
                @endif

                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Customer Status</h6>
                    <div class="badge bg-primary px-3 py-2">
                        {{ $statusName ?? 'Not Set' }}
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Task Status</h6>
                    <div class="badge bg-secondary px-3 py-2">
                        {{ $taskStatuses->firstWhere('id', $defaultTaskStatusId)->name ?? 'Proceed from KAM' }}
                    </div>
                    <input type="hidden" name="task_status_id" value="{{ $defaultTaskStatusId ?? 1 }}">
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="activation_date" class="form-label fw-semibold">Activation Date <span class="text-danger">*</span></label>
                        <input type="date" id="activation_date" name="activation_date" class="form-control" value="{{ $customer->activation_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="inactivation_date" class="form-label fw-semibold">Inactivation Date</label>
                        <input type="date" id="inactivation_date" name="inactivation_date" class="form-control" value="3000-01-01">
                        <small class="text-muted">Leave as default (3000-01-01) for no inactivation</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%;"><i class="fas fa-tools me-2"></i>Service</th>
                                <th><i class="fas fa-chart-line me-2"></i>Current Value</th>
                                <th><i class="fas fa-arrow-up me-2"></i>Increase By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                @php
                                    $currentValue = $customer->getResourceQuantity($service->service_name);
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $service->service_name }}</span>
                                        @if($service->unit)
                                            <span class="text-muted small">({{ $service->unit }})</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $currentValue }} {{ $service->unit }}</span>
                                    </td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="services[{{ $service->id }}]" 
                                            class="form-control" 
                                            min="0" 
                                            placeholder="0"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.clearAllocationForm()">Cancel</button>
                    <button type="submit" class="btn btn-success px-4">Confirm Upgrade</button>
                </div>
            </div>
        </div>
    </form>
@elseif($actionType === 'downgrade')
    <form id="allocation-form" method="POST" data-customer-id="{{ $customer->id }}" data-action-type="{{ $actionType }}" onsubmit="return window.handleAllocationSubmit(event, this)">
        @csrf
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-warning">
                    Downgrade Resources for {{ $customer->customer_name }}
                </h5>
            </div>
            <div class="card-body p-4">
                <!-- Success Message Container -->
                <div id="allocation-success-message" class="alert alert-success alert-dismissible fade d-none" role="alert">
                    <strong>Success!</strong> <span id="success-message-text"></span>
                    <button type="button" class="btn-close" onclick="this.parentElement.classList.add('d-none')"></button>
                </div>
                
                <div class="alert alert-warning mb-4">
                    <small><strong>Downgrade Mode:</strong> Specify the amount to reduce from each resource.</small>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Customer Status</h6>
                    <div class="badge bg-primary px-3 py-2">
                        {{ $statusName ?? 'Not Set' }}
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Task Status</h6>
                    <div class="badge bg-secondary px-3 py-2">
                        {{ $taskStatuses->firstWhere('id', $defaultTaskStatusId)->name ?? 'Proceed from KAM' }}
                    </div>
                    <input type="hidden" name="task_status_id" value="{{ $defaultTaskStatusId ?? 1 }}">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%;"><i class="fas fa-tools me-2"></i>Service</th>
                                <th><i class="fas fa-chart-line me-2"></i>Current Value</th>
                                <th><i class="fas fa-arrow-down me-2"></i>Reduce By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                @php
                                    $currentValue = $customer->getResourceQuantity($service->service_name);
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $service->service_name }}</span>
                                        @if($service->unit)
                                            <span class="text-muted small">({{ $service->unit }})</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $currentValue }} {{ $service->unit }}</span>
                                    </td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="services[{{ $service->id }}]" 
                                            class="form-control downgrade-input" 
                                            min="0" 
                                            max="{{ $currentValue }}"
                                            data-current-value="{{ $currentValue }}"
                                            placeholder="0"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-danger" onclick="window.fillDismantleValues()">
                        <i class="bi bi-trash"></i> Dismantle All
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.clearAllocationForm()">Cancel</button>
                        <button type="submit" class="btn btn-warning px-4">Confirm Downgrade</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endif
