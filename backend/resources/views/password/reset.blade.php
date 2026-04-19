{{--
/**
 * Password Reset Page
 * 
 * Password reset interface including:
 * - New password form with confirmation
 * - Token validation handling
 * - Security requirements display
 * - Form validation and error messages
 * - Success/failure feedback
 */
--}}
@extends('layouts.main')

@section('title', 'Reset Password')

@section('content')
<div class="auth-container">
    <div class="auth-wrapper">
        <div class="auth-form">
            @if(session('error'))
            <div class="error-alert">
                {{ session('error') }}
            </div>
            @endif

            @if(session('success'))
            <div class="success-alert">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
                <div class="error-alert">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <h1>Reset Password</h1>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required placeholder="Enter your email" readonly>
                </div>
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your new password">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirm your new password">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
            <a href="/login">Back to Login</a>
        </div>
    </div>
</div>
@endsection