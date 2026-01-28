<x-app-layout>
    <div class="container-fluid custom-customer-index-container py-4">
        <!-- Background Elements -->
        <div class="custom-customer-index-bg-pattern"></div>
        <div class="custom-customer-index-bg-circle circle-1"></div>
        <div class="custom-customer-index-bg-circle circle-2"></div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="custom-customer-index-header">
                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-center justify-content-between gap-3">
                        <div>
                            <h1 class="custom-customer-index-title fw-bold mb-2">Resource Allocation</h1>
                            <p class="custom-customer-index-subtitle text-muted">
                                Manage customers, dismantle resources, or rewrite cloud details in real time.
                            </p>
                        </div>
                        <div id="header-action-container">
                            <a href="{{ route('customers.create') }}" id="add-customer-btn" class="btn btn-primary custom-customer-index-add-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                </svg>
                                Add New Customer
                            </a>
                            <div id="po-button-container" class="d-none ms-3">
                                <button type="button" class="btn btn-outline-primary fw-semibold" onclick="openPoSheetModal()">
                                    <i class="fas fa-file-pdf me-2"></i>Add PO Project Sheet
                                </button>
                            </div>
                        </div>
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

        <!-- Info Alert -->
        @if(session('info'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="custom-user-management-alert alert alert-info alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="me-3" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                        </svg>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1">Info</h6>
                            <p class="mb-0">{{ session('info') }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">

                        @if ($customers->isEmpty())
                        <p class="text-muted mb-0">No customers found. Please create a customer first.</p>
                        @else
                        <form method="POST" id="resource-action-form"
                            action="{{ route('resource-allocation.process') }}"
                            data-cloud-form-endpoint="{{ url('resource-allocation/customer') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="customer_id" class="form-label fw-semibold">Customer</label>
                                <select id="customer_id" name="customer_id" class="form-select form-select-lg @error('customer_id') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('customer_id') ? '' : 'selected' }}>Choose a customer</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        data-is-new="{{ $customer->is_new ? 'true' : 'false' }}"
                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->customer_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4" id="action-type-container">
                                <label for="action_type" class="form-label fw-semibold">Action</label>
                                <select id="action_type" name="action_type" class="form-select form-select-lg @error('action_type') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select Action</option>
                                    <option value="upgrade" {{ old('action_type') === 'upgrade' ? 'selected' : '' }}>Upgrade</option>
                                    <option value="downgrade" {{ old('action_type') === 'downgrade' ? 'selected' : '' }}>Downgrade</option>
                                    <option value="transfer" {{ old('action_type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                                </select>
                                @error('action_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 d-none" id="status-container">
                                <label for="status_id" class="form-label fw-semibold">Customer Status</label>
                                <select id="status_id" name="status_id" class="form-select form-select-lg @error('status_id') is-invalid @enderror">
                                    <option value="" disabled selected>Select Status</option>
                                    @foreach($customerStatuses as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('status_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 d-none" id="transfer-type-container">
                                <label for="transfer_type" class="form-label fw-semibold">Transfer Type</label>
                                <select id="transfer_type" name="transfer_type" class="form-select form-select-lg @error('transfer_type') is-invalid @enderror">
                                    <option value="" disabled selected>Select Transfer Type</option>
                                    <option value="test_to_billable" {{ old('transfer_type') === 'test_to_billable' ? 'selected' : '' }}>Test to Billable</option>
                                    <option value="billable_to_test" {{ old('transfer_type') === 'billable_to_test' ? 'selected' : '' }}>Billable to Test</option>
                                </select>
                                @error('transfer_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div id="cloud-detail-container" class="h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column justify-content-center text-center text-muted">
                            <p class="mb-0">Select a customer to load their cloud details form instantly.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <div id="resource-allocation-config" class="d-none"
        data-base-url="{{ url('resource-allocation') }}"
        data-index-url="{{ route('resource-allocation.index') }}"
        data-customer-base-url="{{ url('customers') }}"
        data-test-status-id="{{ \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 1 }}"
        data-new-customer-id="{{ session('new_customer_id') }}"
        data-asset-storage-url="{{ asset('storage') }}">
    </div>
    @vite(['resources/views/resource-allocation/resource-allocation.js'])
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