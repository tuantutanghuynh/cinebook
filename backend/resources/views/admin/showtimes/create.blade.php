{{--
/**
 * Admin Showtime Create
 * 
 * Showtime creation interface including:
 * - Movie and room selection
 * - Date and time scheduling
 * - Pricing configuration
 * - Seat availability setup
 * - Schedule conflict detection
 */
--}}
@extends('layouts.admin')

@section('title', 'Create Showtime')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.showtimes.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Showtimes
    </a>
</div>

<div class="card">
    <div class="card-header" style="background-color: var(--deep-teal); color: white;">
        <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Showtime</h4>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('admin.showtimes.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Movie <span class="text-danger">*</span></label>
                    <select name="movie_id" class="form-select @error('movie_id') is-invalid @enderror" required>
                        <option value="">Select Movie</option>
                        @foreach($movies as $movie)
                            <option value="{{ $movie->id }}" {{ old('movie_id') == $movie->id ? 'selected' : '' }}>
                                {{ $movie->title }} ({{ $movie->duration }} mins)
                            </option>
                        @endforeach
                    </select>
                    @error('movie_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Room <span class="text-danger">*</span></label>
                    <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                        <option value="">Select Room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} - {{ $room->screenType->name }} ({{ $room->seats->count() }} seats)
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Show Date <span class="text-danger">*</span></label>
                    <input type="date" name="show_date" class="form-control @error('show_date') is-invalid @enderror"
                           value="{{ old('show_date', now()->format('Y-m-d')) }}"
                           min="{{ now()->format('Y-m-d') }}" required>
                    @error('show_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Show Time <span class="text-danger">*</span></label>
                    <select name="show_time" class="form-select @error('show_time') is-invalid @enderror" required>
                        <option value="">Select Time</option>
                        <option value="09:00:00" {{ old('show_time') == '09:00:00' ? 'selected' : '' }}>09:00 AM</option>
                        <option value="12:00:00" {{ old('show_time') == '12:00:00' ? 'selected' : '' }}>12:00 PM</option>
                        <option value="15:00:00" {{ old('show_time') == '15:00:00' ? 'selected' : '' }}>03:00 PM</option>
                        <option value="18:00:00" {{ old('show_time') == '18:00:00' ? 'selected' : '' }}>06:00 PM</option>
                        <option value="21:00:00" {{ old('show_time') == '21:00:00' ? 'selected' : '' }}>09:00 PM</option>
                    </select>
                    @error('show_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr>

            <h5 class="mb-3" style="color: var(--prussian-blue)">
                <i class="bi bi-cash-stack"></i> Peak Hour Pricing
            </h5>
            <p class="text-muted">Set additional price for each seat type during this showtime (peak hour surcharge)</p>

            <div class="row">
                @foreach($seatTypes as $seatType)
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            {{ ucfirst($seatType->name) }} Seat Surcharge
                            <small class="text-muted">(Base: {{ number_format($seatType->base_price) }}₫)</small>
                        </label>
                        <div class="input-group">
                            <input type="number"
                                   name="seat_type_prices[{{ $seatType->id }}]"
                                   class="form-control @error('seat_type_prices.' . $seatType->id) is-invalid @enderror"
                                   value="{{ old('seat_type_prices.' . $seatType->id, 0) }}"
                                   min="0"
                                   step="1000"
                                   required>
                            <span class="input-group-text">₫</span>
                        </div>
                        @error('seat_type_prices.' . $seatType->id)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Note:</strong> Final ticket price = Base price (seat type) + Screen type price + Peak hour surcharge
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary-cinebook">
                    <i class="bi bi-check-circle"></i> Create Showtime
                </button>
                <a href="{{ route('admin.showtimes.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
