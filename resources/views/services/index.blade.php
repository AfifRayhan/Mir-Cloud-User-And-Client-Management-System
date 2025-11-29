<x-app-layout>
    <div class="container-fluid py-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Service Management</h1>
                <p class="text-muted mb-0">Define reusable services like vCPU, RAM, Storage, and more.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            {{ isset($editableService) ? 'Update Service' : 'Create Service' }}
                        </h5>
                        <form method="POST" action="{{ isset($editableService) ? route('services.update', $editableService) : route('services.store') }}">
                            @csrf
                            @if(isset($editableService))
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label for="service_name" class="form-label fw-semibold">Service Name</label>
                                <input
                                    type="text"
                                    id="service_name"
                                    name="service_name"
                                    class="form-control @error('service_name') is-invalid @enderror"
                                    value="{{ old('service_name', $editableService->service_name ?? '') }}"
                                    placeholder="e.g. vCPU"
                                    required
                                >
                                @error('service_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="unit" class="form-label fw-semibold">Unit (optional)</label>
                                <input
                                    type="text"
                                    id="unit"
                                    name="unit"
                                    class="form-control @error('unit') is-invalid @enderror"
                                    value="{{ old('unit', $editableService->unit ?? '') }}"
                                    placeholder="e.g. GB, core"
                                >
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="unit_price" class="form-label fw-semibold">Unit Price (optional)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    id="unit_price"
                                    name="unit_price"
                                    class="form-control @error('unit_price') is-invalid @enderror"
                                    value="{{ old('unit_price', $editableService->unit_price ?? '') }}"
                                >
                                @error('unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                @if(isset($editableService))
                                    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                @endif
                                <button type="submit" class="btn btn-primary ms-auto">
                                    {{ isset($editableService) ? 'Update Service' : 'Save Service' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Configured Services</h5>
                        @if($services->isEmpty())
                            <p class="text-muted mb-0">No services configured yet.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Service</th>
                                            <th scope="col">Unit</th>
                                            <th scope="col">Unit Price</th>
                                            <th scope="col">Inserted By</th>
                                            <th scope="col" class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($services as $service)
                                            <tr>
                                                <td class="fw-semibold">{{ $service->service_name }}</td>
                                                <td>{{ $service->unit ?? '—' }}</td>
                                                <td>{{ $service->unit_price ? number_format($service->unit_price, 2) : '—' }}</td>
                                                <td>{{ $service->insertedBy->name ?? 'System' }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('services.index', ['service' => $service->id]) }}" class="btn btn-sm btn-outline-primary me-2">
                                                        Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('services.destroy', $service) }}" class="d-inline" onsubmit="return confirm('Delete this service?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                    </form>
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

