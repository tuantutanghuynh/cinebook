<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ReviewHelpful Model
 *
 * Represents a user's helpful vote on a review.
 * Users can mark reviews as helpful (one vote per user per review).
 */
class ReviewHelpful extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'is_helpful'
    ];

    /**
     * Relationship to Review
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
