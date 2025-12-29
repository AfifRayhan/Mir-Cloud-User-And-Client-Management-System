<x-app-layout>
    @push('styles')
        @vite(['resources/css/custom-tech-resource.css'])
    @endpush

    <div class="container-fluid tech-resource-container py-4">
        <div class="tech-resource-bg-pattern"></div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="tech-resource-header">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                        <div>
                            <h1 class="tech-resource-title fw-bold mb-2">Tech Resource Management</h1>
                            <p class="text-muted tech-resource-subtitle">
                                Rapid resource allocation and automated task completion for tech users.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card tech-resource-card border-0 h-100">
                    <div class="card-body p-4">
                        <form id="tech-resource-action-form">
                            @csrf
                            
                            <!-- KAM Selection -->
                            <div class="mb-4">
                                <label for="kam_id" class="form-label tech-resource-form-label">Select KAM / Pro-KAM</label>
                                <select id="kam_id" name="kam_id" class="form-select tech-resource-select form-select-lg" required>
                                    <option value="" disabled selected>Choose a KAM</option>
                                    @foreach($kams as $kam)
                                        <option value="{{ $kam->id }}">{{ $kam->name }} ({{ strtoupper($kam->role->role_name) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="customer_id" class="form-label tech-resource-form-label">Customer</label>
                                <select id="customer_id" name="customer_id" class="form-select tech-resource-select form-select-lg" required>
                                    <option value="" disabled selected>Choose a customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->customer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="action_type" class="form-label tech-resource-form-label">Action</label>
                                <select id="action_type" name="action_type" class="form-select tech-resource-select form-select-lg" required>
                                    <option value="" disabled selected>Select Action</option>
                                    <option value="upgrade">Upgrade</option>
                                    <option value="downgrade">Downgrade</option>
                                </select>
                            </div>

                            <div class="mb-4 d-none" id="status-container">
                                <label for="status_id" class="form-label tech-resource-form-label">Customer Status</label>
                                <select id="status_id" name="status_id" class="form-select tech-resource-select form-select-lg">
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

            <div class="col-lg-8">
                <div id="cloud-detail-container" class="h-100">
                    <div class="card tech-resource-card border-0 h-100">
                        <div class="card-body d-flex flex-column justify-content-center text-center text-muted">
                            <p class="mb-0">Select KAM, Customer, and Action to begin allocation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VDC Selection Modal (Reused from My Tasks) -->
    <div class="modal fade" id="vdcModal" tabindex="-1" aria-labelledby="vdcModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vdcModalLabel">
                        <i class="fas fa-server me-2"></i>Complete Allocation - Select VDC
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="vdcForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="vdc_select" class="form-label fw-semibold">Select Existing VDC</label>
                            <select id="vdc_select" name="vdc_id" class="form-select">
                                <option value="">-- Select VDC --</option>
                            </select>
                        </div>

                        <div class="text-center my-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="toggleNewVdc">
                                <i class="fas fa-plus me-1"></i> Add New VDC
                            </button>
                        </div>

                        <div id="newVdcContainer" class="mb-3" style="display: none;">
                            <label for="new_vdc_name" class="form-label fw-semibold">New VDC Name</label>
                            <input type="text" id="new_vdc_name" name="new_vdc_name" class="form-control" placeholder="Enter VDC name">
                            <small class="text-muted">Leave blank to use selected VDC above</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Finalize Allocation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const kamSelect = document.getElementById('kam_id');
                const customerSelect = document.getElementById('customer_id');
                const actionSelect = document.getElementById('action_type');
                const statusContainer = document.getElementById('status-container');
                const statusSelect = document.getElementById('status_id');
                const container = document.getElementById('cloud-detail-container');

                function renderPlaceholder(message = 'Select options to begin.') {
                    if (!container) return;
                    container.innerHTML = `
                        <div class="card tech-resource-card border-0 h-100">
                            <div class="card-body d-flex flex-column justify-content-center text-center text-muted">
                                <p class="mb-0">${message}</p>
                            </div>
                        </div>
                    `;
                }

                function toggleStatus() {
                    if (!actionSelect || !statusContainer) return;
                    if (actionSelect.value === 'upgrade') {
                        statusContainer.classList.remove('d-none');
                        statusSelect.required = true;
                    } else {
                        statusContainer.classList.add('d-none');
                        statusSelect.required = false;
                        statusSelect.value = "";
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
                        renderPlaceholder('Please select a customer status to proceed with upgrade.');
                        return;
                    }

                    renderPlaceholder('Loading allocation form...');

                    try {
                        const url = new URL(`{{ url('tech-resource-allocation') }}/${customerId}/allocate`);
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

                        if (!response.ok) throw new Error('Request failed');

                        const data = await response.json();
                        container.innerHTML = data.html;
                        
                        // Re-bind tooltips if any
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                        tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl)
                        });
                    } catch (error) {
                        console.error(error);
                        renderPlaceholder('Unable to load allocation form.');
                    }
                }

                // VDC Modal initialization - only if bootstrap is ready
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

                    // Load VDCs for this customer
                    loadCustomerVdcs(currentCustomerId);

                    // Reset form
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
                    fetch(`/my-tasks/customer/${customerId}/vdcs`)
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
                            window.location.reload();
                        })
                        .catch(error => {
                            console.error('Error completing task:', error);
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                            alert(error.message || 'Error completing task. Please try again.');
                        });
                    });
                }

                // Global function required by the partial
                window.handleAllocationSubmit = async function(event, form) {
                    event.preventDefault();
                    
                    const kamId = kamSelect.value;
                    if (!kamId) {
                        alert('Please select a KAM.');
                        return;
                    }
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Confirm';
                    
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                    }

                    const formData = new FormData(form);
                    formData.append('kam_id', kamId);
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
                            alert('Error: ' + (data.message || 'Validation failed'));
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;
                            }
                        }
                    } catch (error) {
                        console.error(error);
                        alert('An unexpected error occurred.');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    }
                    return false;
                };

                if (actionSelect) actionSelect.addEventListener('change', () => { toggleStatus(); loadAllocationForm(); });
                if (customerSelect) {
                    customerSelect.addEventListener('change', function() {
                        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                        if (selectedOption && selectedOption.text.includes('No Resources')) {
                            actionSelect.value = 'upgrade';
                            toggleStatus();
                        }
                        if (actionSelect.value) loadAllocationForm();
                    });
                }
                if (statusSelect) statusSelect.addEventListener('change', loadAllocationForm);
                
                // Initialize
                (function init() {
                    @if(session('new_customer_id'))
                        const newCustomerId = '{{ session('new_customer_id') }}';
                        if (newCustomerId && customerSelect) {
                            customerSelect.value = newCustomerId;
                            customerSelect.dispatchEvent(new Event('change'));
                        }
                    @endif
                })();

                // Global helpers for partial
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
                    const currentVal = parseInt(input.dataset.current) || 0;
                    const change = parseInt(input.value) || 0;
                    const target = document.querySelector(`[data-new-total-for="${serviceId}"]`);
                    if (target) target.textContent = currentVal + change;
                };
                window.updateNewTotalDowngrade = function(input) {
                    const serviceId = input.dataset.serviceId;
                    const currentVal = parseInt(input.dataset.current) || 0;
                    const change = parseInt(input.value) || 0;
                    const target = document.querySelector(`[data-new-total-for="${serviceId}"]`);
                    if (target) target.textContent = Math.max(0, currentVal - change);
                };
                window.fillDismantleValues = function() {
                    const inputs = document.querySelectorAll('.downgrade-input');
                    inputs.forEach(input => {
                        const currentValue = input.getAttribute('data-current-value');
                        if (currentValue) {
                            input.value = currentValue;
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });
                };
                window.clearAllocationForm = function() { window.location.reload(); };
            });
        </script>
    @endpush
</x-app-layout>
