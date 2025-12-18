<section>
    <button type="button" 
            class="custom-profile-save-btn custom-profile-delete-btn w-100" 
            data-bs-toggle="modal" 
            data-bs-target="#confirm-user-deletion">
        <i class="fas fa-user-slash me-2"></i> Delete Account
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirm-user-deletion" tabindex="-1" aria-labelledby="confirm-user-deletion-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-exclamation-triangle text-danger fa-3x"></i>
                        </div>
                        <h5 class="modal-title fw-bold mb-3" id="confirm-user-deletion-label">Are you sure?</h5>
                        <p class="text-muted mb-4">
                            Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.
                        </p>

                        <div class="text-start mb-3">
                            <label for="password" class="custom-profile-label">Confirm Password</label>
                            <input id="password"
                                   name="password"
                                   type="password"
                                   class="form-control custom-profile-input @error('password', 'userDeletion') is-invalid @enderror"
                                   placeholder="Enter your password"
                                   required>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback text-start">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 0.75rem;">Cancel</button>
                        <button type="submit" class="custom-profile-save-btn custom-profile-delete-btn px-4">
                            Permanently Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
