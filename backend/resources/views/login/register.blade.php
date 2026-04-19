{{--
/**
 * Registration Page
 * 
 * User registration interface including:
 * - Registration form with required fields
 * - Form validation and error handling
 * - Terms and conditions acceptance
 * - Login redirect link
 * - Account creation processing
 */
--}}
@extends('layouts.main')

@section('title', 'TCA Cine - Register')

@section('content')
<div class="auth-container">
    <div class="auth-wrapper">
        <div class="auth-form">
            @if(session('error'))
            <div class="error-alert">
                {{ session('error') }}
            </div>
            @endif

            <h1>Register</h1>
            <p class="subtitle">Create your account to get started</p>

            <form method="POST" action="/register" id="registerForm">
                @csrf
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your full name">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number">
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <select name="city" id="city" required>
                        <option value="">Choose your city</option>
                        <option value="TPHCM">Ho Chi Minh City</option>
                        <option value="HN">Hanoi</option>
                        <option value="DN">Da Nang</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter password">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirm password">
                    <span id="passwordError" class="validation-error" style="display: none;">Passwords do not match!</span>
                </div>

                <div class="form-actions">
                    <button type="submit" id="submitBtn" class="btn btn-primary">Create Account</button>
                </div>
            </form>

            <div class="auth-link">
                Already have an account? <a href="/login">Login here</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/register.js') }}"></script>
@endpush