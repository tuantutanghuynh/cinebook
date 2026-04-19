{{--
/**
 * Showtime Reminder Email Template
 *
 * Sent 2 hours before showtime to remind customers including:
 * - Movie and showtime details
 * - Seat information
 * - QR codes for entry
 * - Important reminders
 */
--}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Showtime Reminder - TCA Cine</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header .alert-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header .countdown {
            font-size: 32px;
            font-weight: 700;
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 10px;
        }
        .content {
            padding: 30px 25px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }
        .movie-card {
            background: linear-gradient(135deg, #1a2233 0%, #2c3e50 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 20px 0;
            color: white;
        }
        .movie-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #f7c873;
        }
        .movie-details {
            display: grid;
            gap: 10px;
        }
        .detail-item {
            display: flex;
            align-items: center;
        }
        .detail-item .icon {
            font-size: 20px;
            margin-right: 12px;
            min-width: 25px;
        }
        .detail-item .text {
            font-size: 15px;
        }
        .detail-item .text strong {
            color: #f7c873;
        }
        .seats-badge {
            background: #f7c873;
            color: #1a2233;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 18px;
            text-align: center;
            margin-top: 15px;
        }
        .qr-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .qr-section h3 {
            color: #008080;
            margin-bottom: 15px;
        }
        .qr-code-container {
            background: white;
            border: 2px dashed #008080;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        .qr-code-container img {
            max-width: 180px;
            height: auto;
        }
        .seat-label {
            background: #008080;
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 10px;
            display: inline-block;
        }
        .checklist {
            background: #fff3cd;
            border-left: 4px solid #f7c873;
            padding: 20px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .checklist h4 {
            color: #856404;
            margin-bottom: 12px;
        }
        .checklist ul {
            margin-left: 20px;
            color: #856404;
        }
        .checklist li {
            margin-bottom: 8px;
        }
        .directions {
            background: #e7f3f3;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .directions h4 {
            color: #008080;
            margin-bottom: 10px;
        }
        .directions p {
            color: #555;
            font-size: 14px;
        }
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
        @media only screen and (max-width: 600px) {
            .container { border-radius: 0; }
            .content { padding: 20px 15px; }
            .movie-title { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="alert-icon">🎬</div>
            <h1>Your Movie is Starting Soon!</h1>
            <div class="countdown">{{ $hoursUntilShow }} hour(s) to go!</div>
        </div>

        <div class="content">
            <p class="greeting">Hi {{ $booking->user->name }},</p>
            <p style="color: #555; margin-bottom: 20px;">
                This is a friendly reminder that your movie is starting soon. Don't miss it!
            </p>

            <div class="movie-card">
                <div class="movie-title">{{ $booking->showtime->movie->title }}</div>
                <div class="movie-details">
                    <div class="detail-item">
                        <span class="icon">📅</span>
                        <span class="text"><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('l, F d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="icon">🕐</span>
                        <span class="text"><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('h:i A') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="icon">🏠</span>
                        <span class="text"><strong>Room:</strong> {{ $booking->showtime->room->name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="icon">⏱️</span>
                        <span class="text"><strong>Duration:</strong> {{ $booking->showtime->movie->duration }} minutes</span>
                    </div>
                </div>
                <div class="seats-badge">
                    🪑 Your Seats: {{ $booking->bookingSeats->map(fn($s) => $s->seat->seat_code)->join(', ') }}
                </div>
            </div>

            <div class="qr-section">
                <h3>🎫 Your E-Tickets</h3>
                <p style="color: #666; margin-bottom: 15px;">Show these QR codes at the entrance</p>

                @php
                    $groupedSeats = $booking->bookingSeats->groupBy('qr_code');
                @endphp

                @foreach($groupedSeats as $qrCode => $seats)
                    <div class="qr-code-container">
                        <span class="seat-label">{{ $seats->map(fn($s) => $s->seat->seat_code)->join(', ') }}</span>
                        @php
                            try {
                                $qrImage = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(180)->margin(1)->generate($qrCode));
                            } catch (\Exception $e) {
                                $qrImage = '';
                            }
                        @endphp
                        @if($qrImage)
                            <br><img src="data:image/png;base64,{{ $qrImage }}" alt="QR Code">
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="checklist">
                <h4>📋 Before You Leave Checklist:</h4>
                <ul>
                    <li>Phone charged with QR codes ready</li>
                    <li>Arrive 15-20 minutes early</li>
                    <li>Have your booking ID: <strong>#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</strong></li>
                    <li>Check parking availability if driving</li>
                </ul>
            </div>

            <div class="directions">
                <h4>📍 Theater Location</h4>
                <p>TCA Cine - 123 Cinema Street, Movie District</p>
                <p style="margin-top: 8px;"><a href="#" style="color: #008080;">Get Directions →</a></p>
            </div>
        </div>

        <div class="footer">
            <p>Need to make changes? Contact us!</p>
            <p>
                📧 <a href="mailto:support@tcacine.com" style="color: #008080;">support@tcacine.com</a> |
                📞 <strong>1900-xxxx</strong>
            </p>
            <p style="margin-top: 10px; color: #999;">&copy; {{ date('Y') }} TCA Cine</p>
        </div>
    </div>
</body>
</html>
