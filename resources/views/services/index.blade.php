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
                            Define reusable services like vCPU, RAM, Storage, and more.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="custom-service-management-alert">
                <i class="fas fa-check-circle me-2 text-success"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="custom-service-management-alert custom-service-management-alert-error">
                <i class="fas fa-exclamation-circle me-2 text-danger"></i>
                <span>{{ $errors->first() }}</span>
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
                                <label for="service_name" class="form-label">Service Name</label>
                                <input type="text" id="service_name" name="service_name" 
                                       class="form-control @error('service_name') is-invalid @enderror" 
                                       value="{{ old('service_name', $editableService->service_name ?? '') }}" 
                                       placeholder="e.g. vCPU" required autofocus>
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
                    <div class="custom-service-management-card-header d-flex justify-content-between align-items-center">
                        <h5 class="custom-service-management-card-title">Configured Services</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Total Services:</span>
                            <span class="custom-service-management-stat-number">{{ $services->count() }}</span>
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
                                                        <form method="POST" action="{{ route('services.destroy', $service) }}" 
                                                              class="d-inline" onsubmit="return confirm('Delete this service?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="custom-service-management-action-btn custom-service-management-delete-btn">
                                                                <i class="fas fa-trash-alt me-1"></i><span>Delete</span>
                                                            </button>
                                                        </form>
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
</x-app-layout>

