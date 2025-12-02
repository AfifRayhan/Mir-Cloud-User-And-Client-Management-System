<x-app-layout>
    <div class="container-fluid custom-dashboard-container py-4">
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="custom-dashboard-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div>
                            <h1 class="custom-dashboard-title fw-bold mb-2">Dashboard</h1>
                            <p class="custom-dashboard-subtitle text-muted mb-0">
                                Welcome back, <span class="text-primary fw-semibold">{{ Auth::user()->name }}</span>!
                            </p>
                        </div>
                        <div class="custom-dashboard-date">
                            <span class="badge custom-dashboard-date-badge bg-light text-dark">
                                <i class="fas fa-calendar-alt me-2"></i>
                                {{ now()->format('F j, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="custom-dashboard-alert alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-3 fs-4"></i>
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

        <!-- Dashboard Cards Grid -->
        <div class="row g-4 mb-5">
            <!-- Customers Card -->
            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ route('customers.index') }}" class="custom-dashboard-card-link">
                    <div class="card custom-dashboard-card custom-dashboard-card-primary border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-4">
                                <div class="custom-dashboard-icon-wrapper bg-primary bg-opacity-10">
                                    <i class="fas fa-users text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="custom-dashboard-card-title mb-1">Customer Management</h5>
                                    <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                        Manage customer information
                                    </p>
                                </div>
                            </div>
                            <div class="custom-dashboard-card-footer">
                                <span class="custom-dashboard-card-action">
                                    Access Customers
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </span>
                            </div>
                        </div>
                        <div class="custom-dashboard-card-hover"></div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ route('customers.create') }}" class="custom-dashboard-card-link">
                    <div class="card custom-dashboard-card custom-dashboard-card-primary border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-4">
                                <div class="custom-dashboard-icon-wrapper bg-primary bg-opacity-10">
                                    <i class="fas fa-users text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="custom-dashboard-card-title mb-1">Add New Customer</h5>
                                    <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                        New Customer Onboard
                                    </p>
                                </div>
                            </div>
                            <div class="custom-dashboard-card-footer">
                                <span class="custom-dashboard-card-action">
                                    Add Customers
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </span>
                            </div>
                        </div>
                        <div class="custom-dashboard-card-hover"></div>
                    </div>
                </a>
            </div>

            <!-- Resource Allocation Card -->
            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ route('resource-allocation.index') }}" class="custom-dashboard-card-link">
                    <div class="card custom-dashboard-card custom-dashboard-card-info border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-4">
                                <div class="custom-dashboard-icon-wrapper bg-info bg-opacity-10">
                                    <i class="fas fa-server text-info"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="custom-dashboard-card-title mb-1">Customer Resource Management</h5>
                                    <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                        Dismantle or rewrite customer resources
                                    </p>
                                </div>
                            </div>
                            <div class="custom-dashboard-card-footer">
                                <span class="custom-dashboard-card-action">
                                    Manage Resource Allocation
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </span>
                            </div>
                        </div>
                        <div class="custom-dashboard-card-hover"></div>
                    </div>
                </a>
            </div>

            <!-- Admin Only Cards -->
            @if(Auth::user()->isAdmin())
                <!-- Users Card -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('users.index') }}" class="custom-dashboard-card-link">
                        <div class="card custom-dashboard-card custom-dashboard-card-success border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="custom-dashboard-icon-wrapper bg-success bg-opacity-10">
                                        <i class="fas fa-user-plus text-success"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="custom-dashboard-card-title mb-1">User Management</h5>
                                        <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                            Manage system users
                                        </p>
                                    </div>
                                </div>
                                <div class="custom-dashboard-card-footer">
                                    <span class="custom-dashboard-card-action">
                                        User Edit or Delete
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="custom-dashboard-card-hover"></div>
                        </div>
                    </a>
                </div>


                <!-- Users Card -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('register') }}" class="custom-dashboard-card-link">
                        <div class="card custom-dashboard-card custom-dashboard-card-success border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="custom-dashboard-icon-wrapper bg-success bg-opacity-10">
                                        <i class="fas fa-user-plus text-success"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="custom-dashboard-card-title mb-1">User Management</h5>
                                        <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                            Manage system users
                                        </p>
                                    </div>
                                </div>
                                <div class="custom-dashboard-card-footer">
                                    <span class="custom-dashboard-card-action">
                                        Add New User
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="custom-dashboard-card-hover"></div>
                        </div>
                    </a>
                </div>

                <!-- Platforms Card -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('platforms.index') }}" class="custom-dashboard-card-link">
                        <div class="card custom-dashboard-card custom-dashboard-card-secondary border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="custom-dashboard-icon-wrapper bg-secondary bg-opacity-10">
                                        <i class="fas fa-cloud text-secondary"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="custom-dashboard-card-title mb-1">Platform Management</h5>
                                        <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                            Manage cloud platforms
                                        </p>
                                    </div>
                                </div>
                                <div class="custom-dashboard-card-footer">
                                    <span class="custom-dashboard-card-action">
                                        Manage Platforms
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="custom-dashboard-card-hover"></div>
                        </div>
                    </a>
                </div>

                <!-- Services Card -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('services.index') }}" class="custom-dashboard-card-link">
                        <div class="card custom-dashboard-card custom-dashboard-card-warning border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="custom-dashboard-icon-wrapper bg-warning bg-opacity-10">
                                        <i class="fas fa-cogs text-warning"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="custom-dashboard-card-title mb-1">Service Management</h5>
                                        <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                            Manage available services
                                        </p>
                                    </div>
                                </div>
                                <div class="custom-dashboard-card-footer">
                                    <span class="custom-dashboard-card-action">
                                        Manage Services
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="custom-dashboard-card-hover"></div>
                        </div>
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>