<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ScreenType Model
 *
 * Represents a cinema screen format.
 * Types include: 2D, 3D, IMAX, 4DX with different pricing.
 */
class ScreenType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
