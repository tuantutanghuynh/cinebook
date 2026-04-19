{{--
/**
 * Admin Movies List
 * 
 * Movie management interface including:
 * - Movies list with search and filtering
 * - Movie status management
 * - Bulk operations
 * - Add new movie button
 * - Edit and view movie options
 */
--}}
@extends('layouts.admin')

@section('title', 'Manage Movies')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: var(--prussian-blue)">
        <i class="bi bi-film"></i> Manage Movies
    </h2>
    <a href="{{ route('admin.movies.create') }}" class="btn btn-primary-cinebook">
        <i class="bi bi-plus-circle"></i> Add New Movie
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-body">
        @if($movies->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> No movies found.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Title</th>
                        <th>Duration</th>
                        <th>Genre</th>
                        <th>Release Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movies as $movie)
                    <tr>
                        <td>{{ $movie->id }}</td>
                        <td>
                            @if($movie->poster_url)
                            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
                                style="width: 50px; height: 75px; object-fit: cover;">
                            @else
                            <div class="bg-secondary"
                                style="width: 50px; height: 75px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-film text-white"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $movie->title }}</strong><br>
                            <small class="text-muted">{{ Str::limit($movie->director, 20) }}</small>
                        </td>
                        <td>{{ $movie->duration }} mins</td>
                        <td>
                            @if(isset($movie->genres) && count($movie->genres) > 0)
                                @foreach($movie->genres as $genre)
                                    <span class="badge bg-info me-1">{{ $genre }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">No genres</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($movie->release_date)->format('M d, Y') }}</td>
                        <td>
                            @if($movie->status == 'now_showing')
                            <span class="badge bg-success">Now Showing</span>
                            @elseif($movie->status == 'coming_soon')
                            <span class="badge bg-warning">Coming Soon</span>
                            @else
                            <span class="badge bg-secondary">Ended</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.movies.edit', $movie) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($movies->hasPages())
        <div class="cine-pagination-wrapper">
            <nav aria-label="Movies pagination">
                <ul class="cine-pagination">
                    @foreach ($movies->getUrlRange(1, $movies->lastPage()) as $page => $url)
                    <li class="cine-page-item {{ $page == $movies->currentPage() ? 'is-active' : '' }}">
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