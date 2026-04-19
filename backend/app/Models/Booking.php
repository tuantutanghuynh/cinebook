<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Booking Model
 *
 * Represents a customer's ticket booking.
 * Tracks booking status (pending, confirmed, cancelled, expired)
 * and payment status (pending, paid).
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'showtime_id', 'total_price', 'status', 'payment_status', 'booking_date',
        'reminder_sent_at', 'review_request_sent_at'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'review_request_sent_at' => 'datetime',
    ];

    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }
}
