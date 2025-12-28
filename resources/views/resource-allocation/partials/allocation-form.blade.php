@if($actionType === 'upgrade')
    <form id="allocation-form" method="POST" data-customer-id="{{ $customer->id }}" data-action-type="{{ $actionType }}" data-status-id="{{ $statusId }}" onsubmit="return window.handleAllocationSubmit(event, this)">
        @csrf
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header resource-alloc-card-header">
                <h5 class="resource-alloc-card-title">
                    <span class="resource-alloc-card-title-icon upgrade">
                        <i class="fas fa-arrow-up"></i>
                    </span>
                    <span>
                        @if($isFirstAllocation ?? false)
                            Initial Resource Allocation: {{ $customer->customer_name }}
                        @else
                            Resource Upgrade: {{ $customer->customer_name }}
                        @endif
                        @if($statusName)
                            <span class="resource-alloc-customer-status-badge status-production">
                                {{ $statusName }}
                            </span>
                        @endif
                    </span>
                </h5>
            </div>
            <div class="card-body p-4">

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Task Status</label>
                        <div class="badge resource-alloc-task-status-active d-block text-center">
                            {{ $taskStatuses->firstWhere('id', $defaultTaskStatusId)->name ?? 'Proceed from KAM' }}
                        </div>
                        <input type="hidden" name="task_status_id" value="{{ $defaultTaskStatusId ?? 1 }}">
                    </div>
                    <div class="col-md-4">
                        <label for="activation_date" class="form-label fw-semibold">Activation Date <span class="text-danger">*</span></label>
                        <input type="date" id="activation_date" name="activation_date" class="form-control" value="{{ $customer->activation_date->format('Y-m-d') }}" min="{{ $customer->activation_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inactivation_date" class="form-label fw-semibold">
                            Inactivation Date
                            <span class="resource-alloc-info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Default value (3000-01-01) means no planned inactivation. Change only if you know the specific end date.">
                                <i class="fas fa-info"></i>
                            </span>
                        </label>
                        <input type="date" id="inactivation_date" name="inactivation_date" class="form-control" value="3000-01-01">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle resource-alloc-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%;" class="resource-alloc-service-cell"><i class="fas fa-tools me-2"></i>Service</th>
                                <th style="width: 15%;"><i class="fas fa-chart-line me-2"></i>Current</th>
                                <th style="width: 30%;"><i class="fas fa-arrow-up me-2"></i>Increase By</th>
                                <th style="width: 25%;"><i class="fas fa-equals me-2"></i>New Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                @php
                                    $currentValue = $customer->getResourceQuantity($service->service_name);
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
                                                class="form-control resource-alloc-stepper-input" 
                                                min="0" 
                                                value="0"
                                                data-current="{{ $currentValue }}"
                                                data-service-id="{{ $service->id }}"
                                                oninput="updateNewTotal(this)"
                                                onfocus="this.value == '0' ? this.value = '' : null"
                                                onblur="this.value == '' ? this.value = '0' : null"
                                                placeholder="0"
                                            >
                                            <button type="button" class="resource-alloc-stepper-btn" onclick="incrementValue(this)">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="resource-alloc-new-total">
                                            <span class="resource-alloc-new-total-arrow">→</span>
                                            <span class="resource-alloc-new-total-value" data-new-total-for="{{ $service->id }}">{{ $currentValue }}</span>
                                            <span class="resource-alloc-new-total-unit">{{ $service->unit }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.clearAllocationForm()">Cancel</button>
                    <button type="submit" class="btn resource-alloc-confirm-btn upgrade">
                        <i class="fas fa-check-circle"></i>
                        Confirm Upgrade
                    </button>
                </div>
            </div>
        </div>
    </form>
@elseif($actionType === 'downgrade')
    <form id="allocation-form" method="POST" data-customer-id="{{ $customer->id }}" data-action-type="{{ $actionType }}" onsubmit="return window.handleAllocationSubmit(event, this)">
        @csrf
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header resource-alloc-card-header">
                <h5 class="resource-alloc-card-title">
                    <span class="resource-alloc-card-title-icon downgrade">
                        <i class="fas fa-arrow-down"></i>
                    </span>
                    <span>
                        Resource Downgrade: {{ $customer->customer_name }}
                        @if($statusName)
                            <span class="resource-alloc-customer-status-badge status-staging">
                                {{ $statusName }}
                            </span>
                        @endif
                    </span>
                </h5>
            </div>
            <div class="card-body p-4">

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Task Status</label>
                        <div class="badge resource-alloc-task-status-active d-block text-center">
                            {{ $taskStatuses->firstWhere('id', $defaultTaskStatusId)->name ?? 'Proceed from KAM' }}
                        </div>
                        <input type="hidden" name="task_status_id" value="{{ $defaultTaskStatusId ?? 1 }}">
                    </div>
                    <div class="col-md-4">
                        <label for="activation_date" class="form-label fw-semibold">Activation Date <span class="text-danger">*</span></label>
                        <input type="date" id="activation_date" name="activation_date" class="form-control" value="{{ now()->format('Y-m-d') }}" min="{{ $customer->activation_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inactivation_date" class="form-label fw-semibold">
                            Inactivation Date
                            <span class="resource-alloc-info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Default value (3000-01-01) means no planned inactivation. Change only if you know the specific end date.">
                                <i class="fas fa-info"></i>
                            </span>
                        </label>
                        <input type="date" id="inactivation_date" name="inactivation_date" class="form-control" value="3000-01-01">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle resource-alloc-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%;" class="resource-alloc-service-cell"><i class="fas fa-tools me-2"></i>Service</th>
                                <th style="width: 15%;"><i class="fas fa-chart-line me-2"></i>Current</th>
                                <th style="width: 30%;"><i class="fas fa-arrow-down me-2"></i>Reduce By</th>
                                <th style="width: 25%;"><i class="fas fa-equals me-2"></i>New Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                @php
                                    $currentValue = $customer->getResourceQuantity($service->service_name);
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
                                                class="form-control resource-alloc-stepper-input downgrade-input" 
                                                min="0" 
                                                max="{{ $currentValue }}"
                                                value="0"
                                                data-current="{{ $currentValue }}"
                                                data-current-value="{{ $currentValue }}"
                                                data-service-id="{{ $service->id }}"
                                                oninput="updateNewTotalDowngrade(this)"
                                                onfocus="this.value == '0' ? this.value = '' : null"
                                                onblur="this.value == '' ? this.value = '0' : null"
                                                placeholder="0"
                                            >
                                            <button type="button" class="resource-alloc-stepper-btn" onclick="incrementValue(this)">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="resource-alloc-new-total">
                                            <span class="resource-alloc-new-total-arrow">→</span>
                                            <span class="resource-alloc-new-total-value" data-new-total-for="{{ $service->id }}">{{ $currentValue }}</span>
                                            <span class="resource-alloc-new-total-unit">{{ $service->unit }}</span>
                                        </div>
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
                        <button type="submit" class="btn resource-alloc-confirm-btn downgrade">
                            <i class="fas fa-check-circle"></i>
                            Confirm Downgrade
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endif
