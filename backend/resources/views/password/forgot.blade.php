{{--
/**
 * Forgot Password Page
 * 
 * Password reset request interface including:
 * - Email input form
 * - Reset instructions display
 * - Email validation
 * - Success message after submission
 * - Back to login link
 */
--}}
@extends('layouts.main')

@section('title', 'Password Recovery')

@section('content')
<div class="auth-container">
    <div class="auth-wrapper">
        <div class="auth-form">
            @if($errors->any())
            <div class="error-alert">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

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

            <h1>Forgot Password</h1>
            <p class="subtitle">Enter your email to reset your password</p>
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Send Reset Link</button>
                </div>
            </form>
            <a href="/login">Back to Login</a>
        </div>
    </div>
</div>
@endsection