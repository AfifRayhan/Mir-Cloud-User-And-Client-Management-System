<x-app-layout>
    <div class="container-fluid custom-customer-index-container py-4">
        <!-- Background Elements -->
        <div class="custom-customer-index-bg-pattern"></div>
        <div class="custom-customer-index-bg-circle circle-1"></div>
        <div class="custom-customer-index-bg-circle circle-2"></div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="custom-customer-index-header">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                        <div>
                            <h1 class="custom-customer-index-title fw-bold mb-2">Resource Allocation</h1>
                            <p class="custom-customer-index-subtitle text-muted">
                                Manage customers, dismantle resources, or rewrite cloud details in real time.
                            </p>
                        </div>
                        <a href="{{ route('customers.create') }}" class="btn btn-primary custom-customer-index-add-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                            </svg>
                            Add New Customer
                        </a>
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
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
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
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->customer_name }}
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

                        // If the backend provided a status_id, update the select
                        if (data.status_id && actionType === 'upgrade') {
                            statusSelect.value = data.status_id;
                        }

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

                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Confirm';
                    
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                    }

                    const formData = new FormData(form);
                    const customerId = form.dataset.customerId;
                    const actionType = form.dataset.actionType;
                    const statusId = form.dataset.statusId;

                    formData.append('action_type', actionType);
                    if (statusId) {
                        formData.append('status_id', statusId);
                    }

                    // Clear previous errors
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
                    
                    // Clear any existing page-level alerts
                    const existingAlerts = document.querySelectorAll('.custom-user-management-alert');
                    existingAlerts.forEach(alert => alert.remove());

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
                            // Re-enable button on error
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;
                            }

                            // Handle validation errors - show at top of page
                            if (data.message) {
                                showPageAlert('danger', 'Error!', data.message);
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

                        // Success - show page-level alert
                        showPageAlert('success', 'Success!', data.message || 'Resources updated successfully!');

                        // Reset the form inputs
                        form.reset();

                        // Reload the allocation form to show updated values
                        setTimeout(() => {
                            loadAllocationForm();
                            // Button remains disabled until reload to prevent double-submit during delay
                        }, 1500);

                        return false;
                    } catch (error) {
                        console.error(error);
                        // Re-enable button on error
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                        
                        showPageAlert('danger', 'Error!', 'An error occurred while saving the allocation.');
                        return false;
                    }
                };

                // Function to show page-level alert (matching User Management style)
                function showPageAlert(type, heading, message) {
                    const alertHtml = `
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="custom-user-management-alert alert alert-${type} alert-dismissible fade show" role="alert">
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="me-3" viewBox="0 0 16 16">
                                            ${type === 'success' 
                                                ? '<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />' 
                                                : '<path d="M8 8a1 1 0 0 1 1 1v.01a1 1 0 1 1-2 0V9a1 1 0 0 1 1-1zm.25-2.25a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0v-1.5z" /><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 0 1.5 0v-.25a.75.75 0 0 0-.75-.75h-.25a.75.75 0 0 0-.75.75V9a2 2 0 1 1-4 0v-.25a.75.75 0 0 0-.75-.75h-.25a.75.75 0 0 0-.75.75v.25a.75.75 0 0 0 1.5 0v-4.5A.75.75 0 0 1 8 4z" />'}
                                        </svg>
                                        <div class="flex-grow-1">
                                            <h6 class="alert-heading mb-1">${heading}</h6>
                                            <p class="mb-0">${message}</p>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Insert alert after the header section
                    const headerSection = document.querySelector('.custom-customer-index-header').parentElement.parentElement;
                    headerSection.insertAdjacentHTML('afterend', alertHtml);
                    
                    // Scroll to alert
                    const newAlert = document.querySelector('.custom-user-management-alert');
                    if (newAlert) {
                        newAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }

                function attachFormHandler() {
                    // This function is no longer needed as we use inline handlers
                    console.log('attachFormHandler called (deprecated)');
                }

                // Event listeners
                actionSelect.addEventListener('change', async function() {
                    toggleStatus();
                    
                    // For upgrade, auto-select previous status if available
                    if (actionSelect.value === 'upgrade' && customerSelect.value) {
                        try {
                            const url = new URL(`{{ url('resource-allocation') }}/${customerSelect.value}/allocate`);
                            url.searchParams.append('action_type', 'upgrade');
                            
                            const response = await fetch(url, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });
                            
                            if (response.ok) {
                                const data = await response.json();
                                // Extract status_id from the response HTML if available
                                const tempDiv = document.createElement('div');
                                tempDiv.innerHTML = data.html;
                                const statusBadge = tempDiv.querySelector('.badge.bg-primary');
                                
                                // Try to find matching status in dropdown
                                if (statusBadge) {
                                    const statusName = statusBadge.textContent.trim();
                                    const statusOptions = Array.from(statusSelect.options);
                                    const matchingOption = statusOptions.find(opt => opt.text.trim() === statusName);
                                    if (matchingOption) {
                                        statusSelect.value = matchingOption.value;
                                    }
                                }
                            }
                        } catch (error) {
                            console.error('Error fetching previous status:', error);
                        }
                    }
                    
                    loadAllocationForm();
                });

                customerSelect.addEventListener('change', function() {
                    // Get the selected option text to check if customer has resources
                    const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                    const optionText = selectedOption.text;
                    
                    // Auto-select upgrade if customer has no resources
                    if (optionText.includes('No Resources')) {
                        actionSelect.value = 'upgrade';
                        toggleStatus();
                    }
                    
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

                // Stepper functions for resource allocation inputs
                window.incrementValue = function(button) {
                    const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
                    const currentVal = parseInt(input.value) || 0;
                    const max = input.hasAttribute('max') ? parseInt(input.max) : Infinity;
                    
                    if (currentVal < max) {
                        input.value = currentVal + 1;
                        // Trigger input event for real-time updates
                        const event = new Event('input', { bubbles: true });
                        input.dispatchEvent(event);
                    }
                };

                window.decrementValue = function(button) {
                    const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
                    const currentVal = parseInt(input.value) || 0;
                    const min = parseInt(input.min) || 0;
                    
                    if (currentVal > min) {
                        input.value = currentVal - 1;
                        // Trigger input event for real-time updates
                        const event = new Event('input', { bubbles: true });
                        input.dispatchEvent(event);
                    }
                };

                // Update new total for upgrade
                window.updateNewTotal = function(input) {
                    const serviceId = input.dataset.serviceId;
                    const currentValue = parseInt(input.dataset.current) || 0;
                    const increaseBy = parseInt(input.value) || 0;
                    const newTotal = currentValue + increaseBy;
                    
                    const newTotalElement = document.querySelector(`[data-new-total-for="${serviceId}"]`);
                    if (newTotalElement) {
                        newTotalElement.textContent = newTotal;
                    }
                    
                    // Inline validation
                    if (increaseBy < 0) {
                        input.classList.add('is-invalid');
                        input.parentElement.classList.add('has-error');
                    } else {
                        input.classList.remove('is-invalid');
                        input.parentElement.classList.remove('has-error');
                    }
                };

                // Update new total for downgrade
                window.updateNewTotalDowngrade = function(input) {
                    const serviceId = input.dataset.serviceId;
                    const currentValue = parseInt(input.dataset.current) || 0;
                    const reduceBy = parseInt(input.value) || 0;
                    const newTotal = Math.max(0, currentValue - reduceBy);
                    
                    const newTotalElement = document.querySelector(`[data-new-total-for="${serviceId}"]`);
                    if (newTotalElement) {
                        newTotalElement.textContent = newTotal;
                    }
                    
                    // Inline validation - highlight if exceeds current value
                    if (reduceBy > currentValue) {
                        input.classList.add('is-invalid');
                        input.parentElement.classList.add('has-error');
                    } else {
                        input.classList.remove('is-invalid');
                        input.parentElement.classList.remove('has-error');
                    }
                };

                // Disable arrow keys and mouse wheel on stepper inputs
                document.addEventListener('DOMContentLoaded', function() {
                    // Use event delegation for dynamically loaded inputs
                    document.body.addEventListener('keydown', function(e) {
                        if (e.target.classList.contains('resource-alloc-stepper-input')) {
                            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                                e.preventDefault();
                            }
                        }
                    });
                    
                    document.body.addEventListener('wheel', function(e) {
                        if (e.target.classList.contains('resource-alloc-stepper-input')) {
                            e.preventDefault();
                        }
                    }, { passive: false });
                });

                // Initialize
                toggleStatus();
                renderPlaceholder();
                
                // Auto-select newly created customer if passed from session
                @if(session('new_customer_id'))
                    const newCustomerId = '{{ session('new_customer_id') }}';
                    if (newCustomerId) {
                        customerSelect.value = newCustomerId;
                        // Trigger change event to auto-select upgrade
                        customerSelect.dispatchEvent(new Event('change'));
                    }
                @endif
            })();
        </script>
    @endpush
</x-app-layout>

