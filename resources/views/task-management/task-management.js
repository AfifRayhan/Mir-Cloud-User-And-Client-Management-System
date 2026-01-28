/**
 * Task Management Page JavaScript
 * Handles task viewing, deep linking, and expandable details
 */
(function () {
    // Get configuration from data attributes
    const configEl = document.getElementById('task-management-config');
    if (!configEl) {
        console.error('Task management config element not found');
        return;
    }

    const config = {
        baseUrl: configEl.dataset.baseUrl || '/task-management'
    };

    const viewButtons = document.querySelectorAll('.view-task-btn');
    const params = new URLSearchParams(window.location.search);
    const deepTaskId = params.get('dtid');
    const deepAction = params.get('da');

    // Deep link handling
    if (deepTaskId) {
        console.log('Deep link detected:', {
            dtid: deepTaskId,
            da: deepAction
        });

        // Clear query params immediately via replaceState to prevent reopening on reload
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({
            path: newUrl
        }, '', newUrl);

        if (deepAction === 'view') {
            const targetBtn = document.querySelector(`.view-task-btn[data-task-id="${deepTaskId}"]`);
            console.log('Target view button:', targetBtn);
            if (targetBtn) {
                setTimeout(() => {
                    console.log('Clicking target button');
                    targetBtn.click();
                    targetBtn.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 500);
            }
        } else if (deepAction === 'assign') {
            const assignBtn = document.querySelector(`button[data-bs-target="#assignModal${deepTaskId}"]`);
            console.log('Target assign button:', assignBtn);
            if (assignBtn) {
                setTimeout(() => {
                    console.log('Clicking assign button');
                    assignBtn.click();
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

                    const badgeClass = isTransfer ? 'custom-task-management-value-badge-transfer' : (isUpgrade ? 'badge bg-success' : 'badge bg-warning text-dark');
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
                                        ${(isTransfer || isUpgrade) ? '+' : '-'}${amount}
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
})();
