{{--
/**
 * Admin Seat Types Pricing
 * 
 * Seat pricing management including:
 * - Seat type price configuration
 * - Bulk pricing updates
 * - Price history tracking
 * - Seasonal pricing options
 * - Save and reset functionality
 */
--}}
@extends('layouts.admin')

@section('title', 'Seat Type Prices')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Set Seat Type Prices</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.seat_types.update_prices') }}" method="POST">
                @csrf
                <div class="row">
                    @foreach($seatTypes as $seatType)
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <span class="legend-color {{ strtolower($seatType->name) }} me-2" style="width: 20px; height: 20px; display: inline-block; border-radius: 4px;"></span>
                            {{ ucfirst($seatType->name) }} Price (VND)
                        </label>
                        <input type="number" class="form-control" name="prices[{{ $seatType->id }}]" value="{{ $seatType->base_price }}" min="0" step="1000" required>
                    </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-2"></i>Save All Prices
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.legend-color.standard { background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); }
.legend-color.vip { background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%); }
.legend-color.couple { background: linear-gradient(135deg, #e84393 0%, #d63384 100%); }
</style>
@endpush
