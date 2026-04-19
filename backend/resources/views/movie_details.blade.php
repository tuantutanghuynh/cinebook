{{--
/**
 * Movie Details View
 * 
 * Detailed movie information page displaying:
 * - Movie poster, title, and description
 * - Movie metadata (duration, rating, genre)
 * - Showtimes and booking options
 * - User reviews and rating system
 * - Movie trailer and additional media
 */
--}}
@extends('layouts.main')

@section('title', '{{ $movie->title }} - Movie Details')

@push('styles')
@vite('resources/css/movie_details.css')
@endpush

@section('content')
<div class="movie-details-container">
    <div class="movie-header">
        <h1>{{ $movie->title }}</h1>
    </div>

    <div class="movie-content">
        <div class="movie-poster">
            @if($movie->poster_url)
            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }} Poster" class="poster-img">
            @endif

            <div class="poster-buttons">
                @if($movie->status == 'now_showing')
                <a href="{{ route('movies.showtimes', ['id' => $movie->id]) }}"
                    class="detail-btn detail-btn-primary">Book Now</a>
                @else
                <button class="detail-btn detail-btn-disabled" disabled>Coming Soon</button>
                @endif

                @if($movie->trailer_url)
                <button class="detail-btn detail-btn-trailer" onclick="openTrailerModal()">Watch Trailer</button>
                @endif
            </div>
        </div>

        <div class="movie-info">
            <div class="info-section">
                <h3>Movie Information</h3>

                <!-- Display Genres -->
                <p><strong>Genres:</strong>
                    @if(isset($movie->genres) && count($movie->genres) > 0)
                    @foreach($movie->genres as $index => $genre)
                    <span class="genre-badge">{{ $genre }}</span>{{ $index < count($movie->genres) - 1 ? ' ' : '' }}
                    @endforeach
                    @else
                    <span class="genre-badge">Unknown</span>
                    @endif
                </p>

                <p><strong>Language:</strong> {{ $movie->language }}</p>
                <p><strong>Duration:</strong> {{ $movie->duration }} minutes</p>
                <p><strong>Director:</strong> {{ $movie->director }}</p>
                <p><strong>Cast:</strong> {{ $movie->cast }}</p>
                <p><strong>Release Date:</strong> {{ date('d/m/Y', strtotime($movie->release_date)) }}</p>
                <p><strong>Age Rating:</strong> <span class="age-rating">{{ $movie->age_rating }}</span></p>
                <p><strong>Status:</strong>
                    @if($movie->status == 'now_showing')
                    <span class="status-badge status-showing">Now Showing</span>
                    @elseif($movie->status == 'coming_soon')
                    <span class="status-badge status-soon">Coming Soon</span>
                    @else
                    <span class="status-badge status-ended">{{ $movie->status }}</span>
                    @endif
                </p>
                @if($movie->rating_avg > 0)
                <p><strong>Rating:</strong> <span class="rating">{{ $movie->rating_avg }}/5</span></p>
                @endif
            </div>

            <div class="info-section">
                <h3>Synopsis</h3>
                <p>{{ $movie->description }}</p>
            </div>
            <div class="info-section">
                <h3>Reviews ({{ $movie->reviews->count() }})</h3>

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @auth
                @if($canReview)
                <!-- Form to add a new review -->
                <div class="review-form-container mb-4">
                    <h5>Write a Review</h5>
                    <form action="{{ route('reviews.store', $movie->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="movie_id" value="{{ $movie->id }}">

                        <!-- Star Rating Section -->
                        <div class="mb-3">
                            <label class="form-label">Your Rating</label>
                            <div class="star-rating-input">
                                <input type="radio" name="rating" value="5" id="star5" required>
                                <label for="star5" title="5 stars">★</label>

                                <input type="radio" name="rating" value="4" id="star4">
                                <label for="star4" title="4 stars">★</label>

                                <input type="radio" name="rating" value="3" id="star3">
                                <label for="star3" title="3 stars">★</label>

                                <input type="radio" name="rating" value="2" id="star2">
                                <label for="star2" title="2 stars">★</label>

                                <input type="radio" name="rating" value="1" id="star1">
                                <label for="star1" title="1 star">★</label>
                            </div>
                        </div>

                        <!-- Comment Section -->
                        <div class="mb-3">
                            <label for="comment" class="form-label">Your Comment</label>
                            <textarea name="comment" id="comment" class="form-control" rows="4"
                                placeholder="Share your thoughts about this movie..."
                                maxlength="1000">{{ old('comment') }}</textarea>
                            <small class="text-muted">Maximum 1000 characters</small>
                        </div>

                        <button type="submit" class="detail-btn detail-btn-primary">Submit Review</button>
                    </form>
                </div>
                @else
                @php
                $userReview = $movie->reviews->where('user_id', Auth::id())->first();
                @endphp
                @if($userReview)
                <div class="alert alert-info">
                    You have already reviewed this movie.
                </div>
                @else
                <div class="alert alert-warning">
                    You can only review movies you have watched. Book a ticket and watch the movie first!
                </div>
                @endif
                @endif
                @else
                <!-- User not logged in -->
                <div class="alert alert-warning">
                    Please <a href="{{ route('login') }}" class="alert-link">login</a> to write a review.
                </div>
                @endauth

                <!-- List of all reviews -->
                <div class="reviews-list mt-4">
                    <div class="reviews-header">
                        <h5 class="mb-0">All Reviews</h5>
                        @if($movie->reviews->count() > 0)
                        <div class="review-sort-dropdown">
                            <label for="review_sort">Sort by:</label>
                            <select id="review_sort" onchange="sortReviews(this.value)">
                                <option value="latest" {{ ($reviewSort ?? 'latest') == 'latest' ? 'selected' : '' }}>
                                    Latest</option>
                                <option value="highest" {{ ($reviewSort ?? '') == 'highest' ? 'selected' : '' }}>Highest
                                    Rating</option>
                            </select>
                        </div>
                        @endif
                    </div>
                    @php
                    $sortedReviews = ($reviewSort ?? 'latest') == 'highest'
                    ? $movie->reviews->sortBy([['rating', 'desc'], ['created_at', 'desc']])
                    : $movie->reviews->sortByDesc('created_at');
                    @endphp
                    @forelse($sortedReviews as $review)
                    <div class="review-item">
                        <!-- User and time information -->
                        <div class="review-header">
                            <div class="review-user-info">
                                <strong class="review-username">{{ $review->user->name }}</strong>
                                <!-- Display star rating -->
                                <div class="review-stars">
                                    @for($i = 1; $i <= 5; $i++) @if($i <=$review->rating)
                                        <span class="star-filled">★</span>
                                        @else
                                        <span class="star-empty">★</span>
                                        @endif
                                        @endfor
                                        <span class="rating-number">({{ $review->rating }}/5)</span>
                                </div>
                            </div>
                            <small class="review-time">{{ $review->created_at->diffForHumans() }}</small>
                        </div>
                        <!-- Review Content -->
                        <div class="review-content">
                            <p>{{ $review->comment }}</p>
                        </div>
                        <!-- Delete Button (admin only) -->
                        @auth
                        @if(Auth::user() && Auth::user()->role === 'admin')
                        <div class="review-actions">
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST"
                                class="review-actions-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="review-delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this review?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                        @endif
                        @endauth
                    </div>
                    @empty
                    <div class="no-reviews">
                        <p>No reviews yet. Be the first to review this movie!</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="back-button">
                @if($movie->status == 'now_showing')
                <a href="{{ route('now_showing') }}" class="detail-btn detail-btn-secondary">Back to Now
                    Showing</a>
                @else
                <a href="{{ route('upcoming_movies') }}" class="detail-btn detail-btn-secondary">Back to
                    Upcoming</a>
                @endif
            </div>

            <!-- Trailer Modal -->
            @if($movie->trailer_url)
            <div id="trailerModal" class="modal">
                <div class="modal-content">
                    <button class="modal-close" onclick="closeTrailerModal()">&times;</button>
                    <div class="modal-body">
                        @php
                        // Extract YouTube video ID from URL
                        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/',
                        $movie->trailer_url,
                        $matches);
                        $videoId = $matches[1] ?? '';
                        @endphp
                        @if($videoId)
                        <iframe width="100%" height="600" src="https://www.youtube.com/embed/{{ $videoId }}"
                            title="{{ $movie->title }} Trailer" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                        @else
                        <p><a href="{{ $movie->trailer_url }}" target="_blank">Watch Trailer</a></p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <script>
        function openTrailerModal() {
            document.getElementById('trailerModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeTrailerModal() {
            document.getElementById('trailerModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('trailerModal');
            if (modal && event.target == modal) {
                closeTrailerModal();
            }
        }

        // Close modal when pressing Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('trailerModal');
                if (modal && modal.style.display === 'flex') {
                    closeTrailerModal();
                }
            }
        });

        // Sort reviews function
        function sortReviews(sortValue) {
            const url = new URL(window.location.href);
            url.searchParams.set('review_sort', sortValue);
            window.location.href = url.toString();
        }
        </script>
        @endsection