<x-app-layout>
    <div class="container-fluid custom-service-management-container py-2">
        <!-- Background Elements -->
        <div class="custom-service-management-bg-pattern"></div>
        <div class="custom-service-management-bg-circle circle-1"></div>
        <div class="custom-service-management-bg-circle circle-2"></div>

        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="custom-service-management-header">
                    <div>
                        <h1 class="custom-service-management-title fw-bold mb-2">Service Management</h1>
                        <p class="custom-service-management-subtitle text-muted">
                            Define reusable services like vCPU, Memory, SAS, SSD, BS, PI, SS, EIP, VPN, BDIX, BW, and more.
                        </p>
                    </div>
                </div>
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
        @if($errors->any())
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
                            <p class="mb-0">{{ $errors->first() }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        @endif

        <div class="row g-4">
            <!-- Create/Edit Service Form -->
            <div class="col-lg-4">
                <div class="custom-service-management-card">
                    <div class="custom-service-management-card-header">
                        <h5 class="custom-service-management-card-title">
                            {{ isset($editableService) ? 'Update Service' : 'Create Service' }}
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <form method="POST" action="{{ isset($editableService) ? route('services.update', $editableService) : route('services.store') }}">
                            @csrf
                            @if(isset($editableService))
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label for="platform_id" class="form-label">Platform</label>
                                <select id="platform_id" name="platform_id" 
                                        class="form-select @error('platform_id') is-invalid @enderror" required>
                                    <option value="">Select Platform</option>
                                    @foreach($platforms as $platform)
                                        <option value="{{ $platform->id }}" 
                                            {{ old('platform_id', $editableService->platform_id ?? '') == $platform->id ? 'selected' : '' }}>
                                            {{ $platform->platform_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('platform_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="service_name" class="form-label">Service Name</label>
                                <input type="text" id="service_name" name="service_name" 
                                       class="form-control @error('service_name') is-invalid @enderror" 
                                       value="{{ old('service_name', $editableService->service_name ?? '') }}" 
                                       placeholder="e.g. Memory, SAS, EIP" required autofocus>
                                @error('service_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="unit" class="form-label">Unit (optional)</label>
                                <input type="text" id="unit" name="unit" 
                                       class="form-control @error('unit') is-invalid @enderror" 
                                       value="{{ old('unit', $editableService->unit ?? '') }}" 
                                       placeholder="e.g. GB, core">
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="unit_price" class="form-label">Unit Price (optional)</label>
                                <input type="number" step="0.01" min="0" id="unit_price" name="unit_price" 
                                       class="form-control @error('unit_price') is-invalid @enderror" 
                                       value="{{ old('unit_price', $editableService->unit_price ?? '') }}">
                                @error('unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                @if(isset($editableService))
                                    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary w-100">
                                        Cancel
                                    </a>
                                @endif
                                <button type="submit" class="custom-service-management-save-btn w-100">
                                    <i class="fas fa-save me-2"></i> {{ isset($editableService) ? 'Update' : 'Save' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Services Table -->
            <div class="col-lg-8">
                <div class="custom-service-management-card">
                    <div class="custom-service-management-card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <h5 class="custom-service-management-card-title mb-0">Configured Services</h5>
                        
                        <div class="d-flex align-items-center gap-3 flex-grow-1 flex-md-grow-0">
                            <!-- Platform Filter -->
                            <form action="{{ route('services.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                <label for="filter_platform_id" class="text-muted small text-nowrap mb-0">Filter by Platform:</label>
                                <select name="platform_id" id="filter_platform_id" class="form-select form-select-sm border-0 bg-light shadow-none" style="min-width: 140px;" onchange="this.form.submit()">
                                    <option value="">All Platforms</option>
                                    @foreach($platforms as $platform)
                                        <option value="{{ $platform->id }}" {{ request('platform_id') == $platform->id ? 'selected' : '' }}>
                                            {{ $platform->platform_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>

                            <div class="vr mx-1 d-none d-md-block" style="height: 20px; opacity: 0.1;"></div>

                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted small text-nowrap">Total Services:</span>
                                <span class="custom-service-management-stat-number">{{ $services->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($services->isEmpty())
                            <div class="p-5 text-center text-muted">
                                <i class="fas fa-tools mb-3 opacity-25 fa-3x"></i>
                                <p>No services configured yet.</p>
                            </div>
                        @else
                            <div class="custom-service-management-table-responsive">
                                <table class="custom-service-management-table">
                                    <thead class="custom-service-management-table-head">
                                        <tr>
                                            <th class="custom-service-management-table-header">
                                                <i class="fas fa-layer-group me-2"></i>Platform
                                            </th>
                                            <th class="custom-service-management-table-header">
                                                <i class="fas fa-tag me-2"></i>Service
                                            </th>
                                            <th class="custom-service-management-table-header">
                                                <i class="fas fa-balance-scale me-2"></i>Unit
                                            </th>
                                            <th class="custom-service-management-table-header">
                                                <i class="fas fa-dollar-sign me-2"></i>Price
                                            </th>
                                            <th class="custom-service-management-table-header">
                                                <i class="fas fa-user-edit me-2"></i>Inserted By
                                            </th>
                                            <th class="custom-service-management-table-header text-end">
                                                <i class="fas fa-cogs me-2"></i>Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="custom-service-management-table-body">
                                        @foreach($services as $service)
                                            <tr class="custom-service-management-table-row">
                                                <td class="custom-service-management-table-cell">
                                                    <span class="badge bg-light text-primary border border-primary-subtle px-2 py-1">
                                                        {{ $service->platform->platform_name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="custom-service-management-table-cell">
                                                    <strong>{{ $service->service_name }}</strong>
                                                </td>
                                                <td class="custom-service-management-table-cell">
                                                    {{ $service->unit ?? '—' }}
                                                </td>
                                                <td class="custom-service-management-table-cell">
                                                    {{ $service->unit_price ? number_format($service->unit_price, 2) : '—' }}
                                                </td>
                                                <td class="custom-service-management-table-cell text-muted small">
                                                    {{ $service->insertedBy->name ?? 'System' }}
                                                </td>
                                                <td class="custom-service-management-table-cell text-end">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('services.index', ['service' => $service->id]) }}" 
                                                           class="custom-service-management-action-btn custom-service-management-edit-btn">
                                                            <i class="fas fa-edit me-1"></i><span>Edit</span>
                                                        </a>
                                                        <button type="button" 
                                                                class="custom-service-management-action-btn custom-service-management-delete-btn"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deleteServiceModal{{ $service->id }}">
                                                            <i class="fas fa-trash-alt me-1"></i><span>Delete</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modals -->
    @foreach($services as $service)
    <div class="modal fade" id="deleteServiceModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Delete Service
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-trash-alt fa-3x text-danger opacity-25"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Are you sure?</h5>
                    <p class="text-muted mb-0">
                        Are you sure you want to delete <strong>{{ $service->service_name }}</strong>?
                    </p>
                    <p class="text-danger small mt-2">
                        <i class="fas fa-info-circle me-1"></i>This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('services.destroy', $service) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</x-app-layout>

