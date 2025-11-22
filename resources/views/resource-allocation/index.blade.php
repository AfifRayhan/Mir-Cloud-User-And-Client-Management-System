<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 fw-bold mb-1">Resource Allocation</h1>
                <p class="text-muted mb-0">Select a customer to dismantle resources or rewrite their cloud details (upgrade/downgrade).</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-xl-6">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        @if ($customers->isEmpty())
                            <p class="text-muted mb-0">No customers found. Please create a customer first.</p>
                        @else
                            <form method="POST" action="{{ route('resource-allocation.process') }}">
                                @csrf

                                <div class="mb-4">
                                    <label for="customer_id" class="form-label fw-semibold">Customer</label>
                                    <select id="customer_id" name="customer_id" class="form-select form-select-lg @error('customer_id') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('customer_id') ? '' : 'selected' }}>Choose a customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->customer_name }} @if($customer->cloudDetail) - Active Resources @else - No Resources @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <p class="form-label fw-semibold mb-2">Action</p>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="action_type" id="action_dismantle" value="dismantle" {{ old('action_type', 'dismantle') === 'dismantle' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="action_dismantle">
                                            Dismantle all resources for the selected customer.
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action_type" id="action_rewrite" value="rewrite" {{ old('action_type') === 'rewrite' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="action_rewrite">
                                            Rewrite cloud details with new provisioning requirements (upgrade/downgrade).
                                        </label>
                                    </div>
                                    @error('action_type')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        Proceed
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

