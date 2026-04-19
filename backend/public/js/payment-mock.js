/**
 * Payment Mock Page Script
 * Handles auto-submit and cancel booking functionality
 */

// Cancel booking function
function cancelBooking(bookingId, cancelRoute, csrfToken, showtimeId, seatmapRoute) {
    if (!confirm('Are you sure you want to cancel this booking?')) {
        return;
    }

    fetch(cancelRoute, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            booking_id: bookingId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear countdown timer
            localStorage.removeItem('booking_expiry_time');
            // Redirect to seatmap with success message
            const url = seatmapRoute.replace('SHOWTIME_ID', showtimeId);
            window.location.href = url + '?cancel_success=1';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Error canceling booking:', err);
        alert('Error canceling booking. Please try again.');
    });
}

// Auto-submit payment form after delay
function autoSubmitPayment(formId, delayMs) {
    setTimeout(function() {
        const form = document.getElementById(formId);
        if (form) {
            form.submit();
        }
    }, delayMs);
}

// Initialize payment mock page
function initPaymentMock(config) {
    // Auto-submit after specified delay
    autoSubmitPayment(config.formId, config.autoSubmitDelay);
    
    // Expose cancel function to global scope
    window.cancelBookingHandler = function() {
        cancelBooking(config.bookingId, config.cancelRoute, config.csrfToken, config.showtimeId, config.seatmapRoute);
    };
}
