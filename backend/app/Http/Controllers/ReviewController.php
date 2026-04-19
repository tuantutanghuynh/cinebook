<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Review;
use App\Models\Movie;
use App\Models\ReviewHelpful;

/**
 * ReviewController
 * 
 * Handles movie review operations including:
 * - Review creation and validation (rating 1-5 stars)
 * - User review restrictions (only for watched movies)
 * - Review helpfulness voting system
 * - Review display and aggregation
 * - Review ownership verification
 */
class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'You must be logged in to submit a review');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'movie_id' => 'required|exists:movies,id',
        ]);

        $movieId = $request->input('movie_id');
        $userId = Auth::id();

        // Check if user has already reviewed this movie
        $existingReview = Review::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this movie');
        }

        // Block admins from writing reviews
        if (Auth::user() && Auth::user()->role === 'admin') {
            return redirect()->back()->with('error', 'Admins cannot write reviews');
        }

        // Check if user has a paid booking for this movie AND the showtime has ended
        // Showtime ends = show_date + show_time + movie.duration
        $hasWatched = DB::table('booking_seats')
            ->join('showtimes', 'booking_seats.showtime_id', '=', 'showtimes.id')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->join('movies', 'showtimes.movie_id', '=', 'movies.id')
            ->where('bookings.user_id', $userId)
            ->where('showtimes.movie_id', $movieId)
            ->where('bookings.payment_status', 'paid')
            ->whereRaw('DATE_ADD(CONCAT(showtimes.show_date, " ", showtimes.show_time), INTERVAL movies.duration MINUTE) < NOW()')
            ->exists();

        if (!$hasWatched) {
            return redirect()->back()->with('error', 'You can only review movies after the showtime has ended');
        }

        // Create and save the review
        $review = Review::create([
            'user_id' => $userId,
            'movie_id' => $movieId,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        // Update movie average rating
        $movie = Movie::find($movieId);
        $movie->updateAverageRating();

        return redirect()->back()->with('success', 'Review submitted successfully');
    }

    /**
     * Delete a review - REMOVED: Users can no longer delete reviews
     * Only admin can delete reviews through AdminReviewController
     */
    // public function destroy($id) { ... } - Functionality removed

    /**
     * Check if user can review a specific movie
     */
    public function canReview($movieId)
    {
        if (!Auth::check()) {
            return false;
        }

        $userId = Auth::id();

        // Check if already reviewed
        $hasReviewed = Review::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->exists();

        if ($hasReviewed) {
            return false;
        }

        // Check if user has watched this movie (showtime must have ended)
        // Showtime ends = show_date + show_time + movie.duration
        $hasWatched = DB::table('booking_seats')
            ->join('showtimes', 'booking_seats.showtime_id', '=', 'showtimes.id')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->join('movies', 'showtimes.movie_id', '=', 'movies.id')
            ->where('bookings.user_id', $userId)
            ->where('showtimes.movie_id', $movieId)
            ->where('bookings.payment_status', 'paid')
            ->whereRaw('DATE_ADD(CONCAT(showtimes.show_date, " ", showtimes.show_time), INTERVAL movies.duration MINUTE) < NOW()')
            ->exists();

        return $hasWatched;
    }

    /**
     * Toggle helpful mark on a review
     */
    public function toggleHelpful(Request $request, $reviewId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to mark reviews as helpful.'
            ], 401);
        }

        $review = Review::findOrFail($reviewId);
        $userId = Auth::id();

        // Users cannot mark their own reviews as helpful
        if ($review->user_id === $userId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot mark your own review as helpful.'
            ], 403);
        }

        // Toggle helpful mark
        $existing = ReviewHelpful::where('review_id', $reviewId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();
            $isHelpful = false;
        } else {
            ReviewHelpful::create([
                'review_id' => $reviewId,
                'user_id' => $userId
            ]);
            $isHelpful = true;
        }

        return response()->json([
            'success' => true,
            'is_helpful' => $isHelpful,
            'helpful_count' => $review->helpfuls()->count()
        ]);
    }
}