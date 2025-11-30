<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 fw-bold mb-1">Resource Allocation: {{ ucfirst($actionType) }}</h1>
                <p class="text-muted mb-0">
                    Customer: <strong>{{ $customer->customer_name }}</strong>
                    @if($statusId)
                        | Status: <strong>{{ \App\Models\CustomerStatus::find($statusId)->name }}</strong>
                    @endif
                </p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('resource-allocation.store', $customer->id) }}">
                            @csrf
                            <input type="hidden" name="action_type" value="{{ $actionType }}">
                            @if($statusId)
                                <input type="hidden" name="status_id" value="{{ $statusId }}">
                            @endif

                            {{-- Task Status --}}
                            <div class="mb-4">
                                <label for="task_status_id" class="form-label fw-semibold">Task Status</label>
                                <select id="task_status_id" name="task_status_id" class="form-select @error('task_status_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select Task Status</option>
                                    @foreach($taskStatuses as $status)
                                        <option value="{{ $status->id }}" {{ old('task_status_id') == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('task_status_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            {{-- Cloud Details Form Fields --}}
                            {{-- Reusing logic from cloud-details form but simplified/inline --}}
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="vcpu" class="form-label fw-semibold">vCPU (Core)</label>
                                    <input type="number" id="vcpu" name="vcpu" class="form-control @error('vcpu') is-invalid @enderror"
                                           value="{{ old('vcpu', $customer->cloudDetail->vcpu ?? '') }}" min="0">
                                    @error('vcpu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="ram" class="form-label fw-semibold">RAM (GB)</label>
                                    <input type="number" id="ram" name="ram" class="form-control @error('ram') is-invalid @enderror"
                                           value="{{ old('ram', $customer->cloudDetail->ram ?? '') }}" min="0">
                                    @error('ram') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="storage" class="form-label fw-semibold">Storage (GB)</label>
                                    <input type="number" id="storage" name="storage" class="form-control @error('storage') is-invalid @enderror"
                                           value="{{ old('storage', $customer->cloudDetail->storage ?? '') }}" min="0">
                                    @error('storage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="internet" class="form-label fw-semibold">Internet Bandwidth (Mbps)</label>
                                    <input type="number" id="internet" name="internet" class="form-control @error('internet') is-invalid @enderror"
                                           value="{{ old('internet', $customer->cloudDetail->internet ?? '') }}" min="0">
                                    @error('internet') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="real_ip" class="form-label fw-semibold">Real IP</label>
                                    <input type="number" id="real_ip" name="real_ip" class="form-control @error('real_ip') is-invalid @enderror"
                                           value="{{ old('real_ip', $customer->cloudDetail->real_ip ?? '') }}">
                                    @error('real_ip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="vpn" class="form-label fw-semibold">VPN</label>
                                    <input type="number" id="vpn" name="vpn" class="form-control @error('vpn') is-invalid @enderror"
                                           value="{{ old('vpn', $customer->cloudDetail->vpn ?? '') }}">
                                    @error('vpn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="bdix" class="form-label fw-semibold">BDIX</label>
                                    <input type="number" id="bdix" name="bdix" class="form-control @error('bdix') is-invalid @enderror"
                                           value="{{ old('bdix', $customer->cloudDetail->bdix ?? '') }}">
                                    @error('bdix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('resource-allocation.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">Save Allocation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
