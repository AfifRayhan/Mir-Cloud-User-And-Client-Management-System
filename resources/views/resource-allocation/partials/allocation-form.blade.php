@if($actionType === 'upgrade')
    <form id="allocation-form" method="POST" data-customer-id="{{ $customer->id }}" data-action-type="{{ $actionType }}" data-status-id="{{ $statusId }}" onsubmit="return window.handleAllocationSubmit(event, this)">
        @csrf
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-success">
                    Upgrade Resources for {{ $customer->customer_name }}
                </h5>
            </div>
            <div class="card-body p-4">
                <!-- Success Message Container -->
                <div id="allocation-success-message" class="alert alert-success alert-dismissible fade d-none" role="alert">
                    <strong>Success!</strong> <span id="success-message-text"></span>
                    <button type="button" class="btn-close" onclick="this.parentElement.classList.add('d-none')"></button>
                </div>
                
                <div class="alert alert-success mb-4">
                    <small><strong>Upgrade Mode:</strong> Specify the amount to increase for each resource.</small>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Customer Status</h6>
                    <div class="badge bg-primary px-3 py-2">
                        {{ $statusName ?? 'Not Set' }}
                    </div>
                </div>

                <div class="mb-4">
                    <label for="task_status_id" class="form-label fw-semibold">Task Status <span class="text-danger">*</span></label>
                    <select id="task_status_id" name="task_status_id" class="form-select" required>
                        <option value="" disabled selected>Select Task Status</option>
                        @foreach($taskStatuses as $taskStatus)
                            <option value="{{ $taskStatus->id }}">{{ $taskStatus->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="activation_date" class="form-label fw-semibold">Activation Date <span class="text-danger">*</span></label>
                        <input type="date" id="activation_date" name="activation_date" class="form-control" value="{{ $customer->activation_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="inactivation_date" class="form-label fw-semibold">Inactivation Date</label>
                        @php
                            $inactivationDate = '3000-01-01';
                            if ($customer->cloudDetail && isset($customer->cloudDetail->other_configuration['inactivation_date'])) {
                                $inactivationDate = $customer->cloudDetail->other_configuration['inactivation_date'];
                            }
                        @endphp
                        <input type="date" id="inactivation_date" name="inactivation_date" class="form-control" value="{{ $inactivationDate }}">
                        <small class="text-muted">Leave as default (3000-01-01) for no inactivation</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%;">Service</th>
                                <th>Current Value</th>
                                <th>Increase By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                @php
                                    $columnName = null;
                                    $mapping = [
                                        'vCPU' => 'vcpu',
                                        'RAM' => 'ram',
                                        'Storage' => 'storage',
                                        'Internet' => 'internet',
                                        'Real IP' => 'real_ip',
                                        'VPN' => 'vpn',
                                        'BDIX' => 'bdix',
                                    ];
                                    $columnName = $mapping[$service->service_name] ?? null;
                                    $currentValue = 0;
                                    if ($customer->cloudDetail) {
                                        if ($columnName) {
                                            $currentValue = $customer->cloudDetail->{$columnName} ?? 0;
                                        } else {
                                            // Check other_configuration for unmapped services
                                            $otherConfig = $customer->cloudDetail->other_configuration ?? [];
                                            $currentValue = $otherConfig[$service->service_name] ?? 0;
                                        }
                                    }
                                    // Debug: Service: {{ $service->service_name }}, Column: {{ $columnName }}, Value: {{ $currentValue }}
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
                    <label for="task_status_id_downgrade" class="form-label fw-semibold">Task Status <span class="text-danger">*</span></label>
                    <select id="task_status_id_downgrade" name="task_status_id" class="form-select" required>
                        <option value="" disabled selected>Select Task Status</option>
                        @foreach($taskStatuses as $taskStatus)
                            <option value="{{ $taskStatus->id }}">{{ $taskStatus->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%;">Service</th>
                                <th>Current Value</th>
                                <th>Reduce By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                @php
                                    $columnName = null;
                                    $mapping = [
                                        'vCPU' => 'vcpu',
                                        'RAM' => 'ram',
                                        'Storage' => 'storage',
                                        'Internet' => 'internet',
                                        'Real IP' => 'real_ip',
                                        'VPN' => 'vpn',
                                        'BDIX' => 'bdix',
                                    ];
                                    $columnName = $mapping[$service->service_name] ?? null;
                                    $currentValue = 0;
                                    if ($customer->cloudDetail) {
                                        if ($columnName) {
                                            $currentValue = $customer->cloudDetail->{$columnName} ?? 0;
                                        } else {
                                            // Check other_configuration for unmapped services
                                            $otherConfig = $customer->cloudDetail->other_configuration ?? [];
                                            $currentValue = $otherConfig[$service->service_name] ?? 0;
                                        }
                                    }
                                    // Debug: Service: {{ $service->service_name }}, Column: {{ $columnName }}, Value: {{ $currentValue }}
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
