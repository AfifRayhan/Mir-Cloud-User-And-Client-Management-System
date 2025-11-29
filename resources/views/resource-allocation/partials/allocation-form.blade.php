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
                <div class="alert alert-info mb-4">
                    <small><strong>Upgrade Mode:</strong> Specify the new resource values. The system will calculate and add the difference to current allocations.</small>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Customer Status</h6>
                    <div class="badge bg-primary px-3 py-2">
                        {{ $statusName ?? 'Not Set' }}
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Resources & Services</h6>
                    <div class="row g-3">
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
                                if ($customer->cloudDetail && $columnName) {
                                    $currentValue = $customer->cloudDetail->{$columnName} ?? 0;
                                }
                                // Debug: Log service name, column name, and value
                                // Service: {{ $service->service_name }}, Column: {{ $columnName }}, Value: {{ $currentValue }}
                            @endphp
                            
                            <div class="col-md-6">
                                <label for="service_{{ $service->id }}" class="form-label fw-semibold">
                                    {{ $service->service_name }}
                                    <span class="text-muted small">(Current: {{ $currentValue }}{{ $service->unit ? ' ' . $service->unit : '' }})</span>
                                </label>
                                <div class="input-group">
                                    <input 
                                        type="number" 
                                        min="0" 
                                        class="form-control" 
                                        id="service_{{ $service->id }}" 
                                        name="services[{{ $service->id }}]" 
                                        value="{{ $currentValue }}"
                                        placeholder="New value"
                                    >
                                    @if($service->unit)
                                        <span class="input-group-text">{{ $service->unit }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
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
                <div class="alert alert-warning mb-4">
                    <small><strong>Downgrade Mode:</strong> Specify the amount to reduce from each resource.</small>
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
                                    if ($customer->cloudDetail && $columnName) {
                                        $currentValue = $customer->cloudDetail->{$columnName} ?? 0;
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
                                            max="{{ $currentValue }}"
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
                    <button type="submit" class="btn btn-warning px-4">Confirm Downgrade</button>
                </div>
            </div>
        </div>
    </form>
@endif
