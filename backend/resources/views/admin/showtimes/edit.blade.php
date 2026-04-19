{{--
/**
 * Admin Showtime Edit
 * 
 * Showtime editing interface including:
 * - Pre-filled showtime information
 * - Schedule modification options
 * - Pricing updates
 * - Booking impact warnings
 * - Save and cancel options
 */
--}}
@extends('layouts.admin')

@section('title', 'Edit Showtime')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.showtimes.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Showtimes
    </a>
</div>

<div class="card">
    <div class="card-header" style="background-color: var(--deep-teal); color: white;">
        <h4 class="mb-0"><i class="bi bi-pencil"></i> Edit Showtime #{{ $showtime->id }}</h4>
    </div>
    <div class="card-body">
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($hasBookings)
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Pricing Only Mode:</strong> Only peak hour pricing can be modified for showtimes. Other details are
            locked for data integrity.
        </div>
        @endif

        <form action="{{ route('admin.showtimes.update', $showtime) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Movie <span class="text-danger">*</span></label>
                    <select name="movie_id" class="form-select @error('movie_id') is-invalid @enderror"
                        {{ $hasBookings ? 'disabled' : 'required' }}>
                        <option value="">Select Movie</option>
                        @foreach($movies as $movie)
                        <option value="{{ $movie->id }}"
                            {{ old('movie_id', $showtime->movie_id) == $movie->id ? 'selected' : '' }}>
                            {{ $movie->title }} ({{ $movie->duration }} mins)
                        </option>
                        @endforeach
                    </select>
                    @if($hasBookings)
                    <input type="hidden" name="movie_id" value="{{ $showtime->movie_id }}">
                    @endif
                    @error('movie_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Room <span class="text-danger">*</span></label>
                    <select name="room_id" class="form-select @error('room_id') is-invalid @enderror"
                        {{ $hasBookings ? 'disabled' : 'required' }}>
                        <option value="">Select Room</option>
                        @foreach($rooms as $room)
                        <option value="{{ $room->id }}"
                            {{ old('room_id', $showtime->room_id) == $room->id ? 'selected' : '' }}>
                            {{ $room->name }} - {{ $room->screenType->name }} ({{ $room->seats->count() }} seats)
                        </option>
                        @endforeach
                    </select>
                    @if($hasBookings)
                    <input type="hidden" name="room_id" value="{{ $showtime->room_id }}">
                    @endif
                    @error('room_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Show Date <span class="text-danger">*</span></label>
                    <input type="date" name="show_date" class="form-control @error('show_date') is-invalid @enderror"
                        value="{{ old('show_date', $showtime->show_date->format('Y-m-d')) }}"
                        {{ $hasBookings ? 'disabled' : 'required' }}>
                    @if($hasBookings)
                    <input type="hidden" name="show_date" value="{{ $showtime->show_date->format('Y-m-d') }}">
                    @endif
                    @error('show_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Show Time <span class="text-danger">*</span></label>
                    <select name="show_time" class="form-select @error('show_time') is-invalid @enderror"
                        {{ $hasBookings ? 'disabled' : 'required' }}>
                        <option value="">Select Time</option>
                        @php
                        $currentTime = \Carbon\Carbon::parse($showtime->show_time)->format('H:i:s');
                        @endphp
                        <option value="09:00:00" {{ old('show_time', $currentTime) == '09:00:00' ? 'selected' : '' }}>
                            09:00 AM</option>
                        <option value="12:00:00" {{ old('show_time', $currentTime) == '12:00:00' ? 'selected' : '' }}>
                            12:00 PM</option>
                        <option value="15:00:00" {{ old('show_time', $currentTime) == '15:00:00' ? 'selected' : '' }}>
                            03:00 PM</option>
                        <option value="18:00:00" {{ old('show_time', $currentTime) == '18:00:00' ? 'selected' : '' }}>
                            06:00 PM</option>
                        <option value="21:00:00" {{ old('show_time', $currentTime) == '21:00:00' ? 'selected' : '' }}>
                            09:00 PM</option>
                    </select>
                    @if($hasBookings)
                    <input type="hidden" name="show_time" value="{{ $currentTime }}">
                    @endif
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
                @php
                $existingPrice = $showtime->showtimePrices->where('seat_type_id', $seatType->id)->first();
                $priceValue = $existingPrice ? $existingPrice->price : 0;
                @endphp
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        {{ ucfirst($seatType->name) }} Seat Surcharge
                        <small class="text-muted">(Base: {{ number_format($seatType->base_price) }}₫)</small>
                    </label>
                    <div class="input-group">
                        <input type="number" name="seat_type_prices[{{ $seatType->id }}]"
                            class="form-control @error('seat_type_prices.' . $seatType->id) is-invalid @enderror"
                            value="{{ old('seat_type_prices.' . $seatType->id, $priceValue) }}" min="0" step="1000"
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
                <strong>Note:</strong> Final ticket price = Base price (seat type) + Screen type price + Peak hour
                surcharge
            </div>

            <!-- Booking Status -->
            @php
            $seatStats = $showtime->seat_stats;
            @endphp

            @if($seatStats['booked'] > 0)
            <div class="alert alert-warning">
                <h6><i class="bi bi-exclamation-triangle"></i> Booking Status:</h6>
                <p class="mb-0">
                    <span class="badge bg-success">{{ $seatStats['available'] }} Available</span>
                    <span class="badge bg-danger">{{ $seatStats['booked'] }} Booked</span>
                </p>
                <small class="text-muted">Be careful when editing - there are existing bookings!</small>
            </div>
            @endif

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary-cinebook">
                    <i class="bi bi-check-circle"></i> Update Showtime
                </button>
                <a href="{{ route('admin.showtimes.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection