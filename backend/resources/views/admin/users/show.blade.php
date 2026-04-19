{{--
/**
 * Admin User Details
 * 
 * Detailed user information including:
 * - Complete user profile display
 * - Booking history and statistics
 * - Account activity timeline
 * - Role and permission management
 * - Account actions and controls
 */
--}}
@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Users
    </a>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- User Info Card -->
        <div class="card mb-4">
            <div class="card-header" style="background-color: var(--deep-teal); color: white;">
                <h4 class="mb-0"><i class="bi bi-person-circle"></i> User Profile</h4>
            </div>
            <div class="card-body text-center">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px;">
                        <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                    </div>
                @endif

                <h4>{{ $user->name }}</h4>
                <p class="text-muted mb-3">{{ $user->email }}</p>

                @if($user->role === 'admin')
                    <span class="badge bg-danger fs-6">Administrator</span>
                @else
                    <span class="badge bg-primary fs-6">User</span>
                @endif

                @if($user->id === auth()->id())
                    <span class="badge bg-info fs-6">You</span>
                @endif
            </div>
        </div>

        <!-- Contact Info -->
        <div class="card mb-4">
            <div class="card-header" style="background-color: var(--tan);">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Contact Information</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong><i class="bi bi-envelope"></i> Email:</strong><br>
                    {{ $user->email }}
                </p>
                @if($user->phone)
                    <p class="mb-2">
                        <strong><i class="bi bi-telephone"></i> Phone:</strong><br>
                        {{ $user->phone }}
                    </p>
                @endif
                @if($user->city)
                    <p class="mb-2">
                        <strong><i class="bi bi-geo-alt"></i> City:</strong><br>
                        {{ $user->city }}
                    </p>
                @endif
                <p class="mb-0">
                    <strong><i class="bi bi-calendar-check"></i> Member Since:</strong><br>
                    {{ $user->created_at->format('M d, Y') }}
                </p>
            </div>
        </div>

        <!-- Statistics -->
        <div class="card">
            <div class="card-header" style="background-color: var(--tan);">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Statistics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Total Bookings</h6>
                    <h4 class="mb-0">{{ $stats['total_bookings'] }}</h4>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Confirmed</h6>
                    <h5 class="mb-0 text-success">{{ $stats['confirmed_bookings'] }}</h5>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Cancelled</h6>
                    <h5 class="mb-0 text-danger">{{ $stats['cancelled_bookings'] }}</h5>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Spent</h6>
                    <h4 class="mb-0 text-success">{{ number_format($stats['total_spent']) }}₫</h4>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if($user->id !== auth()->id())
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="mb-3">Admin Actions</h6>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary-cinebook w-100 mb-2">
                        <i class="bi bi-pencil"></i> Edit User
                    </a>
                    <form action="{{ route('admin.users.toggle-role', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100 mb-2"
                                onclick="return confirm('Are you sure you want to change this user\'s role?')">
                            <i class="bi bi-arrow-repeat"></i>
                            {{ $user->role === 'admin' ? 'Make Regular User' : 'Make Administrator' }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-8">
        <!-- Booking History -->
        <div class="card">
            <div class="card-header" style="background-color: var(--deep-teal); color: white;">
                <h4 class="mb-0"><i class="bi bi-ticket-perforated"></i> Booking History</h4>
            </div>
            <div class="card-body">
                @if($user->bookings->isEmpty())
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> No bookings yet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Movie</th>
                                    <th>Showtime</th>
                                    <th>Seats</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->bookings as $booking)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-decoration-none">
                                                <strong>#{{ $booking->id }}</strong>
                                            </a>
                                        </td>
                                        <td>
                                            <strong>{{ $booking->showtime->movie->title }}</strong><br>
                                            <small class="text-muted">{{ $booking->showtime->room->name }}</small>
                                        </td>
                                        <td>
                                            {{ $booking->showtime->show_date->format('M d, Y') }}<br>
                                            <small>{{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $booking->bookingSeats->count() }} seats</span>
                                        </td>
                                        <td>
                                            <strong>{{ number_format($booking->total_price) }}₫</strong>
                                        </td>
                                        <td>
                                            @if($booking->status == 'confirmed')
                                                <span class="badge bg-success">Confirmed</span>
                                            @elseif($booking->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($booking->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $booking->booking_date->format('M d, Y') }}
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
@endsection
