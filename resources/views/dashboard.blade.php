<x-app-layout>
    <div class="container-fluid custom-dashboard-container py-4">
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="custom-dashboard-header">
                    <div class="d-flex justify-content-center align-items-center position-relative">
                        <div class="text-center">
                            <h1 class="custom-dashboard-title fw-bold mb-2">
                                Dashboard
                            </h1>
                            <p class="custom-dashboard-subtitle text-muted mb-0">
                                Welcome back,
                                <span class="text-primary fw-semibold">{{ Auth::user()->name }}</span>!
                            </p>
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
            @if(Auth::user()->isAdmin() || Auth::user()->isProKam()|| Auth::user()->isKam() || Auth::user()->isProTech() || Auth::user()->isManagement())
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
            @endif
            @if(Auth::user()->isAdmin() || Auth::user()->isProKam()|| Auth::user()->isKam() || Auth::user()->isProTech() || Auth::user()->isManagement())
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('customers.create') }}" class="custom-dashboard-card-link">
                        <div class="card custom-dashboard-card custom-dashboard-card-primary border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="custom-dashboard-icon-wrapper bg-primary bg-opacity-10">
                                        <i class="fas fa-user-plus text-primary"></i>
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
            @endif

            <!-- Resource Allocation Card -->
            @if(Auth::user()->isAdmin() || Auth::user()->isProKam()|| Auth::user()->isKam() || Auth::user()->isProTech() || Auth::user()->isManagement())
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('resource-allocation.index') }}" class="custom-dashboard-card-link">
                        <div class="card custom-dashboard-card custom-dashboard-card-info border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="custom-dashboard-icon-wrapper bg-info bg-opacity-10">
                                        <i class="fas fa-server text-info"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="custom-dashboard-card-title mb-1">Resource Allocation</h5>
                                        <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                            Update or dismantle customer resources
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
            @endif

            <!-- Admin Only Cards -->
            @if(Auth::user()->isAdmin())
                <!-- Users Management Card -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('users.index') }}" class="custom-dashboard-card-link">
                        <div class="card custom-dashboard-card custom-dashboard-card-success border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="custom-dashboard-icon-wrapper bg-success bg-opacity-10">
                                        <i class="fas fa-users text-success"></i>
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
                    <a href="{{ route('users.create') }}" class="custom-dashboard-card-link">
                        <div class="card custom-dashboard-card custom-dashboard-card-success border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="custom-dashboard-icon-wrapper bg-success bg-opacity-10">
                                        <i class="fas fa-user-plus text-success"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="custom-dashboard-card-title mb-1">Add New User</h5>
                                        <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                            Add new user
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
            @endif


                <!-- Platforms Card -->
                @if(Auth::user()->isAdmin() || Auth::user()->isProTechOrTech() || Auth::user()->isManagement())
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
                @endif

                <!-- Services Card -->
                @if(Auth::user()->isAdmin() || Auth::user()->isProTech() || Auth::user()->isManagement())
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
            

                
 
             <!-- Task Management Card -->
                @if(Auth::user()->isAdmin() || Auth::user()->isProTech() || Auth::user()->isManagement())
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="position-relative">
                            @if(isset($unassignedTaskCount) && $unassignedTaskCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="z-index: 1000;">
                                    {{ $unassignedTaskCount }}
                                    <span class="visually-hidden">unassigned tasks</span>
                                </span>
                            @endif
                            <a href="{{ route('task-management.index') }}" class="custom-dashboard-card-link">
                                <div class="card custom-dashboard-card custom-dashboard-card-danger border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start mb-4">
                                            <div class="custom-dashboard-icon-wrapper bg-danger bg-opacity-10">
                                                <i class="fas fa-tasks text-danger"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h5 class="custom-dashboard-card-title mb-1">Task Management</h5>
                                                <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                                    View and assign tasks
                                                </p>
                                            </div>
                                        </div>
                                        <div class="custom-dashboard-card-footer">
                                            <span class="custom-dashboard-card-action">
                                                Manage Tasks
                                                <i class="fas fa-arrow-right ms-2"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="custom-dashboard-card-hover"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endif

            <!-- KAM Task Management Card -->
            @if(Auth::user()->isAdmin() || Auth::user()->isProKam() || Auth::user()->isKam())
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('kam-task-management.index') }}" class="custom-dashboard-card-link">
                        <div class="card custom-dashboard-card custom-dashboard-card-success border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="custom-dashboard-icon-wrapper bg-success bg-opacity-10">
                                        <i class="fas fa-tasks text-success"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="custom-dashboard-card-title mb-1">KAM Task Management</h5>
                                        <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                            Edit or delete unassigned tasks
                                         </p>
                                    </div>
                                </div>
                                <div class="custom-dashboard-card-footer">
                                    <span class="custom-dashboard-card-action">
                                        Manage Tasks
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="custom-dashboard-card-hover"></div>
                        </div>
                    </a>
                </div>
            @endif

            <!-- Billing Task Management Card -->
            @if(Auth::user()->isAdmin() || Auth::user()->isBill())
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('billing-task-management.index') }}" class="custom-dashboard-card-link">
                        <div class="card custom-dashboard-card custom-dashboard-card-info border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="custom-dashboard-icon-wrapper bg-info bg-opacity-10">
                                        <i class="fas fa-file-invoice text-info"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="custom-dashboard-card-title mb-1">Billing Task Management</h5>
                                        <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                            View completed tasks and reports
                                         </p>
                                    </div>
                                </div>
                                <div class="custom-dashboard-card-footer">
                                    <span class="custom-dashboard-card-action">
                                        Manage Billing Tasks
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="custom-dashboard-card-hover"></div>
                        </div>
                    </a>
                </div>
            @endif
 
            <!-- Tech Resource Management Card -->
            @if(Auth::user()->isTech() || Auth::user()->isAdmin())
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('tech-resource-allocation.index') }}" class="custom-dashboard-card-link">
                        <div class="card custom-dashboard-card custom-dashboard-card-purple border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="custom-dashboard-icon-wrapper bg-purple bg-opacity-10" style="background-color: rgba(139, 92, 246, 0.1);">
                                        <i class="fas fa-server text-info-purple" style="color: #8b5cf6;"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="custom-dashboard-card-title mb-1">Tech Resource Allocation</h5>
                                        <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                            Rapid allocation and auto-completion
                                        </p>
                                    </div>
                                </div>
                                <div class="custom-dashboard-card-footer">
                                    <span class="custom-dashboard-card-action">
                                        Quick Allocation
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="custom-dashboard-card-hover"></div>
                        </div>
                    </a>
                </div>
            @endif

            <!-- My Tasks Card -->
            @if(Auth::user()->isAdmin() || Auth::user()->isProTechOrTech())
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="position-relative">
                        @if(isset($incompleteTaskCount) && $incompleteTaskCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="z-index: 1000;">
                                {{ $incompleteTaskCount }}
                                <span class="visually-hidden">incomplete tasks</span>
                            </span>
                        @endif
                        <a href="{{ route('my-tasks.index') }}" class="custom-dashboard-card-link">
                            <div class="card custom-dashboard-card custom-dashboard-card-danger border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start mb-4">
                                        <div class="custom-dashboard-icon-wrapper bg-danger bg-opacity-10">
                                            <i class="fas fa-clipboard-list text-danger"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="custom-dashboard-card-title mb-1">My Tasks</h5>
                                            <p class="custom-dashboard-card-subtitle text-muted mb-0">
                                                View your assigned tasks
                                            </p>
                                        </div>
                                    </div>
                                    <div class="custom-dashboard-card-footer">
                                        <span class="custom-dashboard-card-action">
                                            View My Tasks
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="custom-dashboard-card-hover"></div>
                            </div>
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>