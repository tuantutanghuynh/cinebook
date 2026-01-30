<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Showtime Model
 *
 * Represents a movie screening schedule.
 * Manages show date/time, room assignment, and seat availability.
 * Supports status: upcoming, ongoing, done.
 */
class Showtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id', 'room_id', 'show_date', 'show_time'
    ];

    // Cast date/time attributes so Blade views receive Carbon instances
    protected $casts = [
        'show_date' => 'date',
        'show_time' => 'datetime:H:i A',
    ];

    /**
     * Get the start datetime of the showtime
     */
    public function getStartDatetimeAttribute(): Carbon
    {
        $date = $this->show_date instanceof Carbon ? $this->show_date : Carbon::parse($this->show_date);
        $time = $this->show_time instanceof Carbon ? $this->show_time : Carbon::parse($this->show_time);

        return $date->copy()->setTimeFrom($time);
    }

    /**
     * Get the end datetime of the showtime (start + movie duration)
     */
    public function getEndDatetimeAttribute(): Carbon
    {
        $duration = $this->movie ? $this->movie->duration : 0;
        return $this->start_datetime->copy()->addMinutes($duration);
    }

    /**
     * Check if the showtime has ended
     */
    public function isEnded(): bool
    {
        return Carbon::now()->gt($this->end_datetime);
    }

    /**
     * Check if the showtime is currently playing
     */
    public function isPlaying(): bool
    {
        $now = Carbon::now();
        return $now->gte($this->start_datetime) && $now->lte($this->end_datetime);
    }

    /**
     * Check if the showtime is upcoming (not started yet)
     */
    public function isUpcoming(): bool
    {
        return Carbon::now()->lt($this->start_datetime);
    }

    /**
     * Get the status of the showtime
     */
    public function getStatusAttribute(): string
    {
        if ($this->isEnded()) {
            return 'done';
        }
        if ($this->isPlaying()) {
            return 'ongoing';
        }
        return 'upcoming';
    }

    /**
     * Get the status class for badges
     */
    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'done' => 'bg-secondary',
            'ongoing' => 'bg-warning',
            'upcoming' => 'bg-success',
            default => 'bg-secondary'
        };
    }

    /**
     * Get seat statistics for this showtime
     */
    public function getSeatStatsAttribute(): array
    {
        $this->load('showtimeSeats');
        
        $totalRoomSeats = $this->room->seats->count();
        $showtimeSeats = $this->showtimeSeats;
        
        $bookedSeats = $showtimeSeats->where('status', 'booked')->count();
        $availableSeats = $showtimeSeats->where('status', 'available')->count();
        
        // If showtime seats don't match room seats count, calculate available seats
        if ($showtimeSeats->count() !== $totalRoomSeats) {
            $availableSeats = $totalRoomSeats - $bookedSeats;
        }
        
        return [
            'total' => $totalRoomSeats,
            'booked' => $bookedSeats,
            'available' => $availableSeats,
            'booked_percentage' => $totalRoomSeats > 0 ? round(($bookedSeats / $totalRoomSeats) * 100, 1) : 0
        ];
    }

    /**
     * Scope to get only upcoming showtimes
     */
    public function scopeUpcoming($query)
    {
        return $query->whereRaw("CONCAT(show_date, ' ', show_time) > ?", [Carbon::now()]);
    }

    /**
     * Scope to get only ended showtimes
     */
    public function scopeEnded($query)
    {
        return $query->whereHas('movie', function ($q) {
            $q->whereRaw("DATE_ADD(CONCAT(showtimes.show_date, ' ', showtimes.show_time), INTERVAL movies.duration MINUTE) < ?", [Carbon::now()]);
        });
    }

    /**
     * Scope to get active showtimes (upcoming or currently playing)
     */
    public function scopeActive($query)
    {
        return $query->whereHas('movie', function ($q) {
            $q->whereRaw("DATE_ADD(CONCAT(showtimes.show_date, ' ', showtimes.show_time), INTERVAL movies.duration MINUTE) > ?", [Carbon::now()]);
        });
    }

    /**
     * Check if this showtime overlaps with another time range in the same room
     *
     * @param int $roomId
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param int|null $excludeId Showtime ID to exclude (for updates)
     * @return bool
     */
    public static function hasOverlap(int $roomId, Carbon $startTime, Carbon $endTime, ?int $excludeId = null): bool
    {
        $query = self::where('room_id', $roomId)
            ->with('movie');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $showtimes = $query->get();

        foreach ($showtimes as $showtime) {
            $existingStart = $showtime->start_datetime;
            $existingEnd = $showtime->end_datetime;

            // Check for overlap: two ranges overlap if start1 < end2 AND start2 < end1
            if ($startTime->lt($existingEnd) && $existingStart->lt($endTime)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get overlapping showtimes for error message
     */
    public static function getOverlappingShowtimes(int $roomId, Carbon $startTime, Carbon $endTime, ?int $excludeId = null)
    {
        $query = self::where('room_id', $roomId)
            ->with('movie');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $showtimes = $query->get();
        $overlapping = collect();

        foreach ($showtimes as $showtime) {
            $existingStart = $showtime->start_datetime;
            $existingEnd = $showtime->end_datetime;

            if ($startTime->lt($existingEnd) && $existingStart->lt($endTime)) {
                $overlapping->push($showtime);
            }
        }

        return $overlapping;
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function showtimePrices()
    {
        return $this->hasMany(ShowtimePrice::class);
    }

    public function showtimeSeats()
    {
        return $this->hasMany(ShowtimeSeat::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}