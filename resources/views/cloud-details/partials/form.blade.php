@php
    $cloudDetail = $cloudDetail ?? null;
    $services = $services ?? collect();
    $reservedServiceNames = collect([
        'vCPU',
        'RAM',
        'Storage',
        'Real IP',
        'VPN',
        'BDIX',
        'Internet Bandwidth',
    ]);
    $otherServices = $services->reject(function ($service) use ($reservedServiceNames) {
        return $reservedServiceNames->contains($service->service_name);
    });
    $otherConfigValues = old('service_config', optional($cloudDetail)->other_configuration ?? []);
@endphp

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1 class="h3 fw-bold mb-1">Cloud Details</h1>
                    <p class="text-muted mb-0">
                        Provisioning specs for
                        <span class="fw-semibold">{{ $customer->customer_name }}</span>
                    </p>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                    Customer ID: #{{ $customer->id }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-xl-6">
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <p class="mb-2 fw-semibold">Please review the highlighted fields.</p>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('cloud-details.store', $customer->id) }}">
                        @csrf

                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 text-primary">Compute & Storage</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="vcpu" class="form-label fw-semibold">vCPU</label>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control @error('vcpu') is-invalid @enderror" id="vcpu" name="vcpu" value="{{ old('vcpu', $cloudDetail->vcpu ?? '') }}">
                                        <span class="input-group-text">cores</span>
                                    </div>
                                    @error('vcpu')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="ram" class="form-label fw-semibold">RAM</label>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control @error('ram') is-invalid @enderror" id="ram" name="ram" value="{{ old('ram', $cloudDetail->ram ?? '') }}">
                                        <span class="input-group-text">GB</span>
                                    </div>
                                    @error('ram')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="storage" class="form-label fw-semibold">Storage</label>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control @error('storage') is-invalid @enderror" id="storage" name="storage" value="{{ old('storage', $cloudDetail->storage ?? '') }}">
                                        <span class="input-group-text">GB</span>
                                    </div>
                                    @error('storage')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 text-primary">Connectivity Options</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="real_ip" class="form-label fw-semibold">Real IP</label>
                                    <input type="text" class="form-control @error('real_ip') is-invalid @enderror" id="real_ip" name="real_ip" value="{{ old('real_ip', $cloudDetail->real_ip ?? '') }}" placeholder="Value / Status">
                                    @error('real_ip')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="vpn" class="form-label fw-semibold">VPN</label>
                                    <input type="text" class="form-control @error('vpn') is-invalid @enderror" id="vpn" name="vpn" value="{{ old('vpn', $cloudDetail->vpn ?? '') }}" placeholder="Value / Status">
                                    @error('vpn')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="bdix" class="form-label fw-semibold">BDIX</label>
                                    <input type="text" class="form-control @error('bdix') is-invalid @enderror" id="bdix" name="bdix" value="{{ old('bdix', $cloudDetail->bdix ?? '') }}" placeholder="Value / Status">
                                    @error('bdix')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="internet" class="form-label fw-semibold">Internet Bandwidth</label>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control @error('internet') is-invalid @enderror" id="internet" name="internet" value="{{ old('internet', $cloudDetail->internet ?? '') }}">
                                        <span class="input-group-text">Mbps</span>
                                    </div>
                                    @error('internet')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 text-primary">Billing Configuration</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Inserted By</label>
                                    <input type="text" class="form-control" value="{{ $cloudDetail->insertedBy->name ?? Auth::user()->name }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-3 text-primary mb-0">Other Configuration</h5>
                                <a href="{{ route('services.index') }}" class="text-decoration-none small">Manage services</a>
                            </div>
                            <div class="row g-3">
                                @forelse($otherServices as $service)
                                    <div class="col-md-6">
                                        <div class="border rounded-3 p-3 h-100">
                                            <label class="fw-semibold small text-muted text-uppercase d-block mb-2" for="service_config_{{ $service->id }}">
                                                {{ $service->service_name }}
                                            </label>
                                            <input
                                                type="text"
                                                id="service_config_{{ $service->id }}"
                                                name="service_config[{{ $service->id }}]"
                                                class="form-control form-control-sm"
                                                placeholder="Enter value"
                                                value="{{ old('service_config.' . $service->id, $otherConfigValues[$service->id] ?? '') }}"
                                            >
                                            <div class="d-flex justify-content-between mt-2 text-muted small">
                                                <span>Unit: {{ $service->unit ?? '—' }}</span>
                                                <span>Price: {{ $service->unit_price ? number_format($service->unit_price, 2) : '—' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-secondary mb-0">
                                            No services configured yet. <a href="{{ route('services.index') }}" class="alert-link">Add services</a>.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                Complete Provisioning
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

