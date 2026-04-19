{{--
/**
 * Admin Room Create
 * 
 * Room creation interface including:
 * - Room information form
 * - Screen type selection
 * - Seat layout configuration
 * - Capacity and accessibility settings
 * - Room equipment setup
 */
--}}
@extends('layouts.admin')

@section('title', 'Add New Room')

@push('styles')
@vite(['resources/css/admin-room-create.css'])
@endpush

@section('content')
<div class="admin-room-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Add New Room</h2>
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Rooms
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.rooms.store') }}" method="POST" id="createRoomForm">
        @csrf

        <!-- Room Info Form -->
        <div class="room-form-card">
            <h4><i class="bi bi-info-circle me-2"></i>Room Information</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Room Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Room 1, Hall A" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="screen_type_id" class="form-label">Screen Type <span class="text-danger">*</span></label>
                    <select class="form-select" id="screen_type_id" name="screen_type_id" required>
                        @foreach($screenTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="total_rows" class="form-label">Total Rows <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="total_rows" name="total_rows" min="1" max="26" value="8" required>
                    <small class="text-muted">Maximum 26 rows (A-Z)</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="seats_per_row" class="form-label">Seats Per Row <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="seats_per_row" name="seats_per_row" min="1" max="30" value="10" required>
                    <small class="text-muted">Maximum 30 seats per row</small>
                </div>
            </div>
            <button type="button" class="btn btn-outline-primary" id="generatePreviewBtn">
                <i class="bi bi-eye me-2"></i>Generate Preview
            </button>
        </div>

        <!-- Seat Map Preview Section -->
        <div class="seat-preview-section">
            <h4><i class="bi bi-grid-3x3 me-2"></i>Seat Map Preview</h4>
            <p class="section-subtitle">Preview the seat layout and optionally customize seat types before creating the room. Click seats to select, then use sidebar to change type.</p>

            <!-- Quick Templates -->
            <div class="quick-templates">
                <h6><i class="bi bi-magic me-2"></i>Quick Templates</h6>
                <div class="template-btns">
                    <button type="button" class="template-btn" data-template="all-standard">All Standard</button>
                    <button type="button" class="template-btn" data-template="vip-center">VIP Center Rows</button>
                    <button type="button" class="template-btn" data-template="couple-back">Couple Back Rows</button>
                    <button type="button" class="template-btn" data-template="cinema-style">Cinema Style</button>
                </div>
            </div>

            <!-- Legend -->
            <div class="seat-legend">
                <h4>Legend:</h4>
                <div class="legend-items">
                    <div class="legend-item">
                        <span class="legend-color standard"></span>
                        <span>Standard</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color vip"></span>
                        <span>VIP</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color couple"></span>
                        <span>Couple</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color selected"></span>
                        <span>Selected</span>
                    </div>
                </div>
            </div>

            <!-- Selection Mode Bar -->
            <div class="selection-mode-bar" id="selectionModeBar">
                <span class="mode-text"><i class="bi bi-cursor me-2"></i>Selection Mode</span>
                <span class="selected-count"><span id="selectedCountText">0</span> seats selected</span>
            </div>

            <!-- Cinema Screen -->
            <div class="cinema-screen">
                <div class="screen-label">Screen</div>
            </div>

            <!-- Seat Map Preview -->
            <div class="seat-map-wrapper">
                <div id="previewSeatMap">
                    <div class="empty-preview">
                        <i class="bi bi-grid-1x2"></i>
                        <p>Enter room dimensions and click "Generate Preview" to see the seat layout</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-3">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-plus me-2"></i>Create Room
                </button>
                <button type="button" class="btn btn-outline-primary" id="openSidebarBtn" disabled>
                    <i class="bi bi-pencil me-2"></i>Edit Selected Seats
                </button>
            </div>
        </div>

        <!-- Hidden inputs for seat configurations -->
        <div id="seatConfigInputs"></div>
    </form>
</div>

<!-- Sidebar Overlay -->
<div class="seat-sidebar-overlay" id="sidebarOverlay"></div>

<!-- Seat Edit Sidebar -->
<div class="seat-sidebar" id="seatSidebar">
    <div class="sidebar-header d-flex justify-content-between align-items-center">
        <h5><i class="bi bi-grid-1x2 me-2"></i>Edit Seat Type</h5>
        <button type="button" class="close-btn" id="closeSidebarBtn">
            <i class="bi bi-x"></i>
        </button>
    </div>

    <div class="sidebar-body">
        <!-- Selected Seats Info -->
        <div class="selected-seats-info">
            <h6><i class="bi bi-check-square me-2"></i>Selected Seats</h6>
            <div class="selected-seats-list" id="selectedSeatsList">
                <span class="text-muted">No seats selected</span>
            </div>
        </div>

        <!-- Couple Mode Notice -->
        <div class="couple-mode-notice" id="coupleModeNotice">
            <i class="bi bi-exclamation-triangle"></i>
            <p><strong>Couple Seat:</strong> Please select exactly 2 adjacent seats in the same row to create a couple seat.</p>
        </div>

        <!-- Seat Type Options -->
        <div class="seat-type-options">
            <label>Select Seat Type:</label>

            @foreach($seatTypes as $type)
            <label class="seat-type-option" data-type-id="{{ $type->id }}">
                <input type="radio" name="sidebar_seat_type" value="{{ $type->id }}" {{ $loop->first ? 'checked' : '' }}>
                <div class="seat-type-icon {{ strtolower($type->name) }}"></div>
                <div class="seat-type-details">
                    <div class="seat-type-name">{{ $type->name }}</div>
                    <div class="seat-type-desc">
                        @if($type->name == 'Standard')
                            Regular seating
                        @elseif($type->name == 'VIP')
                            Premium comfort seats
                        @elseif($type->name == 'Couple')
                            Paired seats for couples
                        @else
                            {{ $type->description ?? '' }}
                        @endif
                    </div>
                </div>
            </label>
            @endforeach
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-actions">
            <button type="button" class="btn btn-secondary" id="cancelSidebarBtn">Cancel</button>
            <button type="button" class="btn btn-apply" id="applySeatTypeBtn" disabled>
                <i class="bi bi-check me-2"></i>Apply
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/admin-room-create.js') }}"></script>
@endpush
