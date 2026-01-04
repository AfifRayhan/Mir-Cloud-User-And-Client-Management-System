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
                                            <option value="{{ $customer->id }}" 
                                                    data-is-new="{{ $customer->is_new ? 'true' : 'false' }}"
                                                    data-kam-name="{{ $customer->submitter ? $customer->submitter->name : 'N/A' }}"
                                                    data-kam-role="{{ $customer->submitter && $customer->submitter->role ? $customer->submitter->role->role_name : 'N/A' }}">
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
                        <i class="fas fa-server me-2"></i>Complete Allocation
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
        <script>
            (function() {
                const kamInfoContainer = document.getElementById('kam-info-container');
                const displayKamName = document.getElementById('display-kam-name');
                const displayKamRole = document.getElementById('display-kam-role');
                const actionTypeContainer = document.getElementById('action-type-container');
                const customerSelect = document.getElementById('customer_id');
                const actionSelect = document.getElementById('action_type');
                const statusContainer = document.getElementById('status-container');
                const statusSelect = document.getElementById('status_id');
                const container = document.getElementById('cloud-detail-container');
                const testStatusId = {{ \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 1 }};

                function renderPlaceholder(message = 'Select options to begin.', icon = 'fas fa-layer-group') {
                    if (!container) return;
                    container.innerHTML = `
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center text-center text-muted p-5">
                                <div class="mb-3">
                                    <i class="${icon} fa-3x opacity-25"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Ready to Allocate</h5>
                                <p class="mb-0">${message}</p>
                            </div>
                        </div>
                    `;
                }

                function toggleStatus() {
                    if (!actionSelect || !statusContainer) return;
                    if (actionSelect.value === 'upgrade' || actionSelect.value === 'downgrade') {
                        statusContainer.classList.remove('d-none');
                        statusSelect.required = true;
                    } else {
                        statusContainer.classList.add('d-none');
                        statusSelect.required = false;
                        statusSelect.value = "";
                    }
                }

                function updateKamInfo() {
                    if (!customerSelect || !kamInfoContainer) return;
                    const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                    
                    if (selectedOption && selectedOption.value) {
                        const kamName = selectedOption.getAttribute('data-kam-name');
                        const kamRole = selectedOption.getAttribute('data-kam-role');
                        
                        displayKamName.textContent = kamName;
                        displayKamRole.textContent = kamRole;
                        kamInfoContainer.style.display = 'block';
                    } else {
                        kamInfoContainer.style.display = 'none';
                    }
                }

                async function loadAllocationForm() {
                    if (!customerSelect || !actionSelect) return;
                    const customerId = customerSelect.value;
                    const actionType = actionSelect.value;
                    const statusId = statusSelect.value;

                    if (!customerId || !actionType) {
                        renderPlaceholder();
                        return;
                    }

                    if (actionType === 'upgrade' && !statusId) {
                        renderPlaceholder('Please select a customer status to proceed with upgrade.', 'fas fa-info-circle');
                        return;
                    }

                    renderPlaceholder('Loading allocation form...', 'fas fa-circle-notch fa-spin');

                    try {
                        const url = new URL(`{{ url('tech-resource-allocation') }}/${customerId}/allocate`);
                        url.searchParams.append('action_type', actionType);
                        if (statusId) {
                            url.searchParams.append('status_id', statusId);
                        }
                        url.searchParams.append('_t', new Date().getTime());

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) throw new Error('Request failed');

                        const data = await response.json();
                        container.innerHTML = data.html;

                        // Sync status dropdown if server returned a default status
                        if (data.status_id && statusSelect) {
                            statusSelect.value = data.status_id;
                        }
                        
                        // Initialize flatpickr on new inputs with Friday/Saturday restriction
                        if (window.initializeGlobalFlatpickr) {
                            window.initializeGlobalFlatpickr();
                        } else if (window.flatpickr) {
                            flatpickr(".flatpickr-date", {
                                dateFormat: "Y-m-d",
                                allowInput: true,
                                disable: [
                                    function(date) {
                                        // 5 is Friday, 6 is Saturday
                                        return (date.getDay() === 5 || date.getDay() === 6);
                                    }
                                ],
                                locale: {
                                    firstDayOfWeek: 0 // Sunday
                                },
                                onDayCreate: function(dObj, dStr, fp, dayElem) {
                                    if (dayElem.dateObj.getDay() === 5 || dayElem.dateObj.getDay() === 6) {
                                        dayElem.classList.add("blurred-weekend");
                                        dayElem.title = "Weekend (Friday/Saturday) is disabled";
                                    }
                                }
                            });
                        }

                        // Re-bind tooltips
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                        tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl)
                        });
                    } catch (error) {
                        console.error(error);
                        renderPlaceholder('Unable to load allocation form. Please check your connection.', 'fas fa-exclamation-triangle');
                    }
                }

                // VDC Modal Logic
                let vdcModalInstance = null;
                const vdcModalEl = document.getElementById('vdcModal');
                const vdcForm = document.getElementById('vdcForm');
                const vdcSelect = document.getElementById('vdc_select');
                const toggleNewVdcBtn = document.getElementById('toggleNewVdc');
                const newVdcContainer = document.getElementById('newVdcContainer');
                const newVdcInput = document.getElementById('new_vdc_name');

                let currentTaskId = null;
                let currentCustomerId = null;

                window.openVdcModal = function(taskId, customerId) {
                    currentTaskId = taskId;
                    currentCustomerId = customerId;

                    loadCustomerVdcs(currentCustomerId);

                    vdcSelect.value = '';
                    newVdcInput.value = '';
                    newVdcContainer.style.display = 'none';
                    
                    if (!vdcModalInstance && window.bootstrap) {
                        vdcModalInstance = new bootstrap.Modal(vdcModalEl);
                    }
                    if (vdcModalInstance) vdcModalInstance.show();
                };

                if (toggleNewVdcBtn) {
                    toggleNewVdcBtn.addEventListener('click', function() {
                        if (newVdcContainer.style.display === 'none') {
                            newVdcContainer.style.display = 'block';
                            vdcSelect.value = '';
                            newVdcInput.focus();
                        } else {
                            newVdcContainer.style.display = 'none';
                            newVdcInput.value = '';
                        }
                    });
                }

                function loadCustomerVdcs(customerId) {
                    fetch(`/my-tasks/customer/${customerId}/vdcs?_t=${new Date().getTime()}`)
                        .then(response => response.json())
                        .then(data => {
                            vdcSelect.innerHTML = '<option value="">-- Select VDC --</option>';
                            data.vdcs.forEach(vdc => {
                                const option = document.createElement('option');
                                option.value = vdc.id;
                                option.textContent = vdc.vdc_name;
                                vdcSelect.appendChild(option);
                            });
                            if (data.vdcs.length > 0) vdcSelect.value = data.vdcs[0].id;
                        })
                        .catch(error => console.error('Error loading VDCs:', error));
                }

                if (vdcForm) {
                    vdcForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalBtnText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i> Processing...';

                        fetch(`/my-tasks/${currentTaskId}/complete`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) return response.json().then(err => { throw new Error(err.message || 'Server Error'); });
                            return response.json();
                        })
                        .then(data => {
                            if (vdcModalInstance) vdcModalInstance.hide();
                            
                            // Refresh customer dropdown via AJAX
                            setTimeout(async () => {
                                try {
                                    const response = await fetch('{{ route("tech-resource-allocation.index") }}', {
                                        headers: {
                                            'Accept': 'text/html',
                                            'X-Requested-With': 'XMLHttpRequest',
                                        }
                                    });
                                    
                                    if (response.ok) {
                                        const html = await response.text();
                                        const tempDiv = document.createElement('div');
                                        tempDiv.innerHTML = html;
                                        
                                        const newSelect = tempDiv.querySelector('#customer_id');
                                        if (newSelect && customerSelect) {
                                            const currentCustomerIdValue = customerSelect.value;
                                            customerSelect.innerHTML = newSelect.innerHTML;
                                            
                                            if (currentCustomerIdValue) {
                                                customerSelect.value = currentCustomerIdValue;
                                                const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                                                if (selectedOption) {
                                                    selectedOption.setAttribute('data-is-new', 'false');
                                                }
                                            }
                                        }
                                    }
                                    
                                    loadAllocationForm();
                                    showPageAlert('success', 'Success!', 'Allocation completed and task finalized successfully.');
                                } catch (error) {
                                    console.error('Error refreshing customer list:', error);
                                    loadAllocationForm();
                                    showPageAlert('success', 'Success!', 'Allocation completed successfully.');
                                }
                            }, 500);
                        })
                        .catch(error => {
                            console.error('Error completing task:', error);
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                            showPageAlert('danger', 'Error!', error.message || 'Error completing task. Please try again.');
                        });
                    });
                }

                // Global function for form submission in the partial
                window.handleAllocationSubmit = async function(event, form) {
                    event.preventDefault();
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Confirm';
                    
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                    }

                    const formData = new FormData(form);
                    formData.append('action_type', actionSelect.value);
                    if (statusSelect.value) formData.append('status_id', statusSelect.value);

                    try {
                        const response = await fetch(`{{ url('tech-resource-allocation') }}/${customerSelect.value}/allocate`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: formData
                        });

                        const data = await response.json();
                        if (response.ok) {
                            window.openVdcModal(data.task_id, data.customer_id);
                        } else {
                            showPageAlert('danger', 'Error!', data.message || 'Validation failed. Please check your inputs.');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;
                            }
                        }
                    } catch (error) {
                        console.error(error);
                        showPageAlert('danger', 'Error!', 'An unexpected error occurred while processing the allocation.');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    }
                    return false;
                };

                // Alert Utility
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
                    
                    const headerSection = document.querySelector('.custom-customer-index-header').parentElement.parentElement;
                    headerSection.insertAdjacentHTML('afterend', alertHtml);
                    
                    const newAlert = document.querySelector('.custom-user-management-alert');
                    if (newAlert) {
                        newAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }

                // Input Event Listeners
                if (actionSelect) actionSelect.addEventListener('change', () => { toggleStatus(); loadAllocationForm(); });
                if (customerSelect) {
                    customerSelect.addEventListener('change', function() {
                        updateKamInfo();
                        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                        if (selectedOption && selectedOption.value) {
                            const isNew = selectedOption.getAttribute('data-is-new') === 'true';

                            if (isNew) {
                                actionTypeContainer.style.display = 'none';
                                actionSelect.value = 'upgrade';
                                toggleStatus();
                            } else {
                                actionTypeContainer.style.display = 'block';
                            }
                            
                            if (actionSelect.value) loadAllocationForm();
                        } else {
                            renderPlaceholder();
                        }
                    });
                }
                if (statusSelect) statusSelect.addEventListener('change', loadAllocationForm);

                // Global helpers for partials
                window.incrementValue = function(button) {
                    const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
                    const max = input.hasAttribute('max') ? parseInt(input.max) : Infinity;
                    const val = parseInt(input.value) || 0;
                    if (val < max) {
                        input.value = val + 1;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                };
                window.decrementValue = function(button) {
                    const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
                    const min = parseInt(input.min) || 0;
                    const val = parseInt(input.value) || 0;
                    if (val > min) {
                        input.value = val - 1;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                };
                window.updateNewTotal = function(input) {
                    const serviceId = input.dataset.serviceId;
                    const currentTest = parseInt(input.dataset.currentTest) || 0;
                    const currentBillable = parseInt(input.dataset.currentBillable) || 0;
                    const change = parseInt(input.value) || 0;
                    const statusId = input.dataset.statusId;

                    const newTestElement = document.querySelector(`[data-new-test-for="${serviceId}"]`);
                    const newBillableElement = document.querySelector(`[data-new-billable-for="${serviceId}"]`);

                    if (statusId == testStatusId) {
                        if (newTestElement) newTestElement.textContent = currentTest + change;
                        if (newBillableElement) newBillableElement.textContent = currentBillable;
                    } else {
                        if (newTestElement) newTestElement.textContent = currentTest;
                        if (newBillableElement) newBillableElement.textContent = currentBillable + change;
                    }
                };
                window.updateNewTotalDowngrade = function(input) {
                    const serviceId = input.dataset.serviceId;
                    const currentTest = parseInt(input.dataset.currentTest) || 0;
                    const currentBillable = parseInt(input.dataset.currentBillable) || 0;
                    const change = parseInt(input.value) || 0;
                    const statusId = input.dataset.statusId;

                    const newTestElement = document.querySelector(`[data-new-test-for="${serviceId}"]`);
                    const newBillableElement = document.querySelector(`[data-new-billable-for="${serviceId}"]`);

                    if (statusId == testStatusId) {
                        if (newTestElement) newTestElement.textContent = Math.max(0, currentTest - change);
                        if (newBillableElement) newBillableElement.textContent = currentBillable;
                    } else {
                        if (newTestElement) newTestElement.textContent = currentTest;
                        if (newBillableElement) newBillableElement.textContent = Math.max(0, currentBillable - change);
                    }
                };
                window.fillDismantleValues = function() {
                    const inputs = document.querySelectorAll('.downgrade-input');
                    inputs.forEach(input => {
                        const statusId = input.dataset.statusId;
                        const max = (statusId == testStatusId) ? input.dataset.currentTest : input.dataset.currentBillable;
                        input.value = max;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    });
                };
                window.clearAllocationForm = function() { 
                    renderPlaceholder();
                    if (customerSelect) customerSelect.value = '';
                    if (actionSelect) actionSelect.value = '';
                    if (statusSelect) statusSelect.value = '';
                    if (kamInfoContainer) kamInfoContainer.style.display = 'none';
                    toggleStatus();
                };

                // Initialize state
                toggleStatus();
                updateKamInfo();
                
                @if(session('new_customer_id'))
                    const newCustomerId = '{{ session('new_customer_id') }}';
                    if (newCustomerId && customerSelect) {
                        customerSelect.value = newCustomerId;
                        customerSelect.dispatchEvent(new Event('change'));
                    }
                @endif
            })();
        </script>
    @endpush
</x-app-layout>
