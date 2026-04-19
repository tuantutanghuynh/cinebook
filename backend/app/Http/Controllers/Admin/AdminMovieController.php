<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * AdminMovieController
 * 
 * Handles admin movie management operations including:
 * - Movie listing with genre relationships
 * - Movie creation with genre assignment
 * - Movie editing and updates
 * - Movie status management (now_showing, coming_soon, ended)
 * - Genre attachment and relationship management
 * Note: Movie deletion disabled to protect data integrity
 */
class AdminMovieController extends Controller
{
    /**
     * Helper function to attach genres to movies (using Eloquent relationships)
     */
    private function attachGenresToMovies($movies)
    {   
        // Eager load genres for all movies
        $movieIds = collect($movies)->pluck('id')->toArray();
        
        $moviesWithGenres = Movie::with('genres')
            ->whereIn('id', $movieIds)
            ->get()
            ->keyBy('id');
        
        // Attach genres to each movie
        foreach ($movies as $movie) {
            $movieModel = $moviesWithGenres->get($movie->id);
            $movie->genres = $movieModel ? $movieModel->genres->pluck('name')->toArray() : [];
        }
        
        return $movies;
    }

    public function index()
    {
        // Auto-update movie statuses based on real-time
        $this->updateMovieStatuses();
        
        $movies = Movie::latest()->paginate(20);
        
        // Convert to array for helper function
        $moviesArray = $movies->items();
        $moviesArray = $this->attachGenresToMovies($moviesArray);
        
        return view('admin.movies.index', compact('movies'));
    }

    /**
     * Auto-update movie statuses based on current date and time
     */
    private function updateMovieStatuses()
    {
        $now = Carbon::now();
        
        // Update coming_soon to now_showing for movies released today or before
        // (but only if they haven't been manually ended)
        Movie::where('status', 'coming_soon')
            ->where('release_date', '<=', $now->toDateString())
            ->update(['status' => 'now_showing']);
    }

    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.movies.create', compact('genres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'director' => 'required|string|max:255',
            'cast' => 'nullable|string',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
            'duration' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'language' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:0|max:10',
            'poster_url' => 'nullable|url',
            'trailer_url' => 'nullable|url',
            'status' => 'required|in:now_showing,coming_soon,ended',
            'description' => 'nullable|string',
        ]);

        // Check for duplicate movie (same title and release date)
        $existingMovie = Movie::where('title', $validated['title'])
                             ->where('release_date', $validated['release_date'])
                             ->first();
        
        if ($existingMovie) {
            return back()->withErrors([
                'title' => 'A movie with this title and release date already exists.'
            ])->withInput();
        }

        // Validate status based on release date and real time
        $releaseDate = Carbon::parse($validated['release_date']);
        $now = Carbon::now();

        // Auto-determine status based on release date if not manually overridden
        if ($releaseDate->isFuture()) {
            // Movies with future release date must be coming_soon
            if ($validated['status'] === 'now_showing') {
                return back()->withErrors([
                    'status' => 'Movies with future release date must be set as "Coming Soon".'
                ])->withInput();
            }
            $validated['status'] = 'coming_soon';
        } elseif ($releaseDate->isPast() || $releaseDate->isToday()) {
            // Movies released today or before can be now_showing or ended
            if ($validated['status'] === 'coming_soon' && ($releaseDate->isPast() || $releaseDate->isToday())) {
                return back()->withErrors([
                    'status' => 'Movies already released cannot be set as "Coming Soon".'
                ])->withInput();
            }
        }

        // Remove genres from validated data for mass assignment
        $genres = $validated['genres'] ?? [];
        unset($validated['genres']);

        $movie = Movie::create($validated);

        // Sync genres
        if (!empty($genres)) {
            $movie->genres()->sync($genres);
        }

        return redirect()->route('admin.movies.index')
            ->with('success', 'Movie created successfully');
    }

    public function edit(Movie $movie)
    {
        $genres = Genre::orderBy('name')->get();
        $movie->load('genres'); // Eager load genres
        return view('admin.movies.edit', compact('movie', 'genres'));
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'director' => 'required|string|max:255',
            'cast' => 'nullable|string',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
            'duration' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'language' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:0|max:10',
            'poster_url' => 'nullable|url',
            'trailer_url' => 'nullable|url',
            'status' => 'required|in:now_showing,coming_soon,ended',
            'description' => 'nullable|string',
        ]);

        // Check for duplicate movie (same title and release date) excluding current movie
        $existingMovie = Movie::where('title', $validated['title'])
                             ->where('release_date', $validated['release_date'])
                             ->where('id', '!=', $movie->id)
                             ->first();
        
        if ($existingMovie) {
            return back()->withErrors([
                'title' => 'A movie with this title and release date already exists.'
            ])->withInput();
        }

        // Validate status based on release date and real time
        $releaseDate = Carbon::parse($validated['release_date']);
        $now = Carbon::now();

        // Check if trying to set coming_soon for already released movie
        if ($releaseDate->isPast() && $validated['status'] === 'coming_soon') {
            return back()->withErrors([
                'status' => 'Movies already released cannot be set as "Coming Soon".'
            ])->withInput();
        }

        // Check if trying to set now_showing for future release
        if ($releaseDate->isFuture() && $validated['status'] === 'now_showing') {
            return back()->withErrors([
                'status' => 'Movies with future release date cannot be set as "Now Showing".'
            ])->withInput();
        }

        // Check if trying to set ended status
        if ($validated['status'] === 'ended') {
            // Get the latest showtime for this movie
            $latestShowtime = Showtime::where('movie_id', $movie->id)
                ->orderBy('show_date', 'desc')
                ->orderBy('show_time', 'desc')
                ->first();

            if ($latestShowtime) {
                // Parse show_date and show_time separately then combine
                $showDate = Carbon::parse($latestShowtime->show_date);
                $showTime = Carbon::parse($latestShowtime->show_time);
                
                // Combine date and time properly
                $showtimeDateTime = $showDate->setTime($showTime->hour, $showTime->minute, $showTime->second)
                    ->addMinutes($movie->duration); // Add movie duration

                if ($showtimeDateTime->isFuture()) {
                    return back()->withErrors([
                        'status' => 'Cannot end movie while there are future showtimes. Last showtime ends at ' . 
                                  $showtimeDateTime->format('M d, Y h:i A') . '.'
                    ])->withInput();
                }
            }
        }

        // Remove genres from validated data for mass assignment
        $genres = $validated['genres'] ?? [];
        unset($validated['genres']);

        $movie->update($validated);

        // Sync genres
        $movie->genres()->sync($genres);

        return redirect()->route('admin.movies.index')
            ->with('success', 'Movie updated successfully');
    }
}