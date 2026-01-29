<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Room Model
 *
 * Represents a cinema screening room.
 * Manages room layout, seat configuration, and screen type.
 */
class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'total_rows', 'seats_per_row', 'screen_type_id'
    ];

    public function screenType()
    {
        return $this->belongsTo(ScreenType::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}