{{--
/**
 * Mock Payment Page
 * 
 * Simulated payment processing interface including:
 * - Payment form with card details
 * - Order summary and total amount
 * - Security features simulation
 * - Payment processing animation
 * - Success/failure handling
 */
--}}
@extends('layouts.main')

@section('title', 'Payment - {{ $booking->payment_method }}')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2>💳 Online Payment</h2>
    
    <!-- Countdown Timer -->
    <div style="background: #ff6b6b; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
        <h3 style="margin: 0;">⏰ Time Remaining</h3>
        <div id="countdown" 
             data-showtime-id="{{ $booking->showtime_id }}"
             data-seats-id="{{ $booking->bookingSeats->pluck('seat_id')->implode('_') }}"
             data-timeleft="120"
             style="font-size: 32px; font-weight: bold; margin-top: 10px;">02:00</div>
        <p style="margin: 5px 0 0 0; font-size: 14px;">Complete payment before time expires</p>
    </div>
    
    <!-- Payment Method Info -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <h3 style="margin-top: 0;">
            @if($booking->payment_method == 'vnpay')
                🏦 VNPay
            @elseif($booking->payment_method == 'momo')
                💰 MoMo
            @else
                💳 {{ $booking->payment_method }}
            @endif
        </h3>
        <p><strong>Movie:</strong> {{ $booking->showtime->movie->title }}</p>
        <p><strong>Showtime:</strong> {{ $booking->showtime->show_date->format('d/m/Y') }} {{ $booking->showtime->show_time }}</p>
        <p><strong>Total Price:</strong> {{ number_format($booking->total_price) }} VND</p>
        <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
    </div>
    
    <!-- Fake Payment Interface -->
    @if($booking->payment_method == 'vnpay')
        <!-- VNPay Mock -->
        <div style="background: #0066cc; color: white; padding: 25px; border-radius: 10px; text-align: center; margin-bottom: 20px;">
            <h3 style="margin: 0 0 15px 0;">🏦 VNPay Gateway</h3>
            <!-- Real QR Code -->
            <div style="background: white; padding: 20px; border-radius: 10px; margin: 15px auto; max-width: 250px;">
                {!! QrCode::size(200)->generate('VNPAY-' . $booking->id . '-' . $booking->total_price) !!}
            </div>
            <p style="margin: 15px 0;">📱 Scan the QR code using the VNPay app to pay</p>
            <p style="font-size: 14px; opacity: 0.9;">Total: {{ number_format($booking->total_price) }} VND</p>
        </div>
        
    @elseif($booking->payment_method == 'momo')
        <!-- MoMo Mock -->
        <div style="background: #d82d8b; color: white; padding: 25px; border-radius: 10px; text-align: center; margin-bottom: 20px;">
            <h3 style="margin: 0 0 15px 0;">💰 MoMo</h3>
            
            <!-- Real QR Code -->
            <div style="background: white; padding: 20px; border-radius: 10px; margin: 15px auto; max-width: 250px;">
                {!! QrCode::size(200)->generate('MOMO-' . $booking->id . '-' . $booking->total_price) !!}
            </div>
            
            <p style="margin: 15px 0;">📱 Scan the QR code using the MoMo app to pay</p>
            <p style="font-size: 14px; opacity: 0.9;">Total: {{ number_format($booking->total_price) }} VND</p>
        </div>
    @endif
    <!-- Action Buttons -->
    <div style="display: flex; gap: 15px; flex-direction: column;">
        <!-- Hidden Form (Auto-submit) -->
    <form method="POST" action="{{ route('payment.confirm', ['booking_id' => $booking->id]) }}" id="paymentForm" style="display: none;">
        @csrf
    </form>
        
        <!-- Cancel Button -->
        <button type="button" onclick="cancelBookingHandler()" 
           style="width: 100%; background: #6c757d; color: white; padding: 15px; border: none; border-radius: 8px; text-align: center; text-decoration: none; display: block; font-size: 16px; font-weight: bold; cursor: pointer;">
            ❌ Cancel Booking
        </button>
    </div>
</div>

<!-- Load Scripts -->
<script src="{{ asset('js/booking-countdown.js') }}"></script>
<script src="{{ asset('js/payment-mock.js') }}"></script>
<script>
    // Initialize payment mock page
    initPaymentMock({
        formId: 'paymentForm',
        autoSubmitDelay: 10000, // 10 seconds
        bookingId: {{ $booking->id }},
        cancelRoute: '{{ route('booking.cancel') }}',
        csrfToken: '{{ csrf_token() }}',
        showtimeId: {{ $booking->showtime_id }},
        seatmapRoute: '{{ route('booking.seatmap', ['showtime_id' => 'SHOWTIME_ID']) }}'
    });
</script>
@endsection