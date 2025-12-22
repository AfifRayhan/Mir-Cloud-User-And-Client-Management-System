<x-app-layout>
    <div class="container-fluid custom-platform-management-container py-2">
        <!-- Background Elements -->
        <div class="custom-platform-management-bg-pattern"></div>
        <div class="custom-platform-management-bg-circle circle-1"></div>
        <div class="custom-platform-management-bg-circle circle-2"></div>

        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="custom-platform-management-header">
                    <div>
                        <h1 class="custom-platform-management-title fw-bold mb-2">Platform Management</h1>
                        <p class="custom-platform-management-subtitle text-muted">
                            Add or remove cloud platforms such as ACS or Huawei.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="custom-user-management-alert alert alert-success alert-dismissible fade show" role="alert">
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

        <!-- Error Alert -->
        @if($errors->any())
        <div class="row mb-4">
            <div class="col-12">
                <div class="custom-user-management-alert alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="me-3" viewBox="0 0 16 16">
                            <path d="M8 8a1 1 0 0 1 1 1v.01a1 1 0 1 1-2 0V9a1 1 0 0 1 1-1zm.25-2.25a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0v-1.5z" />
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 0 1.5 0v-.25a.75.75 0 0 0-.75-.75h-.25a.75.75 0 0 0-.75.75V9a2 2 0 1 1-4 0v-.25a.75.75 0 0 0-.75-.75h-.25a.75.75 0 0 0-.75.75v.25a.75.75 0 0 0 1.5 0v-4.5A.75.75 0 0 1 8 4z" />
                        </svg>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1">Error!</h6>
                            <p class="mb-0">{{ $errors->first() }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        @endif

        <div class="row g-4">
            <!-- Add Platform Form -->
            <div class="col-lg-5">
                <div class="custom-platform-management-card">
                    <div class="custom-platform-management-card-header">
                        <h5 class="custom-platform-management-card-title">Add a Platform</h5>
                    </div>
                    <div class="card-body p-3">
                        <form method="POST" action="{{ route('platforms.store') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="platform_name" class="form-label">Platform Name</label>
                                <input type="text" id="platform_name" name="platform_name" 
                                       class="form-control @error('platform_name') is-invalid @enderror" 
                                       value="{{ old('platform_name') }}" 
                                       placeholder="e.g. ACS, Huawei" required autofocus>
                                @error('platform_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="custom-platform-management-save-btn w-100">
                                <i class="fas fa-save me-2"></i> Save Platform
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Platforms Table -->
            <div class="col-lg-7">
                <div class="custom-platform-management-card">
                    <div class="custom-platform-management-card-header d-flex justify-content-between align-items-center">
                        <h5 class="custom-platform-management-card-title">Existing Platforms</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Total Platforms:</span>
                            <span class="custom-platform-management-stat-number">{{ $platforms->count() }}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($platforms->isEmpty())
                            <div class="p-5 text-center text-muted">
                                <i class="fas fa-server mb-3 opacity-25 fa-3x"></i>
                                <p>No platforms recorded yet.</p>
                            </div>
                        @else
                            <div class="custom-platform-management-table-responsive">
                                <table class="custom-platform-management-table">
                                    <thead class="custom-platform-management-table-head">
                                        <tr>
                                            <th class="custom-platform-management-table-header">
                                                <i class="fas fa-server me-2"></i>Platform
                                            </th>
                                            <th class="custom-platform-management-table-header text-end">
                                                <i class="fas fa-cogs me-2"></i>Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="custom-platform-management-table-body">
                                        @foreach($platforms as $platform)
                                            <tr class="custom-platform-management-table-row">
                                                <td class="custom-platform-management-table-cell">
                                                    <strong>{{ $platform->platform_name }}</strong>
                                                </td>
                                                <td class="custom-platform-management-table-cell text-end">
                                                    <button type="button" 
                                                            class="custom-platform-management-action-btn custom-platform-management-delete-btn"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deletePlatformModal{{ $platform->id }}">
                                                        <i class="fas fa-trash-alt me-1"></i><span>Delete</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modals -->
    @foreach($platforms as $platform)
    <div class="modal fade" id="deletePlatformModal{{ $platform->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Delete Platform
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-trash-alt fa-3x text-danger opacity-25"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Are you sure?</h5>
                    <p class="text-muted mb-0">
                        Are you sure you want to delete <strong>{{ $platform->platform_name }}</strong>?
                    </p>
                    <p class="text-danger small mt-2">
                        <i class="fas fa-info-circle me-1"></i>This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('platforms.destroy', $platform) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</x-app-layout>

