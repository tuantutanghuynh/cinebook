<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use App\Models\Showtime;
use App\Mail\ShowtimeReminderMail;
use Carbon\Carbon;

/**
 * SendShowtimeReminders Command
 *
 * Artisan command to send reminder emails before showtimes.
 * Notifies customers about their upcoming movie screenings.
 * Usage: php artisan email:showtime-reminders --hours=2
 */
class SendShowtimeReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:showtime-reminders {--hours=2 : Hours before showtime to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to customers before their showtime';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoursBeforeShow = (int) $this->option('hours');
        $now = Carbon::now();

        // Calculate the target showtime window (e.g., showtimes starting in 2 hours)
        $targetStart = $now->copy()->addHours($hoursBeforeShow)->subMinutes(5);
        $targetEnd = $now->copy()->addHours($hoursBeforeShow)->addMinutes(5);

        $this->info("Looking for showtimes between {$targetStart} and {$targetEnd}");

        // Get bookings with showtimes in the target window
        $bookings = Booking::with(['user', 'showtime.movie', 'showtime.room', 'bookingSeats.seat'])
            ->where('payment_status', 'paid')
            ->where('status', 'confirmed')
            ->whereNull('reminder_sent_at') // Only send once
            ->whereHas('showtime', function ($query) use ($targetStart, $targetEnd) {
                $query->whereRaw(
                    "CONCAT(show_date, ' ', show_time) BETWEEN ? AND ?",
                    [$targetStart->toDateTimeString(), $targetEnd->toDateTimeString()]
                );
            })
            ->get();

        $this->info("Found {$bookings->count()} bookings to remind");

        $sentCount = 0;
        $failedCount = 0;

        foreach ($bookings as $booking) {
            try {
                // Send the reminder email
                Mail::to($booking->user->email)->send(new ShowtimeReminderMail($booking, $hoursBeforeShow));

                // Mark as sent
                $booking->update(['reminder_sent_at' => Carbon::now()]);

                $sentCount++;
                $this->line("✓ Sent reminder to {$booking->user->email} for booking #{$booking->id}");
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("✗ Failed to send to {$booking->user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Completed: {$sentCount} sent, {$failedCount} failed");

        return Command::SUCCESS;
    }
}
