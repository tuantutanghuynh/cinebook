<?php

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file contains console commands and scheduled tasks for CineBook.
| Includes automated email sending for reminders and review requests.
|
*/

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Email Commands
|--------------------------------------------------------------------------
|
| These commands send automated emails to customers:
| - Showtime reminders: 2 hours before showtime
| - Review requests: 2 hours after showtime ends
|
| To run the scheduler, add this cron entry to your server:
| * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
|
*/

// Send showtime reminder emails every 10 minutes
Schedule::command('email:showtime-reminders --hours=2')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Send review request emails every 30 minutes
Schedule::command('email:review-requests --hours=2')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->runInBackground();
