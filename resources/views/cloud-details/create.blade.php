<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Cloud Details</h1>
                        <p class="text-muted mb-0">Provisioning specs for <span class="fw-semibold">{{ $customer->customer_name }}</span></p>
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
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="real_ip" name="real_ip" value="1" {{ old('real_ip', $cloudDetail->real_ip ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="real_ip">Real IP</label>
                                        </div>
                                        @error('real_ip')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="vpn" name="vpn" value="1" {{ old('vpn', $cloudDetail->vpn ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="vpn">VPN</label>
                                        </div>
                                        @error('vpn')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="bdix" name="bdix" value="1" {{ old('bdix', $cloudDetail->bdix ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="bdix">BDIX</label>
                                        </div>
                                        @error('bdix')
                                            <div class="text-danger small">{{ $message }}</div>
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
                                        <label for="billing_type" class="form-label fw-semibold">Billing Type</label>
                                        <select id="billing_type" name="billing_type" class="form-select @error('billing_type') is-invalid @enderror" required>
                                            <option value="billable" {{ old('billing_type', $cloudDetail->billing_type ?? 'billable') === 'billable' ? 'selected' : '' }}>Billable</option>
                                            <option value="test" {{ old('billing_type', $cloudDetail->billing_type ?? '') === 'test' ? 'selected' : '' }}>Test</option>
                                        </select>
                                        @error('billing_type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
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
</x-app-layout>

