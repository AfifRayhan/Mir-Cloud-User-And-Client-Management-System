/**
 * Billing Task Management Page JavaScript
 * Handles task viewing, deep linking, and billing confirmation
 */
(function () {
    // Get configuration from data attributes
    const configEl = document.getElementById('billing-task-management-config');
    if (!configEl) {
        console.error('Billing task management config element not found');
        return;
    }

    const config = {
        baseUrl: configEl.dataset.baseUrl || '/billing-task-management'
    };

    const viewButtons = document.querySelectorAll('.view-task-btn');

    // View button click handlers
    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const taskId = this.dataset.taskId;
            const detailsRow = document.getElementById('details-' + taskId);
            const btnText = this.querySelector('.btn-text');
            const btnIcon = this.querySelector('i');

            if (detailsRow.style.display === 'none') {
                // Close others
                document.querySelectorAll('.task-details-row').forEach(row => row.style.display = 'none');
                document.querySelectorAll('.view-task-btn').forEach(btn => {
                    btn.querySelector('.btn-text').textContent = 'View';
                    btn.querySelector('i').className = 'fas fa-eye me-1';
                });

                detailsRow.style.display = 'table-row';
                btnText.textContent = 'Hide';
                btnIcon.className = 'fas fa-eye-slash me-1';

                if (!detailsRow.dataset.loaded) {
                    loadTaskDetails(taskId);
                }
            } else {
                detailsRow.style.display = 'none';
                btnText.textContent = 'View';
                btnIcon.className = 'fas fa-eye me-1';
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

                    const badgeClass = isTransfer ? 'custom-billing-task-management-value-badge-transfer' : (isUpgrade ? 'badge bg-success' : 'badge bg-warning text-dark');

                    html += `
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle bg-white mb-0 shadow-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Service</th>
                                        <th>${currentHeader}</th>
                                        <th>${label}</th>
                                        <th>${newHeader}</th>
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

                        html += `
                            <tr>
                                <td>${detail.service.service_name} ${detail.service.unit ? `(${detail.service.unit})` : ''}</td>
                                <td><span class="badge bg-light text-dark border">${prev}</span></td>
                                <td><span class="${badgeClass}">${isUpgrade ? '+' : (isTransfer ? '' : '-')}${amount}</span></td>
                                <td><span class="fw-bold text-primary">→ ${next}</span></td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table></div>`;
                } else {
                    html = '<div class="text-center text-muted py-3">No details found</div>';
                }

                container.innerHTML = html;
                detailsRow.dataset.loaded = 'true';
            });
    }

    // Billing Modal handling
    const billingConfirmModal = document.getElementById('billingConfirmModal');
    if (billingConfirmModal) {
        billingConfirmModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget.closest('.bill-task-btn');
            if (!button) return;

            const actionUrl = button.getAttribute('data-action-url');
            const customerName = button.getAttribute('data-customer-name');

            const form = this.querySelector('#billingConfirmForm');
            const customerNameSpan = this.querySelector('#modalCustomerName');

            form.action = actionUrl;
            customerNameSpan.textContent = customerName;
        });
    }

    // Auto-open task details if query params exist (e.g. from email link)
    const urlParams = new URLSearchParams(window.location.search);
    const dtid = urlParams.get('dtid');
    const da = urlParams.get('da');

    if (dtid && da === 'view') {
        const targetBtn = document.querySelector(`.view-task-btn[data-task-id="${dtid}"]`);
        if (targetBtn) {
            // Slight delay to ensure DOM is fully ready / other scripts initialized
            setTimeout(() => {
                targetBtn.click();
                targetBtn.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 500);
        }
    }
})();
