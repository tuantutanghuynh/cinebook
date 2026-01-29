<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;

/**
 * TestBookingEmail Command
 *
 * Artisan command to test booking confirmation email.
 * Sends a test email for a specific booking.
 * Usage: php artisan email:test-booking {booking_id}
 */
class TestBookingEmail extends Command
{
    protected $signature = 'email:test-booking {booking_id}';
    protected $description = 'Test booking confirmation email for a specific booking';

    public function handle()
    {
        $bookingId = $this->argument('booking_id');

        $this->info("Testing booking confirmation email for booking #{$bookingId}");

        try {
            // Load booking with all necessary relationships
            $booking = Booking::with([
                'user',
                'bookingSeats.seat',
                'showtime.movie',
                'showtime.room'
            ])->find($bookingId);

            if (!$booking) {
                $this->error("❌ Booking #{$bookingId} not found!");
                return 1;
            }

            if (!$booking->user || !$booking->user->email) {
                $this->error("❌ Booking has no user or user email!");
                return 1;
            }

            $this->info("Sending to: {$booking->user->email}");
            $this->info("Movie: {$booking->showtime->movie->title}");
            $this->info("Seats: " . $booking->bookingSeats->map(fn($s) => $s->seat->seat_code)->join(', '));

            Mail::to($booking->user->email)->send(new BookingConfirmationMail($booking));

            $this->info("✅ Email sent successfully!");
            $this->info("Check your inbox at: {$booking->user->email}");

        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
            $this->error("Full error: " . $e->getTraceAsString());
        }
    }
}
