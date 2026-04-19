{{--
/**
 * Admin QR Check-in Scanner
 * 
 * QR code scanning interface including:
 * - Camera-based QR scanner
 * - Manual booking code entry
 * - Real-time booking validation
 * - Check-in status updates
 * - Attendance tracking
 */
--}}
@extends('layouts.admin')

@section('title', 'QR Check-in')

@push('styles')
@vite(['resources/css/qr_checkin.css'])
@endpush

@section('content')
<div class="container py-4">
    <h1 class="mb-4">
        <i class="bi bi-qr-code-scan"></i> QR Code Check-in
    </h1>

    <div class="row">
        <!-- Scanner Section -->
        <div class="col-md-6 mb-4">
            <div class="card qr-scanner-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-upc-scan"></i> Scan QR Code</h5>
                </div>
                <div class="card-body">
                    <!-- Camera Reader -->
                    <div id="reader" width="600px" class="mb-3"></div>

                    <!-- Manual Input -->
                    <div class="mb-3">
                        <label for="qrInput" class="form-label">Enter QR Code:</label>
                        <input type="text" id="qrInput" class="form-control form-control-lg"
                               placeholder="Enter or scan QR code..." autofocus>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button id="previewBtn" class="btn btn-info btn-lg">
                            <i class="bi bi-eye"></i> Preview
                        </button>
                        <button id="checkInBtn" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Check-in
                        </button>
                    </div>

                    <!-- Status Message -->
                    <div id="statusMessage" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Result Section -->
        <div class="col-md-6 mb-4">
            <div class="card qr-result-card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Booking Information</h5>
                </div>
                <div class="card-body" id="resultSection">
                    <p class="text-muted text-center">Scan QR code to view information</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Check-ins -->
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Check-in History</h5>
        </div>
        <div class="card-body p-0">
            <div id="recentCheckIns">
                <p class="text-muted p-3">No check-ins yet</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Pass routes to JS
    window.qrRoutes = {
        preview: '{{ route("admin.qr.preview") }}',
        checkIn: '{{ route("admin.qr.checkin") }}'
    };
</script>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="{{ asset('js/qr_checkin.js') }}"></script>
@endpush
