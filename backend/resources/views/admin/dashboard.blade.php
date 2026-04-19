{{--
/**
 * Admin Dashboard
 * 
 * Main admin control panel including:
 * - Key performance metrics and statistics
 * - Recent activity summaries
 * - Quick action buttons
 * - System status indicators
 * - Data visualization charts
 */
--}}
@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')

@section('content')
<div class="admin-header">
    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
    <p class="text-muted">Overview of CineBook System</p>
</div>

<div class="container-fluid">
    <!-- Row 1: Business Pulse -->
    <h5 class="text-muted mb-3"><i class="bi bi-activity"></i> Business Pulse</h5>
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Tickets Sold Today</h6>
                            <h2 class="mb-0">{{ $ticketsSoldToday }}</h2>
                        </div>
                        <i class="bi bi-ticket-perforated fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white"
                style="background: linear-gradient(135deg, var(--deep-teal), var(--deep-space-blue));">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Revenue Today</h6>
                            <h2 class="mb-0">{{ number_format($revenueToday) }}₫</h2>
                        </div>
                        <i class="bi bi-currency-dollar fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white" style="background: linear-gradient(135deg, var(--burnt-peach), #ff6b35);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Showtimes With Bookings</h6>
                            <h2 class="mb-0">{{ $showtimesWithBookingsToday }}</h2>
                        </div>
                        <i class="bi bi-collection-play fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Active Showtimes</h6>
                            <h2 class="mb-0">{{ $activeShowtimes }}</h2>
                        </div>
                        <i class="bi bi-calendar-event fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Risk & Future -->
    <h5 class="text-muted mb-3"><i class="bi bi-exclamation-triangle"></i> Risk & Future</h5>
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Revenue at risk (24h)</h6>
                            <h3 class="text-primary mb-0">{{ number_format($upcomingRevenue24h) }}₫</h3>
                        </div>
                        <i class="bi bi-clock-history fs-1 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Refund Amount This Month</h6>
                            <h3 class="text-danger mb-0">{{ number_format($refundAmountThisMonth) }}₫</h3>
                        </div>
                        <i class="bi bi-arrow-return-left fs-1 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Performance -->
    <h5 class="text-muted mb-3"><i class="bi bi-trophy"></i> Performance</h5>
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100 border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-star"></i> Top Movie by Revenue</h6>
                </div>
                <div class="card-body">
                    @if($topMovieByRevenue && $topMovieByRevenue->revenue > 0)
                    <h5 class="card-title">{{ $topMovieByRevenue->title }}</h5>
                    <p class="text-success fs-4 mb-0">{{ number_format($topMovieByRevenue->revenue) }}₫</p>
                    @else
                    <p class="text-muted mb-0">No revenue data yet</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100 border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-graph-up-arrow"></i> Top Showtime Today</h6>
                </div>
                <div class="card-body">
                    @if($topShowtimeToday && $topShowtimeToday->booked_count > 0)
                    <h5 class="card-title">{{ $topShowtimeToday->movie->title }}</h5>
                    <p class="text-muted mb-1">
                        {{ \Carbon\Carbon::parse($topShowtimeToday->show_time)->format('h:i A') }} -
                        {{ $topShowtimeToday->room->name }}
                    </p>
                    <p class="text-primary fs-5 mb-0">{{ $topShowtimeToday->booked_count }} tickets sold</p>
                    @else
                    <p class="text-muted mb-0">No bookings for today's showtimes yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <h5 class="text-muted mb-3"><i class="bi bi-bar-chart"></i> Overview</h5>
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 text-secondary"></i>
                    <h4 class="mt-2">{{ $totalUsers }}</h4>
                    <p class="text-muted mb-0">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <i class="bi bi-film fs-1 text-secondary"></i>
                    <h4 class="mt-2">{{ $totalMovies }}</h4>
                    <p class="text-muted mb-0">Total Movies</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack fs-1 text-info"></i>
                    <h4 class="mt-2 text-info">{{ number_format($revenueThisMonth) }}₫</h4>
                    <p class="text-muted mb-0">This Month's Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="bi bi-wallet2 fs-1 text-success"></i>
                    <h4 class="mt-2 text-success">{{ number_format($totalRevenue) }}₫</h4>
                    <p class="text-muted mb-0">Total Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Recent Bookings -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background-color: var(--prussian-blue); color: white;">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Bookings</h5>
                </div>
                <div class="card-body">
                    @if($recentBookings->isEmpty())
                    <p class="text-muted mb-0">No bookings yet.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Movie</th>
                                    <th>Showtime</th>
                                    <th>Seats</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                <tr>
                                    <td><strong>#{{ $booking->id }}</strong></td>
                                    <td>{{ $booking->user->name }}</td>
                                    <td>{{ $booking->showtime->movie->title }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('M d, Y') }}<br>
                                        <small
                                            class="text-muted">{{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('h:i A') }}</small>
                                    </td>
                                    <td>{{ $booking->bookingSeats->count() }} seats</td>
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
                                        @else
                                        <span class="badge bg-warning">{{ ucfirst($booking->payment_status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
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
</div>
@endsection