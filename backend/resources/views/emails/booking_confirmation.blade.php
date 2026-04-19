{{--
/**
 * Booking Confirmation Email Template
 * 
 * Professional email template for booking confirmations including:
 * - Complete booking details and movie information
 * - QR code generation for ticket entry validation
 * - Theater room and seat assignment details
 * - Important instructions and guidelines
 * - Contact support information and social links
 * - Responsive design for mobile and desktop
 * - Brand-consistent styling with TCA Cine colors
 */
--}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - TCA Cine</title>
    <style>
    /* Global reset and base styles */
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
        padding: 20px;
    }

    /* Email wrapper for consistent background */
    .email-wrapper {
        background-color: #f4f4f4;
        padding: 20px;
    }

    /* Main container with card-like appearance */
    .container {
        max-width: 600px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    /* Header section with brand gradient */
    .header {
        background: linear-gradient(135deg, #008080 0%, #006666 100%);
        color: white;
        padding: 30px 20px;
        text-align: center;
    }

    .header h1 {
        font-size: 28px;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .header .logo {
        font-size: 36px;
        margin-bottom: 10px;
    }

    /* Content section padding */
    .content {
        padding: 30px 25px;
    }

    /* Greeting and introduction */
    .greeting {
        font-size: 18px;
        margin-bottom: 15px;
        color: #008080;
    }

    .intro-text {
        margin-bottom: 25px;
        color: #555;
    }

    /* Booking details section with card style */
    .details-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin: 25px 0;
    }

    .details-section h3 {
        color: #008080;
        margin-bottom: 15px;
        font-size: 20px;
        border-bottom: 2px solid #f7c873;
        padding-bottom: 8px;
    }

    /* Detail rows for booking information */
    .detail-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #555;
        min-width: 140px;
    }

    .detail-value {
        color: #333;
        flex: 1;
    }

    /* Total price highlight */
    .total-price {
        background: linear-gradient(135deg, #f7c873 0%, #e6a040 100%);
        color: #1a2233;
        font-size: 24px;
        font-weight: 700;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        margin-top: 10px;
    }

    /* QR code section */
    .qr-section {
        margin: 30px 0;
        text-align: center;
    }

    .qr-section h3 {
        color: #008080;
        margin-bottom: 10px;
        font-size: 22px;
    }

    .qr-instructions {
        color: #555;
        margin-bottom: 20px;
        font-size: 15px;
        background: #fff3cd;
        padding: 12px;
        border-radius: 6px;
        border-left: 4px solid #f7c873;
    }

    /* QR code container with dashed border */
    .qr-code-container {
        background: #ffffff;
        border: 2px dashed #008080;
        border-radius: 10px;
        padding: 20px;
        margin: 15px 0;
        box-shadow: 0 2px 8px rgba(0, 128, 128, 0.1);
    }

    /* Seat information badge */
    .seat-info {
        background: #008080;
        color: white;
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-weight: 600;
        font-size: 16px;
    }

    /* QR code image styling */
    .qr-code-container img {
        max-width: 220px;
        height: auto;
        margin: 10px auto;
        display: block;
    }

    /* QR code text identifier */
    .qr-text {
        font-size: 12px;
        color: #777;
        margin-top: 10px;
        font-family: 'Courier New', monospace;
        word-wrap: break-word;
    }

    /* Important notes section */
    .important-note {
        background: #e7f3f3;
        border-left: 4px solid #008080;
        padding: 15px;
        margin: 20px 0;
        border-radius: 6px;
    }

    .important-note strong {
        color: #008080;
        display: block;
        margin-bottom: 8px;
    }

    /* Footer section */
    .footer {
        background: #f8f9fa;
        padding: 20px;
        text-align: center;
        border-top: 3px solid #008080;
    }

    .footer p {
        font-size: 13px;
        color: #666;
        margin: 5px 0;
    }

    .footer .contact {
        color: #008080;
        font-weight: 600;
        text-decoration: none;
    }

    /* Social media links */
    .social-links {
        margin-top: 15px;
    }

    .social-links a {
        display: inline-block;
        margin: 0 8px;
        color: #008080;
        text-decoration: none;
        font-size: 14px;
    }

    /* Responsive design for mobile devices */
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
            <h2>Booking Confirmation Successful</h2>
        </div>

        <p>Hello <strong>{{ $booking->user->name }}</strong>,</p>
        <p>Thank you for booking with TCA Cine. Below are your ticket details:</p>

        <div class="details-section">
            <h3>📋 Booking Information</h3>

            <div class="detail-row">
                <span class="detail-label">Booking ID:</span>
                <span class="detail-value"><strong>#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</strong></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Movie:</span>
                <span class="detail-value"><strong>{{ $booking->showtime->movie->title }}</strong></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Theater Room:</span>
                <span class="detail-value">{{ $booking->showtime->room->name }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Show Date:</span>
                <span
                    class="detail-value">{{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('l, F d, Y') }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Show Time:</span>
                <span class="detail-value">{{ $booking->showtime->show_time }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Seats:</span>
                <span class="detail-value">
                    <strong>{{ $booking->bookingSeats->map(fn($s) => $s->seat->seat_code)->join(', ') }}</strong>
                    ({{ $booking->bookingSeats->count() }} {{ $booking->bookingSeats->count() > 1 ? 'seats' : 'seat' }})
                </span>
            </div>

            <div class="total-price">
                Total: {{ number_format($booking->total_price) }} VND
            </div>
        </div>

        <div class="qr-section">
            <h3>🎫 Your E-Tickets</h3>
            <div class="qr-instructions">
                <strong>📱 Instructions:</strong>
                Please present the QR code(s) below at the ticket counter or entrance gate.
            </div>

            @php
            // Group seats by QR code (couple seats may share one QR)
            $groupedSeats = $booking->bookingSeats->groupBy('qr_code');
            @endphp

            @foreach($groupedSeats as $qrCode => $seats)
                <div class="qr-code-container">
                    <div class="seat-info">
                        🪑 Seat(s): {{ $seats->map(fn($s) => $s->seat->seat_code)->join(', ') }}
                    </div>
                    
                    {{-- Generate QR Code Image with error handling --}}
                    @php
                        try {
                            // Use Endroid QR Code with GD writer (no Imagick required)
                            $qrCode_obj = \Endroid\QrCode\Builder\Builder::create()
                                ->data($qrCode)
                                ->size(250)
                                ->margin(15)
                                ->build();
                            
                            $qrImage = base64_encode($qrCode_obj->getString());
                            $qrSuccess = true;
                        } catch (\Exception $e) {
                            \Log::error('QR Code generation failed: ' . $e->getMessage(), [
                                'booking_id' => $booking->id,
                                'qr_code' => $qrCode,
                            ]);
                            $qrImage = '';
                            $qrSuccess = false;
                        }
                    @endphp
                    
                    <div style="text-align: center; padding: 15px; background: #fff; border: 3px solid #008080; border-radius: 8px; margin: 10px 0;">
                        @if($qrSuccess && $qrImage)
                            <img src="data:image/png;base64,{{ $qrImage }}" 
                                 alt="QR Code for {{ $seats->map(fn($s) => $s->seat->seat_code)->join(', ') }}" 
                                 width="250" 
                                 height="250"
                                 style="max-width: 250px; display: block; margin: 0 auto; border: 2px solid #ddd;">
                        @else
                            <div style="width: 250px; height: 250px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 2px dashed #999;">
                                <p style="color: #666; font-size: 14px;">QR Code</p>
                            </div>
                        @endif
                        
                        <p style="margin-top: 15px; color: #666; font-size: 13px; font-style: italic;">
                            If QR code doesn't display, present this code at check-in:
                        </p>
                        <p style="font-family: 'Courier New', monospace; font-size: 11px; color: #333; background: #f9f9f9; padding: 10px; border-radius: 4px; word-break: break-all; margin: 10px auto; max-width: 90%;">
                            {{ $qrCode }}
                        </p>
                    </div>
                    
                    <p class="qr-text">{{ $qrCode }}</p>
                </div>
            @endforeach
        </div>

        <div class="important-note">
            <strong>⚠️ Important Notes:</strong>
            <ul style="margin-left: 20px; color: #555;">
                <li>Please arrive at least 15 minutes before showtime</li>
                <li>Present your QR code or booking ID at the ticket counter</li>
                <li>Each QR code can only be used once</li>
                <li>Do not share your QR code with others</li>
            </ul>
        </div>

        <div class="footer">
            <p>If you have any questions, please contact us:</p>
            <p>
                📧 Email: <a href="mailto:support@tcacine.com" class="contact">support@tcacine.com</a> |
                📞 Hotline: <strong>1900-xxxx</strong>
            </p>
            <div class="social-links">
                <a href="#">Facebook</a> |
                <a href="#">Instagram</a> |
                <a href="#">Twitter</a>
            </div>
            <p style="margin-top: 15px; color: #999;">
                &copy; {{ date('Y') }} TCA Cine. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>