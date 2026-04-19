<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Genre Model
 *
 * Represents a movie genre category.
 * Supports many-to-many relationship with movies.
 */
class Genre extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Get the movies associated with the genre.
     */
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_genres');
    }
}