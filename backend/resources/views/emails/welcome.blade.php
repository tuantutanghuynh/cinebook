{{--
/**
 * Welcome Email Template
 *
 * Sent when a new user successfully registers including:
 * - Welcome greeting with user's name
 * - Brief introduction to TCA Cine features
 * - Quick links to get started
 * - Contact information
 */
--}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to TCA Cine</title>
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
            background: linear-gradient(135deg, #008080 0%, #006666 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .header .subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 30px 25px;
        }
        .greeting {
            font-size: 20px;
            color: #008080;
            margin-bottom: 20px;
        }
        .intro-text {
            color: #555;
            margin-bottom: 25px;
            font-size: 15px;
        }
        .features-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .features-section h3 {
            color: #008080;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        .feature-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .feature-icon {
            font-size: 24px;
            margin-right: 15px;
            min-width: 35px;
        }
        .feature-text h4 {
            color: #333;
            margin-bottom: 5px;
            font-size: 15px;
        }
        .feature-text p {
            color: #666;
            font-size: 13px;
        }
        .cta-section {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #f7c873 0%, #e6a040 100%);
            color: #1a2233 !important;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(247, 200, 115, 0.4);
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .tips-section {
            background: #e7f3f3;
            border-left: 4px solid #008080;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .tips-section strong {
            color: #008080;
            display: block;
            margin-bottom: 8px;
        }
        .tips-section ul {
            margin-left: 20px;
            color: #555;
        }
        .tips-section li {
            margin-bottom: 5px;
        }
        .footer {
            background: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-top: 3px solid #008080;
        }
        .footer p {
            font-size: 13px;
            color: #666;
            margin: 5px 0;
        }
        .social-links {
            margin-top: 15px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #008080;
            text-decoration: none;
            font-size: 14px;
        }
        @media only screen and (max-width: 600px) {
            .container { border-radius: 0; }
            .content { padding: 20px 15px; }
            .header h1 { font-size: 26px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to TCA Cine!</h1>
            <p class="subtitle">Your ultimate movie experience awaits</p>
        </div>

        <div class="content">
            <p class="greeting">Hello {{ $user->name }},</p>

            <p class="intro-text">
                Thank you for joining TCA Cine! We're thrilled to have you as part of our movie-loving community.
                Get ready for an amazing cinematic journey with the best movies, comfortable seats, and unforgettable experiences.
            </p>

            <div class="features-section">
                <h3>What you can do with TCA Cine:</h3>

                <div class="feature-item">
                    <span class="feature-icon">🎬</span>
                    <div class="feature-text">
                        <h4>Browse Latest Movies</h4>
                        <p>Explore now showing and upcoming blockbusters with detailed information and trailers.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <span class="feature-icon">🎫</span>
                    <div class="feature-text">
                        <h4>Easy Online Booking</h4>
                        <p>Choose your preferred seats and showtimes with our intuitive booking system.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <span class="feature-icon">📱</span>
                    <div class="feature-text">
                        <h4>Digital E-Tickets</h4>
                        <p>Get instant QR code tickets sent to your email - no printing required!</p>
                    </div>
                </div>

                <div class="feature-item">
                    <span class="feature-icon">⭐</span>
                    <div class="feature-text">
                        <h4>Rate & Review</h4>
                        <p>Share your movie experiences and help others discover great films.</p>
                    </div>
                </div>
            </div>

            <div class="cta-section">
                <a href="{{ url('/now-showing') }}" class="cta-button">Start Exploring Movies</a>
            </div>

            <div class="tips-section">
                <strong>Quick Tips to Get Started:</strong>
                <ul>
                    <li>Complete your profile for a personalized experience</li>
                    <li>Check out "Now Showing" for current movies</li>
                    <li>Book early for the best seats on popular movies</li>
                    <li>Keep your QR code handy for quick check-in</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p>Need help? We're here for you!</p>
            <p>
                Email: <a href="mailto:support@tcacine.com" style="color: #008080;">support@tcacine.com</a> |
                Hotline: <strong>1900-xxxx</strong>
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
