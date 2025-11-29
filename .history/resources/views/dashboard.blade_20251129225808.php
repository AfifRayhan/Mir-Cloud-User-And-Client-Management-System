<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 fw-bold mb-0">Dashboard</h1>
                <p class="text-muted">Welcome back, {{ Auth::user()->name }}!</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1">Customers</h5>
                                <p class="text-muted mb-0 small">Manage customer information</p>
                            </div>
                        </div>
                        <hr>
                        <a href="{{ route('customers.create') }}" class="btn btn-primary w-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg>
                            Add New Customer
                        </a>
                    </div>
                </div>
            </div>

            @if(Auth::user()->isAdmin())
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-success" viewBox="0 0 16 16">
                                        <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-9.5-3a5.5 5.5 0 1 1 11 0 5.5 5.5 0 0 1-11 0m16.5-3a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h1.5V1.5a.5.5 0 0 1 .5-.5"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1">Users</h5>
                                <p class="text-muted mb-0 small">Manage system users</p>
                            </div>
                        </div>
                        <hr>
                        <a href="{{ route('register') }}" class="btn btn-success w-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg>
                            Add New User
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1">Resource Allocation</h5>
                                <p class="text-muted mb-0 small">Dismantle or rewrite customer resources</p>
                            </div>
                        </div>
                        <hr>
                        <a href="{{ route('resource-allocation.index') }}" class="btn btn-primary w-100">
                            Manage Resource Allocation
                        </a>
                    </div>
                </div>
            </div>

            @if(Auth::user()->isAdmin() || Auth::user()->isProTech())
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-success" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1">Assign Tasks</h5>
                                <p class="text-muted mb-0 small">Create and assign tasks to team members</p>
                            </div>
                        </div>
                        <hr>
                        <a href="{{ route('tasks.create') }}" class="btn btn-success w-100">
                            Assign New Task
                        </a>
                    </div>
                </div>
            </div>
            @endif

            @if(Auth::user()->isAdmin() || Auth::user()->isProTechOrTech())
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-info" viewBox="0 0 16 16">
                                        <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                        <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1">Tasks</h5>
                                <p class="text-muted mb-0 small">View and manage your tasks</p>
                            </div>
                        </div>
                        <hr>
                        <a href="{{ route('tasks.index') }}" class="btn btn-info text-white w-100">
                            View Tasks
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
