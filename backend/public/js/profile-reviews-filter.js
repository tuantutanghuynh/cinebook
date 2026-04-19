/**
 * Profile Reviews Filter Validation
 *
 * Client-side validation for review filtering functionality including:
 * - Date range validation (from date cannot be after to date)
 * - Real-time form validation feedback
 * - Error styling and custom validity messages
 * - Form submission handling with validation checks
 * - Input event listeners for immediate feedback
 */

/**
 * Handles date range validation and form submission
 */

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("filterForm");
    const dateFromInput = document.getElementById("date_from");
    const dateToInput = document.getElementById("date_to");

    // Date validation function
    function validateDates() {
        const fromDate = dateFromInput.value;
        const toDate = dateToInput.value;

        if (fromDate && toDate) {
            const from = new Date(fromDate);
            const to = new Date(toDate);

            if (from > to) {
                dateToInput.setCustomValidity(
                    "To Date must be after or equal to From Date",
                );
                dateToInput.classList.add("error");
                return false;
            } else {
                dateToInput.setCustomValidity("");
                dateToInput.classList.remove("error");
                return true;
            }
        }

        dateToInput.setCustomValidity("");
        dateToInput.classList.remove("error");
        return true;
    }

    // Add event listeners for date validation
    if (dateFromInput && dateToInput) {
        dateFromInput.addEventListener("change", validateDates);
        dateToInput.addEventListener("change", validateDates);
    }

    // Form submission validation
    if (form) {
        form.addEventListener("submit", function (e) {
            if (!validateDates()) {
                e.preventDefault();
                alert(
                    "Please check your date range. To Date must be after or equal to From Date.",
                );
            }
        });
    }

    // Initialize validation on page load
    validateDates();
});
