<x-app-layout>
    <div class="container-fluid custom-profile-container">
        <!-- Background Elements -->
        <div class="custom-profile-bg-pattern"></div>
        <div class="custom-profile-bg-circle circle-1"></div>
        <div class="custom-profile-bg-circle circle-2"></div>

        <div class="container py-2">
            <!-- Header -->
            <div class="custom-profile-header">
                <div>
                    <h1 class="custom-profile-title">Account Settings</h1>
                    <p class="custom-profile-subtitle">Manage your personal information, security, and account preferences</p>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <!-- Row 1 Left: Profile Information (Read-only Summary) -->
                <div class="col-lg-8">
                    <div class="custom-profile-card">
                        <div class="custom-profile-card-header">
                            <h2 class="custom-profile-card-title">Account Overview</h2>
                            <p class="custom-profile-card-subtitle">Your current account status and details</p>
                        </div>
                        <div class="custom-profile-card-body">
                            <div class="custom-profile-info-grid mb-0">
                                <div class="custom-profile-info-item">
                                    <label>Role</label>
                                    <span>{{ ucfirst(str_replace('-', ' ', $user->role->role_name)) }}</span>
                                </div>
                                <div class="custom-profile-info-item">
                                    <label>Department</label>
                                    <span>{{ $user->department ? $user->department->department_name : 'Not Assigned' }}</span>
                                </div>
                                <div class="custom-profile-info-item">
                                    <label>Email Address</label>
                                    <span>{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 1 Right: Danger Zone -->
                <div class="col-lg-4">
                    <div class="custom-profile-card h-100">
                        <div class="custom-profile-card-header">
                            <h2 class="custom-profile-card-title text-danger">Danger Zone</h2>
                            <p class="custom-profile-card-subtitle">Permanent account actions</p>
                        </div>
                        <div class="custom-profile-card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <!-- Row 2 Left: Update Profile Form -->
                <div class="col-lg-8">
                    <div class="custom-profile-card">
                        <div class="custom-profile-card-header">
                            <h2 class="custom-profile-card-title">Update Information</h2>
                            <p class="custom-profile-card-subtitle">Modify your basic account details</p>
                        </div>
                        <div class="custom-profile-card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <!-- Row 2 Right: Password Security -->
                <div class="col-lg-4">
                    <div class="custom-profile-card">
                        <div class="custom-profile-card-header">
                            <h2 class="custom-profile-card-title">Password Security</h2>
                            <p class="custom-profile-card-subtitle">Manage your authentication</p>
                        </div>
                        <div class="custom-profile-card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
