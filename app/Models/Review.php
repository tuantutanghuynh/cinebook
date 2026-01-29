<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Review Model
 *
 * Represents a user's review for a movie.
 * Contains rating (1-5 stars) and optional comment.
 * Supports helpful voting system.
 */
class Review extends Model
{
    use HasFactory;
    /** @var array<int, string> */
    protected $fillable = [
        'user_id', 'movie_id', 'rating', 'comment'
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the user that wrote this review
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the movie that this review is for
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    /**
     * Get the helpful marks for this review
     */
    public function helpfuls()
    {
        return $this->hasMany(ReviewHelpful::class);
    }

    /**
     * Get the count of helpful marks
     */
    public function getHelpfulCountAttribute()
    {
        return $this->helpfuls()->count();
    }

    /**
     * Check if a specific user marked this review as helpful
     */
    public function isHelpfulBy($userId)
    {
        return $this->helpfuls()->where('user_id', $userId)->exists();
    }

    // ==================== SCOPES ====================

    /**
     * Scope to get reviews sorted by latest
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get reviews sorted by highest rating
     */
    public function scopeHighestRated($query)
    {
        return $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get reviews sorted by lowest rating
     */
    public function scopeLowestRated($query)
    {
        return $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get reviews sorted by most helpful
     */
    public function scopeMostHelpful($query)
    {
        return $query->withCount('helpfuls')->orderBy('helpfuls_count', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Scope to filter by rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to filter by movie
     */
    public function scopeByMovie($query, $movieId)
    {
        return $query->where('movie_id', $movieId);
    }
}