<x-app-layout>
    <div class="container-fluid py-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Platform Management</h1>
                <p class="text-muted mb-0">Add or remove cloud platforms such as ACS or Huawei.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Add a Platform</h5>
                        <form method="POST" action="{{ route('platforms.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="platform_name" class="form-label fw-semibold">Platform Name</label>
                                <input type="text" id="platform_name" name="platform_name" class="form-control @error('platform_name') is-invalid @enderror" value="{{ old('platform_name') }}" placeholder="e.g. ACS, Huawei" required autofocus>
                                @error('platform_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                Save Platform
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Existing Platforms</h5>
                        @if($platforms->isEmpty())
                            <p class="text-muted mb-0">No platforms recorded yet.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Platform</th>
                                            <th scope="col" class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($platforms as $platform)
                                            <tr>
                                                <td>{{ $platform->platform_name }}</td>
                                                <td class="text-end">
                                                    <form method="POST" action="{{ route('platforms.destroy', $platform) }}" onsubmit="return confirm('Delete this platform?');" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
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

