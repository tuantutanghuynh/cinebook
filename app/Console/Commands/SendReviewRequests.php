<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use App\Models\Review;
use App\Mail\ReviewRequestMail;
use Carbon\Carbon;

/**
 * SendReviewRequests Command
 *
 * Artisan command to send review request emails to customers.
 * Runs after showtimes end to request movie reviews.
 * Usage: php artisan email:review-requests --hours=2
 */
class SendReviewRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:review-requests {--hours=2 : Hours after showtime ends to send review request}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send review request emails to customers after their showtime ends';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoursAfterShow = (int) $this->option('hours');
        $now = Carbon::now();

        $this->info("Looking for showtimes that ended {$hoursAfterShow} hours ago");

        // Get bookings where:
        // 1. Payment is confirmed
        // 2. Showtime has ended (show_date + show_time + duration < now - hoursAfterShow buffer)
        // 3. User hasn't reviewed this movie yet
        // 4. Review request hasn't been sent yet
        $bookings = Booking::with(['user', 'showtime.movie', 'showtime.room', 'bookingSeats.seat'])
            ->where('payment_status', 'paid')
            ->where('status', 'confirmed')
            ->whereNull('review_request_sent_at')
            ->whereHas('showtime', function ($query) use ($now, $hoursAfterShow) {
                // Showtime ended at least {hoursAfterShow} hours ago
                $targetTime = $now->copy()->subHours($hoursAfterShow);
                $query->whereHas('movie', function ($movieQuery) use ($targetTime) {
                    $movieQuery->whereRaw(
                        "DATE_ADD(CONCAT(showtimes.show_date, ' ', showtimes.show_time), INTERVAL movies.duration MINUTE) < ?",
                        [$targetTime->toDateTimeString()]
                    );
                });
            })
            ->get();

        $this->info("Found {$bookings->count()} potential bookings");

        $sentCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        foreach ($bookings as $booking) {
            // Check if user has already reviewed this movie
            $hasReviewed = Review::where('user_id', $booking->user_id)
                ->where('movie_id', $booking->showtime->movie_id)
                ->exists();

            if ($hasReviewed) {
                $skippedCount++;
                $this->line("- Skipped {$booking->user->email}: Already reviewed");
                continue;
            }

            try {
                // Send the review request email
                Mail::to($booking->user->email)->send(new ReviewRequestMail($booking));

                // Mark as sent
                $booking->update(['review_request_sent_at' => Carbon::now()]);

                $sentCount++;
                $this->line("✓ Sent review request to {$booking->user->email} for {$booking->showtime->movie->title}");
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("✗ Failed to send to {$booking->user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Completed: {$sentCount} sent, {$skippedCount} skipped (already reviewed), {$failedCount} failed");

        return Command::SUCCESS;
    }
}
