/**
 * Tech Resource Allocation Page JavaScript
 * Handles form interactions, customer selection, VDC modals, and allocation logic
 */
(function () {
    // Get configuration from data attributes
    const configEl = document.getElementById('tech-resource-allocation-config');
    if (!configEl) {
        console.error('Tech resource allocation config element not found');
        return;
    }

    const config = {
        baseUrl: configEl.dataset.baseUrl,
        indexUrl: configEl.dataset.indexUrl,
        customerBaseUrl: configEl.dataset.customerBaseUrl,
        testStatusId: parseInt(configEl.dataset.testStatusId) || 1,
        newCustomerId: configEl.dataset.newCustomerId || null,
        assetStorageUrl: configEl.dataset.assetStorageUrl,
        csrfToken: configEl.dataset.csrfToken
    };

    const kamInfoContainer = document.getElementById('kam-info-container');
    const displayKamName = document.getElementById('display-kam-name');
    const displayKamRole = document.getElementById('display-kam-role');
    const actionTypeContainer = document.getElementById('action-type-container');
    const customerSelect = document.getElementById('customer_id');
    const actionSelect = document.getElementById('action_type');
    const statusContainer = document.getElementById('status-container');
    const statusSelect = document.getElementById('status_id');
    const container = document.getElementById('cloud-detail-container');
    const poButtonContainer = document.getElementById('po-button-container');

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

        // Toggle PO Button
        if (actionSelect.value === 'upgrade') {
            if (poButtonContainer) poButtonContainer.classList.remove('d-none');
        } else {
            if (poButtonContainer) poButtonContainer.classList.add('d-none');
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

        renderPlaceholder('Loading allocation form...', 'fas fa-circle-notch fa-spin');

        try {
            const url = new URL(`${config.baseUrl}/${customerId}/allocate`);
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

            // Initialize flatpickr on new inputs with Friday/Saturday restriction
            if (window.initializeGlobalFlatpickr) {
                window.initializeGlobalFlatpickr();
            } else if (window.flatpickr) {
                flatpickr(".flatpickr-date", {
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    disable: [
                        function (date) {
                            return (date.getDay() === 5 || date.getDay() === 6);
                        }
                    ],
                    locale: {
                        firstDayOfWeek: 0
                    },
                    onDayCreate: function (dObj, dStr, fp, dayElem) {
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

    window.openVdcModal = function (taskId, customerId) {
        currentTaskId = taskId;
        currentCustomerId = customerId;

        // Update modal title with customer name
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        const customerName = selectedOption ? selectedOption.text.trim() : 'Select VDC';
        const modalTitle = document.getElementById('vdcModalLabel');
        if (modalTitle) {
            modalTitle.innerHTML = `<i class="fas fa-server me-2"></i>${customerName} - Select VDC`;
        }

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
        toggleNewVdcBtn.addEventListener('click', function () {
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
        vdcForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i> Processing...';

            fetch(`/my-tasks/${currentTaskId}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken,
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
    window.handleAllocationSubmit = async function (event, form) {
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
            const response = await fetch(`${config.baseUrl}/${customerSelect.value}/allocate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken,
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
        customerSelect.addEventListener('change', function () {
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
    window.incrementValue = function (button) {
        const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
        const max = input.hasAttribute('max') ? parseInt(input.max) : Infinity;
        const val = parseInt(input.value) || 0;
        if (val < max) {
            input.value = val + 1;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
    };
    window.decrementValue = function (button) {
        const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
        const min = parseInt(input.min) || 0;
        const val = parseInt(input.value) || 0;
        if (val > min) {
            input.value = val - 1;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
    };
    window.updateNewTotal = function (input) {
        const serviceId = input.dataset.serviceId;
        const currentTest = parseInt(input.dataset.currentTest) || 0;
        const currentBillable = parseInt(input.dataset.currentBillable) || 0;
        const change = parseInt(input.value) || 0;
        const statusId = input.dataset.statusId;

        const newTestElement = document.querySelector(`[data-new-test-for="${serviceId}"]`);
        const newBillableElement = document.querySelector(`[data-new-billable-for="${serviceId}"]`);

        if (statusId == config.testStatusId) {
            if (newTestElement) newTestElement.textContent = currentTest + change;
            if (newBillableElement) newBillableElement.textContent = currentBillable;
        } else {
            if (newTestElement) newTestElement.textContent = currentTest;
            if (newBillableElement) newBillableElement.textContent = currentBillable + change;
        }
    };
    window.updateNewTotalDowngrade = function (input) {
        const serviceId = input.dataset.serviceId;
        const currentTest = parseInt(input.dataset.currentTest) || 0;
        const currentBillable = parseInt(input.dataset.currentBillable) || 0;
        const change = parseInt(input.value) || 0;
        const statusId = input.dataset.statusId;

        const newTestElement = document.querySelector(`[data-new-test-for="${serviceId}"]`);
        const newBillableElement = document.querySelector(`[data-new-billable-for="${serviceId}"]`);

        if (statusId == config.testStatusId) {
            if (newTestElement) newTestElement.textContent = Math.max(0, currentTest - change);
            if (newBillableElement) newBillableElement.textContent = currentBillable;
        } else {
            if (newTestElement) newTestElement.textContent = currentTest;
            if (newBillableElement) newBillableElement.textContent = Math.max(0, currentBillable - change);
        }
    };
    window.fillDismantleValues = function () {
        const inputs = document.querySelectorAll('.downgrade-input');
        inputs.forEach(input => {
            const statusId = input.dataset.statusId;
            const max = (statusId == config.testStatusId) ? input.dataset.currentTest : input.dataset.currentBillable;
            input.value = max;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        });
    };
    window.clearAllocationForm = function () {
        renderPlaceholder();
        if (customerSelect) customerSelect.value = '';
        if (actionSelect) actionSelect.value = '';
        if (statusSelect) statusSelect.value = '';
        if (kamInfoContainer) kamInfoContainer.style.display = 'none';
        toggleStatus();
    };

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
                    'X-CSRF-TOKEN': config.csrfToken,
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

    // Initialize state
    toggleStatus();
    updateKamInfo();

    // Auto-select newly created customer if passed
    if (config.newCustomerId && customerSelect) {
        customerSelect.value = config.newCustomerId;
        customerSelect.dispatchEvent(new Event('change'));
    }
})();
