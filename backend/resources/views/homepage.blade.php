{{--
/**
 * Homepage View
 * 
 * Main landing page displaying:
 * - Hero banner with featured content
 * - Now showing movies carousel
 * - Upcoming movies section
 * - Interactive movie filtering and search
 * - Homepage-specific styling and scripts
 */
--}}
@extends('layouts.main')

@section('title', 'TCA Cine - Home')

@push('styles')
@vite(['resources/css/homepage.css'])
@endpush

@section('content')
<!-- Hero Banner -->
<div class="hero-section">
    <h1>Welcome to TCA Cine</h1>
    <p>Experience the Wonderful World of Cinema</p>
    <form action="{{ route('search') }}" method="get" class="search-form">
        <input type="text" name="q" class="search-input" placeholder="Search by title, genre, director, language..."
            required>
        <button type="submit" class="search-btn">Search</button>
    </form>
    <a href="{{ route('now_showing') }}" class="btn-cta btn-cta-primary btn-lg">Book Tickets Now</a>
</div>

<!-- Featured Movies -->
<div class="featured-movies">
    <h2 class="section-title">🔥 Featured Movies</h2>

    @if(isset($movies) && count($movies) > 0)
    <div class="movies-container">
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

                    <div
                        class="movie-badge {{ ($movie->status ?? '') === 'coming_soon' ? 'coming-soon' : 'now-showing' }}">
                        {{ ($movie->status ?? '') === 'coming_soon' ? 'Coming Soon' : 'Now Showing' }}
                    </div>
                </div>

                <div class="movie-info">
                    <h3 class="movie-title">{{ $movie->title }}</h3>

                    <div class="movie-meta">
                        <span class="genre">
                            @if(isset($movie->genres) && count($movie->genres) > 0)
                            {{ implode(', ', $movie->genres) }}
                            @else
                            Unknown
                            @endif
                        </span>
                        <span class="age-rating"> {{ $movie->age_rating ?? 'Not Rated' }}</span>
                        <span class="duration">⏱️ {{ $movie->duration ?? '120' }} min</span>
                    </div>

                    @if(isset($movie->rating_avg) && $movie->rating_avg > 0)
                    <div class="movie-rating">
                        <span class="rating-value">⭐ {{ $movie->rating_avg }}/5</span>
                    </div>
                    @endif

                    <p class="movie-description">
                        {{ Str::limit($movie->description ?? 'Experience this amazing film in theaters now.', 110) }}
                    </p>

                    <div class="movie-actions">
                        <a href="/movies/{{ $movie->id }}" class="btn btn-detail">View Details</a>
                        <a href="{{ route('movies.showtimes', ['id' => $movie->id]) }}" class="btn btn-secondary">Book
                            Now</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="btn-view-all-container">
        <a href="{{ route('now_showing') }}" class="btn-view-all">
            View All Now Showing Movies →
        </a>
    </div>
    @else
    <div class="empty-state">
        <p>No movies available at the moment. Please check back later!</p>
    </div>
    @endif
</div>
<!-- Upcoming Movies Section -->
<div class="featured-movies">
    <h2 class="section-title">🎬 Upcoming Movies</h2>
    @if(isset($upcomingMovies) && count($upcomingMovies) > 0)
    <div class="movies-container">
        <div class="movies-grid">
            @foreach($upcomingMovies as $movie)
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

                    <div
                        class="movie-badge {{ ($movie->status ?? '') === 'coming_soon' ? 'coming-soon' : 'now-showing' }}">
                        {{ ($movie->status ?? '') === 'coming_soon' ? 'Coming Soon' : 'Now Showing' }}
                    </div>
                </div>

                <div class="movie-info">
                    <h3 class="movie-title">{{ $movie->title }}</h3>

                    <div class="movie-meta">
                        <span class="genre">
                            @if(isset($movie->genres) && count($movie->genres) > 0)
                            {{ implode(', ', $movie->genres) }}
                            @else
                            Unknown
                            @endif
                        </span>
                        <span class="age-rating">{{ $movie->age_rating ?? 'N/A' }}</span>
                        <span class="duration">⏱️ {{ $movie->duration ?? '120' }} min</span>
                    </div>

                    {{-- Only show rating for movies that are not coming soonb  --}}
                    @if(isset($movie->rating_avg) && $movie->rating_avg > 0 && ($movie->status ?? '') !== 'coming_soon')
                    <div class="movie-rating">
                        <span class="rating-value">⭐ {{ $movie->rating_avg }}/5</span>
                    </div>
                    @endif

                    <p class="movie-description">
                        {{ Str::limit($movie->description ?? 'Upcoming movies', 110) }}
                    </p>

                    <div class="movie-actions">
                        <a href="/movies/{{ $movie->id }}" class="btn btn-detail">View Details</a>
                        <a href="{{ route('movies.showtimes', ['id' => $movie->id]) }}" class="btn btn-secondary">Book
                            Now</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="btn-view-all-container">
        <a href="{{ route('upcoming_movies') }}" class="btn-view-all">
            View All Upcoming Movies →
        </a>
    </div>
    @else
    <div class="empty-state">
        <p>No movies available at the moment. Please check back later!</p>
    </div>
    @endif
</div>

<!-- Promotion Section -->
<div class="cinema-corner-section promotion-section">
    <div class="cinema-corner-header">
        <span class="corner-title-bar"></span>
        <span class="corner-title">SPECIAL PROMOTIONS</span>
        <span class="corner-tab active">Cinema Gifts</span>
    </div>
    <div class="cinema-corner-content">
        <div class="corner-main-article">
            <img src="{{ asset('images/tca_promo_popcorn_centered.png') }}"
                alt="Premium Combo" class="corner-main-img">
            <div class="corner-main-title">🎁 Get Free Premium Popcorn Combo - Limited Time Offer!</div>
            <div class="corner-main-meta">
                <a href="{{ route('promotions') }}" class="corner-like-btn">🎟️ Claim Now</a>
                <span class="corner-view"><i class="fa fa-fire"></i> Hot Deal</span>
            </div>
        </div>
        <div class="corner-side-articles">
            <div class="corner-side-article">
                <img src="{{ asset('images/tca_promo_merch_centered.png') }}" alt="Movie Merchandise"
                    class="corner-side-img">
                <div class="corner-side-info">
                    <div class="corner-side-title">🎬 Exclusive Movie Merchandise - Buy 2 Tickets Get 1 Free Collectible</div>
                    <div class="corner-side-meta">
                        <a href="{{ route('promotions') }}" class="corner-like-btn">🛍️ Shop Now</a>
                        <span class="corner-view"><i class="fa fa-gift"></i> Limited</span>
                    </div>
                </div>
            </div>
            <div class="corner-side-article">
                <img src="{{ asset('images/tca_promo_birthday_centered.png') }}"
                    alt="Birthday Special" class="corner-side-img">
                <div class="corner-side-info">
                    <div class="corner-side-title">🎂 Birthday Special - Free Ticket on Your Special Day!</div>
                    <div class="corner-side-meta">
                        <a href="{{ route('register') }}" class="corner-like-btn">🎉 Register</a>
                        <span class="corner-view"><i class="fa fa-birthday-cake"></i> Members</span>
                    </div>
                </div>
            </div>
            <div class="corner-side-article">
                <img src="{{ asset('images/tca_promo_student_centered.png') }}" alt="Student Discount" class="corner-side-img">
                <div class="corner-side-info">
                    <div class="corner-side-title">🎓 Student Discount - 20% Off Every Tuesday & Wednesday</div>
                    <div class="corner-side-meta">
                        <a href="{{ route('promotions') }}" class="corner-like-btn">💳 Get Card</a>
                        <span class="corner-view"><i class="fa fa-percent"></i> Save 20%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="corner-more-btn-container">
        <a href="{{ route('promotions') }}" class="corner-more-btn">View All Promotions <span>&rarr;</span></a>
    </div>
</div>

<!-- Cinema Corner Section -->
<div class="cinema-corner-section">
    <div class="cinema-corner-header">
        <span class="corner-title-bar"></span>
        <span class="corner-title">CINEMA CORNER</span>
        <span class="corner-tab active">Movie Reviews</span>
        <span class="corner-tab">Cinema Blog</span>
    </div>
    <div class="cinema-corner-content">
        <div class="corner-main-article">
            <img src="https://i.postimg.cc/jdBWvdWn/avengers-endgame-final-battle-all-heroes.avif"
                alt="Avengers: Endgame" class="corner-main-img">
            <div class="corner-main-title">[Review] Avengers: Endgame – The Epic Conclusion to the Infinity Saga</div>
            <div class="corner-main-meta">
                <span class="corner-like-btn">👍 Like</span>
                <span class="corner-view"><i class="fa fa-eye"></i> 1205</span>
            </div>
        </div>
        <div class="corner-side-articles">
            <div class="corner-side-article">
                <img src="https://i.postimg.cc/cJP0GrSm/johnwick.jpg" alt="John Wick: Chapter 4"
                    class="corner-side-img">
                <div class="corner-side-info">
                    <div class="corner-side-title">[Review] John Wick: Chapter 4 – Relentless Action and Deeper
                        Mythology</div>
                    <div class="corner-side-meta">
                        <span class="corner-like-btn">👍 Like</span>
                        <span class="corner-view"><i class="fa fa-eye"></i> 980</span>
                    </div>
                </div>
            </div>
            <div class="corner-side-article">
                <img src="https://i.postimg.cc/rwbvK3P8/the-kim-family-woo-sik-choi-kang-ho-song-hye-jin-jang-so-dam-park-in-parasite-courtesy-of-neon.avif"
                    alt="Parasite" class="corner-side-img">
                <div class="corner-side-info">
                    <div class="corner-side-title">[Review] Parasite – A Masterpiece of Social Satire and Suspense</div>
                    <div class="corner-side-meta">
                        <span class="corner-like-btn">👍 Like</span>
                        <span class="corner-view"><i class="fa fa-eye"></i> 1502</span>
                    </div>
                </div>
            </div>
            <div class="corner-side-article">
                <img src="https://i.postimg.cc/Pr764Zzx/02ri78xl7t.jpg" alt="Avatar" class="corner-side-img">
                <div class="corner-side-info">
                    <div class="corner-side-title">[Review] Avatar – Visual Brilliance and Environmental Message</div>
                    <div class="corner-side-meta">
                        <span class="corner-like-btn">👍 Like</span>
                        <span class="corner-view"><i class="fa fa-eye"></i> 2103</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="corner-more-btn-container">
        <a href="#" class="corner-more-btn">See more <span>&rarr;</span></a>
    </div>
</div>

<!-- Call to Action Section -->
<div class="cta-section">
    <h2>Ready to Watch?</h2>
    <p>Join TCA Cine's movie-loving community today</p>
    <div class="cta-buttons">
        <a href="{{ route('register') }}" class="btn-cta btn-cta-primary">
            Sign Up Now
        </a>
        <a href="#contact" class="btn-cta btn-cta-secondary">
            Contact Us
        </a>
    </div>
</div>
@endsection