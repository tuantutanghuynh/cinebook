{{--
/**
 * Booking Success Page
 * 
 * Post-booking confirmation display including:
 * - Booking confirmation details
 * - QR code for entry
 * - Booking reference number
 * - Download/email ticket options
 * - Next steps instructions
 */
--}}
@extends('layouts.main')

@section('title', 'Booking Success')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <!-- Success header -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #4CAF50; font-size: 36px; margin-bottom: 10px;">Booking Successful!</h1>
        <p style="font-size: 18px; margin-bottom: 15px;">Thank you for your booking!</p>
        <a href="{{ url('/') }}" style="color: #007bff; text-decoration: none;">Return to Home</a>
    </div>

    <!-- Main Content: Booking Details (Left) + QR Codes (Right) -->
    <div style="display: flex; gap: 30px; align-items: flex-start; margin-bottom: 30px;">
        <!-- Left: Booking Details -->
        <div style="flex: 1; min-width: 400px;">
            <h2 style="font-size: 24px; margin-bottom: 15px;">Booking Details:</h2>
            <ul style="list-style-type: none; padding: 0; font-size: 16px; line-height: 1.8;">
                <li><strong>Booking ID:</strong> {{ $booking->id }}</li>
                <li><strong>Movie:</strong> {{ $booking->showtime->movie->title }}</li>
                <li><strong>Date:</strong> {{ $booking->showtime->show_date->format('F j, Y') }}</li>
                <li><strong>Time:</strong> {{ $booking->showtime->show_time }}</li>
                <li><strong>Room:</strong> {{ $booking->showtime->room->name }}</li>
                <li><strong>Payment Method:</strong> {{ $booking->payment_method }}</li>
                <li><strong>Status:</strong> {{ $booking->status }}
                    @if ($booking->payment_status =='paid')
                        <span style="color: #28a745;">✅ All Paid</span>
                    @else
                        <span style="color: #ffc107;">⏳ Pending Payment</span>
                    @endif
                </li>
                <li><strong>Total:</strong> {{ number_format($booking->total_price) }} VND</li>
            </ul>
        </div>

        <!-- Right: QR Codes -->
        <div style="flex: 1; min-width: 400px;">
            <h2 style="font-size: 24px; margin-bottom: 15px;">Seat Details & QR Codes:</h2>
            <p style="color: #666; margin-bottom: 15px; font-size: 14px;">Each seat or couple seat pair has a unique QR code. Please present at the cinema for check-in.</p>
            
            @php
                $displayedQRs = []; // Track displayed QR codes to avoid duplicates
            @endphp
            
            <div style="display: flex; flex-direction: column; gap: 15px;">
                @foreach ($seats as $bookingSeat)
                    @if (!in_array($bookingSeat->qr_code, $displayedQRs))
                        @php
                            // Get all seats with same QR code (couple seats)
                            $seatsWithSameQR = $seats->where('qr_code', $bookingSeat->qr_code);
                            $displayedQRs[] = $bookingSeat->qr_code;
                        @endphp
                        
                        <div style="border: 2px solid #ddd; padding: 12px; border-radius: 8px; background: #f9f9f9; display: flex; gap: 15px; align-items: center;">
                            <!-- QR Code -->
                            <div style="background: white; padding: 8px; border-radius: 5px; flex-shrink: 0;">
                                {!! QrCode::size(120)->generate($bookingSeat->qr_code) !!}
                            </div>
                            
                            <!-- Seat Info -->
                            <div style="flex: 1; font-size: 14px;">
                                <p style="margin: 5px 0;"><strong>Seat:</strong> 
                                    {{ $seatsWithSameQR->pluck('seat.seat_code')->join(', ') }}
                                </p>
                                <p style="margin: 5px 0;"><strong>Type:</strong> {{ $bookingSeat->seat->seatType->name }}</p>
                                <p style="margin: 5px 0;"><strong>Price:</strong> 
                                    {{ number_format($seatsWithSameQR->sum('price')) }} VND
                                </p>
                                <p style="color: #4CAF50; font-weight: bold; margin: 5px 0;">
                                    <span style="display: inline-block; width: 8px; height: 8px; background: #4CAF50; border-radius: 50%; margin-right: 5px;"></span>
                                    {{ $bookingSeat->qr_status === 'active' ? 'Not Checked-in' : 'Checked-in' }}
                                </p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Action buttons -->
    <div style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
        <a href="{{ route('homepage') }}" style="display: inline-block; background: #007bff; color: white; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-weight: bold;">Back to Homepage</a>
    </div>
</div>

<script>
    // Clear booking countdown from localStorage
    localStorage.removeItem('booking_expiry_time');
</script>
@endsection