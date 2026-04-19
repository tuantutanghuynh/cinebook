{{--
/**
 * Admin Reviews Management
 * 
 * Review moderation interface including:
 * - All reviews list with filtering
 * - Review approval/rejection
 * - Inappropriate content flagging
 * - User and movie filtering
 * - Bulk review operations
 */
--}}
@extends('layouts.admin')

@section('title', 'Manage Reviews')

@section('content')
<h2 class="mb-4" style="color: var(--prussian-blue)">
    <i class="bi bi-star-fill"></i> Manage Reviews
</h2>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white"
            style="background: linear-gradient(135deg, var(--deep-teal), var(--deep-space-blue));">
            <div class="card-body">
                <h6 class="card-title">Total Reviews</h6>
                <h2 class="mb-0">{{ $reviews->total() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h6 class="card-title">Average Rating</h6>
                <h2 class="mb-0">{{ number_format($reviews->avg('rating'), 1) }}/5</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title">5 Star Reviews</h6>
                <h2 class="mb-0">{{ $reviews->where('rating', 5)->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h6 class="card-title">1-2 Star Reviews</h6>
                <h2 class="mb-0">{{ $reviews->whereIn('rating', [1, 2])->count() }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reviews.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Movie</label>
                    <select name="movie_id" class="form-select">
                        <option value="">All Movies</option>
                        @foreach($movies as $movie)
                        <option value="{{ $movie->id }}" {{ request('movie_id') == $movie->id ? 'selected' : '' }}>
                            {{ $movie->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="highest_rated" {{ request('sort') == 'highest_rated' ? 'selected' : '' }}>Highest Rated</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-cinebook w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reviews Table -->
<div class="card">
    <div class="card-body">
        @if($reviews->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> No reviews found.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Movie</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reviews as $review)
                    <tr>
                        <td><strong>#{{ $review->id }}</strong></td>
                        <td>
                            {{ $review->user->name }}<br>
                            <small class="text-muted">{{ $review->user->email }}</small>
                        </td>
                        <td>
                            <strong>{{ $review->movie->title }}</strong>
                        </td>
                        <td>
                            <div class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <span class="text-warning">★</span>
                                    @else
                                        <span class="text-muted">★</span>
                                    @endif
                                @endfor
                                <br>
                                <span class="badge bg-warning text-dark">{{ $review->rating }}/5</span>
                            </div>
                        </td>
                        <td>
                            <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                {{ Str::limit($review->comment, 100) }}
                            </div>
                        </td>
                        <td>
                            {{ $review->created_at->format('M d, Y') }}<br>
                            <small class="text-muted">{{ $review->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Review"
                                    onclick="return confirm('Are you sure you want to delete this review?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($reviews->hasPages())
        <div class="cine-pagination-wrapper">
            <nav aria-label="Reviews pagination">
                <ul class="cine-pagination">
                    @foreach ($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                    <li class="cine-page-item {{ $page == $reviews->currentPage() ? 'is-active' : '' }}">
                        <a class="cine-page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endforeach
                </ul>
            </nav>
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
