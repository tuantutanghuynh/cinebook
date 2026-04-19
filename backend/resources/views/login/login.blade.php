{{--
/**
 * Login Page
 * 
 * User authentication interface including:
 * - Login form with email/password fields
 * - Remember me functionality
 * - Forgot password link
 * - Registration redirect link
 * - Form validation and error display
 */
--}}
@extends('layouts.main')

@section('title', 'TCA Cine - Login')

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

            <h1>Login</h1>
            <p class="subtitle">Welcome back! Please login to your account</p>

            <form method="POST" action="/login">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>

            <div class="auth-link">
                Don't have an account? <a href="{{ route('register') }}">Register here</a>
            </div>
            <div class="auth-link">
                <a href="{{ route('password.forgot') }}">Forgot your password?</a>
            </div>
        </div>
    </div>
</div>
@endsection