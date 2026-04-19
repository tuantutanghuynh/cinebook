{{--
/**
 * Admin Showtimes List
 * 
 * Showtime management interface including:
 * - Showtimes list with filtering
 * - Schedule overview and calendar view
 * - Room and movie assignment
 * - Pricing and availability management
 * - Quick scheduling actions
 */
--}}
@extends('layouts.admin')

@section('title', 'Manage Showtimes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: var(--prussian-blue)">
        <i class="bi bi-calendar-event"></i> Manage Showtimes
    </h2>
    <a href="{{ route('admin.showtimes.create') }}" class="btn btn-primary-cinebook">
        <i class="bi bi-plus-circle"></i> Add New Showtime
    </a>
</div>

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

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.showtimes.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Movie</label>
                    <select name="movie_id" class="form-select">
                        <option value="">All Movies</option>
                        @foreach($movies as $movie)
                        <option value="{{ $movie->id }}" {{ request('movie_id') == $movie->id ? 'selected' : '' }}>
                            {{ $movie->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Room</label>
                    <select name="room_id" class="form-select">
                        <option value="">All Rooms</option>
                        @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Filter</label>
                    <select name="filter" class="form-select">
                        <option value="">All Showtimes</option>
                        <option value="empty" {{ request('filter') === 'empty' ? 'selected' : '' }}>
                            Empty (No Bookings)
                        </option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end gap-1">
                    <button type="submit" class="btn btn-primary-cinebook">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('admin.showtimes.index') }}" class="btn btn-outline-secondary" title="Clear filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
        @if(request('filter') === 'empty')
        <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Showing upcoming empty showtimes only</strong> - Future showtimes with no bookings (sorted by date, earliest first).
        </div>
        @endif
    </div>
</div>

<!-- Showtimes Table -->
<div class="card">
    <div class="card-body">
        @if($showtimes->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> No showtimes found.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Movie</th>
                        <th>Room</th>
                        <th>Screen Type</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Seats Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($showtimes as $showtime)
                    @php
                    $seatStats = $showtime->seat_stats;
                    @endphp
                    <tr>
                        <td>{{ $showtime->id }}</td>
                        <td>
                            <strong>{{ $showtime->movie->title }}</strong><br>
                            <small class="text-muted">{{ $showtime->movie->duration }} mins</small>
                        </td>
                        <td>{{ $showtime->room->name }}</td>
                        <td>
                            @php
                            $screenTypeBg = match($showtime->room->screenType->name) {
                            '2D' => 'bg-primary',
                            '3D' => 'bg-success',
                            'IMAX' => 'bg-danger',
                            default => 'bg-secondary'
                            };
                            @endphp
                            <span class="badge {{ $screenTypeBg }}">
                                {{ $showtime->room->screenType->name }}
                            </span>
                        </td>
                        <td>{{ $showtime->show_date->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($showtime->show_time)->format('h:i A') }}</td>
                        <td>
                            <span class="badge {{ $showtime->status_class }}">
                                {{ ucfirst($showtime->status) }}
                            </span>
                        </td>
                        <td>
                            <small>
                                <span class="text-success">{{ $seatStats['available'] }} available</span> /
                                <span class="text-danger">{{ $seatStats['booked'] }} booked</span>
                            </small>
                            @if($seatStats['total'] > 0)
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar bg-danger"
                                    style="width: {{ $seatStats['booked_percentage'] }}%"></div>
                            </div>
                            @else
                            <div class="text-muted small mt-1">No seats configured</div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.showtimes.edit', $showtime) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>

                            @php
                            $hasBookings = $showtime->bookings()->exists() ||
                            $showtime->showtimeSeats()->whereIn('status', ['booked', 'reserved'])->exists();
                            @endphp

                            @if($hasBookings)
                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                title="Cannot delete - has bookings">
                                <i class="bi bi-trash"></i>
                            </button>
                            @else
                            <form action="{{ route('admin.showtimes.destroy', $showtime) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Are you sure you want to delete this showtime?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($showtimes->hasPages())
        <div class="cine-pagination-wrapper">
            <nav aria-label="Showtimes pagination">
                <ul class="cine-pagination">
                    @foreach ($showtimes->appends(request()->query())->getUrlRange(1, $showtimes->lastPage()) as $page => $url)
                    <li class="cine-page-item {{ $page == $showtimes->currentPage() ? 'is-active' : '' }}">
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