/**
 * Resource Allocation Page JavaScript
 * Handles form interactions, customer selection, VDC modals, and allocation logic
 */
(function () {
    // Get configuration from data attributes
    const configEl = document.getElementById('resource-allocation-config');
    if (!configEl) {
        console.error('Resource allocation config element not found');
        return;
    }

    const config = {
        baseUrl: configEl.dataset.baseUrl,
        indexUrl: configEl.dataset.indexUrl,
        customerBaseUrl: configEl.dataset.customerBaseUrl,
        testStatusId: parseInt(configEl.dataset.testStatusId) || 1,
        newCustomerId: configEl.dataset.newCustomerId || null,
        assetStorageUrl: configEl.dataset.assetStorageUrl
    };

    const actionTypeContainer = document.getElementById('action-type-container');
    const customerSelect = document.getElementById('customer_id');
    const actionSelect = document.getElementById('action_type');
    const statusContainer = document.getElementById('status-container');
    const statusSelect = document.getElementById('status_id');
    const transferTypeContainer = document.getElementById('transfer-type-container');
    const transferTypeSelect = document.getElementById('transfer_type');
    const container = document.getElementById('cloud-detail-container');
    const resourceForm = document.getElementById('resource-action-form');
    const addCustomerBtn = document.getElementById('add-customer-btn');
    const poButtonContainer = document.getElementById('po-button-container');

    if (!customerSelect || !actionSelect || !container || !actionTypeContainer) {
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
        const action = actionSelect.value;
        if (action === 'upgrade' || action === 'downgrade') {
            statusContainer.classList.remove('d-none');
            statusSelect.required = true;
            transferTypeContainer.classList.add('d-none');
            transferTypeSelect.required = false;
            console.log('Status container shown');
        } else if (action === 'transfer') {
            statusContainer.classList.add('d-none');
            statusSelect.required = false;
            statusSelect.value = "";
            transferTypeContainer.classList.remove('d-none');
            transferTypeSelect.required = true;
            console.log('Transfer Type container shown');
        } else {
            statusContainer.classList.add('d-none');
            statusSelect.required = false;
            statusSelect.value = "";
            transferTypeContainer.classList.add('d-none');
            transferTypeSelect.required = false;
            transferTypeSelect.value = "";
            transferTypeSelect.value = "";
            console.log('All extra containers hidden');
        }

        // Toggle Add Customer Button / PO Upload
        if (action === 'upgrade') {
            if (addCustomerBtn) addCustomerBtn.classList.add('d-none');
            if (poButtonContainer) poButtonContainer.classList.remove('d-none');
        } else {
            if (addCustomerBtn) addCustomerBtn.classList.remove('d-none');
            if (poButtonContainer) poButtonContainer.classList.add('d-none');
        }
    }

    async function loadAllocationForm() {
        const customerId = customerSelect.value;
        const actionType = actionSelect.value;
        const statusId = statusSelect.value;
        const transferType = transferTypeSelect.value;

        if (!customerId || !actionType) {
            renderPlaceholder();
            return;
        }

        renderPlaceholder('Loading allocation form...');

        try {
            const url = new URL(`${config.baseUrl}/${customerId}/allocate`);
            url.searchParams.append('action_type', actionType);
            if (statusId) {
                url.searchParams.append('status_id', statusId);
            }
            if (transferType) {
                url.searchParams.append('transfer_type', transferType);
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
            if (data.transfer_type && !transferTypeSelect.value) {
                transferTypeSelect.value = data.transfer_type;
            }

            // Disable options if pool is empty
            if (data.action_type === 'transfer') {
                const testToBillableOpt = transferTypeSelect.querySelector('option[value="test_to_billable"]');
                const billableToTestOpt = transferTypeSelect.querySelector('option[value="billable_to_test"]');

                if (testToBillableOpt) testToBillableOpt.disabled = !data.has_test;
                if (billableToTestOpt) billableToTestOpt.disabled = !data.has_billable;
            }

            container.innerHTML = data.html;

            // If the backend provided a status_id, update the select
            if (data.status_id && (actionType === 'upgrade' || actionType === 'downgrade')) {
                if (!statusSelect.value) {
                    statusSelect.value = data.status_id;
                }
            }

            // Store testStatusId if provided
            if (data.test_status_id) {
                window.currentTestStatusId = data.test_status_id;
            }

            // Attach form submit handler
            attachFormHandler();

            // Re-initialize Flatpickr for the new form elements
            if (window.initializeGlobalFlatpickr) {
                window.initializeGlobalFlatpickr();
            }
        } catch (error) {
            console.error(error);
            renderPlaceholder('Unable to load allocation form.');
        }
    }

    // Global function to handle allocation form submission
    window.handleAllocationSubmit = async function (event, form) {
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
        const transferType = form.dataset.transferType;

        formData.append('action_type', actionType);
        if (statusId) {
            formData.append('status_id', statusId);
        }
        if (transferType) {
            formData.append('transfer_type', transferType);
        }

        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        // Clear any existing page-level alerts
        const existingAlerts = document.querySelectorAll('.custom-user-management-alert');
        existingAlerts.forEach(alert => alert.remove());

        try {
            const response = await fetch(`${config.baseUrl}/${customerId}/allocate`, {
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

                    // Update column highlighting based on action type
                    if (actionType === 'transfer') {
                        // For transfers, preserve highlighting from server
                    } else {
                        // For upgrades/downgrades, highlight based on status
                        const currentTestStatusId = window.currentTestStatusId || config.testStatusId;
                        const isTest = statusId == currentTestStatusId;
                        document.querySelectorAll('.resource-alloc-test-col').forEach(el => {
                            if (isTest) el.classList.add('status-highlighted');
                            else el.classList.remove('status-highlighted');
                        });
                        document.querySelectorAll('.resource-alloc-billable-col').forEach(el => {
                            if (!isTest && statusId) el.classList.add('status-highlighted');
                            else el.classList.remove('status-highlighted');
                        });
                    }

                    // Scroll to error message
                    const errorAlert = document.querySelector('.alert-danger');
                    if (errorAlert) {
                        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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

            // Success - show page-level alert
            showPageAlert('success', 'Success!', data.message || 'Resources updated successfully!');

            // Reset the form inputs
            form.reset();

            // Refresh only the customer dropdown to remove "(No Resources)" label
            setTimeout(async () => {
                try {
                    const response = await fetch(config.indexUrl, {
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
                        if (newSelect) {
                            const currentCustomerId = customerSelect.value;
                            customerSelect.innerHTML = newSelect.innerHTML;

                            if (currentCustomerId) {
                                customerSelect.value = currentCustomerId;
                                const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                                if (selectedOption) {
                                    selectedOption.setAttribute('data-is-new', 'false');
                                }
                            }
                        }
                    }

                    loadAllocationForm();
                } catch (error) {
                    console.error('Error refreshing customer list:', error);
                    loadAllocationForm();
                }
            }, 1500);

            return false;
        } catch (error) {
            console.error(error);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }

            showPageAlert('danger', 'Error!', 'An error occurred while saving the allocation.');
            return false;
        }
    };

    // Function to show page-level alert
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

    function attachFormHandler() {
        console.log('attachFormHandler called (deprecated)');
    }

    // Event listeners
    actionSelect.addEventListener('change', async function () {
        toggleStatus();
        loadAllocationForm();
    });

    customerSelect.addEventListener('change', function () {
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        const isNew = selectedOption.getAttribute('data-is-new') === 'true';

        if (isNew) {
            actionTypeContainer.style.display = 'none';
            actionSelect.value = 'upgrade';
            toggleStatus();
        } else {
            actionTypeContainer.style.display = 'block';
            if (selectedOption.text.includes('No Resources')) {
                actionSelect.value = 'upgrade';
                toggleStatus();
            }
        }

        statusSelect.value = '';

        if (actionSelect.value) {
            loadAllocationForm();
        }
    });

    statusSelect.addEventListener('change', function () {
        if (actionSelect.value === 'upgrade' || actionSelect.value === 'downgrade') {
            loadAllocationForm();
        }
    });

    transferTypeSelect.addEventListener('change', function () {
        if (actionSelect.value === 'transfer') {
            loadAllocationForm();
        }
    });

    resourceForm.addEventListener('submit', function (e) {
        const actionType = actionSelect.value;
        if (actionType !== 'dismantle') {
            e.preventDefault();
        }
    });

    // Global function to fill all transfer inputs with max values
    window.fillTransferAllValues = function () {
        const inputs = document.querySelectorAll('.transfer-input');
        inputs.forEach(input => {
            const maxVal = input.getAttribute('max');
            if (maxVal) {
                input.value = maxVal;
                const event = new Event('input', { bubbles: true });
                input.dispatchEvent(event);
            }
        });
    };

    // Global function to fill all downgrade inputs with current values (for dismantle)
    window.fillDismantleValues = function () {
        const inputs = document.querySelectorAll('.downgrade-input');
        inputs.forEach(input => {
            const currentValue = input.getAttribute('data-current-value');
            if (currentValue) {
                input.value = currentValue;
                const event = new Event('input', { bubbles: true });
                input.dispatchEvent(event);
            }
        });
    };

    // Global function for cancel button
    window.clearAllocationForm = function () {
        renderPlaceholder();
        customerSelect.value = '';
        actionSelect.value = '';
        statusSelect.value = '';
        transferTypeSelect.value = '';
        toggleStatus();
    };

    // Stepper functions for resource allocation inputs
    window.incrementValue = function (button) {
        const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
        const currentVal = parseInt(input.value) || 0;
        const max = input.hasAttribute('max') ? parseInt(input.max) : Infinity;

        if (currentVal < max) {
            input.value = currentVal + 1;
            const event = new Event('input', { bubbles: true });
            input.dispatchEvent(event);
        }
    };

    window.decrementValue = function (button) {
        const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
        const currentVal = parseInt(input.value) || 0;
        const min = parseInt(input.min) || 0;

        if (currentVal > min) {
            input.value = currentVal - 1;
            const event = new Event('input', { bubbles: true });
            input.dispatchEvent(event);
        }
    };

    // Update new total for upgrade
    window.updateNewTotal = function (input) {
        const serviceId = input.dataset.serviceId;
        const statusId = parseInt(input.dataset.statusId) || 0;
        const currentTestValue = parseInt(input.dataset.currentTest) || 0;
        const currentBillableValue = parseInt(input.dataset.currentBillable) || 0;
        const increaseBy = parseInt(input.value) || 0;

        const currentTestStatusId = window.currentTestStatusId || config.testStatusId;
        const isTest = statusId == currentTestStatusId;
        const newTestTotal = currentTestValue + (isTest ? increaseBy : 0);
        const newBillableTotal = currentBillableValue + (isTest ? 0 : increaseBy);

        const newTestElement = document.querySelector(`[data-new-test-for="${serviceId}"]`);
        const newBillableElement = document.querySelector(`[data-new-billable-for="${serviceId}"]`);

        if (newTestElement) {
            newTestElement.textContent = newTestTotal;
        }
        if (newBillableElement) {
            newBillableElement.textContent = newBillableTotal;
        }

        if (increaseBy < 0) {
            input.classList.add('is-invalid');
            input.parentElement.classList.add('has-error');
        } else {
            input.classList.remove('is-invalid');
            input.parentElement.classList.remove('has-error');
        }
    };

    // Update new total for downgrade
    window.updateNewTotalDowngrade = function (input) {
        const serviceId = input.dataset.serviceId;
        const statusId = parseInt(input.dataset.statusId) || 0;
        const currentTestValue = parseInt(input.dataset.currentTest) || 0;
        const currentBillableValue = parseInt(input.dataset.currentBillable) || 0;
        const reduceBy = parseInt(input.value) || 0;

        const currentTestStatusId = window.currentTestStatusId || config.testStatusId;
        const isTest = statusId == currentTestStatusId;
        const newTestTotal = Math.max(0, currentTestValue - (isTest ? reduceBy : 0));
        const newBillableTotal = Math.max(0, currentBillableValue - (isTest ? 0 : reduceBy));

        const newTestElement = document.querySelector(`[data-new-test-for="${serviceId}"]`);
        const newBillableElement = document.querySelector(`[data-new-billable-for="${serviceId}"]`);

        if (newTestElement) {
            newTestElement.textContent = newTestTotal;
        }
        if (newBillableElement) {
            newBillableElement.textContent = newBillableTotal;
        }

        const maxAllowed = isTest ? currentTestValue : currentBillableValue;
        if (reduceBy > maxAllowed) {
            input.classList.add('is-invalid');
            input.parentElement.classList.add('has-error');
        } else {
            input.classList.remove('is-invalid');
            input.parentElement.classList.remove('has-error');
        }
    };

    // Update new total for transfer
    window.updateNewTotalTransfer = function (input) {
        const serviceId = input.dataset.serviceId;
        const currentTestValue = parseInt(input.dataset.currentTest) || 0;
        const currentBillableValue = parseInt(input.dataset.currentBillable) || 0;
        const transferType = input.dataset.transferType;
        const transferAmount = parseInt(input.value) || 0;

        let newTestTotal, newBillableTotal;

        if (transferType === 'test_to_billable') {
            newTestTotal = Math.max(0, currentTestValue - transferAmount);
            newBillableTotal = currentBillableValue + transferAmount;
        } else {
            newBillableTotal = Math.max(0, currentBillableValue - transferAmount);
            newTestTotal = currentTestValue + transferAmount;
        }

        const newTestElement = document.querySelector(`[data-new-test-for="${serviceId}"]`);
        const newBillableElement = document.querySelector(`[data-new-billable-for="${serviceId}"]`);

        if (newTestElement) {
            newTestElement.textContent = newTestTotal;
        }
        if (newBillableElement) {
            newBillableElement.textContent = newBillableTotal;
        }

        const maxAllowed = transferType === 'test_to_billable' ? currentTestValue : currentBillableValue;
        if (transferAmount > maxAllowed) {
            input.classList.add('is-invalid');
            input.parentElement.classList.add('has-error');
        } else {
            input.classList.remove('is-invalid');
            input.parentElement.classList.remove('has-error');
        }
    };

    // Disable arrow keys and mouse wheel on stepper inputs
    document.body.addEventListener('keydown', function (e) {
        if (e.target.classList.contains('resource-alloc-stepper-input')) {
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                e.preventDefault();
            }
        }
    });

    document.body.addEventListener('wheel', function (e) {
        if (e.target.classList.contains('resource-alloc-stepper-input')) {
            e.preventDefault();
        }
    }, { passive: false });

    // PO Project Sheets Modal Functions
    window.openPoSheetModal = async function () {
        const customerId = customerSelect.value;
        if (!customerId) {
            alert('Please select a customer first.');
            return;
        }

        const modal = new bootstrap.Modal(document.getElementById('poSheetModal'));
        const modalBody = document.getElementById('poSheetModalBody');

        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading sheets...</p></div>';
        modal.show();

        try {
            const response = await fetch(`${config.customerBaseUrl}/${customerId}/po-sheets`);
            const data = await response.json();

            if (data.success) {
                renderPoSheetsList(data.po_project_sheets);
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger">Failed to load PO sheets.</div>';
            }
        } catch (error) {
            console.error(error);
            modalBody.innerHTML = '<div class="alert alert-danger">An error occurred while fetching PO sheets.</div>';
        }
    };

    function renderPoSheetsList(sheets) {
        const modalBody = document.getElementById('poSheetModalBody');
        if (!sheets || sheets.length === 0) {
            modalBody.innerHTML = '<p class="text-muted text-center py-3">No PO Project Sheets uploaded yet.</p>';
        } else {
            let html = '<div class="list-group mb-4">';
            sheets.forEach(sheet => {
                html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-pdf text-danger me-3 fs-4"></i>
                            <div>
                                <div class="fw-semibold text-truncate" style="max-width: 250px;">${sheet.name}</div>
                                <small class="text-muted">${(sheet.size / 1024 / 1024).toFixed(2)} MB</small>
                            </div>
                        </div>
                        <a href="${config.assetStorageUrl}/${sheet.path}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                    </div>
                `;
            });
            html += '</div>';
            modalBody.innerHTML = html;
        }

        modalBody.innerHTML += `
            <hr>
            <div class="mt-3">
                <label class="form-label fw-semibold">Upload New Sheets</label>
                <div class="input-group">
                    <input type="file" id="new_po_sheets" class="form-control" multiple accept=".pdf">
                    <button class="btn btn-primary" type="button" onclick="uploadPoSheets()">Upload</button>
                </div>
                <div id="upload-status" class="mt-2"></div>
            </div>
        `;
    }

    window.uploadPoSheets = async function () {
        const customerId = customerSelect.value;
        const fileInput = document.getElementById('new_po_sheets');
        const statusDiv = document.getElementById('upload-status');

        if (!fileInput.files.length) {
            statusDiv.innerHTML = '<small class="text-danger">Please select files to upload.</small>';
            return;
        }

        const formData = new FormData();
        for (let i = 0; i < fileInput.files.length; i++) {
            formData.append('po_project_sheets[]', fileInput.files[i]);
        }

        statusDiv.innerHTML = '<small class="text-primary"><i class="fas fa-spinner fa-spin me-1"></i>Uploading and optimizing...</small>';

        try {
            const response = await fetch(`${config.customerBaseUrl}/${customerId}/po-sheets`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                statusDiv.innerHTML = '<small class="text-success">Uploaded successfully!</small>';
                renderPoSheetsList(data.po_project_sheets);
            } else {
                statusDiv.innerHTML = `<small class="text-danger">${data.message || 'Upload failed'}</small>`;
            }
        } catch (error) {
            console.error(error);
            statusDiv.innerHTML = '<small class="text-danger">An error occurred during upload.</small>';
        }
    };

    // Initialize
    toggleStatus();
    renderPlaceholder();

    // Auto-select newly created customer if passed
    if (config.newCustomerId) {
        customerSelect.value = config.newCustomerId;
        customerSelect.dispatchEvent(new Event('change'));
    }
})();
