{{--
/**
 * Movie Showtimes Page
 * 
 * Showtimes listing and booking interface including:
 * - Date and time selection
 * - Theater room and screen type display
 * - Seat availability indicators
 * - Price information
 * - Direct booking links
 */
--}}
@extends('layouts.main')

@section('title', 'Showtimes - TCA Cine')

@vite('resources/css/showtimes.css')

@section('content')
<div class="showtimes-container">
    <div class="showtimes-header">
        <h2>Showtimes for: {{ $movie->title }}</h2>
    </div>

    @if (count($showtimes) > 0)
    <table class="showtimes-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Room</th>
                <th>Screen Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($showtimes as $showtime)
            @php
            // Check if showtime is in the past
            $showtimeDateTime = \Carbon\Carbon::parse($showtime->show_date->format('Y-m-d') . ' ' .
            $showtime->show_time->format('H:i:s'));
            $isPast = $showtimeDateTime->isPast();
            @endphp
            <tr>
                <td data-label="Date" class="showtime-date">{{ $showtime->show_date->format('d/m/Y') }}</td>
                <td data-label="Time" class="showtime-time">{{ $showtime->show_time->format('H:i') }}</td>
                <td data-label="Room" class="showtime-room">
                    <span class="room-badge">{{ $showtime->room->name }}</span>
                </td>
                <td data-label="Screen Type" class="showtime-screen-type">
                    @php
                    $screenTypeBg = match($showtime->room->screenType->name) {
                    '2D' => 'badge-primary',
                    '3D' => 'badge-success',
                    'IMAX' => 'badge-danger',
                    default => 'badge-secondary'
                    };
                    @endphp
                    <span class="screen-type-badge {{ $screenTypeBg }}">
                        {{ $showtime->room->screenType->name }}
                    </span>
                </td>
                <td data-label="Action">
                    @if($isPast)
                    <span class="expired-showtime">Expired</span>
                    @else
                    <a href="{{ route('booking.seatmap', ['showtime_id' => $showtime->id]) }}"
                        class="showtimes-select-btn">
                        Select Seats
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-showtimes">
        <div class="no-showtimes-icon">🎬</div>
        <p>No showtimes available for this movie.</p>
    </div>
    @endif

    <div class="back-navigation">
        <a href="{{ route('homepage') }}" class="showtimes-back-btn">Back to Homepage</a>
    </div>
</div>
@endsection