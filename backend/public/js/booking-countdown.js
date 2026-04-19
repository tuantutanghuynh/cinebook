/** 
 * Booking Countdown Timer Script
 * Auto redirect to payment page if not paid within 10 minutes
 * Time persists across pages using localStorage
 */

class BookingCountDown{
    constructor(options = {}) {
        // Default settings
        this.countdownElement = document.getElementById(options.elementId || 'countdown');
        this.paymentForm = options.paymentForm || null;
        this.redirectUrl = options.redirectUrl || '/';
        this.warningThreshold = options.warningThreshold || 60;
        this.storageKey = options.storageKey || 'booking_expiry_time';
        
        // Get or set expiry time
        this.expiryTime = this.getOrSetExpiryTime(options.timeLeft || 120);
        
        //Validate elements
        if (!this.countdownElement) {
            console.error('Countdown element not found');
            return;
        }
        
        // Start the countdown
        this.startCountdown();
    }
    
    /* Get existing expiry time or set new one */
    getOrSetExpiryTime(defaultSeconds) {
        const stored = localStorage.getItem(this.storageKey);
        
        console.log('🔍 Storage Key:', this.storageKey);
        console.log('🔍 Stored Value:', stored);
        console.log('🔍 Default Seconds:', defaultSeconds);
        
        if (stored) {
            // Use existing expiry time
            const expiryTime = parseInt(stored);
            const timeLeft = Math.floor((expiryTime - Date.now()) / 1000);
            console.log('⏰ Time left from storage:', timeLeft, 'seconds');
            
            // iF time has expired, create new
            if (timeLeft <= 0) {
                console.warn('⚠️ Stored time expired, creating new countdown');
                localStorage.removeItem(this.storageKey);
                const newExpiryTime = Date.now() + (defaultSeconds * 1000);
                localStorage.setItem(this.storageKey, newExpiryTime);
                return newExpiryTime;
            }
            
            return expiryTime;
        } else {
            // Set new expiry time (current time + duration)
            const expiryTime = Date.now() + (defaultSeconds * 1000);
            console.log('✅ Creating new countdown:', defaultSeconds, 'seconds');
            localStorage.setItem(this.storageKey, expiryTime);
            return expiryTime;
        }
    }
    
    /* Calculate time left */
    getTimeLeft() {
        const now = Date.now();
        const timeLeft = Math.floor((this.expiryTime - now) / 1000);
        return Math.max(0, timeLeft);
    }
    
    /* Start the countdown timer */
    startCountdown() {
        // Update immediately
        this.updateDisplay();

        // Update every second
        this.timer = setInterval(() => {
            const timeLeft = this.getTimeLeft();
            
            if (timeLeft <= 0) {
                clearInterval(this.timer);
                this.handleTimeout();
            } else {
                this.updateDisplay();
                
                // Warning style
                if (timeLeft <= this.warningThreshold && timeLeft > this.warningThreshold - 1) {
                    this.setWarningStyle();
                }
            }
        }, 1000);
    }
    
    /* Update the countdown display */
    updateDisplay() {
        if (!this.countdownElement) return;
        
        const timeLeft = this.getTimeLeft();
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;

        const formattedTime = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        this.countdownElement.textContent = formattedTime;
    }
    
    /* Warning style */
    setWarningStyle() {
        if (this.countdownElement) {
            this.countdownElement.style.color = '#ff3333';
            this.countdownElement.style.fontWeight = 'bold';
        }
    }
    
    /* Handle expiration */
    handleTimeout() {
        if (this.timer) {
            clearInterval(this.timer);
        }
        
        if (this.countdownElement) {
            this.countdownElement.textContent = '00:00';
        }
        
        // Clear storage
        localStorage.removeItem(this.storageKey);
        
        // Cancel reserved seats before redirecting
        if (typeof bookingData !== 'undefined') {
            fetch('/booking/cancel-reserved', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(bookingData)
            }).finally(() => {
                // Always redirect after canceling (or trying to)
                alert('Booking time has expired. Reserved seats released.');
                setTimeout(() => {
                    window.location.href = this.redirectUrl;
                }, 2000);
            });
        } else {
            alert('Booking time has expired. You will be redirected.');
            setTimeout(() => {
                window.location.href = this.redirectUrl;
            }, 2000);
        }
    }
    
    /* Clear countdown on successful payment */
    static clearCountdown(storageKey = 'booking_expiry_time') {
        localStorage.removeItem(storageKey);
    }
}

//Auto init on DOM load
document.addEventListener('DOMContentLoaded', () => {
    const countdown = document.getElementById('countdown');
    const paymentForm = document.getElementById('payment-form');

    if (countdown) {
        //Read atrributes for customization
        //Read showtime ID if needed
        const showtimeId = countdown.getAttribute('data-showtime-id');
        //Read seats ID
        const seatsId = countdown.getAttribute('data-seats-id');
        //Read countdown time if specified, if not default 120 seconds
        const timeLeft = parseInt(countdown.getAttribute('data-timeleft')) || 120;

        //Create unique key for storage 
        let storageKey = 'booking_expiry_time';
        if (showtimeId && seatsId) {
            storageKey = `booking_expiry_time_${showtimeId}_${seatsId}`;
        }
        new BookingCountDown({
            elementId: 'countdown',
            paymentForm: paymentForm,
            timeLeft: timeLeft,
            storageKey: storageKey,
            redirectUrl: '/',
            warningThreshold: 30
        });
    }
});