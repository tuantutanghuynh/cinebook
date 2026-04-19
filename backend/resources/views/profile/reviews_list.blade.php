{{--
/**
 * User Reviews List View
 * 
 * Displays user's movie reviews with comprehensive filtering and search functionality:
 * - Rating-based filtering (1-5 stars)
 * - Date range filtering with validation
 * - Movie title search functionality  
 * - Paginated review display with poster images
 * - Empty state handling for no reviews
 */
--}}

{{--
/**
 * User Reviews List
 * 
 * User review management interface including:
 * - List of user's movie reviews
 * - Review editing and deletion options
 * - Rating and comment display
 * - Review filtering and search
 * - Review statistics
 */
--}}
@extends('profile.profilepage')

@section('title', 'My Reviews - TCA Cine')

@section('page-title', 'My Reviews')

@push('styles')
@vite('resources/css/profile_reviews.css')
@endpush

@push('scripts')
<script src="{{ asset('js/profile-reviews-filter.js') }}"></script>
@endpush

@section('content')
<div class="profile-reviews-container">
    <div class="profile-reviews-header">
        <h2><i class="fas fa-star"></i> My Reviews</h2>
        <p class="profile-reviews-subtitle">Manage and view all your movie reviews</p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="error-alert">
        <div class="error-content">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="error-text">
                <strong>Validation Error:</strong>
                <ul class="error-list">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
    <!-- Filters -->
    <div class="filters-section">
        <div class="filter-header">
            <h4><i class="fas fa-filter"></i> Filter Reviews</h4>
        </div>
        <form method="GET" action="{{ route('user.reviews.list') }}" id="filterForm">
            <div class="filter-row">
                <!-- Rating Filter -->
                <div class="filter-group">
                    <label for="rating">Rating</label>
                    <select name="rating" id="rating" class="filter-control">
                        <option value="">All Ratings</option>
                        @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                        </option>
                        @endfor
                    </select>
                    @error('rating')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Date From -->
                <div class="filter-group">
                    <label for="date_from">From Date</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="filter-control {{ $errors->has('date_from') ? 'error' : '' }}">
                    @error('date_from')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Date To -->
                <div class="filter-group">
                    <label for="date_to">To Date</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                        class="filter-control {{ $errors->has('date_to') ? 'error' : '' }}">
                    @error('date_to')
                    <span class="field-error">{{ $message }}</span>
                    @enderror

                </div>

            </div>
            <!-- Filter Actions -->
            <div class="filter-actions">
                <button type="submit" class="btn-apply">
                    <i class="fas fa-search"></i> Apply
                </button>
                <a href="{{ route('user.reviews.list') }}" class="btn-reset">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <h4><i class="fas fa-search"></i> Search Movies</h4>
        <form method="GET" action="{{ route('user.reviews.list') }}">
            <!-- Preserve existing filters -->
            @if(request('rating'))
            <input type="hidden" name="rating" value="{{ request('rating') }}">
            @endif
            @if(request('date_from'))
            <input type="hidden" name="date_from" value="{{ request('date_from') }}">
            @endif
            @if(request('date_to'))
            <input type="hidden" name="date_to" value="{{ request('date_to') }}">
            @endif

            <div class="search-row">
                <div class="search-group">
                    <input type="text" name="search" placeholder="Movie title..." value="{{ request('search') }}"
                        class="search-input">
                    <button type="submit" class="btn-apply search-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Reviews List -->
    @if($reviews->count() > 0)
    <div class="reviews-list">
        @foreach($reviews as $review)
        <div class="review-card">
            <div class="review-movie-info">
                <img src="{{ $review->movie->poster_url ?: asset('images/default-poster.jpg') }}"
                    alt="{{ $review->movie->title }}" class="review-movie-poster"
                    onerror="this.src='{{ asset('images/default-poster.jpg') }}';">
                <div class="review-movie-details">
                    <h5>{{ $review->movie->title }}</h5>
                    <p class="text-muted">{{ $review->movie->genres->first()->name ?? 'Unknown Genre' }}</p>
                </div>
            </div>

            <div class="review-content-wrapper">
                <div class="review-rating">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++) <i class="fas fa-star{{ $i <= $review->rating ? '' : ' empty' }}">
                            </i>
                            @endfor
                    </div>
                    <span class="rating-text">{{ $review->rating }}/5 stars</span>
                </div>

                @if($review->comment)
                <div class="review-content">
                    {{ $review->comment }}
                </div>
                @endif

                <div class="review-meta">
                    <div class="review-date">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $review->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($reviews->hasPages())
    <div class="reviews-pagination">
        <div class="pagination-simple">
            {{-- Previous Page Link --}}
            @if ($reviews->onFirstPage())
            <span class="page-btn disabled">
                <i class="fas fa-chevron-left"></i> Previous
            </span>
            @else
            <a href="{{ $reviews->previousPageUrl() }}" class="page-btn prev">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
            @endif

            {{-- Page Info --}}
            <span class="page-info">
                Page {{ $reviews->currentPage() }} of {{ $reviews->lastPage() }}
                <small>({{ $reviews->total() }} reviews)</small>
            </span>

            {{-- Next Page Link --}}
            @if ($reviews->hasMorePages())
            <a href="{{ $reviews->nextPageUrl() }}" class="page-btn next">
                Next <i class="fas fa-chevron-right"></i>
            </a>
            @else
            <span class="page-btn disabled">
                Next <i class="fas fa-chevron-right"></i>
            </span>
            @endif
        </div>
    </div>
    @endif
    @else
    <div class="empty-state">
        <h4>No Reviews Found</h4>
        <p>You haven't written any reviews yet or no reviews match your filters.</p>
        <a href="{{ route('now_showing') }}" class="btn-browse-movies">
            <i class="fas fa-film"></i> Browse Movies to Review
        </a>
    </div>
    @endif
</div>

@endsection