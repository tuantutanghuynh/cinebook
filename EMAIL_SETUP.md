# Email Configuration Options for CineBook

## Option 1: Gmail SMTP (Production)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="CineBook"
```

**Setup Steps for Gmail:**

1. Enable 2-factor authentication on your Gmail account
2. Generate App Password: Google Account → Security → App passwords
3. Use the App Password (not your regular password) in MAIL_PASSWORD

## Option 2: MailHog (Development/Testing)

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="test@cinebook.local"
MAIL_FROM_NAME="CineBook"
```

**Setup Steps for MailHog:**

1. Download MailHog from https://github.com/mailhog/MailHog
2. Run: `./mailhog.exe`
3. View emails at: http://localhost:8025

## Option 3: Log Only (Debug)

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="debug@cinebook.local"
MAIL_FROM_NAME="CineBook"
```

Emails will be written to `storage/logs/laravel.log`

## Test Email Command

```bash
php artisan email:test your-email@example.com
```
