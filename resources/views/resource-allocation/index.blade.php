<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 fw-bold mb-1">Resource Allocation</h1>
                <p class="text-muted mb-0">Manage customers, dismantle resources, or rewrite cloud details in real time.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                {{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

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
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->customer_name }} @if($customer->hasResourceAllocations()) - Active Resources @else - No Resources @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="action_type" class="form-label fw-semibold">Action</label>
                                    <select id="action_type" name="action_type" class="form-select form-select-lg @error('action_type') is-invalid @enderror" required>
                                        <option value="" disabled selected>Select Action</option>
                                        <option value="upgrade" {{ old('action_type') === 'upgrade' ? 'selected' : '' }}>Upgrade</option>
                                        <option value="downgrade" {{ old('action_type') === 'downgrade' ? 'selected' : '' }}>Downgrade</option>
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
        <script>
            (function() {
                const customerSelect = document.getElementById('customer_id');
                const actionSelect = document.getElementById('action_type');
                const statusContainer = document.getElementById('status-container');
                const statusSelect = document.getElementById('status_id');
                const container = document.getElementById('cloud-detail-container');
                const resourceForm = document.getElementById('resource-action-form');

                if (!customerSelect || !actionSelect || !container) {
                    return;
                }

                function renderPlaceholder(message = 'Select customer and action to begin.') {
                    container.innerHTML = `
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center text-center text-muted">
                                <p class="mb-0">${message}</p>
                            </div>
                        </div>
                    `;
                }

                function toggleStatus() {
                    console.log('toggleStatus called, action:', actionSelect.value);
                    if (actionSelect.value === 'upgrade') {
                        statusContainer.classList.remove('d-none');
                        statusSelect.required = true;
                        console.log('Status container shown');
                    } else {
                        statusContainer.classList.add('d-none');
                        statusSelect.required = false;
                        statusSelect.value = "";
                        console.log('Status container hidden');
                    }
                }

                async function loadAllocationForm() {
                    const customerId = customerSelect.value;
                    const actionType = actionSelect.value;
                    const statusId = statusSelect.value;

                    if (!customerId || !actionType) {
                        renderPlaceholder();
                        return;
                    }

                    // For upgrade, require status selection
                    if (actionType === 'upgrade' && !statusId) {
                        renderPlaceholder('Please select a customer status to proceed with upgrade.');
                        return;
                    }

                    renderPlaceholder('Loading allocation form...');

                    try {
                        const url = new URL(`{{ url('resource-allocation') }}/${customerId}/allocate`);
                        url.searchParams.append('action_type', actionType);
                        if (statusId) {
                            url.searchParams.append('status_id', statusId);
                        }

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Request failed');
                        }

                        const data = await response.json();
                        container.innerHTML = data.html;

                        // Attach form submit handler
                        attachFormHandler();
                    } catch (error) {
                        console.error(error);
                        renderPlaceholder('Unable to load allocation form.');
                    }
                }

                // Global function to handle allocation form submission
                window.handleAllocationSubmit = async function(event, form) {
                    event.preventDefault();
                    console.log('Form submitted', form);

                    const formData = new FormData(form);
                    const customerId = form.dataset.customerId;
                    const actionType = form.dataset.actionType;
                    const statusId = form.dataset.statusId;

                    formData.append('action_type', actionType);
                    if (statusId) {
                        formData.append('status_id', statusId);
                    }

                    // Clear previous errors and messages
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
                    const successMsg = document.getElementById('allocation-success-message');
                    if (successMsg) {
                        successMsg.classList.add('d-none');
                        successMsg.classList.remove('show');
                    }

                    try {
                        const response = await fetch(`{{ url('resource-allocation') }}/${customerId}/allocate`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            // Handle validation errors
                            if (data.message) {
                                // Show error in the success message container (but as danger)
                                if (successMsg) {
                                    successMsg.classList.remove('alert-success', 'd-none');
                                    successMsg.classList.add('alert-danger', 'show');
                                    document.getElementById('success-message-text').textContent = data.message;
                                }
                            }
                            if (data.errors) {
                                Object.keys(data.errors).forEach(key => {
                                    const input = document.querySelector(`[name="${key}"]`);
                                    if (input) {
                                        input.classList.add('is-invalid');
                                        const feedback = input.nextElementSibling;
                                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                                            feedback.textContent = data.errors[key][0];
                                        }
                                    }
                                });
                            }
                            return false;
                        }

                        // Success - show inline message
                        if (successMsg) {
                            successMsg.classList.remove('alert-danger', 'd-none');
                            successMsg.classList.add('alert-success', 'show');
                            document.getElementById('success-message-text').textContent = data.message || 'Resources updated successfully!';
                            
                            // Scroll to the success message
                            successMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }

                        // Reset the form inputs
                        form.reset();

                        // Reload the allocation form to show updated values
                        setTimeout(() => {
                            loadAllocationForm();
                        }, 1500);

                        return false;
                    } catch (error) {
                        console.error(error);
                        if (successMsg) {
                            successMsg.classList.remove('alert-success', 'd-none');
                            successMsg.classList.add('alert-danger', 'show');
                            document.getElementById('success-message-text').textContent = 'An error occurred while saving the allocation.';
                        }
                        return false;
                    }
                };

                function attachFormHandler() {
                    // This function is no longer needed as we use inline handlers
                    console.log('attachFormHandler called (deprecated)');
                }

                // Event listeners
                actionSelect.addEventListener('change', function() {
                    toggleStatus();
                    loadAllocationForm();
                });

                customerSelect.addEventListener('change', function() {
                    if (actionSelect.value) {
                        loadAllocationForm();
                    }
                });

                statusSelect.addEventListener('change', function() {
                    if (actionSelect.value === 'upgrade') {
                        loadAllocationForm();
                    }
                });

                // Override form submission for dismantle
                resourceForm.addEventListener('submit', function(e) {
                    const actionType = actionSelect.value;
                    if (actionType !== 'dismantle') {
                        e.preventDefault();
                        // For upgrade/downgrade, the form is loaded via AJAX
                    }
                });

                // Global function to fill all downgrade inputs with current values (for dismantle)
                window.fillDismantleValues = function() {
                    const inputs = document.querySelectorAll('.downgrade-input');
                    inputs.forEach(input => {
                        const currentValue = input.getAttribute('data-current-value');
                        if (currentValue) {
                            input.value = currentValue;
                        }
                    });
                };

                // Global function for cancel button
                window.clearAllocationForm = function() {
                    renderPlaceholder();
                    customerSelect.value = '';
                    actionSelect.value = '';
                    statusSelect.value = '';
                    toggleStatus();
                };

                // Initialize
                toggleStatus();
                renderPlaceholder();
            })();
        </script>
    @endpush
</x-app-layout>

