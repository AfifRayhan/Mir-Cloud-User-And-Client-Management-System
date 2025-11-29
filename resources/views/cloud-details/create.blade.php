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
                                <h5 class="fw-bold mb-3 text-primary">Resources & Services</h5>
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
                                            $currentValue = $cloudDetail && $columnName ? ($cloudDetail->{$columnName} ?? '') : '';
                                        @endphp
                                        
                                        <div class="col-md-6">
                                            <label for="service_{{ $service->id }}" class="form-label fw-semibold">
                                                {{ $service->service_name }}
                                            </label>
                                            <div class="input-group">
                                                <input 
                                                    type="number" 
                                                    min="0" 
                                                    class="form-control @error('services.' . $service->id) is-invalid @enderror" 
                                                    id="service_{{ $service->id }}" 
                                                    name="services[{{ $service->id }}]" 
                                                    value="{{ old('services.' . $service->id, $currentValue) }}"
                                                    placeholder="0"
                                                >
                                                @if($service->unit)
                                                    <span class="input-group-text">{{ $service->unit }}</span>
                                                @endif
                                            </div>
                                            @error('services.' . $service->id)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach
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
