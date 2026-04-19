{{--
/**
 * Review Request Email Template
 *
 * Sent after showtime ends to request customer review including:
 * - Personalized greeting
 * - Movie watched details
 * - Direct link to review page
 * - Star rating visual
 */
--}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Your Review - TCA Cine</title>
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
            background: linear-gradient(135deg, #f7c873 0%, #e6a040 100%);
            color: #1a2233;
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
        .header .subtitle {
            font-size: 14px;
            opacity: 0.8;
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
        .movie-card {
            background: linear-gradient(135deg, #1a2233 0%, #2c3e50 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 20px 0;
            color: white;
            text-align: center;
        }
        .movie-card .movie-title {
            font-size: 24px;
            font-weight: 700;
            color: #f7c873;
            margin-bottom: 10px;
        }
        .movie-card .movie-date {
            font-size: 14px;
            opacity: 0.8;
        }
        .stars-section {
            text-align: center;
            margin: 30px 0;
        }
        .stars-section h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .stars {
            font-size: 40px;
            letter-spacing: 5px;
        }
        .stars .star {
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }
        .stars .star:hover,
        .stars .star.active {
            color: #f7c873;
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
            padding: 18px 50px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 18px;
            box-shadow: 0 4px 15px rgba(0, 128, 128, 0.3);
        }
        .benefits {
            background: #e7f3f3;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .benefits h4 {
            color: #008080;
            margin-bottom: 12px;
        }
        .benefits ul {
            margin-left: 20px;
            color: #555;
        }
        .benefits li {
            margin-bottom: 8px;
        }
        .quote {
            font-style: italic;
            color: #666;
            text-align: center;
            padding: 20px;
            border-left: 4px solid #f7c873;
            margin: 20px 0;
            background: #fffbf0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 3px solid #f7c873;
        }
        .footer p {
            font-size: 13px;
            color: #666;
            margin: 5px 0;
        }
        .unsubscribe {
            font-size: 11px;
            color: #999;
            margin-top: 15px;
        }
        @media only screen and (max-width: 600px) {
            .container { border-radius: 0; }
            .content { padding: 20px 15px; }
            .movie-card .movie-title { font-size: 20px; }
            .stars { font-size: 32px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">⭐</div>
            <h1>How Was Your Movie?</h1>
            <p class="subtitle">We'd love to hear your thoughts!</p>
        </div>

        <div class="content">
            <p class="greeting">Hi {{ $booking->user->name }},</p>

            <p class="message">
                We hope you enjoyed your recent movie experience at TCA Cine!
                Your feedback helps other movie lovers make great choices and helps us improve.
            </p>

            <div class="movie-card">
                <p class="movie-title">{{ $booking->showtime->movie->title }}</p>
                <p class="movie-date">
                    Watched on {{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('F d, Y') }}
                    at {{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('h:i A') }}
                </p>
            </div>

            <div class="stars-section">
                <h3>Rate this movie:</h3>
                <div class="stars">
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                </div>
            </div>

            <div class="cta-section">
                <a href="{{ url('/movie/' . $booking->showtime->movie->id) }}" class="cta-button">
                    Write Your Review
                </a>
            </div>

            <div class="benefits">
                <h4>Why leave a review?</h4>
                <ul>
                    <li>Help fellow movie enthusiasts discover great films</li>
                    <li>Share your unique perspective and opinions</li>
                    <li>Contribute to our movie-loving community</li>
                    <li>Your voice matters to us!</li>
                </ul>
            </div>

            <div class="quote">
                "A great movie review is like a conversation with a friend about something you both love."
            </div>
        </div>

        <div class="footer">
            <p>Thank you for choosing TCA Cine!</p>
            <p>
                📧 <a href="mailto:support@tcacine.com" style="color: #008080;">support@tcacine.com</a> |
                📞 <strong>1900-xxxx</strong>
            </p>
            <p class="unsubscribe">
                Don't want to receive review requests?
                <a href="#" style="color: #999;">Unsubscribe</a>
            </p>
            <p style="margin-top: 10px; color: #999;">&copy; {{ date('Y') }} TCA Cine</p>
        </div>
    </div>
</body>
</html>
