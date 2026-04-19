/**
 * Register Form Validation
 */
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    const errorSpan = document.getElementById('passwordError');
    const submitBtn = document.getElementById('submitBtn');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    /**
     * Validate password length (minimum 6 characters)
     * Adds error class if password is too short
     */
    function validatePasswordLength() {
        if (password.value && password.value.length < 6) {
            password.classList.add('error');
            password.setCustomValidity('Password must be at least 6 characters long');
            return false;
        } else {
            password.classList.remove('error');
            password.setCustomValidity('');
            return true;
        }
    }

    /**
     * Validate password confirmation match
     * Checks if password and confirm password fields match
     */
    function validatePasswords() {
        if (confirmPassword.value && password.value !== confirmPassword.value) {
            errorSpan.style.display = 'inline';
            confirmPassword.classList.add('error');
            submitBtn.disabled = true;
        } else {
            errorSpan.style.display = 'none';
            confirmPassword.classList.remove('error');
            submitBtn.disabled = false;
        }
    }

    /**
     * Validate email format
     * Uses regex pattern to check valid email structure
     */
    function validateEmail() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailInput.value && !emailRegex.test(emailInput.value)) {
            emailInput.classList.add('error');
        } else {
            emailInput.classList.remove('error');
        }
    }

    /**
     * Validate phone number format
     * Accepts 10-11 digit phone numbers only
     */
    function validatePhone() {
        const phoneRegex = /^[0-9]{10,11}$/;
        if (phoneInput.value && !phoneRegex.test(phoneInput.value)) {
            phoneInput.classList.add('error');
        } else {
            phoneInput.classList.remove('error');
        }
    }

    // Event listeners
    password.addEventListener('input', function() {
        validatePasswordLength();
        validatePasswords();
    });
    confirmPassword.addEventListener('input', validatePasswords);
    emailInput.addEventListener('blur', validateEmail);
    phoneInput.addEventListener('blur', validatePhone);
});
