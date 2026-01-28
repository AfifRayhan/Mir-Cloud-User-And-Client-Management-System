/**
 * KAM Task Management Page JavaScript
 * Handles task viewing, editing, stepper inputs, and deep linking
 */
(function () {
    // Get configuration from data attributes
    const configEl = document.getElementById('kam-task-management-config');
    if (!configEl) {
        console.error('KAM task management config element not found');
        return;
    }

    const config = {
        baseUrl: configEl.dataset.baseUrl || '/kam-task-management'
    };

    // Stepper functions for resource allocation inputs
    window.incrementValue = function (button) {
        const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
        const currentVal = parseInt(input.value) || 0;
        const max = input.hasAttribute('max') ? parseInt(input.max) : Infinity;

        if (currentVal < max) {
            input.value = currentVal + 1;
            // Trigger input event for real-time updates
            const event = new Event('input', {
                bubbles: true
            });
            input.dispatchEvent(event);
        }
    };

    window.decrementValue = function (button) {
        const input = button.parentElement.querySelector('.resource-alloc-stepper-input');
        const currentVal = parseInt(input.value) || 0;
        const min = parseInt(input.min) || 0;

        if (currentVal > min) {
            input.value = currentVal - 1;
            // Trigger input event for real-time updates
            const event = new Event('input', {
                bubbles: true
            });
            input.dispatchEvent(event);
        }
    };

    // Update new total for upgrade
    window.updateNewTotal = function (input) {
        const serviceId = input.dataset.serviceId;
        const taskId = input.dataset.taskId;
        const currentValue = parseInt(input.dataset.current) || 0;
        const increaseBy = parseInt(input.value) || 0;
        const newTotal = currentValue + increaseBy;

        // Scope to the specific modal/task using service-id and task-id
        const newTotalElement = document.querySelector(`[data-new-total-for="${serviceId}-${taskId}"]`);
        if (newTotalElement) {
            newTotalElement.textContent = newTotal;
        }
    };

    // Update new total for downgrade
    window.updateNewTotalDowngrade = function (input) {
        const serviceId = input.dataset.serviceId;
        const taskId = input.dataset.taskId;
        const currentValue = parseInt(input.dataset.current) || 0;
        const reduceBy = parseInt(input.value) || 0;
        const newTotal = Math.max(0, currentValue - reduceBy);

        const newTotalElement = document.querySelector(`[data-new-total-for="${serviceId}-${taskId}"]`);
        if (newTotalElement) {
            newTotalElement.textContent = newTotal;
        }
    };

    const viewButtons = document.querySelectorAll('.view-task-btn');
    const params = new URLSearchParams(window.location.search);
    const deepTaskId = params.get('dtid');
    const deepAction = params.get('da');

    // Clean up any stray open rows on load
    document.querySelectorAll('.kam-task-details-row').forEach(row => row.classList.remove('show-row'));

    // Deep link handling
    if (deepTaskId) {
        if (deepAction === 'view') {
            const targetBtn = document.querySelector(`.view-task-btn[data-task-id="${deepTaskId}"]`);
            if (targetBtn) {
                setTimeout(() => {
                    targetBtn.click();
                    targetBtn.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 500);
            }
        } else if (deepAction === 'edit') {
            const editBtn = document.querySelector(`button[data-bs-target="#editModal${deepTaskId}"]`);
            if (editBtn) {
                // We need to expand the row first to see the edit button if it's inside
                const viewBtn = document.querySelector(`.view-task-btn[data-task-id="${deepTaskId}"]`);
                if (viewBtn) {
                    viewBtn.click();
                    setTimeout(() => {
                        editBtn.click();
                    }, 500);
                } else {
                    // If button exists but view doesn't, just try clicking edit
                    editBtn.click();
                }
            }
        }
    }

    // View button click handlers
    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const taskId = this.dataset.taskId;
            const detailsRow = document.getElementById('details-' + taskId);
            const btnIcon = this.querySelector('i');

            if (!detailsRow.classList.contains('show-row')) {
                // Close other details
                document.querySelectorAll('.kam-task-details-row').forEach(row => row.classList.remove('show-row'));
                document.querySelectorAll('.view-task-btn i').forEach(icon => {
                    icon.className = 'fas fa-eye me-1';
                });

                detailsRow.classList.add('show-row');
                btnIcon.className = 'fas fa-eye-slash me-1';

                if (!detailsRow.dataset.loaded) {
                    loadTaskDetails(taskId);
                }
            } else {
                detailsRow.classList.remove('show-row');
                btnIcon.className = 'fas fa-eye me-1';
            }
        });
    });

    function loadTaskDetails(taskId) {
        const row = document.getElementById('details-' + taskId);
        const container = row.querySelector('.task-details-content');
        const actions = document.getElementById('actions-' + taskId);

        // Show spinner inside container while loading
        container.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

        fetch(`${config.baseUrl}/${taskId}/details`)
            .then(response => response.json())
            .then(data => {
                const task = data.task;
                const resourceDetails = data.resourceDetails;

                let html = '';
                if (resourceDetails && resourceDetails.length > 0) {
                    const isUpgrade = task.allocation_type === 'upgrade';
                    const isTransfer = task.allocation_type === 'transfer';

                    let label = isUpgrade ? 'Increase By' : 'Reduce By';
                    if (isTransfer) label = 'Transfer Amount';

                    let currentHeader = 'Current';
                    let newHeader = 'New Total';

                    if (isTransfer) {
                        if (task.status && task.status.name === 'Billable to Test') {
                            currentHeader = 'Billable';
                            newHeader = 'Test';
                        } else if (task.status && task.status.name === 'Test to Billable') {
                            currentHeader = 'Test';
                            newHeader = 'Billable';
                        }
                    }

                    const badgeClass = isTransfer ? 'custom-kam-task-management-value-badge-transfer' : (isUpgrade ? 'badge bg-success' : 'badge bg-warning text-dark');
                    const arrowIcon = isTransfer ? '<i class="fas fa-exchange-alt me-2"></i>' : (isUpgrade ? '<i class="fas fa-arrow-up me-2"></i>' : '<i class="fas fa-arrow-down me-2"></i>');

                    html += `
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="resource-alloc-service-cell"><i class="fas fa-tools me-2"></i>Service</th>
                                    <th><i class="fas fa-chart-line me-2"></i>${currentHeader}</th>
                                    <th>${arrowIcon}${label}</th>
                                    <th><i class="fas fa-equals me-2"></i>${newHeader}</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                    resourceDetails.forEach(detail => {
                        let amount, prev, next;
                        if (isTransfer) {
                            amount = detail.transfer_amount || 0;
                            prev = detail.current_source_quantity || 0;
                            next = detail.new_target_quantity || 0;
                        } else {
                            amount = isUpgrade ? (detail.upgrade_amount || 0) : (detail.downgrade_amount || 0);
                            next = detail.quantity || 0;
                            prev = isUpgrade ? (next - amount) : (next + amount);
                        }

                        const prevDisplay = prev < 0 ?
                            `<span class="text-danger fw-bold">${prev}</span>` :
                            `<span class="badge bg-secondary">${prev}</span>`;

                        const newDisplay = next < 0 ?
                            `<span class="text-danger fw-bold">${next}</span>` :
                            `<span class="resource-alloc-new-total-value">${next}</span>`;

                        // Display service name with unit inline
                        const serviceName = detail.service.service_name;
                        const serviceUnit = detail.service.unit ? ` <span class="resource-alloc-service-unit">(${detail.service.unit})</span>` : '';
                        const serviceDisplay = `<span class="resource-alloc-service-name">${serviceName}${serviceUnit}</span>`;

                        html += `
                        <tr>
                            <td class="resource-alloc-service-cell">${serviceDisplay}</td>
                            <td>${prevDisplay} ${detail.service.unit || ''}</td>
                            <td>
                                <span class="${badgeClass}">
                                    ${isUpgrade ? '+' : (isTransfer ? '' : '-')}${amount}
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
                    html = '<p class="text-center text-muted mb-0">No resource details found.</p>';
                }

                container.innerHTML = html;
                row.dataset.loaded = 'true';

                // Render Approve/Undo buttons if eligible
                if (data.is_approvable && !data.is_approved) {
                    const approvalHtml = `
                        <div class="kam-task-approval-actions mt-4 pt-3 border-top d-flex gap-2 justify-content-end" id="approval-actions-${taskId}">
                            <button class="custom-kam-task-management-action-btn custom-kam-task-management-approve-btn approve-task-btn" data-task-id="${taskId}">
                                <i class="fas fa-check-circle me-1"></i> Approve Task
                            </button>
                            <button class="custom-kam-task-management-action-btn custom-kam-task-management-undo-btn undo-task-btn" data-task-id="${taskId}">
                                <i class="fas fa-undo me-1"></i> Undo Task
                            </button>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', approvalHtml);

                    // Add event listeners for the new buttons
                    const approveBtn = container.querySelector('.approve-task-btn');
                    const undoBtn = container.querySelector('.undo-task-btn');

                    if (approveBtn) {
                        approveBtn.addEventListener('click', function () {
                            handleTaskAction(taskId, 'approve');
                        });
                    }

                    if (undoBtn) {
                        undoBtn.addEventListener('click', function () {
                            handleTaskAction(taskId, 'undo');
                        });
                    }
                } else if (data.is_approved) {
                    container.insertAdjacentHTML('beforeend', `
                        <div class="mt-4 pt-3 border-top text-end text-success fw-bold">
                            <i class="fas fa-check-double me-1"></i> Task Handled
                        </div>
                    `);
                }

                // Show actions if they exist
                if (actions) {
                    actions.style.display = 'flex';
                }
            })
            .catch(err => {
                container.innerHTML = '<div class="alert alert-danger mb-0">Error loading details.</div>';
            });
    }

    function handleTaskAction(taskId, action) {
        const approvalActions = document.getElementById(`approval-actions-${taskId}`);
        if (approvalActions) {
            approvalActions.style.opacity = '0.5';
            approvalActions.style.pointerEvents = 'none';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(`${config.baseUrl}/${taskId}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Success feedback
                    if (approvalActions) {
                        approvalActions.innerHTML = `
                        <div class="text-success fw-bold">
                            <i class="fas fa-check-circle me-1"></i> ${data.message}
                        </div>
                    `;
                        approvalActions.style.opacity = '1';
                    }
                    // Optional: refresh page or update status badge? 
                    // User said "buttons will disappear later on after clicking", so we just hide/replace them.
                } else {
                    alert(data.error || 'Action failed.');
                    if (approvalActions) {
                        approvalActions.style.opacity = '1';
                        approvalActions.style.pointerEvents = 'auto';
                    }
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while processing the request.');
                if (approvalActions) {
                    approvalActions.style.opacity = '1';
                    approvalActions.style.pointerEvents = 'auto';
                }
            });
    }
})();
