<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SeatType Model
 *
 * Represents a seat category with base pricing.
 * Types include: Standard, VIP, Couple.
 */
class SeatType extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['name', 'base_price', 'description'];

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function showtimePrices()
    {
        return $this->hasMany(ShowtimePrice::class);
    }
}
