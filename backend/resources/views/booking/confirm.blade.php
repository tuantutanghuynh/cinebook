{{--
/**
 * Booking Confirmation Page
 * 
 * Final booking review and confirmation including:
 * - Movie and showtime details summary
 * - Selected seats and pricing breakdown
 * - Payment method selection
 * - Terms and conditions
 * - Final booking submission
 */
--}}
@extends('layouts.main')

@section('title', 'Confirm Booking')

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h2>Confirm Booking</h2>
    <!-- Countdown Timer -->
<div style="background: #ff6b6b; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
    <h3 style="margin: 0;">⏰ Expiration Time</h3>
    <div id="countdown" 
         data-showtime-id="{{ $showtime_id }}"
         data-seats-id="{{ implode('_', array_column($seatDetails, 'id')) }}"
         data-timeleft="120"
         style="font-size: 32px; font-weight: bold; margin-top: 10px;">02:00</div>
    <p style="margin: 5px 0 0 0; font-size: 14px;">Booking will be automatically canceled if not paid within this time</p>
</div>
    <!-- Display movie and showtime details -->
    <div>
        <h3>Booking Details</h3>
        <p><strong>Movie:</strong> {{ $movie->title ?? 'N/A' }}</p>
        <p><strong>Showday:</strong> {{ $showtime->show_date ? $showtime->show_date->format('F j, Y') : 'N/A' }}</p>
        <p><strong>Showtime:</strong> {{ $showtime->show_time ?? 'N/A' }}</p>
        <p><strong>Room:</strong> {{ $room->name ?? 'N/A' }}</p>
    </div>
    <!-- Display selected seats -->
    <div>
        <h3>Selected Seats</h3>
        <table>
            <thead>
                <tr>
                    <th>Seat Code</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($seatDetails as $seat)
                    <tr>
                        <td>{{ $seat['seat_code'] }}</td>
                        <td>{{ number_format($seat['price']) }} VND</td>
                    </tr>
                @endforeach
                <tr>
                    <td><strong>Total Price</strong></td>
                    <td><strong>{{ number_format($totalPrice) }} VND</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
    <!-- Payment form could go here -->
    <form method="POST" action="{{ route('booking.process', ['showtime_id' => $showtime_id]) }}" id="payment-form" style="display: flex; flex-direction: column; align-items: center;">
        @csrf
        <input type="hidden" name="showtime_id" value="{{ $showtime_id }}">
        <input type="hidden" name="seats" value="{{ json_encode(array_column($seatDetails, 'id')) }}">
        <input type="hidden" name="total_price" value="{{ $totalPrice }}">
        <h3 style="text-align: center;">Payment method</h3>
        <select name="payment_method" required style="width: 300px; margin-bottom: 16px; text-align: center;">
            <option value="">Select Payment Method</option>
            <option value="vnpay">VNPay</option>
            <option value="momo">Momo</option>
        </select>
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button type="submit" style="padding: 10px 24px; background: #4b6e57; color: white; border: none; border-radius: 6px; font-size: 16px;">Confirm and Pay</button>
            <button type="button" id="cancel-btn" onclick="cancelAndGoBackHandler()" style="padding: 10px 24px; background: #e0e0e0; color: #333; border: none; border-radius: 6px; font-size: 16px; cursor: pointer;">Back to Seat Selection</button>
        </div>
    </form>

    <!-- Load Scripts -->
    <script src="{{ asset('js/booking-countdown.js') }}"></script>
    <script src="{{ asset('js/booking-confirm.js') }}"></script>
    <script>
        // Clear old localStorage keys before initializing new countdown
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('booking_expiry_time')) {
                const expiry = parseInt(localStorage.getItem(key));
                // Delete if expired or not this booking
                if (!key.includes('{{ $showtime_id }}_{{ implode("_", array_column($seatDetails, "id")) }}') || expiry < Date.now()) {
                    localStorage.removeItem(key);
                }
            }
        });
        
        // Initialize booking confirmation page
        initBookingConfirm({
            bookingData: {
                showtime_id: {{ $showtime_id }},
                seats: {!! json_encode(array_column($seatDetails, 'id')) !!}
            },
            cancelRoute: '{{ route('booking.cancel-reserved') }}',
            csrfToken: '{{ csrf_token() }}',
            seatMapRoute: '{{ route('booking.seatmap', ['showtime_id' => $showtime_id]) }}'
        });
    </script>
@endsection