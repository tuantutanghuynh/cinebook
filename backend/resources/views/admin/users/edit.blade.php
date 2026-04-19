{{--
/**
 * Admin User Edit
 * 
 * User editing interface including:
 * - User information form
 * - Role and permission assignment
 * - Account status controls
 * - Password reset options
 * - Save and cancel actions
 */
--}}
@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to User Details
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header" style="background-color: var(--deep-teal); color: white;">
                <h4 class="mb-0"><i class="bi bi-pencil"></i> Edit User: {{ $user->name }}</h4>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city', $user->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Note:</strong> User's password cannot be changed from this form. Users must reset their password themselves.
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle"></i> User Statistics:</h6>
                        <ul class="mb-0">
                            <li>Member since: {{ $user->created_at->format('M d, Y') }}</li>
                            <li>Total bookings: {{ $user->bookings()->count() }}</li>
                            <li>Confirmed bookings: {{ $user->bookings()->where('status', 'confirmed')->count() }}</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-cinebook">
                            <i class="bi bi-check-circle"></i> Update User
                        </button>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
