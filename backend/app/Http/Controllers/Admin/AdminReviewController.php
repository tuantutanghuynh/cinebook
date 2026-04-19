<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Movie;
use Illuminate\Http\Request;

/**
 * AdminReviewController
 * 
 * Handles admin review management including:
 * - Review listing and filtering
 * - Movie-based review filtering
 * - Review moderation and management
 */
class AdminReviewController extends Controller
{
    /**
     * Display all reviews
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'movie']);

        // Filter by movie
        if ($request->filled('movie_id')) {
            $query->where('movie_id', $request->movie_id);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'highest_rated') {
            $query->highestRated();
        } else {
            $query->latest();
        }

        $reviews = $query->paginate(20);
        $movies = Movie::orderBy('title')->get();

        return view('admin.reviews.index', compact('reviews', 'movies'));
    }

    /**
     * Delete a review (Admin can delete any review)
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $movieId = $review->movie_id;

        $review->delete();

        // Update movie average rating
        $movie = Movie::find($movieId);
        $movie->updateAverageRating();

        return redirect()->back()->with('success', 'Review deleted successfully');
    }
}
