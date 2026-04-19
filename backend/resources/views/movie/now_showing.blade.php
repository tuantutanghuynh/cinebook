{{--
/**
 * Now Showing Movies Page
 * 
 * Current movies display including:
 * - Grid layout of currently playing movies
 * - Movie posters and basic information
 * - Quick booking access
 * - Genre and rating filters
 * - Search and sort functionality
 */
--}}
@extends('layouts.main')

@section('title', 'Now Showing - TCA Cine')

@push('styles')
@vite('resources/css/now_showing.css')
@endpush

@section('content')


<!-- Page Header -->
<div class="page-header">
    <div class="header-content">
        <h1>Now Showing</h1>
        <p>Discover what’s currently playing in theaters — before everyone spoils it for you.
            🎬🔥</p>
        <!-- Filter & Sort Form -->
        <form method="GET" action="" class="movie-filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="genre">Genre</label>
                    <select name="genre" id="genre">
                        <option value="">All Genres</option>
                        @if(isset($genres))
                        @foreach($genres as $genre)
                        <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                            {{ $genre->name }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="filter-group">
                    <label for="language">Language</label>
                    <select name="language" id="language">
                        <option value="">All Languages</option>
                        @if(isset($languages))
                        @foreach($languages as $lang)
                        <option value="{{ $lang }}" {{ request('language') == $lang ? 'selected' : '' }}>{{ $lang }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="filter-group">
                    <label for="rating">Rating</label>
                    <select name="rating" id="rating">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2+ Stars</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1+ Stars</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="showtime_date">Showtime Date</label>
                    <input type="date" name="showtime_date" id="showtime_date"
                        value="{{ request('showtime_date') ?? date('Y-m-d') }}">
                </div>
                <div class="filter-group">
                    <label for="sort">Sort by</label>
                    <select name="sort" id="sort">
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)
                        </option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)
                        </option>
                        <option value="release_desc" {{ request('sort') == 'release_desc' ? 'selected' : '' }}>Release
                            Date
                            (Newest)</option>
                        <option value="release_asc" {{ request('sort') == 'release_asc' ? 'selected' : '' }}>Release
                            Date
                            (Oldest)</option>
                    </select>
                </div>
                <div class="filter-group filter-actions">
                    <button type="submit" class="movie-btn movie-btn-primary">Apply</button>
                    <a href="{{ route('now_showing') }}" class="movie-btn movie-btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>



<!-- Movies Grid -->
<div class="movies-container">
    @if(isset($movies) && count($movies) > 0)
    <div class="movies-grid">
        @foreach($movies as $movie)
        <div class="movie-card">
            <div class="movie-poster-wrapper">
                @if(isset($movie->poster_url) && $movie->poster_url)
                <img src="{{ strpos($movie->poster_url, 'http') === 0 ? $movie->poster_url : asset('images/' . $movie->poster_url) }}"
                    alt="{{ $movie->title }}" class="movie-poster">
                @else
                <div class="movie-poster-placeholder">
                    <span>No Poster</span>
                </div>
                @endif
                <div class="movie-badge now-showing">Now Showing</div>
            </div>

            <div class="movie-info">
                <h3 class="movie-title">{{ $movie->title }}</h3>
                <div class="movie-meta">
                    <span class="genre">
                        @if(isset($movie->genres) && is_array($movie->genres) && count($movie->genres) > 0)
                        {{ implode(', ', $movie->genres) }}
                        @elseif(method_exists($movie, 'getGenresStringAttribute') && $movie->genres &&
                        count($movie->genres) > 0)
                        {{ $movie->genres_string }}
                        @else
                        Unknown
                        @endif
                    </span>
                    <span class="age-rating">{{ $movie->age_rating ?? 'N/A' }}</span>
                    <span class="duration">⏱️ {{ $movie->duration ?? '120' }} min</span>
                </div>

                @if($movie->rating_avg > 0)
                <div class="movie-rating">
                    <span class="rating-value">⭐ {{ $movie->rating_avg }}/5</span>
                </div>
                @endif

                <p class="movie-description">
                    {{ Str::limit($movie->description ?? 'Experience this amazing film in theaters now.', 100) }}</p>

                <div class="movie-actions">
                    <a href="/movies/{{ $movie->id }}" class="movie-btn movie-btn-primary">View Details</a>
                    <a href="{{ route('movies.showtimes', ['id' => $movie->id]) }}"
                        class="movie-btn movie-btn-secondary">Book Now</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">🎬</div>
        <h3>No movies currently showing</h3>
        <p>Check back later for new releases!</p>
        <a href="{{ route('homepage') }}" class="movie-btn movie-btn-primary">Back to Homepage</a>
    </div>
    @endif
</div>

<!-- Navigation Links -->
<div class="page-navigation">
    <a href="{{ route('homepage') }}" class="nav-link">← Back to Homepage</a>
    <a href="{{ route('upcoming_movies') }}" class="nav-link">Upcoming Movies →</a>
</div>
@endsection