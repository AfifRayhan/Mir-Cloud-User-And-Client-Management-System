<x-app-layout>
    <div class="container-fluid custom-customer-index-container py-4">
        <!-- Background Elements -->
        <div class="custom-customer-index-bg-pattern"></div>
        <div class="custom-customer-index-bg-circle circle-1"></div>
        <div class="custom-customer-index-bg-circle circle-2"></div>

        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="custom-customer-index-header">
                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-center justify-content-between gap-3">
                        <div>
                            <h1 class="custom-customer-index-title fw-bold mb-2">Customer Management</h1>
                            <p class="custom-customer-index-subtitle text-muted">
                                View and manage all customers in the system
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
                <div class="custom-customer-index-alert alert alert-success alert-dismissible fade show" role="alert">
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

        <!-- Search and Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card custom-customer-index-filter-card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <form action="{{ route('customers.index') }}" method="GET" id="filterForm">
                            <div class="row g-3 align-items-end">
                                <!-- Search -->
                                <div class="col-12 col-md-4">
                                    <label class="form-label small fw-bold text-muted">Search Customer</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                               placeholder="Search by name..." value="{{ request('search') }}">
                                    </div>
                                </div>

                                <!-- Platform Filter -->
                                <div class="col-12 col-md-3">
                                    <label class="form-label small fw-bold text-muted">Filter by Platform</label>
                                    <select name="platform_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">All Platforms</option>
                                        @foreach($platforms as $platform)
                                            <option value="{{ $platform->id }}" {{ request('platform_id') == $platform->id ? 'selected' : '' }}>
                                                {{ $platform->platform_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div class="col-12 col-md-3">
                                    <label class="form-label small fw-bold text-muted">Filter by Status</label>
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="">All Statuses</option>
                                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active customers (Has Resources)</option>
                                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive customers (No Resources)</option>
                                    </select>
                                </div>

                                <!-- Actions -->
                                <div class="col-12 col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    @if(request()->anyFilled(['search', 'platform_id', 'status']))
                                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary" title="Clear All">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-12">
                @if($customers->isEmpty())
                <!-- Empty State -->
                <div class="card custom-customer-index-empty-card border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <div class="custom-customer-index-empty-icon mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="text-muted" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                            </svg>
                        </div>
                        <h5 class="custom-customer-index-empty-title mb-3">No Customers Found</h5>
                        <p class="custom-customer-index-empty-text text-muted mb-4">
                            Get started by adding your first customer to the system.
                        </p>
                        <a href="{{ route('customers.create') }}" class="btn btn-primary custom-customer-index-empty-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                            </svg>
                            Add First Customer
                        </a>
                    </div>
                </div>
                @else
                <!-- Customer Table -->
                <div class="card custom-customer-index-card border-0 shadow-lg">
                    <div class="card-header custom-customer-index-card-header border-0 bg-white">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                            <div>
                                <h5 class="custom-customer-index-card-title mb-1">Customer List</h5>
                                <p class="custom-customer-index-card-subtitle text-muted mb-0">
                                    Total {{ $customers->total() }} customer(s)
                                </p>
                            </div>
                            <div class="custom-customer-index-stats">
                                <div class="d-flex gap-3">
                                    <div class="text-center">
                                        <div class="custom-customer-index-stat-number">{{ $customers->total() }}</div>
                                        <div class="custom-customer-index-stat-label">Total</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="custom-customer-index-table-responsive">
                            <table class="table custom-customer-index-table mb-0">
                                <thead class="custom-customer-index-table-head">
                                    <tr>
                                        <th class="custom-customer-index-table-header">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-building me-2"></i>Customer
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="ms-1" viewBox="0 0 16 16">
                                                    <path d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293V2.5zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zM7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1z" />
                                                </svg>
                                            </div>
                                        </th>
                                        <th class="custom-customer-index-table-header">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar-check me-2"></i>Customer Activation Date
                                            </div>
                                        </th>
                                        <th class="custom-customer-index-table-header">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-hashtag me-2"></i>PO Number
                                            </div>
                                        </th>
                                        <th class="custom-customer-index-table-header">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-server me-2"></i>Platform
                                            </div>
                                        </th>
                                        <th class="custom-customer-index-table-header">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-tie me-2"></i>Submitted By
                                            </div>
                                        </th>
                                        <th class="custom-customer-index-table-header">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-clock me-2"></i>Created At
                                            </div>
                                        </th>
                                        <th class="custom-customer-index-table-header text-end" style="min-width: 180px;">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <i class="fas fa-cogs me-2"></i>Actions
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="custom-customer-index-table-body">
                                    @foreach($customers as $customer)
                                    <tr class="custom-customer-index-table-row">
                                        <td class="custom-customer-index-table-cell">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="custom-customer-index-customer-name fw-semibold">
                                                        {{ $customer->customer_name }}
                                                    </div>
                                                    @if($customer->customer_address)
                                                    <div class="custom-customer-index-customer-address text-muted small">
                                                        {{ Str::limit($customer->customer_address, 30) }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="custom-customer-index-table-cell">
                                            <div class="custom-customer-index-date">
                                                <div class="custom-customer-index-date-day">
                                                    {{ $customer->customer_activation_date->format('d') }}
                                                </div>
                                                <div class="custom-customer-index-date-details">
                                                    <div class="custom-customer-index-date-month">
                                                        {{ $customer->customer_activation_date->format('M') }}
                                                    </div>
                                                    <div class="custom-customer-index-date-year">
                                                        {{ $customer->customer_activation_date->format('Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="custom-customer-index-table-cell">
                                            @if($customer->po_number)
                                            <span class="custom-customer-index-badge bg-primary bg-opacity-10 text-primary">
                                                {{ $customer->po_number }}
                                            </span>
                                            @else
                                            <span class="custom-customer-index-badge bg-secondary bg-opacity-10 text-secondary">
                                                N/A
                                            </span>
                                            @endif
                                        </td>
                                        <td class="custom-customer-index-table-cell">
                                            @if($customer->platform)
                                            <span class="custom-customer-index-platform">
                                                {{ $customer->platform->platform_name }}
                                            </span>
                                            @else
                                            <span class="text-muted">Any</span>
                                            @endif
                                        </td>
                                        <td class="custom-customer-index-table-cell">
                                            <div class="d-flex align-items-center">
                                                <span class="custom-customer-index-user-name">
                                                    {{ $customer->submitter->name ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="custom-customer-index-table-cell">
                                            <span class="custom-customer-index-created-at">
                                                {{ $customer->created_at->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td class="custom-customer-index-table-cell text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('customers.show', $customer->id) }}"
                                                    class="btn btn-sm custom-customer-index-action-btn custom-customer-index-view-btn"
                                                    title="View Customer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                    </svg>
                                                    <span class="d-none d-sm-inline">View</span>
                                                </a>
                                                <a href="{{ route('customers.edit', $customer->id) }}"
                                                    class="btn btn-sm custom-customer-index-action-btn custom-customer-index-edit-btn"
                                                    title="Edit Customer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                                                    </svg>
                                                    <span class="d-none d-sm-inline">Edit</span>
                                                </a>
                                                @if(Auth::user()->isAdmin() || Auth::user()->isManagement())
                                                <button type="button"
                                                        class="btn btn-sm custom-customer-index-action-btn custom-customer-index-delete-btn"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteCustomerModal{{ $customer->id }}"
                                                        title="Delete Customer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                    </svg>
                                                    <span class="d-none d-sm-inline">Delete</span>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Card Footer -->
                    <div class="card-footer custom-customer-index-card-footer border-0 bg-white">
                        <div class="d-flex justify-content-center">
                            {{ $customers->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modals -->
    @foreach($customers as $customer)
    @if(Auth::user()->isAdmin() || Auth::user()->isManagement())
    <div class="modal fade text-start" id="deleteCustomerModal{{ $customer->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Delete Customer
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-minus fa-3x text-danger opacity-25"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Confirm Deletion</h5>
                    <p class="text-muted mb-3">
                        Are you sure you want to permanently delete <strong>{{ $customer->customer_name }}</strong>?
                    </p>
                    <div class="alert alert-warning border-0 small text-start mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>This will also delete all related:
                        <ul class="mb-0 mt-2 list-unstyled ps-3">
                            <li>• Cloud details</li>
                            <li>• Resource allocations</li>
                            <li>• All associated data</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('customers.destroy', $customer->id) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="fas fa-trash-alt me-1"></i> Delete Permanent
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</x-app-layout>