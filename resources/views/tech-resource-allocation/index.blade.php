<x-app-layout>
    @push('styles')
    @vite(['resources/css/custom-customer-index.css', 'resources/css/custom-resource-allocation.css'])
    <style>
        .tech-kam-card {
            border-left: 4px solid var(--custom-customer-index-primary);
            transition: var(--custom-customer-index-transition);
        }

        .tech-kam-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--custom-customer-index-shadow-lg);
        }

        .tech-kam-avatar {
            width: 48px;
            height: 48px;
            background: var(--custom-customer-index-primary-light);
            color: var(--custom-customer-index-primary);
            font-size: 1.25rem;
        }
    </style>
    @endpush

    <div class="container-fluid custom-customer-index-container py-4">
        <!-- Background Elements -->
        <div class="custom-customer-index-bg-pattern"></div>
        <div class="custom-customer-index-bg-circle circle-1"></div>
        <div class="custom-customer-index-bg-circle circle-2"></div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="custom-customer-index-header">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                        <div>
                            <h1 class="custom-customer-index-title fw-bold mb-2">Tech Resource Management</h1>
                            <p class="custom-customer-index-subtitle text-muted">
                                Rapid resource allocation and automated task completion for tech users.
                            </p>
                        </div>
                        <div id="po-button-container" class="d-none">
                            <button type="button" class="btn btn-outline-primary fw-semibold" onclick="openPoSheetModal()">
                                <i class="fas fa-file-pdf me-2"></i>Add PO Project Sheet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex flex-column gap-4">
                    <!-- KAM Information (Set Automatically) -->
                    <div id="kam-info-container" style="display: none;">
                        <div class="card border-0 shadow-sm tech-kam-card">
                            <div class="card-body p-3">
                                <label class="form-label fw-bold text-muted small text-uppercase mb-3">Assigned KAM / Pro-KAM</label>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="tech-kam-avatar rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0 fw-bold" id="display-kam-name" style="color: #1a1f44;">---</h6>
                                        <small class="text-muted" id="display-kam-role">---</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Selection Form -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form id="tech-resource-action-form">
                                @csrf
                                <div class="mb-4">
                                    <label for="customer_id" class="form-label fw-semibold">Customer</label>
                                    <select id="customer_id" name="customer_id" class="form-select form-select-lg @error('customer_id') is-invalid @enderror" required>
                                        <option value="" disabled selected>Choose a customer</option>
                                        @foreach($customers as $customer)
                                        @php
                                        $roleName = $customer->submitter && $customer->submitter->role ? $customer->submitter->role->role_name : 'N/A';
                                        if (strtolower((string)$roleName) === 'kam') {
                                        $roleName = 'KAM';
                                        } elseif (strtolower((string)$roleName) === 'pro-kam') {
                                        $roleName = 'Pro-KAM';
                                        }
                                        @endphp
                                        <option value="{{ $customer->id }}"
                                            data-is-new="{{ $customer->is_new ? 'true' : 'false' }}"
                                            data-kam-name="{{ $customer->submitter ? $customer->submitter->name : 'N/A' }}"
                                            data-kam-role="{{ $roleName }}">
                                            {{ $customer->customer_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4" id="action-type-container">
                                    <label for="action_type" class="form-label fw-semibold">Action</label>
                                    <select id="action_type" name="action_type" class="form-select form-select-lg" required>
                                        <option value="" disabled selected>Select Action</option>
                                        <option value="upgrade">Upgrade</option>
                                        <option value="downgrade">Downgrade</option>
                                    </select>
                                </div>

                                <div class="mb-0 d-none" id="status-container">
                                    <label for="status_id" class="form-label fw-semibold">Customer Status</label>
                                    <select id="status_id" name="status_id" class="form-select form-select-lg">
                                        <option value="" disabled selected>Select Status</option>
                                        @foreach($customerStatuses as $status)
                                        <option value="{{ $status->id }}">
                                            {{ $status->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div id="cloud-detail-container" class="h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column justify-content-center text-center text-muted p-5">
                            <div class="mb-3">
                                <i class="fas fa-layer-group fa-3x opacity-25"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Ready to Allocate</h5>
                            <p class="mb-0">Select a customer and action to begin the resource allocation process.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VDC Selection Modal -->
    <div class="modal fade" id="vdcModal" tabindex="-1" aria-labelledby="vdcModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold" id="vdcModalLabel">
                        <i class="fas fa-server me-2"></i>Select VDC
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="vdcForm" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="source" value="tech_allocation">

                        <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info mb-4">
                            <i class="fas fa-info-circle me-2"></i> Select a VDC to finalize this allocation and notify the KAM.
                        </div>

                        <div class="mb-3">
                            <label for="vdc_select" class="form-label fw-semibold">Select Existing VDC</label>
                            <select id="vdc_select" name="vdc_id" class="form-select form-select-lg">
                                <option value="">-- Select VDC --</option>
                            </select>
                        </div>

                        <div class="text-center my-4 position-relative">
                            <hr>
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small fw-bold">OR</span>
                        </div>

                        <div class="text-center mb-4">
                            <button type="button" class="btn btn-outline-primary w-100" id="toggleNewVdc">
                                <i class="fas fa-plus me-1"></i> Add New VDC
                            </button>
                        </div>

                        <div id="newVdcContainer" class="mb-3" style="display: none;">
                            <label for="new_vdc_name" class="form-label fw-semibold">New VDC Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <input type="text" id="new_vdc_name" name="new_vdc_name" class="form-control form-control-lg" placeholder="Enter VDC name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 p-3">
                        <button type="button" class="btn btn-link text-muted fw-semibold text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="fas fa-check me-1"></i> Finalize Allocation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <div id="tech-resource-allocation-config" class="d-none"
        data-base-url="{{ url('tech-resource-allocation') }}"
        data-index-url="{{ route('tech-resource-allocation.index') }}"
        data-customer-base-url="{{ url('customers') }}"
        data-test-status-id="{{ \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 1 }}"
        data-new-customer-id="{{ session('new_customer_id') }}"
        data-asset-storage-url="{{ asset('storage') }}"
        data-csrf-token="{{ csrf_token() }}">
    </div>
    @vite(['resources/views/tech-resource-allocation/tech-resource-allocation.js'])
    @endpush
    @push('modals')
    <!-- PO Project Sheets Modal -->
    <div class="modal fade" id="poSheetModal" tabindex="-1" aria-labelledby="poSheetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="poSheetModalLabel">
                        <i class="fas fa-file-pdf text-primary me-2"></i>PO Project Sheets
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="poSheetModalBody">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endpush
</x-app-layout>