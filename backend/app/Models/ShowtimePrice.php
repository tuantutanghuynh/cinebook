<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ShowtimePrice Model
 *
 * Represents pricing for a specific seat type at a showtime.
 * Allows custom pricing per showtime (peak hours, special events).
 */
class ShowtimePrice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['showtime_id', 'seat_type_id', 'price'];

    /**
     * Get the showtime that this price belongs to
     */
    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    /**
     * Get the seat type for this price
     */
    public function seatType()
    {
        return $this->belongsTo(SeatType::class);
    }
}