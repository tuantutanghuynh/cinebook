{{--
/**
 * Admin Bookings List
 * 
 * Booking management interface including:
 * - All bookings list with filtering
 * - Booking status management
 * - Customer information display
 * - Revenue tracking
 * - Search and export options
 */
--}}
@extends('layouts.admin')

@section('title', 'Manage Bookings')

@section('content')
<h2 class="mb-4" style="color: var(--prussian-blue)">
    <i class="bi bi-ticket-perforated"></i> Manage Bookings
</h2>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white"
            style="background: linear-gradient(135deg, var(--deep-teal), var(--deep-space-blue));">
            <div class="card-body">
                <h6 class="card-title">Total Bookings</h6>
                <h2 class="mb-0">{{ $stats['total'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title">Confirmed</h6>
                <h2 class="mb-0">{{ $stats['confirmed'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h6 class="card-title">Pending</h6>
                <h2 class="mb-0">{{ $stats['pending'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h6 class="card-title">Cancelled/Expired</h6>
                <h2 class="mb-0">{{ $stats['cancelled'] + $stats['expired'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Revenue (Paid)</h6>
                <h3 class="text-success mb-0">{{ number_format($stats['total_revenue']) }}₫</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Today's Bookings</h6>
                <h3 class="mb-0" style="color: var(--deep-teal)">{{ $stats['today_bookings'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-warning">
            <div class="card-body">
                <h6 class="text-muted">Cancelled Today</h6>
                <h3 class="text-warning mb-0">{{ $stats['cancelled_today'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.bookings.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Booking ID, User name/email"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment</label>
                    <select name="payment_status" class="form-select">
                        <option value="">All Payment</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>
                            Refunded</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Showtime</label>
                    <select name="showtime_id" class="form-select">
                        <option value="">All Showtimes</option>
                        @foreach($showtimes as $showtime)
                        <option value="{{ $showtime->id }}"
                            {{ request('showtime_id') == $showtime->id ? 'selected' : '' }}>
                            {{ $showtime->movie->title }} -
                            {{ \Carbon\Carbon::parse($showtime->show_date)->format('M j') }}
                            {{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }}
                            ({{ $showtime->room->name }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Booking Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-cinebook w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bookings Table -->
<div class="card">
    <div class="card-body">
        @if($bookings->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> No bookings found.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Movie</th>
                        <th>Showtime</th>
                        <th>Seats</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Booked On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td><strong>#{{ $booking->id }}</strong></td>
                        <td>
                            {{ $booking->user->name }}<br>
                            <small class="text-muted">{{ $booking->user->email }}</small>
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
                            @foreach($booking->bookingSeats->take(3) as $bookingSeat)
                            <span class="badge bg-secondary">{{ $bookingSeat->seat->seat_code }}</span>
                            @endforeach
                            @if($booking->bookingSeats->count() > 3)
                            <span class="badge bg-secondary">+{{ $booking->bookingSeats->count() - 3 }}</span>
                            @endif
                        </td>
                        <td><strong>{{ number_format($booking->total_price) }}₫</strong></td>
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
                            @if($booking->payment_status == 'paid')
                            <span class="badge bg-success">Paid</span>
                            @elseif($booking->payment_status == 'refunded')
                            <span class="badge bg-info">Refunded</span>
                            @else
                            <span class="badge bg-warning">{{ ucfirst($booking->payment_status) }}</span>
                            @endif
                            <br><small class="text-muted">{{ ucfirst($booking->payment_method) }}</small>
                        </td>
                        <td>
                            {{ $booking->booking_date->format('M d, Y') }}<br>
                            <small class="text-muted">{{ $booking->booking_date->format('h:i A') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.bookings.show', $booking) }}"
                                class="btn btn-sm btn-outline-primary" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($booking->status == 'confirmed' || $booking->status == 'pending')
                                @php
                                    // Check if showtime has ended
                                    $showtimeEnded = $booking->showtime->status === 'done';
                                @endphp
                                
                                @if($showtimeEnded)
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled 
                                            title="Cannot cancel - showtime has ended">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @else
                                    <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel Booking"
                                            onclick="return confirm('Are you sure you want to cancel this booking?')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($bookings->hasPages())
        <div class="cine-pagination-wrapper">
            <nav aria-label="Bookings pagination">
                <ul class="cine-pagination">
                    @foreach ($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                    <li class="cine-page-item {{ $page == $bookings->currentPage() ? 'is-active' : '' }}">
                        <a class="cine-page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endforeach
                </ul>
            </nav>
        </div>
        @endif
        @endif
    </div>
</div>
@endsection