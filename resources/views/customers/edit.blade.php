<x-app-layout>
    <div class="container-fluid custom-addcustomer-container py-4">
        <!-- Background decorative elements -->
        <div class="custom-addcustomer-bg-pattern"></div>
        <div class="custom-addcustomer-bg-circle circle-1"></div>
        <div class="custom-addcustomer-bg-circle circle-2"></div>
        <div class="custom-addcustomer-bg-circle circle-3"></div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="custom-addcustomer-header text-center mb-4">
                    <h1 class="custom-addcustomer-title fw-bold mb-2">Edit Customer</h1>
                    <p class="custom-addcustomer-subtitle text-muted">
                        Update customer details.
                    </p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card custom-addcustomer-card border-0 shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <!-- Error Alert -->
                        @if ($errors->any())
                            <div class="alert alert-danger custom-addcustomer-alert mb-4">
                                <p class="mb-2 fw-semibold">Please fix the following:</p>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Form -->
                        <form method="POST" action="{{ route('customers.update', $customer->id) }}" id="customerForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Customer Overview Section -->
                            <div class="custom-addcustomer-section mb-5">
                                <div class="custom-addcustomer-section-header mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="custom-addcustomer-section-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                            </svg>
                                        </div>
                                        <h5 class="custom-addcustomer-section-title mb-0 fw-bold text-primary">Customer Overview</h5>
                                    </div>
                                    <span class="custom-addcustomer-section-badge">Required Fields</span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="customer_name" class="form-label fw-semibold custom-addcustomer-label">
                                            Customer Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-lg custom-addcustomer-input @error('customer_name') is-invalid @enderror" 
                                               id="customer_name" 
                                               name="customer_name" 
                                               value="{{ old('customer_name', $customer->customer_name) }}" 
                                               required 
                                               autofocus>
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="customer_activation_date" class="form-label fw-semibold custom-addcustomer-label">
                                            Customer Activation Date <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" 
                                                   class="form-control form-control-lg custom-addcustomer-input flatpickr-date @error('customer_activation_date') is-invalid @enderror" 
                                                   id="customer_activation_date" 
                                                   name="customer_activation_date" 
                                                   value="{{ old('customer_activation_date', $customer->customer_activation_date->format('Y-m-d')) }}" 
                                                   required>
                                        </div>
                                        @error('customer_activation_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="customer_address" class="form-label fw-semibold custom-addcustomer-label">
                                            Customer Address
                                        </label>
                                        <textarea class="form-control custom-addcustomer-textarea @error('customer_address') is-invalid @enderror" 
                                                  id="customer_address" 
                                                  name="customer_address" 
                                                  rows="3" 
                                                  placeholder="Street, city, country">{{ old('customer_address', $customer->customer_address) }}</textarea>
                                        @error('customer_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="platform_id" class="form-label fw-semibold custom-addcustomer-label">
                                            Platform <span class="text-danger">*</span>
                                        </label>
                                        <select id="platform_id" 
                                                name="platform_id" 
                                                class="form-select custom-addcustomer-select @error('platform_id') is-invalid @enderror"
                                                required>
                                            <option value="" disabled>Select Platform</option>
                                            @foreach($platforms as $platform)
                                                <option value="{{ $platform->id }}" {{ old('platform_id', $customer->platform_id) == $platform->id ? 'selected' : '' }}>
                                                    {{ $platform->platform_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('platform_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="bin_number" class="form-label fw-semibold custom-addcustomer-label">
                                            BIN Number
                                        </label>
                                        <input type="text" 
                                               class="form-control custom-addcustomer-input @error('bin_number') is-invalid @enderror" 
                                               id="bin_number" 
                                               name="bin_number" 
                                               value="{{ old('bin_number', $customer->bin_number) }}" 
                                               placeholder="Enter BIN number">
                                        @error('bin_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Original PO Number Field -->
                                    <div class="col-md-6">
                                        <label for="po_number" class="form-label fw-semibold custom-addcustomer-label">
                                            PO Number
                                        </label>
                                        <input type="text" 
                                               class="form-control custom-addcustomer-input @error('po_number') is-invalid @enderror" 
                                               id="po_number" 
                                               name="po_number" 
                                               value="{{ old('po_number', $customer->po_number) }}" 
                                               placeholder="Enter PO number">
                                        @error('po_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Added PO Project Sheet Field -->
                                    <div class="col-md-6">
                                        <label for="po_project_sheets" class="form-label fw-semibold custom-addcustomer-label">
                                            PO Project Sheet (Add New PDFs)
                                        </label>
                                        <input type="file" 
                                               class="form-control custom-addcustomer-input @error('po_project_sheets') is-invalid @enderror" 
                                               id="po_project_sheets" 
                                               name="po_project_sheets[]" 
                                               accept=".pdf"
                                               multiple>
                                        <div class="form-text small opacity-75">
                                            <i class="fas fa-info-circle me-1"></i> Multiple PDFs allowed. Files will be optimized for storage.
                                        </div>
                                        @error('po_project_sheets.*')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        <!-- Existing Files -->
                                        @if($customer->po_project_sheets && count($customer->po_project_sheets) > 0)
                                            <div class="mt-3">
                                                <label class="form-label fw-semibold small text-muted text-uppercase">Existing Sheets</label>
                                                <div id="existing-sheets-container">
                                                    @foreach($customer->po_project_sheets as $index => $sheet)
                                                        <div class="d-flex align-items-center justify-content-between p-2 mb-2 bg-light rounded border border-light-subtle existing-sheet-item" data-index="{{ $index }}">
                                                            <div class="d-flex align-items-center overflow-hidden">
                                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                                <span class="text-truncate small fw-medium" title="{{ $sheet['name'] }}">{{ $sheet['name'] }}</span>
                                                                <span class="ms-2 badge bg-secondary-subtle text-secondary x-small" style="font-size: 0.65rem;">{{ round($sheet['size'] / 1024, 2) }} KB</span>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 py-0 remove-sheet-btn" onclick="removeExistingSheet({{ $index }})">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                            <input type="hidden" name="removed_sheets[]" id="removed-sheet-{{ $index }}" value="" disabled>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Commercial Contact Section -->
                            <div class="custom-addcustomer-section mb-4">
                                <div class="custom-addcustomer-section-header mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="custom-addcustomer-section-icon bg-primary bg-opacity-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#4f46e5" viewBox="0 0 16 16">
                                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm5.5 10a.5.5 0 0 0 .832.374l4.5-4a.5.5 0 0 0 0-.748l-4.5-4A.5.5 0 0 0 5.5 4v8z"/>
                                            </svg>
                                        </div>
                                        <h5 class="custom-addcustomer-section-title mb-0 fw-bold text-primary">Commercial Contact</h5>
                                    </div>
                                    <span class="badge custom-addcustomer-section-badge bg-light text-dark">Sales / Billing</span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="commercial_contact_name">Name</label>
                                        <input type="text" 
                                               class="form-control custom-addcustomer-input" 
                                               id="commercial_contact_name" 
                                               name="commercial_contact_name" 
                                               value="{{ old('commercial_contact_name', $customer->commercial_contact_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="commercial_contact_designation">Designation</label>
                                        <input type="text" 
                                               class="form-control custom-addcustomer-input" 
                                               id="commercial_contact_designation" 
                                               name="commercial_contact_designation" 
                                               value="{{ old('commercial_contact_designation', $customer->commercial_contact_designation) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="commercial_contact_email">Email</label>
                                        <input type="email" 
                                               class="form-control custom-addcustomer-input" 
                                               id="commercial_contact_email" 
                                               name="commercial_contact_email" 
                                               value="{{ old('commercial_contact_email', $customer->commercial_contact_email) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="commercial_contact_phone">Phone</label>
                                        <input type="text" 
                                               class="form-control custom-addcustomer-input" 
                                               id="commercial_contact_phone" 
                                               name="commercial_contact_phone" 
                                               value="{{ old('commercial_contact_phone', $customer->commercial_contact_phone) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Technical Contact Section -->
                            <div class="custom-addcustomer-section mb-4">
                                <div class="custom-addcustomer-section-header mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="custom-addcustomer-section-icon bg-success bg-opacity-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#10b981" viewBox="0 0 16 16">
                                                <path d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5z"/>
                                                <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                                            </svg>
                                        </div>
                                        <h5 class="custom-addcustomer-section-title mb-0 fw-bold text-primary">Technical Contact</h5>
                                    </div>
                                    <span class="badge custom-addcustomer-section-badge bg-light text-dark">Operations</span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="technical_contact_name">Name</label>
                                        <input type="text" 
                                               class="form-control custom-addcustomer-input" 
                                               id="technical_contact_name" 
                                               name="technical_contact_name" 
                                               value="{{ old('technical_contact_name', $customer->technical_contact_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="technical_contact_designation">Designation</label>
                                        <input type="text" 
                                               class="form-control custom-addcustomer-input" 
                                               id="technical_contact_designation" 
                                               name="technical_contact_designation" 
                                               value="{{ old('technical_contact_designation', $customer->technical_contact_designation) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="technical_contact_email">Email</label>
                                        <input type="email" 
                                               class="form-control custom-addcustomer-input" 
                                               id="technical_contact_email" 
                                               name="technical_contact_email" 
                                               value="{{ old('technical_contact_email', $customer->technical_contact_email) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="technical_contact_phone">Phone</label>
                                        <input type="text" 
                                               class="form-control custom-addcustomer-input" 
                                               id="technical_contact_phone" 
                                               name="technical_contact_phone" 
                                               value="{{ old('technical_contact_phone', $customer->technical_contact_phone) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Optional Contact Section -->
                            <div class="custom-addcustomer-section mb-5">
                                <div class="custom-addcustomer-section-header mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="custom-addcustomer-section-icon bg-secondary bg-opacity-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#6b7280" viewBox="0 0 16 16">
                                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                            </svg>
                                        </div>
                                        <h5 class="custom-addcustomer-section-title mb-0 fw-bold text-primary">Optional Contact</h5>
                                    </div>
                                    <span class="badge custom-addcustomer-section-badge bg-light text-dark">Optional</span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="optional_contact_name">Name</label>
                                        <input type="text" 
                                               class="form-control custom-addcustomer-input" 
                                               id="optional_contact_name" 
                                               name="optional_contact_name" 
                                               value="{{ old('optional_contact_name', $customer->optional_contact_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="optional_contact_designation">Designation</label>
                                        <input type="text" 
                                               class="form-control custom-addcustomer-input" 
                                               id="optional_contact_designation" 
                                               name="optional_contact_designation" 
                                               value="{{ old('optional_contact_designation', $customer->optional_contact_designation) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="optional_contact_email">Email</label>
                                        <input type="email" 
                                               class="form-control custom-addcustomer-input" 
                                               id="optional_contact_email" 
                                               name="optional_contact_email" 
                                               value="{{ old('optional_contact_email', $customer->optional_contact_email) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold custom-addcustomer-label" for="optional_contact_phone">Phone</label>
                                        <input type="text" 
                                               class="form-control custom-addcustomer-input" 
                                               id="optional_contact_phone" 
                                               name="optional_contact_phone" 
                                               value="{{ old('optional_contact_phone', $customer->optional_contact_phone) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-2 pt-3 custom-addcustomer-actions">
                                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary custom-addcustomer-cancel-btn">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4 custom-addcustomer-submit-btn">
                                    Update Customer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function removeExistingSheet(index) {
            const item = document.querySelector(`.existing-sheet-item[data-index="${index}"]`);
            const input = document.getElementById(`removed-sheet-${index}`);
            
            if (item && input) {
                if (confirm('Are you sure you want to remove this sheet? It will be deleted permanently when you save.')) {
                    item.style.display = 'none';
                    input.value = index;
                    input.disabled = false;
                }
            }
        }
    </script>
</x-app-layout>
