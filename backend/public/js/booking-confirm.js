/**
 * Booking Confirmation Page Script
 * Handles cancel and go back functionality
 */

// Cancel reserved seats and go back to seat selection
function cancelAndGoBack(bookingData, cancelRoute, csrfToken, seatMapRoute) {
    fetch(cancelRoute, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(bookingData)
    })
    .then(() => {
        // Clear countdown timer
        localStorage.removeItem('booking_expiry_time');
        // Redirect to seat selection
        window.location.href = seatMapRoute;
    })
    .catch(err => {
        console.error('Error canceling seats:', err);
        // Still redirect even if error
        window.location.href = seatMapRoute;
    });
}

// Handle browser back button - try to cancel seats
function handleBeforeUnload(bookingData, cancelRoute) {
    window.addEventListener('beforeunload', function(e) {
        // Try to cancel seats (may not work due to browser restrictions)
        navigator.sendBeacon(cancelRoute, new URLSearchParams(bookingData));
    });
}

// Initialize booking confirmation page
function initBookingConfirm(config) {
    // Expose cancel function to global scope
    window.cancelAndGoBackHandler = function() {
        cancelAndGoBack(config.bookingData, config.cancelRoute, config.csrfToken, config.seatMapRoute);
    };
    
    // Handle page unload
    handleBeforeUnload(config.bookingData, config.cancelRoute);
}
