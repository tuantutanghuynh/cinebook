{{--
/**
 * User Bookings List
 * 
 * User booking history display including:
 * - Past and upcoming bookings list
 * - Booking details and status
 * - QR codes for active bookings
 * - Cancellation options where applicable
 * - Booking search and filtering
 */
--}}
@extends('profile.profilepage')

@section('title','Your Bookings')

@section('content')
<div class="admin-header">
    <h2><i class="fas fa-history"></i> Your Booking History</h2>
    <p class="text-muted">Review your bookings and details</p>
</div>

<!-- Upcoming Bookings -->
<div class="mb-4">
    <h4 class="mb-3"><i class="fas fa-calendar-check text-success"></i> Upcoming Bookings</h4>
    @forelse($upcomingBookings as $booking)
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <div class="row">
                <!-- Left: Booking Info -->
                <div class="col-md-7">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-film text-primary"></i> {{ $booking->showtime->movie->title ?? 'N/A' }}
                    </h5>
                    <p class="mb-2">
                        <strong><i class="fas fa-calendar"></i> Showtime:</strong> 
                        {{ $booking->showtime->show_date->format('d M Y') }}, {{ $booking->showtime->show_time->format('H:i') }}
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-door-open"></i> Room:</strong> {{ $booking->showtime->room->name ?? 'N/A' }}
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-chair"></i> Seats:</strong>
                        @php
                            $seats = $booking->bookingSeats()->with('seat')->get();
                        @endphp
                        {{ $seats->pluck('seat.seat_code')->join(', ') }}
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-money-bill"></i> Total:</strong> {{ number_format($booking->total_price) }} VND
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-info-circle"></i> Status:</strong> 
                        <span class="badge bg-success">{{ ucfirst($booking->payment_status) }}</span>
                    </p>
                    <p class="mb-0 text-muted">
                        <small><i class="fas fa-clock"></i> Booked: {{ $booking->created_at->format('d M Y, H:i') }}</small>
                    </p>
                </div>
                
                <!-- Right: QR Codes -->
                <div class="col-md-5">
                    <h6 class="mb-2">QR Codes for Check-in:</h6>
                    @php
                        $displayedQRs = [];
                    @endphp
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($seats as $bookingSeat)
                            @if ($bookingSeat->qr_code && !in_array($bookingSeat->qr_code, $displayedQRs))
                                @php
                                    $seatsWithSameQR = $seats->where('qr_code', $bookingSeat->qr_code);
                                    $displayedQRs[] = $bookingSeat->qr_code;
                                @endphp
                                <div class="text-center" style="border: 1px solid #ddd; padding: 8px; border-radius: 5px; background: white;">
                                    {!! QrCode::size(100)->generate($bookingSeat->qr_code) !!}
                                    <small class="d-block mt-1">{{ $seatsWithSameQR->pluck('seat.seat_code')->join(', ') }}</small>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if($seats->whereNotNull('qr_code')->isEmpty())
                        <p class="text-muted"><small>No QR codes available for this booking.</small></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> No upcoming bookings.
    </div>
    @endforelse
</div>

<!-- Past Bookings -->
<div class="mb-4">
    <h4 class="mb-3"><i class="fas fa-history text-secondary"></i> Past Bookings</h4>
    @forelse($pastBookings as $booking)
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <div class="row">
                <!-- Left: Booking Info -->
                <div class="col-md-7">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-film text-primary"></i> {{ $booking->showtime->movie->title ?? 'N/A' }}
                    </h5>
                    <p class="mb-2">
                        <strong><i class="fas fa-calendar"></i> Showtime:</strong> 
                        {{ $booking->showtime->show_date->format('d M Y') }}, {{ $booking->showtime->show_time->format('H:i') }}
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-door-open"></i> Room:</strong> {{ $booking->showtime->room->name ?? 'N/A' }}
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-chair"></i> Seats:</strong>
                        @php
                            $seats = $booking->bookingSeats()->with('seat')->get();
                        @endphp
                        {{ $seats->pluck('seat.seat_code')->join(', ') }}
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-money-bill"></i> Total:</strong> {{ number_format($booking->total_price) }} VND
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-info-circle"></i> Status:</strong> 
                        <span class="badge bg-secondary">{{ ucfirst($booking->payment_status) }}</span>
                    </p>
                    <p class="mb-0 text-muted">
                        <small><i class="fas fa-clock"></i> Booked: {{ $booking->created_at->format('d M Y, H:i') }}</small>
                    </p>
                </div>
                
                <!-- Right: QR Codes (Past bookings - for reference) -->
                <div class="col-md-5">
                    <h6 class="mb-2">QR Codes (Reference):</h6>
                    @php
                        $displayedQRs = [];
                    @endphp
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($seats as $bookingSeat)
                            @if ($bookingSeat->qr_code && !in_array($bookingSeat->qr_code, $displayedQRs))
                                @php
                                    $seatsWithSameQR = $seats->where('qr_code', $bookingSeat->qr_code);
                                    $displayedQRs[] = $bookingSeat->qr_code;
                                @endphp
                                <div class="text-center" style="border: 1px solid #ddd; padding: 8px; border-radius: 5px; background: #f5f5f5; opacity: 0.7;">
                                    {!! QrCode::size(80)->generate($bookingSeat->qr_code) !!}
                                    <small class="d-block mt-1">{{ $seatsWithSameQR->pluck('seat.seat_code')->join(', ') }}</small>
                                    <small class="badge bg-{{ $bookingSeat->qr_status === 'checked' ? 'success' : 'secondary' }}">
                                        {{ $bookingSeat->qr_status === 'checked' ? 'Checked-in' : 'Not used' }}
                                    </small>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if($seats->whereNotNull('qr_code')->isEmpty())
                        <p class="text-muted"><small>No QR codes available for this booking.</small></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> No past bookings.
    </div>
    @endforelse
</div>
@endsection