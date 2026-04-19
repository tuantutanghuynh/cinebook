{{--
/**
 * Sitemap Page
 * 
 * Complete site structure and navigation map including:
 * - All public pages and features
 * - User account pages
 * - Admin panel sections
 * - Movie browsing pages
 * - Booking and payment flows
 * - Visual hierarchy with icons
 */
--}}
@extends('layouts.main')

@section('title', 'Sitemap - TCA Cine')

@push('styles')
@vite('resources/css/sitemap.css')
@endpush

@section('content')
<div class="sitemap-container">
    <div class="sitemap-header">
        <h1>🗺️ Site Map</h1>
        <p>Complete navigation guide to all pages and features of TCA Cine</p>
    </div>

    <div class="sitemap-grid">
        <!-- Main Pages -->
        <div class="sitemap-section">
            <h2><span class="icon">🏠</span> Main Pages</h2>
            <ul class="sitemap-links">
                <li><a href="{{ route('homepage') }}"><i class="fas fa-home"></i> Homepage</a></li>
                <li><a href="{{ route('now_showing') }}"><i class="fas fa-film"></i> Now Showing</a></li>
                <li><a href="{{ route('upcoming_movies') }}"><i class="fas fa-calendar-alt"></i> Upcoming Movies</a>
                </li>
                <li><a href="{{ route('promotions') }}"><i class="fas fa-gift"></i> Promotions & Offers</a></li>
                <li><a href="{{ route('sitemap') }}"><i class="fas fa-sitemap"></i> Sitemap</a></li>
            </ul>
        </div>

        <!-- Movies & Showtimes -->
        <div class="sitemap-section">
            <h2><span class="icon">🎬</span> Movies & Showtimes</h2>
            <div class="sitemap-subsection">
                <h3>How It Works</h3>
                <ul class="sitemap-links">
                    <li><i class="fas fa-info-circle"></i> Browse movies and view details</li>
                    <li><i class="fas fa-clock"></i> Select showtimes for each movie</li>
                    <li><i class="fas fa-star"></i> Read and write reviews</li>
                </ul>
            </div>
        </div>

        <!-- Booking Process -->
        <div class="sitemap-section">
            <h2><span class="icon">🎫</span> Booking Process</h2>
            <ul class="sitemap-links">
                <li><i class="fas fa-info-circle"></i> Select a movie from the list</li>
                <li><i class="fas fa-clock"></i> Choose your preferred showtime</li>
                <li><i class="fas fa-couch"></i> Pick your seats on the seat map</li>
                <li><i class="fas fa-credit-card"></i> Complete payment</li>
                <li><i class="fas fa-check-circle"></i> Receive booking confirmation</li>
            </ul>
        </div>

        <!-- User Account -->
        <div class="sitemap-section">
            <h2><span class="icon">👤</span> User Account</h2>
            <ul class="sitemap-links">
                <li><a href="/login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <li><a href="/register"><i class="fas fa-user-plus"></i> Register</a></li>
                @auth
                <li><a href="{{ route('user.profile') }}"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="{{ route('user.bookings.list') }}"><i class="fas fa-ticket-alt"></i> My Bookings</a></li>
                <li><a href="{{ route('user.reviews.list') }}"><i class="fas fa-comment"></i> My Reviews</a></li>
                <li><a href="{{ route('user.profile.edit') }}"><i class="fas fa-edit"></i> Edit Profile</a></li>
                <li><a href="{{ route('user.profile.change-password') }}"><i class="fas fa-key"></i> Change Password</a>
                </li>
                @endauth
            </ul>
            <div class="sitemap-subsection">
                <h3>Password Reset</h3>
                <ul class="sitemap-links">
                    <li><a href="{{ route('password.forgot') }}"><i class="fas fa-unlock"></i> Forgot Password</a></li>
                    <li><a href="#"><i class="fas fa-envelope"></i> Reset Email</a></li>
                </ul>
            </div>
        </div>

        <!-- Promotions -->
        <div class="sitemap-section">
            <h2><span class="icon">🎁</span> Promotions</h2>
            <ul class="sitemap-links">
                <li><a href="{{ route('promotions') }}"><i class="fas fa-tags"></i> All Promotions</a></li>
            </ul>
            <div class="sitemap-subsection">
                <h3>Categories</h3>
                <ul class="sitemap-links">
                    <li><a href="{{ route('promotions') }}#cinema-gifts"><i class="fas fa-gift"></i> Cinema Gifts</a>
                    </li>
                    <li><a href="{{ route('promotions') }}#member-rewards"><i class="fas fa-crown"></i> Member
                            Rewards</a></li>
                    <li><a href="{{ route('promotions') }}#student-deals"><i class="fas fa-graduation-cap"></i> Student
                            Deals</a></li>
                    <li><a href="{{ route('promotions') }}#seasonal"><i class="fas fa-calendar-check"></i> Seasonal
                            Offers</a></li>
                </ul>
            </div>
        </div>

        @php
        $currentUser = session('user_id') ? \App\Models\User::find(session('user_id')) : null;
        @endphp

        @if($currentUser && $currentUser->role === 'admin')
        <!-- Admin Panel -->
        <div class="sitemap-section admin-section">
            <h2><span class="icon">⚙️</span> Admin Panel</h2>
            <ul class="sitemap-links">
                <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="{{ route('admin.movies.index') }}"><i class="fas fa-film"></i> Manage Movies</a></li>
                <li><a href="{{ route('admin.rooms.index') }}"><i class="fas fa-door-open"></i> Manage Rooms</a></li>
                <li><a href="{{ route('admin.showtimes.index') }}"><i class="fas fa-clock"></i> Manage Showtimes</a>
                </li>
                <li><a href="{{ route('admin.bookings.index') }}"><i class="fas fa-ticket-alt"></i> Manage Bookings</a>
                </li>
                <li><a href="{{ route('admin.users.index') }}"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="{{ route('admin.reviews.index') }}"><i class="fas fa-comments"></i> Manage Reviews</a></li>
                <li><a href="{{ route('admin.qr.index') }}"><i class="fas fa-qrcode"></i> QR Check-in</a></li>
                <li><a href="{{ route('admin.seat_types.edit_prices') }}"><i class="fas fa-dollar-sign"></i> Seat
                        Pricing</a></li>
            </ul>
        </div>
        @endif

        <!-- Additional Information -->
        <div class="sitemap-section">
            <h2><span class="icon">ℹ️</span> Additional Information</h2>
            <ul class="sitemap-links">
                <li><i class="fas fa-ticket-alt"></i> Dynamic booking flow based on movie selection</li>
                <li><i class="fas fa-qrcode"></i> QR code for ticket check-in</li>
                <li><i class="fas fa-users"></i> User registration for exclusive benefits</li>
                <li><i class="fas fa-crown"></i> Member rewards and promotions</li>
            </ul>
        </div>
    </div>

    <!-- Back to Top -->
    <div style="text-align: center; margin-top: 40px;">
        <a href="#"
            style="display: inline-block; background: var(--color-accent, #f7c873); color: var(--color-primary, #1a2233); padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-arrow-up"></i> Back to Top
        </a>
    </div>
</div>
@endsection