@php
    $isEdit = isset($user);
@endphp

<div class="mb-3">
    <label for="name" class="form-label fw-semibold">Full Name</label>
    <input id="name"
           type="text"
           name="name"
           class="form-control form-control-lg @error('name') is-invalid @enderror"
           value="{{ old('name', $user->name ?? '') }}"
           required
           autocomplete="name"
           autofocus>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="designation" class="form-label fw-semibold">Designation</label>
    <input id="designation"
           type="text"
           name="designation"
           class="form-control form-control-lg @error('designation') is-invalid @enderror"
           value="{{ old('designation', $user->designation ?? '') }}"
           placeholder="e.g. Account Manager">
    @error('designation')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label fw-semibold">Email</label>
    <input id="email"
           type="email"
           name="email"
           class="form-control form-control-lg @error('email') is-invalid @enderror"
           value="{{ old('email', $user->email ?? '') }}"
           required
           autocomplete="email">
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="username" class="form-label fw-semibold">Username</label>
    <input id="username"
           type="text"
           name="username"
           class="form-control form-control-lg @error('username') is-invalid @enderror"
           value="{{ old('username', $user->username ?? '') }}"
           required
           autocomplete="username">
    @error('username')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="phone" class="form-label fw-semibold">Phone</label>
    <input id="phone"
           type="text"
           name="phone"
           class="form-control form-control-lg @error('phone') is-invalid @enderror"
           value="{{ old('phone', $user->phone ?? '') }}"
           placeholder="+1 555 555 5555">
    @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="role_id" class="form-label fw-semibold">Role</label>
    <select id="role_id"
            name="role_id"
            class="form-select form-select-lg @error('role_id') is-invalid @enderror"
            required>
        <option value="">Select a role</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ (string) old('role_id', $user->role_id ?? '') === (string) $role->id ? 'selected' : '' }}>
                {{ ucfirst(str_replace('-', ' ', $role->role_name)) }}
            </option>
        @endforeach
    </select>
    @error('role_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label for="department_id" class="form-label fw-semibold">Department</label>
    <select id="department_id"
            name="department_id"
            class="form-select form-select-lg @error('department_id') is-invalid @enderror"
            required>
        <option value="">Select a department</option>
        @foreach($departments as $department)
            <option value="{{ $department->id }}" {{ (string) old('department_id', $user->department_id ?? '') === (string) $department->id ? 'selected' : '' }}>
                {{ $department->department_name }}
            </option>
        @endforeach
    </select>
    @error('department_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row g-3 align-items-end">
    <div class="col-md-6">
        <label for="password" class="form-label fw-semibold">Password {{ $isEdit ? '(leave blank to keep current)' : '' }}</label>
        <input id="password"
               type="password"
               name="password"
               class="form-control form-control-lg @error('password') is-invalid @enderror"
               autocomplete="new-password"
               {{ $isEdit ? '' : 'required' }}>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
        <input id="password_confirmation"
               type="password"
               name="password_confirmation"
               class="form-control form-control-lg"
               autocomplete="new-password"
               {{ $isEdit ? '' : 'required' }}>
    </div>
</div>

