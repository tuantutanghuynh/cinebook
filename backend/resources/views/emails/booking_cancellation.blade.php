{{--
/**
 * Booking Cancellation Email Template
 *
 * Sent when a booking is cancelled including:
 * - Cancellation confirmation
 * - Original booking details
 * - Refund information (if applicable)
 * - Contact information
 */
--}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Cancellation - TCA Cine</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
        background-color: #f4f4f4;
    }

    .container {
        max-width: 600px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .header {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        padding: 30px 20px;
        text-align: center;
    }

    .header .icon {
        font-size: 48px;
        margin-bottom: 10px;
    }

    .header h1 {
        font-size: 24px;
        margin-bottom: 5px;
    }

    .content {
        padding: 30px 25px;
    }

    .greeting {
        font-size: 18px;
        color: #333;
        margin-bottom: 15px;
    }

    .message {
        color: #555;
        margin-bottom: 20px;
    }

    .cancellation-box {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }

    .cancellation-box h3 {
        color: #721c24;
        margin-bottom: 10px;
    }

    .cancellation-box .booking-id {
        font-size: 20px;
        font-weight: 700;
        color: #721c24;
    }

    .details-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }

    .details-section h3 {
        color: #495057;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #dee2e6;
    }

    .detail-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #666;
        min-width: 120px;
    }

    .detail-value {
        color: #333;
    }

    .refund-section {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }

    .refund-section h3 {
        color: #155724;
        margin-bottom: 10px;
    }

    .refund-amount {
        font-size: 28px;
        font-weight: 700;
        color: #155724;
    }

    .refund-note {
        font-size: 14px;
        color: #155724;
        margin-top: 10px;
    }

    .reason-box {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 15px;
        margin: 20px 0;
        border-radius: 6px;
    }

    .reason-box strong {
        color: #856404;
    }

    .reason-box p {
        color: #856404;
        margin-top: 5px;
    }

    .cta-section {
        text-align: center;
        margin: 30px 0;
    }

    .cta-button {
        display: inline-block;
        background: linear-gradient(135deg, #008080 0%, #006666 100%);
        color: white !important;
        text-decoration: none;
        padding: 15px 40px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
    }

    .footer {
        background: #f8f9fa;
        padding: 20px;
        text-align: center;
        border-top: 3px solid #6c757d;
    }

    .footer p {
        font-size: 13px;
        color: #666;
        margin: 5px 0;
    }

    @media only screen and (max-width: 600px) {
        .container {
            border-radius: 0;
        }

        .content {
            padding: 20px 15px;
        }

        .detail-row {
            flex-direction: column;
        }

        .detail-label {
            margin-bottom: 5px;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="icon">❌</div>
            <h1>Booking Cancelled</h1>
        </div>

        <div class="content">
            <p class="greeting">Hello {{ $booking->user->name }},</p>

            <p class="message">
                We're writing to confirm that your booking has been cancelled.
                Below are the details of the cancelled booking.
            </p>

            <div class="cancellation-box">
                <h3>Cancellation Confirmed</h3>
                <p>Booking ID: <span class="booking-id">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span></p>
                <p style="margin-top: 5px; font-size: 14px;">Cancelled on: {{ now()->format('F d, Y \a\t h:i A') }}</p>
            </div>

            @if($reason)
            <div class="reason-box">
                <strong>Reason for Cancellation:</strong>
                <p>{{ $reason }}</p>
            </div>
            @endif

            <div class="details-section">
                <h3>Original Booking Details</h3>

                <div class="detail-row">
                    <span class="detail-label">Movie:</span>
                    <span class="detail-value">{{ $booking->showtime->movie->title }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Show Date:</span>
                    <span
                        class="detail-value">{{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('l, F d, Y') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Show Time:</span>
                    <span
                        class="detail-value">{{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('h:i A') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Room:</span>
                    <span class="detail-value">{{ $booking->showtime->room->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Seats:</span>
                    <span
                        class="detail-value">{{ $booking->bookingSeats->map(fn($s) => $s->seat->seat_code)->join(', ') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Original Amount:</span>
                    <span class="detail-value"><strong>{{ number_format($booking->total_price) }}VND</strong></span>
                </div>
            </div>

            @if($refundAmount > 0)
            <div class="refund-section">
                <h3>💰 Refund Information</h3>
                <p>Refund Amount: <span class="refund-amount">{{ number_format($refundAmount) }}₫</span></p>
                <p class="refund-note">
                    The refund will be processed to your original payment method within 5-7 business days.
                </p>
            </div>
            @endif

            <div class="cta-section">
                <p style="color: #555; margin-bottom: 15px;">Want to book another movie?</p>
                <a href="{{ url('/now-showing') }}" class="cta-button">Browse Movies</a>
            </div>
        </div>

        <div class="footer">
            <p>Questions about your cancellation or refund?</p>
            <p>
                📧 <a href="mailto:support@tcacine.com" style="color: #008080;">support@tcacine.com</a> |
                📞 <strong>1900-xxxx</strong>
            </p>
            <p style="margin-top: 10px; color: #999;">&copy; {{ date('Y') }} TCA Cine</p>
        </div>
    </div>
</body>

</html>