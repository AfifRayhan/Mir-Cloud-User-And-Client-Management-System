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

        <!-- Alerts -->
        @if(session('success'))
            <div class="custom-platform-management-alert">
                <i class="fas fa-check-circle me-2 text-success"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="custom-platform-management-alert custom-platform-management-alert-error">
                <i class="fas fa-exclamation-circle me-2 text-danger"></i>
                <span>{{ $errors->first() }}</span>
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
                                                    <form method="POST" action="{{ route('platforms.destroy', $platform) }}" 
                                                          onsubmit="return confirm('Delete this platform?');" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="custom-platform-management-action-btn custom-platform-management-delete-btn">
                                                            <i class="fas fa-trash-alt me-1"></i><span>Delete</span>
                                                        </button>
                                                    </form>
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
</x-app-layout>

