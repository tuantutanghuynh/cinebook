{{--
/**
 * Password Reset Email Template
 * 
 * Email template for password reset including:
 * - Reset link with secure token
 * - Security instructions
 * - Link expiration information
 * - Contact support details
 * - Brand consistent styling
 */
--}}
<!DOCTYPE html>
<html>

<head>
    <style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
    }

    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }

    .button {
        background-color: #4CAF50;
        color: white !important;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
    }

    .footer {
        margin-top: 30px;
        font-size: 12px;
        color: #666;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Password Reset Request</h2>
        <p>You requested a password reset. Click the button below to reset your password:</p>
        <a href="{{ url('/password/reset', $token) }}?email={{ urlencode($email) }}" class="button">Reset Password</a>

        <p>This link will expire in 10 minutes.</p>
        <p>If you did not request a password reset, please ignore this email.</p>

        <div class="footer">
            <p>Thank you,<br>The TCA Cine Team</p>
        </div>
    </div>
</body>

</html>