<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Showtime;
use App\Models\Booking;

/**
 * ShowtimeCancellationMail
 * 
 * Email sent when a showtime is cancelled by the cinema
 * Notifies customers about automatic refund and booking cancellation
 */
class ShowtimeCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $showtime;
    public $booking;
    public $reason;
    public $refundAmount;

    /**
     * Create a new message instance.
     */
    public function __construct(Showtime $showtime, Booking $booking, string $reason, float $refundAmount)
    {
        $this->showtime = $showtime;
        $this->booking = $booking;
        $this->reason = $reason;
        $this->refundAmount = $refundAmount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Showtime Cancelled - Automatic Refund Issued - TCA Cine',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.showtime_cancellation',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
