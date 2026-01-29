<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * BookingSeat Model
 *
 * Represents an individual seat within a booking.
 * Contains QR code for check-in and seat pricing.
 */
class BookingSeat extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'showtime_id', 'seat_id', 'price', 'qr_code', 'qr_status', 'checked_at'];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Generate unique QR code for seat(s)
     */
    public static function generateQRCode($bookingId, $seatInfo)
    {
        return hash('sha256', $bookingId . '_' . $seatInfo . '_' . microtime(true));
    }

    /**
     * Check if QR code is valid and active
     */
    public static function validateQRCode($qrCode)
    {
        return self::where('qr_code', $qrCode)
            ->where('qr_status', 'active')
            ->exists();
    }

    /**
     * Check-in with QR code
     */
    public static function checkInWithQR($qrCode)
    {
        $seats = self::where('qr_code', $qrCode)
            ->where('qr_status', 'active')
            ->get();

        if ($seats->isEmpty()) {
            return [
                'success' => false,
                'message' => 'QR code is invalid or already used'
            ];
        }

        // Update all seats with this QR code
        self::where('qr_code', $qrCode)->update([
            'qr_status' => 'checked',
            'checked_at' => now()
        ]);

        return [
            'success' => true,
            'message' => 'Check-in successful',
            'seats' => $seats->pluck('seat.seat_code'),
            'booking_id' => $seats->first()->booking_id
        ];
    }
}
