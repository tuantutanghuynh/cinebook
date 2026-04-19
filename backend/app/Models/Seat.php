<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Seat Model
 *
 * Represents a physical seat in a cinema room.
 * Contains seat position (row, number, code) and type information.
 */
class Seat extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'room_id', 'seat_row', 'seat_number', 'seat_code', 'seat_type_id'
    ];

    public function seatType()
    {
        return $this->belongsTo(SeatType::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }
}
