<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

/**
 * BookingCancellationMail
 *
 * Email sent to customers when their booking is cancelled.
 * Contains cancellation reason and refund information if applicable.
 */
class BookingCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $reason;
    public $refundAmount;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, string $reason = '', float $refundAmount = 0)
    {
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
            subject: 'Booking Cancellation Confirmation - TCA Cine',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_cancellation',
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
