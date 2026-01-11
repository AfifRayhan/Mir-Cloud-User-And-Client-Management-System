@php
    $hasTestValues = $services->contains(fn($s) => $customer->getResourceTestQuantity($s->service_name) > 0);
    $hasBillableValues = $services->contains(fn($s) => $customer->getResourceBillableQuantity($s->service_name) > 0);
    $isTargetingTest = ($statusId == ($testStatusId ?? 1));
    $showTestColumns = $isTargetingTest || $hasTestValues;
    $showBillableColumns = !$isTargetingTest || $hasBillableValues;
@endphp
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
                            <span class="resource-alloc-customer-status-badge {{ $actionType == 'upgrade' ? 'status-production' : 'status-test' }}">
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
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="text" id="activation_date" name="activation_date" class="form-control flatpickr-date" value="{{ $defaultActivationDate }}" data-min-date="{{ $defaultActivationDate }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="inactivation_date" class="form-label fw-semibold">
                            Inactivation Date
                            <span class="resource-alloc-info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Default value (3000-01-01) means no planned inactivation. Change only if you know the specific end date.">
                                <i class="fas fa-info"></i>
                            </span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="text" id="inactivation_date" name="inactivation_date" class="form-control flatpickr-date" value="3000-01-01">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle resource-alloc-table">
                        <thead class="table-light">
                            <tr>
                                <th class="resource-alloc-service-cell"><i class="fas fa-tools me-2"></i>Service</th>
                                @if(!($isFirstAllocation ?? false))
                                    @if($showTestColumns)
                                        <th class="resource-alloc-test-col {{ $statusId == $testStatusId ? 'status-highlighted' : '' }}"><i class="fas fa-flask me-2"></i>Current Test</th>
                                    @endif
                                    @if($showBillableColumns)
                                        <th class="resource-alloc-billable-col {{ $statusId != $testStatusId && $statusId ? 'status-highlighted' : '' }}"><i class="fas fa-dollar-sign me-2"></i>Current Billable</th>
                                    @endif
                                @endif
                                <th><i class="fas fa-arrow-up me-2"></i>{{ ($isFirstAllocation ?? false) ? 'Allocation Amount' : 'Increase By' }}</th>
                                @if(!($isFirstAllocation ?? false))
                                    @if($showTestColumns)
                                        <th class="resource-alloc-test-col {{ $statusId == $testStatusId ? 'status-highlighted' : '' }}"><i class="fas fa-equals me-2"></i>New Test</th>
                                    @endif
                                    @if($showBillableColumns)
                                        <th class="resource-alloc-billable-col {{ $statusId != $testStatusId && $statusId ? 'status-highlighted' : '' }}"><i class="fas fa-equals me-2"></i>New Billable</th>
                                    @endif
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                @php
                                    $currentTestValue = $customer->getResourceTestQuantity($service->service_name);
                                    $currentBillableValue = $customer->getResourceBillableQuantity($service->service_name);
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
                                    @if(!($isFirstAllocation ?? false))
                                        @if($showTestColumns)
                                            <td class="resource-alloc-test-col {{ $statusId == $testStatusId ? 'status-highlighted' : '' }}">
                                                <div class="resource-alloc-current-display">
                                                    <span class="resource-alloc-current-value">{{ $currentTestValue }}</span>
                                                    <span class="resource-alloc-current-unit">{{ $service->unit }}</span>
                                                </div>
                                            </td>
                                        @endif
                                        @if($showBillableColumns)
                                            <td class="resource-alloc-billable-col {{ $statusId != $testStatusId && $statusId ? 'status-highlighted' : '' }}">
                                                <div class="resource-alloc-current-display">
                                                    <span class="resource-alloc-current-value">{{ $currentBillableValue }}</span>
                                                    <span class="resource-alloc-current-unit">{{ $service->unit }}</span>
                                                </div>
                                            </td>
                                        @endif
                                    @endif
                                    <td>
                                        <div class="resource-alloc-stepper-group">
                                            <button type="button" class="resource-alloc-stepper-btn" onclick="decrementValue(this)">−</button>
                                            <input 
                                                type="number" 
                                                name="services[{{ $service->id }}]" 
                                                class="form-control resource-alloc-stepper-input" 
                                                min="0" 
                                                value="0"
                                                data-current-test="{{ $currentTestValue }}"
                                                data-current-billable="{{ $currentBillableValue }}"
                                                data-service-id="{{ $service->id }}"
                                                data-status-id="{{ $statusId }}"
                                                oninput="updateNewTotal(this)"
                                                onfocus="this.value == '0' ? this.value = '' : null"
                                                onblur="this.value == '' ? this.value = '0' : null"
                                                placeholder="0"
                                            >
                                            <button type="button" class="resource-alloc-stepper-btn" onclick="incrementValue(this)">+</button>
                                        </div>
                                    </td>
                                    @if(!($isFirstAllocation ?? false))
                                        @if($showTestColumns)
                                            <td class="resource-alloc-test-col {{ $statusId == $testStatusId ? 'status-highlighted' : '' }}">
                                                <div class="resource-alloc-new-total">
                                                    <span class="resource-alloc-new-total-arrow">→</span>
                                                    <span class="resource-alloc-new-total-value" data-new-test-for="{{ $service->id }}">{{ $currentTestValue }}</span>
                                                    <span class="resource-alloc-new-total-unit">{{ $service->unit }}</span>
                                                </div>
                                            </td>
                                        @endif
                                        @if($showBillableColumns)
                                            <td class="resource-alloc-billable-col {{ $statusId != $testStatusId && $statusId ? 'status-highlighted' : '' }}">
                                                <div class="resource-alloc-new-total">
                                                    <span class="resource-alloc-new-total-arrow">→</span>
                                                    <span class="resource-alloc-new-total-value" data-new-billable-for="{{ $service->id }}">{{ $currentBillableValue }}</span>
                                                    <span class="resource-alloc-new-total-unit">{{ $service->unit }}</span>
                                                </div>
                                            </td>
                                        @endif
                                    @endif
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
    <form id="allocation-form" method="POST" data-customer-id="{{ $customer->id }}" data-action-type="{{ $actionType }}" data-status-id="{{ $statusId }}" onsubmit="return window.handleAllocationSubmit(event, this)">
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
                            <span class="resource-alloc-customer-status-badge {{ $actionType == 'downgrade' ? 'status-test' : 'status-production' }}">
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
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="text" id="activation_date" name="activation_date" class="form-control flatpickr-date" value="{{ $defaultActivationDate }}" data-min-date="{{ $defaultActivationDate }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="inactivation_date" class="form-label fw-semibold">
                            Inactivation Date <small class="text-muted">(Optional)</small>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="text" id="inactivation_date" name="inactivation_date" class="form-control flatpickr-date" value="3000-01-01" data-min-date="{{ $customer->customer_activation_date->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle resource-alloc-table">
                        <thead class="table-light">
                            <tr>
                                <th class="resource-alloc-service-cell"><i class="fas fa-tools me-2"></i>Service</th>
                                @if(!($isFirstAllocation ?? false))
                                    @if($showTestColumns)
                                        <th class="resource-alloc-test-col {{ $statusId == $testStatusId ? 'status-highlighted' : '' }}"><i class="fas fa-flask me-2"></i>Current Test</th>
                                    @endif
                                    @if($showBillableColumns)
                                        <th class="resource-alloc-billable-col {{ $statusId != $testStatusId && $statusId ? 'status-highlighted' : '' }}"><i class="fas fa-dollar-sign me-2"></i>Current Billable</th>
                                    @endif
                                @endif
                                <th><i class="fas fa-arrow-down me-2"></i>Reduce By</th>
                                @if(!($isFirstAllocation ?? false))
                                    @if($showTestColumns)
                                        <th class="resource-alloc-test-col {{ $statusId == $testStatusId ? 'status-highlighted' : '' }}"><i class="fas fa-equals me-2"></i>New Test</th>
                                    @endif
                                    @if($showBillableColumns)
                                        <th class="resource-alloc-billable-col {{ $statusId != $testStatusId && $statusId ? 'status-highlighted' : '' }}"><i class="fas fa-equals me-2"></i>New Billable</th>
                                    @endif
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                @php
                                    $currentTestValue = $customer->getResourceTestQuantity($service->service_name);
                                    $currentBillableValue = $customer->getResourceBillableQuantity($service->service_name);
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
                                    @if($showTestColumns)
                                        <td class="resource-alloc-test-col {{ $statusId == $testStatusId ? 'status-highlighted' : '' }}">
                                            <div class="resource-alloc-current-display">
                                                <span class="resource-alloc-current-value">{{ $currentTestValue }}</span>
                                                <span class="resource-alloc-current-unit">{{ $service->unit }}</span>
                                            </div>
                                        </td>
                                    @endif
                                    @if($showBillableColumns)
                                        <td class="resource-alloc-billable-col {{ $statusId != $testStatusId && $statusId ? 'status-highlighted' : '' }}">
                                            <div class="resource-alloc-current-display">
                                                <span class="resource-alloc-current-value">{{ $currentBillableValue }}</span>
                                                <span class="resource-alloc-current-unit">{{ $service->unit }}</span>
                                            </div>
                                        </td>
                                    @endif
                                    <td>
                                        <div class="resource-alloc-stepper-group">
                                            <button type="button" class="resource-alloc-stepper-btn" onclick="decrementValue(this)">−</button>
                                            <input 
                                                type="number" 
                                                name="services[{{ $service->id }}]" 
                                                class="form-control resource-alloc-stepper-input downgrade-input" 
                                                min="0" 
                                                @php
                                                    $maxAllowed = $statusId == $testStatusId ? $currentTestValue : $currentBillableValue;
                                                @endphp
                                                max="{{ $maxAllowed }}"
                                                value="0"
                                                data-current-test="{{ $currentTestValue }}"
                                                data-current-billable="{{ $currentBillableValue }}"
                                                data-current-value="{{ $currentTestValue + $currentBillableValue }}"
                                                data-service-id="{{ $service->id }}"
                                                data-status-id="{{ $statusId }}"
                                                oninput="updateNewTotalDowngrade(this)"
                                                onfocus="this.value == '0' ? this.value = '' : null"
                                                onblur="this.value == '' ? this.value = '0' : null"
                                                placeholder="0"
                                            >
                                            <button type="button" class="resource-alloc-stepper-btn" onclick="incrementValue(this)">+</button>
                                        </div>
                                    </td>
                                    @if($showTestColumns)
                                        <td class="resource-alloc-test-col {{ $statusId == $testStatusId ? 'status-highlighted' : '' }}">
                                            <div class="resource-alloc-new-total">
                                                <span class="resource-alloc-new-total-arrow">→</span>
                                                <span class="resource-alloc-new-total-value" data-new-test-for="{{ $service->id }}">{{ $currentTestValue }}</span>
                                                <span class="resource-alloc-new-total-unit">{{ $service->unit }}</span>
                                            </div>
                                        </td>
                                    @endif
                                    @if($showBillableColumns)
                                        <td class="resource-alloc-billable-col {{ $statusId != $testStatusId && $statusId ? 'status-highlighted' : '' }}">
                                            <div class="resource-alloc-new-total">
                                                <span class="resource-alloc-new-total-arrow">→</span>
                                                <span class="resource-alloc-new-total-value" data-new-billable-for="{{ $service->id }}">{{ $currentBillableValue }}</span>
                                                <span class="resource-alloc-new-total-unit">{{ $service->unit }}</span>
                                            </div>
                                        </td>
                                    @endif
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
@elseif($actionType === 'transfer')
    <form id="allocation-form" method="POST" data-customer-id="{{ $customer->id }}" data-action-type="{{ $actionType }}" data-transfer-type="{{ $transferType }}" onsubmit="return window.handleAllocationSubmit(event, this)">
        @csrf
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header resource-alloc-card-header">
                <h5 class="resource-alloc-card-title">
                    <span class="resource-alloc-card-title-icon" style="background: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                        <i class="fas fa-exchange-alt"></i>
                    </span>
                    <span>
                        Resource Transfer: {{ $customer->customer_name }}
                        <span class="resource-alloc-customer-status-badge" style="background: rgba(13, 110, 253, 0.1); color: #0d6efd; border: 1px solid rgba(13, 110, 253, 0.2);">
                            {{ $transferType === 'test_to_billable' ? 'Test to Billable Pool' : 'Billable to Test Pool' }}
                        </span>
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
                        <label for="activation_date" class="form-label fw-semibold">Transfer Date <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="text" id="activation_date" name="activation_date" class="form-control flatpickr-date" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>

                @php
                    $allTestZero = true;
                    $allBillableZero = true;
                    foreach($services as $service) {
                        if($customer->getResourceTestQuantity($service->service_name) > 0) $allTestZero = false;
                        if($customer->getResourceBillableQuantity($service->service_name) > 0) $allBillableZero = false;
                    }
                @endphp

                <div class="table-responsive">
                    <table class="table table-bordered align-middle resource-alloc-table">
                        <thead class="table-light">
                            <tr>
                                <th class="resource-alloc-service-cell"><i class="fas fa-tools me-2"></i>Service</th>
                                @if(!$allTestZero)
                                    <th class="resource-alloc-test-col {{ $transferType === 'test_to_billable' ? 'status-highlighted' : '' }}"><i class="fas fa-flask me-2"></i>Current Test</th>
                                @endif
                                @if(!$allBillableZero)
                                    <th class="resource-alloc-billable-col {{ $transferType === 'billable_to_test' ? 'status-highlighted' : '' }}"><i class="fas fa-dollar-sign me-2"></i>Current Billable</th>
                                @endif
                                <th><i class="fas fa-exchange-alt me-2"></i>Move Amount</th>
                                <th class="resource-alloc-test-col {{ $transferType === 'billable_to_test' ? 'status-highlighted' : '' }}"><i class="fas fa-equals me-2"></i>New Test</th>
                                <th class="resource-alloc-billable-col {{ $transferType === 'test_to_billable' ? 'status-highlighted' : '' }}"><i class="fas fa-equals me-2"></i>New Billable</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                @php
                                    $currentTestValue = $customer->getResourceTestQuantity($service->service_name);
                                    $currentBillableValue = $customer->getResourceBillableQuantity($service->service_name);
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
                                    @if(!$allTestZero)
                                        <td class="resource-alloc-test-col {{ $transferType === 'test_to_billable' ? 'status-highlighted' : '' }}">
                                            <div class="resource-alloc-current-display">
                                                <span class="resource-alloc-current-value">{{ $currentTestValue }}</span>
                                                <span class="resource-alloc-current-unit">{{ $service->unit }}</span>
                                            </div>
                                        </td>
                                    @endif
                                    @if(!$allBillableZero)
                                        <td class="resource-alloc-billable-col {{ $transferType === 'billable_to_test' ? 'status-highlighted' : '' }}">
                                            <div class="resource-alloc-current-display">
                                                <span class="resource-alloc-current-value">{{ $currentBillableValue }}</span>
                                                <span class="resource-alloc-current-unit">{{ $service->unit }}</span>
                                            </div>
                                        </td>
                                    @endif
                                    <td>
                                        <div class="resource-alloc-stepper-group">
                                            <button type="button" class="resource-alloc-stepper-btn" onclick="decrementValue(this)">−</button>
                                            <input 
                                                type="number" 
                                                name="services[{{ $service->id }}]" 
                                                class="form-control resource-alloc-stepper-input transfer-input" 
                                                min="0" 
                                                @php
                                                    $maxAllowed = $transferType === 'test_to_billable' ? $currentTestValue : $currentBillableValue;
                                                @endphp
                                                max="{{ $maxAllowed }}"
                                                value="0"
                                                data-current-test="{{ $currentTestValue }}"
                                                data-current-billable="{{ $currentBillableValue }}"
                                                data-service-id="{{ $service->id }}"
                                                data-transfer-type="{{ $transferType }}"
                                                oninput="window.updateNewTotalTransfer(this)"
                                                onfocus="this.value == '0' ? this.value = '' : null"
                                                onblur="this.value == '' ? this.value = '0' : null"
                                                placeholder="0"
                                            >
                                            <button type="button" class="resource-alloc-stepper-btn" onclick="incrementValue(this)">+</button>
                                        </div>
                                    </td>
                                    <td class="resource-alloc-test-col {{ $transferType === 'billable_to_test' ? 'status-highlighted' : '' }}">
                                        <div class="resource-alloc-new-total">
                                            <span class="resource-alloc-new-total-arrow">→</span>
                                            <span class="resource-alloc-new-total-value" data-new-test-for="{{ $service->id }}">{{ $currentTestValue }}</span>
                                            <span class="resource-alloc-new-total-unit">{{ $service->unit }}</span>
                                        </div>
                                    </td>
                                    <td class="resource-alloc-billable-col {{ $transferType === 'test_to_billable' ? 'status-highlighted' : '' }}">
                                        <div class="resource-alloc-new-total">
                                            <span class="resource-alloc-new-total-arrow">→</span>
                                            <span class="resource-alloc-new-total-value" data-new-billable-for="{{ $service->id }}">{{ $currentBillableValue }}</span>
                                            <span class="resource-alloc-new-total-unit">{{ $service->unit }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-primary" onclick="window.fillTransferAllValues()">
                        <i class="fas fa-exchange-alt"></i> Transfer All
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.clearAllocationForm()">Cancel</button>
                        <button type="submit" class="btn resource-alloc-confirm-btn" style="background: #0d6efd; color: white;">
                            <i class="fas fa-check-circle"></i>
                            Confirm Transfer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endif
