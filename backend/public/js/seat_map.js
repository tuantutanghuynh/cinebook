/**
 * Seat Map JavaScript - UI/UX Only
 * Handles seat selection interactions and visual feedback
 * Price calculation is for display purposes only (not trusted by server)
 */

document.addEventListener("DOMContentLoaded", function () {
    const seatButtons = document.querySelectorAll(".seat-btn.available");
    const selectedSeatIds = document.getElementById("selectedSeatIds");
    const seatList = document.getElementById("seatList");
    const bookBtn = document.getElementById("bookBtn");
    let selectedSeats = [];
    const ESTIMATED_PRICES = {
        1: 50000,
        2: 60000,
        3: 80000,
    };
    seatButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const seatType = parseInt(this.getAttribute("data-seat-type"));
            if (seatType === 3) {
                handleCoupleSeatButton(this);
            } else {
                handleRegularSeatButton(this);
            }
            updateSelectedSeatsDisplay();
        });
    });
    function handleCoupleSeatButton(button) {
        const seatId1 = button.getAttribute("data-seat-id");
        const seatId2 = button.getAttribute("data-seat-id2");
        const seatCode1 = button.getAttribute("data-seat-code");
        const seatCode2 = button.getAttribute("data-seat-code2");
        // if both seats are selected, deselect both; else select both
        if (
            selectedSeats.find((seat) => seat.id === seatId1) &&
            selectedSeats.find((seat) => seat.id === seatId2)
        ) {
            deselectSeat(button, seatId1);
            deselectSeat(button, seatId2);
        } else {
            selectSeat(button, seatId1, seatCode1, 3);
            selectSeat(button, seatId2, seatCode2, 3);
        }
    }
    function handleRegularSeatButton(button) {
        const seatId = button.getAttribute("data-seat-id");
        const seatCode = button.getAttribute("data-seat-code");
        const seatType = parseInt(button.getAttribute("data-seat-type"));
        if (selectedSeats.find((seat) => seat.id === seatId)) {
            deselectSeat(button, seatId);
        } else {
            selectSeat(button, seatId, seatCode, seatType);
        }
    }
    function selectSeat(button, seatId, seatCode, seatType) {
        button.classList.add("selected");
        if (!selectedSeats.find((seat) => seat.id === seatId)) {
            selectedSeats.push({
                id: seatId,
                code: seatCode,
                type: seatType,
                estimatedPrice:
                    ESTIMATED_PRICES[seatType] || ESTIMATED_PRICES[1],
            });
        }
    }
    function deselectSeat(button, seatId) {
        selectedSeats = selectedSeats.filter((seat) => seat.id !== seatId);
        button.classList.remove("selected");
    }
    function updateSelectedSeatsDisplay() {
        if (selectedSeats.length === 0) {
            seatList.textContent = "None";
            selectedSeatIds.value = "";
            bookBtn.disabled = true;
        } else {
            const seatCodes = selectedSeats.map((seat) => seat.code).join(", ");
            seatList.textContent = seatCodes;
            const seatIds = selectedSeats.map((seat) => seat.id);
            selectedSeatIds.value = JSON.stringify(seatIds);
            bookBtn.disabled = false;
        }
    }
});
