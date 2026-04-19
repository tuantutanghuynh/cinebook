{{--
/**
 * Change Password Page
 * 
 * Password change interface including:
 * - Current password verification
 * - New password and confirmation fields
 * - Password strength indicators
 * - Security requirements display
 * - Success/error feedback
 */
--}}
@extends('profile.profilepage')

@section('title', 'Change Password')

@section('page-title', 'Change Password')

@section('content')
<div class="admin-header">
    <h2><i class="fas fa-key"></i> Change Password</h2>
    <p class="text-muted">Update your account password</p>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header" style="background-color: var(--burnt-peach); color: white;">
                    <h5 class="mb-0"><i class="fas fa-lock"></i> Password Security</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.profile.change-password.post') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">
                                Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required>
                            </div>
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">
                                New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" 
                                       class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" 
                                       name="new_password" 
                                       required
                                       minlength="8">
                            </div>
                            <small class="text-muted">Password must be at least 8 characters long</small>
                            @error('new_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">
                                Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation" 
                                       required
                                       minlength="8">
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Password Requirements:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Minimum 8 characters</li>
                                <li>Make sure to remember your new password</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Password
                            </button>
                            <a href="{{ route('user.profile') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-shield-alt text-success"></i> Security Tips</h6>
                    <ul class="small text-muted mb-0">
                        <li>Use a strong, unique password</li>
                        <li>Don't share your password with anyone</li>
                        <li>Change your password regularly</li>
                        <li>Use a combination of letters, numbers, and symbols</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection