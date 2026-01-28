/**
 * My Tasks Page JavaScript
 * Handles task viewing, VDC selection modal, and task completion
 */
(function () {
    // Get configuration from data attributes
    const configEl = document.getElementById('my-tasks-config');
    if (!configEl) {
        console.error('My tasks config element not found');
        return;
    }

    const config = {
        csrfToken: configEl.dataset.csrfToken,
        baseUrl: configEl.dataset.baseUrl || '/my-tasks'
    };

    const viewButtons = document.querySelectorAll('.view-task-btn');
    const params = new URLSearchParams(window.location.search);
    const deepTaskId = params.get('dtid');
    const deepAction = params.get('da');

    // Deep link handling
    if (deepTaskId) {
        console.log('Deep link detected:', { dtid: deepTaskId, da: deepAction });
        if (deepAction === 'view') {
            const targetBtn = document.querySelector(`.view-task-btn[data-task-id="${deepTaskId}"]`);
            console.log('Target view button:', targetBtn);
            if (targetBtn) {
                setTimeout(() => {
                    console.log('Clicking view button');
                    targetBtn.click();
                    targetBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 500);
            }
        } else if (deepAction === 'complete') {
            const completeBtn = document.querySelector(`.complete-task-btn[data-task-id="${deepTaskId}"]`);
            console.log('Target complete button:', completeBtn);
            if (completeBtn) {
                setTimeout(() => {
                    console.log('Clicking complete button');
                    completeBtn.click();
                    completeBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 500);
            }
        }
    }

    // View button click handlers
    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const taskId = this.dataset.taskId;
            const detailsRow = document.getElementById('details-' + taskId);
            const btnText = this.querySelector('.btn-text');
            const btnIcon = this.querySelector('i');

            // Toggle visibility
            if (detailsRow.style.display === 'none') {
                // Close all other open details
                document.querySelectorAll('.task-details-row').forEach(row => {
                    row.style.display = 'none';
                });

                // Reset all other buttons
                document.querySelectorAll('.view-task-btn').forEach(btn => {
                    btn.querySelector('.btn-text').textContent = 'View';
                    btn.querySelector('i').className = 'fas fa-eye me-1';
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                });

                // Show this row
                detailsRow.style.display = 'table-row';
                btnText.textContent = 'Hide';
                btnIcon.className = 'fas fa-eye-slash me-1';
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');

                // Load details if not already loaded
                if (!detailsRow.dataset.loaded) {
                    loadTaskDetails(taskId);
                }
            } else {
                // Hide this row
                detailsRow.style.display = 'none';
                btnText.textContent = 'View';
                btnIcon.className = 'fas fa-eye me-1';
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
            }
        });
    });

    function loadTaskDetails(taskId) {
        const detailsRow = document.getElementById('details-' + taskId);
        const container = detailsRow.querySelector('.task-details-container');

        fetch(`${config.baseUrl}/${taskId}/details`)
            .then(response => response.json())
            .then(data => {
                const task = data.task;
                const resourceDetails = data.resourceDetails;
                const vdcName = task.vdc ? task.vdc.vdc_name : 'N/A';

                let html = '';

                if (resourceDetails && resourceDetails.length > 0) {
                    const isUpgrade = task.allocation_type === 'upgrade';
                    const label = isUpgrade ? 'Increase By' : 'Reduce By';
                    const badgeClass = isUpgrade ? 'badge bg-success' : 'badge bg-warning text-dark';
                    const arrowIcon = isUpgrade ? '<i class="fas fa-arrow-up me-2"></i>' : '<i class="fas fa-arrow-down me-2"></i>';

                    html += `
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-server me-2"></i>VDC</th>
                                        <th class="resource-alloc-service-cell"><i class="fas fa-tools me-2"></i>Service</th>
                                        <th><i class="fas fa-chart-line me-2"></i>Current</th>
                                        <th>${arrowIcon}${label}</th>
                                        <th><i class="fas fa-equals me-2"></i>New Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    resourceDetails.forEach(detail => {
                        const amount = isUpgrade ? detail.upgrade_amount : detail.downgrade_amount;
                        const prev = isUpgrade ? (detail.quantity - amount) : (detail.quantity + amount);

                        const prevDisplay = prev < 0
                            ? `<span class="text-danger fw-bold">${prev}</span>`
                            : `<span class="badge bg-secondary">${prev}</span>`;

                        const newDisplay = detail.quantity < 0
                            ? `<span class="text-danger fw-bold">${detail.quantity}</span>`
                            : `<span class="resource-alloc-new-total-value">${detail.quantity}</span>`;

                        // Display service name with unit inline
                        const serviceName = detail.service.service_name;
                        const serviceUnit = detail.service.unit ? ` <span class="resource-alloc-service-unit">(${detail.service.unit})</span>` : '';
                        const serviceDisplay = `<span class="resource-alloc-service-name">${serviceName}${serviceUnit}</span>`;

                        html += `
                            <tr>
                                <td>
                                    <span class="badge bg-info text-dark">${vdcName}</span>
                                </td>
                                <td class="resource-alloc-service-cell">${serviceDisplay}</td>
                                <td>${prevDisplay} ${detail.service.unit || ''}</td>
                                <td>
                                    <span class="${badgeClass}">
                                        ${isUpgrade ? '+' : '-'}${amount}
                                    </span>
                                </td>
                                <td>
                                    <div class="resource-alloc-new-total">
                                        <span class="resource-alloc-new-total-arrow">→</span>
                                        ${newDisplay}
                                        <span class="resource-alloc-new-total-unit">${detail.service.unit || ''}</span>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    html = `
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No resource details available.</p>
                        </div>
                    `;
                }

                container.innerHTML = html;
                detailsRow.dataset.loaded = 'true';
            })
            .catch(error => {
                console.error('Error loading task details:', error);
                container.innerHTML = `
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error loading task details. Please try again.
                    </div>
                `;
            });
    }

    // VDC Modal handling
    const vdcModal = document.getElementById('vdcModal');
    const vdcForm = document.getElementById('vdcForm');
    const vdcSelect = document.getElementById('vdc_select');
    const toggleNewVdcBtn = document.getElementById('toggleNewVdc');
    const newVdcContainer = document.getElementById('newVdcContainer');
    const newVdcInput = document.getElementById('new_vdc_name');

    let currentTaskId = null;
    let currentCustomerId = null;

    // Handle Complete button click
    document.querySelectorAll('.complete-task-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            handleCompleteClick(this.dataset.taskId, this.dataset.customerId, this.dataset.customerName);
        });
    });

    // Handle Complete Anyway button click from warning modal
    document.querySelectorAll('.complete-task-anyway-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            handleCompleteClick(this.dataset.taskId, this.dataset.customerId, this.dataset.customerName);

            // Manually open the VDC modal since we dismissed the warning modal
            const modal = new bootstrap.Modal(vdcModal);
            modal.show();
        });
    });

    function handleCompleteClick(taskId, customerId, customerName) {
        currentTaskId = taskId;
        currentCustomerId = customerId;

        // Update modal title
        const modalTitle = document.getElementById('vdcModalLabel');
        if (modalTitle) {
            modalTitle.innerHTML = `<i class="fas fa-server me-2"></i>${customerName} - Select VDC`;
        }

        // Load VDCs for this customer
        loadCustomerVdcs(currentCustomerId);

        // Reset form
        vdcSelect.value = '';
        newVdcInput.value = '';
        newVdcContainer.style.display = 'none';
    }

    // Toggle new VDC input
    if (toggleNewVdcBtn) {
        toggleNewVdcBtn.addEventListener('click', function () {
            if (newVdcContainer.style.display === 'none') {
                newVdcContainer.style.display = 'block';
                vdcSelect.value = ''; // Clear selection
                newVdcInput.focus();
            } else {
                newVdcContainer.style.display = 'none';
                newVdcInput.value = '';
            }
        });
    }

    // Load VDCs for customer
    function loadCustomerVdcs(customerId) {
        fetch(`${config.baseUrl}/customer/${customerId}/vdcs`)
            .then(response => response.json())
            .then(data => {
                vdcSelect.innerHTML = '<option value="">-- Select VDC --</option>';
                data.vdcs.forEach(vdc => {
                    const option = document.createElement('option');
                    option.value = vdc.id;
                    option.textContent = vdc.vdc_name;
                    vdcSelect.appendChild(option);
                });

                // Select the first VDC by default if available
                if (data.vdcs.length > 0) {
                    vdcSelect.value = data.vdcs[0].id;
                }
            })
            .catch(error => {
                console.error('Error loading VDCs:', error);
            });
    }

    // Handle form submission
    if (vdcForm) {
        vdcForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i> Processing...';

            // Set the form action to the complete route
            const completeUrl = `${config.baseUrl}/${currentTaskId}/complete`;

            fetch(completeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Server Error'); });
                    }
                    return response.json();
                })
                .then(data => {
                    // Close modal
                    const modalInstance = bootstrap.Modal.getInstance(vdcModal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    // Reload page to show updated task status
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
})();
