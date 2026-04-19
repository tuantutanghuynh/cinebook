<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Movie Model
 *
 * Represents a movie in the cinema system.
 * Manages movie details, genres, reviews, and status.
 * Supports statuses: now_showing, coming_soon, ended.
 */
class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'language', 'director', 'cast', 'duration',
        'release_date', 'age_rating', 'status', 'poster_url', 'trailer_url',
        'description', 'rating_avg'
    ];

    protected $casts = [
        'release_date' => 'datetime',
    ];

    /**
     * Get the genres associated with the movie.
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movie_genres');
    }

    /**
     * Helper method to get genres as a comma-separated string
     */
    public function getGenresStringAttribute()
    {
        return $this->genres->pluck('name')->join(', ');
    }

    /**
     * Get all reviews for this movie
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Update the average rating for this movie
     */
    public function updateAverageRating()
    {
        $avgRating = $this->reviews()->avg('rating');
        $this->update([
            'rating_avg' => $avgRating ? round($avgRating, 2) : 0
        ]);
    }
}
