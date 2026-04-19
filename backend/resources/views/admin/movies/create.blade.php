{{--
/**
 * Admin Movie Create
 * 
 * Movie creation interface including:
 * - Movie information form
 * - Poster and media upload
 * - Genre and rating selection
 * - Showtime scheduling
 * - Form validation and submission
 */
--}}
@extends('layouts.admin')

@section('title', 'Create Movie')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.movies.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Movies
    </a>
</div>

<div class="card">
    <div class="card-header" style="background-color: var(--deep-teal); color: white;">
        <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Movie</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.movies.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                           value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Director <span style="color: red;">*</span></label>
                    <input type="text" name="director" class="form-control @error('director') is-invalid @enderror" 
                           value="{{ old('director') }}" required>
                    @error('director')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Genres</label>
                    <select name="genres[]" class="form-control @error('genres') is-invalid @enderror" multiple>
                        @foreach($genres as $genre)
                            <option value="{{ $genre->id }}" {{ (in_array($genre->id, old('genres', []))) ? 'selected' : '' }}>
                                {{ $genre->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple genres</small>
                    @error('genres')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                    <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" 
                           value="{{ old('duration') }}" min="1" required>
                    @error('duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Release Date <span class="text-danger">*</span></label>
                    <input type="date" name="release_date" class="form-control @error('release_date') is-invalid @enderror" 
                           value="{{ old('release_date') }}" required>
                    @error('release_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Language</label>
                    <input type="text" name="language" class="form-control @error('language') is-invalid @enderror" 
                           value="{{ old('language') }}" placeholder="e.g., English, Vietnamese">
                    @error('language')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Rating (0-10)</label>
                    <input type="number" name="rating" class="form-control @error('rating') is-invalid @enderror" 
                           value="{{ old('rating') }}" min="0" max="10" step="0.1">
                    @error('rating')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="now_showing" {{ old('status') == 'now_showing' ? 'selected' : '' }}>Now Showing</option>
                        <option value="coming_soon" {{ old('status') == 'coming_soon' ? 'selected' : '' }}>Coming Soon</option>
                        <option value="ended" {{ old('status') == 'ended' ? 'selected' : '' }}>Ended</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Cast</label>
                <textarea name="cast" class="form-control @error('cast') is-invalid @enderror" rows="2" 
                          placeholder="e.g., Actor 1, Actor 2, Actor 3">{{ old('cast') }}</textarea>
                @error('cast')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Poster URL</label>
                <input type="url" name="poster_url" class="form-control @error('poster_url') is-invalid @enderror" 
                       value="{{ old('poster_url') }}" placeholder="https://example.com/poster.jpg">
                @error('poster_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Trailer URL</label>
                <input type="url" name="trailer_url" class="form-control @error('trailer_url') is-invalid @enderror" 
                       value="{{ old('trailer_url') }}" placeholder="https://www.youtube.com/watch?v=...">
                @error('trailer_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" 
                          placeholder="Movie synopsis...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary-cinebook">
                    <i class="bi bi-check-circle"></i> Create Movie
                </button>
                <a href="{{ route('admin.movies.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
