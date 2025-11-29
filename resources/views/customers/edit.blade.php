<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 fw-bold mb-1">Edit Customer</h1>
                <p class="text-muted mb-0">Update customer details.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10 col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <p class="mb-2 fw-semibold">Please fix the following:</p>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <h5 class="fw-bold mb-3 text-primary">Customer Overview</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="customer_name" class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', $customer->customer_name) }}" required autofocus>
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="activation_date" class="form-label fw-semibold">Activation Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control form-control-lg @error('activation_date') is-invalid @enderror" id="activation_date" name="activation_date" value="{{ old('activation_date', $customer->activation_date->format('Y-m-d')) }}" required>
                                        @error('activation_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="customer_address" class="form-label fw-semibold">Customer Address</label>
                                        <textarea class="form-control @error('customer_address') is-invalid @enderror" id="customer_address" name="customer_address" rows="3" placeholder="Street, city, country">{{ old('customer_address', $customer->customer_address) }}</textarea>
                                        @error('customer_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="platform_id" class="form-label fw-semibold">Platform</label>
                                        <select id="platform_id" name="platform_id" class="form-select @error('platform_id') is-invalid @enderror">
                                            <option value="">{{ __('Any') }}</option>
                                            @foreach($platforms as $platform)
                                                <option value="{{ $platform->id }}" {{ old('platform_id', $customer->platform_id) == $platform->id ? 'selected' : '' }}>
                                                    {{ $platform->platform_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('platform_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bin_number" class="form-label fw-semibold">BIN Number</label>
                                        <input type="text" class="form-control @error('bin_number') is-invalid @enderror" id="bin_number" name="bin_number" value="{{ old('bin_number', $customer->bin_number) }}" placeholder="Enter BIN number">
                                        @error('bin_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="po_number" class="form-label fw-semibold">PO Number</label>
                                        <input type="text" class="form-control @error('po_number') is-invalid @enderror" id="po_number" name="po_number" value="{{ old('po_number', $customer->po_number) }}" placeholder="Enter PO number">
                                        @error('po_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-bold mb-0 text-primary">Commercial Contact</h5>
                                    <span class="badge bg-light text-dark">Sales / Billing</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="commercial_contact_name">Name</label>
                                        <input type="text" class="form-control" id="commercial_contact_name" name="commercial_contact_name" value="{{ old('commercial_contact_name', $customer->commercial_contact_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="commercial_contact_designation">Designation</label>
                                        <input type="text" class="form-control" id="commercial_contact_designation" name="commercial_contact_designation" value="{{ old('commercial_contact_designation', $customer->commercial_contact_designation) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="commercial_contact_email">Email</label>
                                        <input type="email" class="form-control" id="commercial_contact_email" name="commercial_contact_email" value="{{ old('commercial_contact_email', $customer->commercial_contact_email) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="commercial_contact_phone">Phone</label>
                                        <input type="text" class="form-control" id="commercial_contact_phone" name="commercial_contact_phone" value="{{ old('commercial_contact_phone', $customer->commercial_contact_phone) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-bold mb-0 text-primary">Technical Contact</h5>
                                    <span class="badge bg-light text-dark">Operations</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="technical_contact_name">Name</label>
                                        <input type="text" class="form-control" id="technical_contact_name" name="technical_contact_name" value="{{ old('technical_contact_name', $customer->technical_contact_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="technical_contact_designation">Designation</label>
                                        <input type="text" class="form-control" id="technical_contact_designation" name="technical_contact_designation" value="{{ old('technical_contact_designation', $customer->technical_contact_designation) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="technical_contact_email">Email</label>
                                        <input type="email" class="form-control" id="technical_contact_email" name="technical_contact_email" value="{{ old('technical_contact_email', $customer->technical_contact_email) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="technical_contact_phone">Phone</label>
                                        <input type="text" class="form-control" id="technical_contact_phone" name="technical_contact_phone" value="{{ old('technical_contact_phone', $customer->technical_contact_phone) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-bold mb-0 text-primary">Optional Contact</h5>
                                    <span class="badge bg-light text-dark">Optional</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="optional_contact_name">Name</label>
                                        <input type="text" class="form-control" id="optional_contact_name" name="optional_contact_name" value="{{ old('optional_contact_name', $customer->optional_contact_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="optional_contact_designation">Designation</label>
                                        <input type="text" class="form-control" id="optional_contact_designation" name="optional_contact_designation" value="{{ old('optional_contact_designation', $customer->optional_contact_designation) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="optional_contact_email">Email</label>
                                        <input type="email" class="form-control" id="optional_contact_email" name="optional_contact_email" value="{{ old('optional_contact_email', $customer->optional_contact_email) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="optional_contact_phone">Phone</label>
                                        <input type="text" class="form-control" id="optional_contact_phone" name="optional_contact_phone" value="{{ old('optional_contact_phone', $customer->optional_contact_phone) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 pt-3">
                                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    Update Customer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
