<x-app-layout>
    <div class="container-fluid custom-customer-index-container py-4">
        <!-- Background Elements -->
        <div class="custom-customer-index-bg-pattern"></div>
        <div class="custom-customer-index-bg-circle circle-1"></div>
        <div class="custom-customer-index-bg-circle circle-2"></div>

        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="custom-customer-index-header">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <h1 class="custom-customer-index-title fw-bold mb-1">{{ $customer->customer_name }}</h1>
                                <p class="custom-customer-index-subtitle text-muted mb-0">Customer Information</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary custom-customer-index-add-btn">
                                <i class="fas fa-edit me-2"></i> Edit Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Company Details -->
            <div class="col-lg-8">
                <div class="card custom-customer-index-card border-0 shadow-lg h-100">
                    <div class="card-header custom-customer-index-card-header border-0 bg-white">
                        <h5 class="custom-customer-index-card-title mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>Company Profile
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Platform</label>
                                <div class="fs-5 fw-semibold text-primary">
                                    {{ $customer->platform->platform_name ?? 'Any' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Customer Activation Date</label>
                                <div class="fs-5 fw-semibold">
                                    {{ $customer->customer_activation_date->format('F d, Y') }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Address</label>
                                <div class="fs-6 py-2 px-3 bg-light rounded border border-light-subtle">
                                    {{ $customer->customer_address ?: 'No address provided' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold text-uppercase mb-1 d-block">BIN Number</label>
                                <div class="fs-6 fw-medium">
                                    {{ $customer->bin_number ?: 'N/A' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold text-uppercase mb-1 d-block">PO Number</label>
                                <div class="fs-6 fw-medium">
                                    {{ $customer->po_number ?: 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-10">

                        <h6 class="fw-bold mb-4">System Information</h6>
                        <div class="row g-4 text-muted small">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-plus me-2 opacity-50"></i>
                                    <span>Submitted By: <strong>{{ $customer->submitter->name ?? 'System' }}</strong></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock me-2 opacity-50"></i>
                                    <span>Created At: <strong>{{ $customer->created_at->format('M d, Y H:i') }}</strong></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-check me-2 opacity-50"></i>
                                    <span>Last Processed By: <strong>{{ $customer->processor->name ?? 'N/A' }}</strong></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-sync-alt me-2 opacity-50"></i>
                                    <span>Last Updated: <strong>{{ $customer->updated_at->format('M d, Y H:i') }}</strong></span>
                                </div>
                            </div>
                            
                            @if($customer->po_project_sheets && count($customer->po_project_sheets) > 0)
                            <div class="col-12 mt-4 pt-3 border-top border-light">
                                <h6 class="fw-bold mb-3 small text-dark">
                                    <i class="fas fa-file-invoice text-danger me-2"></i>PO Project Sheets
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($customer->po_project_sheets as $sheet)
                                        <div class="d-flex align-items-center p-2 bg-light rounded border border-light-subtle transition-all hover-shadow-sm"
                                             style="min-width: 200px; border-left: 3px solid #dc3545 !important; cursor: pointer;"
                                             onclick="previewPdf('{{ asset('storage/' . $sheet['path']) }}', '{{ addslashes($sheet['name']) }}', '{{ round($sheet['size'] / 1024, 2) }} KB')">
                                            <i class="fas fa-file-pdf text-danger me-2 fa-lg"></i>
                                            <div class="overflow-hidden flex-grow-1">
                                                <div class="text-truncate small fw-bold text-dark" title="{{ $sheet['name'] }}">{{ $sheet['name'] }}</div>
                                                <div class="x-small text-muted" style="font-size: 0.7rem;">{{ round($sheet['size'] / 1024, 2) }} KB</div>
                                            </div>
                                            <div class="ms-2 text-muted small opacity-50">
                                                <i class="fas fa-eye"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Contacts -->
            <div class="col-lg-4">
                <div class="row g-4">
                    <!-- Commercial Contact -->
                    <div class="col-12">
                        <div class="card custom-customer-index-card border-0 shadow-lg border-start border-4 border-primary">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary">
                                        <i class="fas fa-briefcase fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Commercial Contact</h6>
                                </div>
                                @if($customer->commercial_contact_name)
                                    <div class="mb-2 fw-bold text-dark">{{ $customer->commercial_contact_name }}</div>
                                    <div class="small text-muted mb-3">{{ $customer->commercial_contact_designation ?: 'N/A' }}</div>
                                    <div class="d-flex align-items-center mb-2 small">
                                        <i class="fas fa-envelope me-2 text-muted" style="width: 16px;"></i>
                                        <a href="mailto:{{ $customer->commercial_contact_email }}" class="text-decoration-none text-primary">{{ $customer->commercial_contact_email ?: 'N/A' }}</a>
                                    </div>
                                    <div class="d-flex align-items-center small">
                                        <i class="fas fa-phone me-2 text-muted" style="width: 16px;"></i>
                                        <span>{{ $customer->commercial_contact_phone ?: 'N/A' }}</span>
                                    </div>
                                @else
                                    <div class="text-muted small italic">No commercial contact specified</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Technical Contact -->
                    <div class="col-12">
                        <div class="card custom-customer-index-card border-0 shadow-lg border-start border-4 border-success">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success bg-opacity-10 p-2 rounded me-3 text-success">
                                        <i class="fas fa-code fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Technical Contact</h6>
                                </div>
                                @if($customer->technical_contact_name)
                                    <div class="mb-2 fw-bold text-dark">{{ $customer->technical_contact_name }}</div>
                                    <div class="small text-muted mb-3">{{ $customer->technical_contact_designation ?: 'N/A' }}</div>
                                    <div class="d-flex align-items-center mb-2 small">
                                        <i class="fas fa-envelope me-2 text-muted" style="width: 16px;"></i>
                                        <a href="mailto:{{ $customer->technical_contact_email }}" class="text-decoration-none text-success">{{ $customer->technical_contact_email ?: 'N/A' }}</a>
                                    </div>
                                    <div class="d-flex align-items-center small">
                                        <i class="fas fa-phone me-2 text-muted" style="width: 16px;"></i>
                                        <span>{{ $customer->technical_contact_phone ?: 'N/A' }}</span>
                                    </div>
                                @else
                                    <div class="text-muted small italic">No technical contact specified</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Optional Contact -->
                    
                    <div class="col-12">
                        <div class="card custom-customer-index-card border-0 shadow-lg border-start border-4 border-info">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-info bg-opacity-10 p-2 rounded me-3 text-info">
                                        <i class="fas fa-user fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Optional Contact</h6>
                                </div>
                                <div class="mb-2 fw-bold text-dark">{{ $customer->optional_contact_name }}</div>
                                <div class="small text-muted mb-3">{{ $customer->optional_contact_designation ?: 'N/A' }}</div>
                                <div class="d-flex align-items-center mb-2 small">
                                    <i class="fas fa-envelope me-2 text-muted" style="width: 16px;"></i>
                                    <a href="mailto:{{ $customer->optional_contact_email }}" class="text-decoration-none text-info">{{ $customer->optional_contact_email ?: 'N/A' }}</a>
                                </div>
                                <div class="d-flex align-items-center small">
                                    <i class="fas fa-phone me-2 text-muted" style="width: 16px;"></i>
                                    <span>{{ $customer->optional_contact_phone ?: 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <!-- PDF Preview Modal -->
        <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" style="max-height: 95vh;">
                <div class="modal-content shadow-2xl border-0">
                    <div class="modal-header border-bottom bg-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 p-2 rounded me-3 text-danger">
                                <i class="fas fa-file-pdf fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark" id="pdfPreviewModalLabel">PDF Preview</h5>
                                <p class="text-muted small mb-0" id="pdfPreviewModalSublabel"></p>
                            </div>
                        </div>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <a href="#" id="pdfDownloadBtn" class="btn btn-sm btn-outline-secondary px-3" download>
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                            <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body p-0" style="height: 80vh; background: #525659;">
                        <iframe id="pdfPreviewFrame" src="" class="w-100 h-100 border-0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pdfPreviewModal = document.getElementById('pdfPreviewModal');
            const pdfPreviewFrame = document.getElementById('pdfPreviewFrame');
            const pdfPreviewModalLabel = document.getElementById('pdfPreviewModalLabel');
            const pdfPreviewModalSublabel = document.getElementById('pdfPreviewModalSublabel');
            const pdfDownloadBtn = document.getElementById('pdfDownloadBtn');

            // Handle preview button click
            window.previewPdf = function(url, name, size) {
                pdfPreviewModalLabel.textContent = name;
                pdfPreviewModalSublabel.textContent = `File size: ${size}`;
                pdfDownloadBtn.href = url;
                pdfDownloadBtn.setAttribute('download', name); // Force download with specific name
                pdfPreviewFrame.src = url;
                
                const modal = new bootstrap.Modal(pdfPreviewModal);
                modal.show();
            };

            // Clear iframe src when modal is hidden to free memory
            pdfPreviewModal.addEventListener('hidden.bs.modal', function () {
                pdfPreviewFrame.src = '';
            });
        });
    </script>
</x-app-layout>
