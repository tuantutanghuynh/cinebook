{{--
/**
 * Admin Room Edit
 * 
 * Room editing interface including:
 * - Pre-filled room information
 * - Seat layout modifications
 * - Screen type updates
 * - Capacity adjustments
 * - Active showtime impact warnings
 */
--}}
@extends('layouts.admin')

@section('title', 'Edit Room & Seats')

@push('styles')
@vite(['resources/css/admin-room-edit.css'])
@endpush

@section('content')
<div class="admin-room-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Edit Room: {{ $room->name }}</h2>
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Rooms
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Room Info Form -->
    <div class="room-info-card">
        <h4><i class="bi bi-info-circle me-2"></i>Room Information</h4>
        <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Room Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $room->name }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="screen_type_id" class="form-label">Screen Type</label>
                    <select class="form-select" id="screen_type_id" name="screen_type_id" required>
                        @foreach($screenTypes as $type)
                            <option value="{{ $type->id }}" @if($room->screen_type_id == $type->id) selected @endif>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i>Update Room Info
            </button>
        </form>
    </div>

    <!-- Seat Prices Section has been moved to a global system section -->

    <!-- Seat Map Section -->
    <div class="seat-map-section">
        <h4><i class="bi bi-grid-3x3 me-2"></i>Seat Map Configuration</h4>

        @if($hasFutureShowtimes)
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Không thể chỉnh sửa loại ghế!</strong> Phòng này đang có suất chiếu trong tương lai.
            Vui lòng hủy tất cả suất chiếu trước khi thay đổi cấu hình ghế.
        </div>
        @else
        <p class="section-subtitle">Click on seats to select them, then choose a seat type from the sidebar. You can select multiple seats at once.</p>
        @endif

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

        <!-- Seat Map Form -->
        <form action="{{ route('admin.rooms.update-seats', $room->id) }}" method="POST" id="adminSeatMapForm">
            @csrf
            <div class="seat-map-wrapper">
                <div id="adminSeatMap">
                    @foreach($seatsByRow as $row => $seats)
                    <div class="seat-row">
                        <div class="seat-row-label">Row {{ $row }}:</div>
                        <div class="seats-container">
                            @php $i = 0; @endphp
                            @while($i < count($seats))
                                @php $seat = $seats[$i]; @endphp
                                @if($seat->seat_type_id == 3 && isset($seats[$i+1]) && $seats[$i+1]->seat_type_id == 3)
                                    {{-- Couple seat - render as single wide button --}}
                                    @php $seat2 = $seats[$i+1]; @endphp
                                    <button type="button"
                                        class="seat-btn couple seat-type-3"
                                        data-seat-id="{{ $seat->id }}"
                                        data-seat-id2="{{ $seat2->id }}"
                                        data-seat-type="3"
                                        data-seat-row="{{ $row }}"
                                        data-seat-number="{{ $seat->seat_number }}"
                                        data-seat-number2="{{ $seat2->seat_number }}"
                                        data-seat-index="{{ $i }}">
                                        {{ $seat->seat_number }}-{{ $seat2->seat_number }}
                                    </button>
                                    <input type="hidden" name="seats[{{ $seat->id }}][seat_id]" value="{{ $seat->id }}">
                                    <input type="hidden" name="seats[{{ $seat->id }}][seat_type_id]" value="{{ $seat->seat_type_id }}" id="seat-type-input-{{ $seat->id }}">
                                    <input type="hidden" name="seats[{{ $seat2->id }}][seat_id]" value="{{ $seat2->id }}">
                                    <input type="hidden" name="seats[{{ $seat2->id }}][seat_type_id]" value="{{ $seat2->seat_type_id }}" id="seat-type-input-{{ $seat2->id }}">
                                    @php $i += 2; @endphp
                                @else
                                    {{-- Regular seat --}}
                                    <button type="button"
                                        class="seat-btn seat-type-{{ $seat->seat_type_id }}"
                                        data-seat-id="{{ $seat->id }}"
                                        data-seat-type="{{ $seat->seat_type_id }}"
                                        data-seat-row="{{ $row }}"
                                        data-seat-number="{{ $seat->seat_number }}"
                                        data-seat-index="{{ $i }}">
                                        {{ $seat->seat_number }}
                                    </button>
                                    <input type="hidden" name="seats[{{ $seat->id }}][seat_id]" value="{{ $seat->id }}">
                                    <input type="hidden" name="seats[{{ $seat->id }}][seat_type_id]" value="{{ $seat->seat_type_id }}" id="seat-type-input-{{ $seat->id }}">
                                    @php $i++; @endphp
                                @endif
                            @endwhile
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 d-flex gap-3">
                <button type="submit" class="btn btn-success btn-lg" {{ $hasFutureShowtimes ? 'disabled' : '' }}>
                    <i class="bi bi-save me-2"></i>Save All Changes
                </button>
                <button type="button" class="btn btn-outline-primary" id="openSidebarBtn" {{ $hasFutureShowtimes ? 'disabled' : '' }}>
                    <i class="bi bi-pencil me-2"></i>Edit Selected Seats
                </button>
            </div>
        </form>
    </div>
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
<script>
    // Biến kiểm tra có suất chiếu tương lai hay không
    window.hasFutureShowtimes = {{ $hasFutureShowtimes ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/admin-room-edit.js') }}"></script>
<script>
// Pass dynamic data to the JavaScript file
if (document.getElementById('seatPricesForm')) {
    document.getElementById('seatPricesForm').dataset.updateUrl = '{{ route("admin.rooms.update-prices", $room->id) }}';
}
</script>
@endpush
