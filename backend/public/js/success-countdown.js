let countdown = 10;
const countdownDisplay = document.getElementById('countdown');
const secondsDisplay = document.getElementById('seconds');
const paymentForm = document.getElementById('paymentForm');

const timer = setInterval(() => {
    countdown--;
    countdownDisplay.textContent = countdown;
    secondsDisplay.textContent = countdown;
    
    if (countdown <= 0) {
        clearInterval(timer);
        // Auto-submit form after 10 seconds
        paymentForm.submit();
    }
}, 1000);