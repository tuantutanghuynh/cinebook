/**
 * Admin Room Edit - Seat Map Editor
 *
 * Handles interactive seat map editing including:
 * - Seat selection and type modification
 * - Couple seat validation and assignment
 * - Sidebar for bulk seat type changes
 * - Keyboard shortcuts and double-click support
 * - AJAX seat price updates
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const seatSidebar = document.getElementById('seatSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const cancelSidebarBtn = document.getElementById('cancelSidebarBtn');
    const openSidebarBtn = document.getElementById('openSidebarBtn');
    const applySeatTypeBtn = document.getElementById('applySeatTypeBtn');
    const selectedSeatsList = document.getElementById('selectedSeatsList');
    const coupleModeNotice = document.getElementById('coupleModeNotice');
    const selectionModeBar = document.getElementById('selectionModeBar');
    const selectedCountText = document.getElementById('selectedCountText');
    const seatTypeOptions = document.querySelectorAll('.seat-type-option');
    const seatBtns = document.querySelectorAll('.seat-btn');

    // State
    let selectedSeats = []; // Array of {id, id2 (for couple), row, number, number2 (for couple), element, currentType, isCouple}
    let selectedSeatType = 1; // Default to Standard

    // Functions
    function openSidebar() {
        seatSidebar.classList.add('show');
        sidebarOverlay.classList.add('show');
        document.body.style.overflow = 'hidden';
        updateSidebarContent();
    }

    function closeSidebar() {
        seatSidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    function updateSelectionBar() {
        const totalSeats = selectedSeats.reduce((sum, seat) => sum + (seat.isCouple ? 2 : 1), 0);
        if (totalSeats > 0) {
            selectionModeBar.classList.add('show');
            selectedCountText.textContent = totalSeats;
        } else {
            selectionModeBar.classList.remove('show');
        }
    }

    function updateSidebarContent() {
        if (selectedSeats.length === 0) {
            selectedSeatsList.innerHTML = '<span class="text-muted">No seats selected</span>';
            applySeatTypeBtn.disabled = true;
        } else {
            selectedSeatsList.innerHTML = selectedSeats.map(seat => {
                if (seat.isCouple) {
                    return `<span class="selected-seat-tag">${seat.row}${seat.number}-${seat.number2}</span>`;
                }
                return `<span class="selected-seat-tag">${seat.row}${seat.number}</span>`;
            }).join('');

            // Check if couple type is selected
            if (selectedSeatType == 3) {
                const canCouple = checkCanCouple();
                applySeatTypeBtn.disabled = !canCouple;
                coupleModeNotice.classList.toggle('show', !canCouple);
            } else {
                applySeatTypeBtn.disabled = false;
                coupleModeNotice.classList.remove('show');
            }
        }
    }

    function checkCanCouple() {
        // Get all individual seats (expand couple selections)
        let allSeats = [];
        selectedSeats.forEach(seat => {
            if (seat.isCouple) {
                allSeats.push({ row: seat.row, number: seat.number, index: seat.index });
                allSeats.push({ row: seat.row, number: seat.number2, index: seat.index + 1 });
            } else {
                allSeats.push({ row: seat.row, number: seat.number, index: seat.index });
            }
        });

        // Must have exactly 2 seats
        if (allSeats.length !== 2) return false;

        // Same row
        if (allSeats[0].row !== allSeats[1].row) return false;

        // Adjacent
        return Math.abs(allSeats[0].index - allSeats[1].index) === 1;
    }

    function selectSeat(btn) {
        // Không cho phép chọn ghế nếu có suất chiếu tương lai
        if (window.hasFutureShowtimes) {
            return;
        }

        const seatId = btn.dataset.seatId;
        const seatId2 = btn.dataset.seatId2; // For couple seats
        const isCouple = btn.classList.contains('couple');

        const existing = selectedSeats.find(s => s.id === seatId);

        if (existing) {
            // Deselect
            selectedSeats = selectedSeats.filter(s => s.id !== seatId);
            btn.classList.remove('selected');
        } else {
            // Select
            const seatInfo = {
                id: seatId,
                id2: seatId2 || null,
                row: btn.dataset.seatRow,
                number: parseInt(btn.dataset.seatNumber),
                number2: btn.dataset.seatNumber2 ? parseInt(btn.dataset.seatNumber2) : null,
                element: btn,
                currentType: parseInt(btn.dataset.seatType),
                index: parseInt(btn.dataset.seatIndex),
                isCouple: isCouple
            };
            selectedSeats.push(seatInfo);
            btn.classList.add('selected');
        }

        updateSelectionBar();
        if (seatSidebar.classList.contains('show')) {
            updateSidebarContent();
        }
    }

    function applySeatType() {
        if (selectedSeats.length === 0) return;

        const newType = parseInt(selectedSeatType);

        // Special handling for couple type
        if (newType == 3) {
            if (!checkCanCouple()) {
                alert('Please select exactly 2 adjacent seats in the same row for couple seats.');
                return;
            }
        }

        // Collect all seat IDs to update
        let seatIdsToUpdate = [];
        selectedSeats.forEach(seat => {
            seatIdsToUpdate.push(seat.id);
            if (seat.id2) {
                seatIdsToUpdate.push(seat.id2);
            }
        });

        // Update hidden inputs
        seatIdsToUpdate.forEach(seatId => {
            const hiddenInput = document.getElementById('seat-type-input-' + seatId);
            if (hiddenInput) {
                hiddenInput.value = newType;
            }
        });

        // Clear selection and reload page to show updated layout
        // (Since changing from couple to single or vice versa requires DOM restructuring)
        clearSelection();
        closeSidebar();

        // Submit the form to save and reload
        document.getElementById('adminSeatMapForm').submit();
    }

    function clearSelection() {
        selectedSeats.forEach(seat => {
            seat.element.classList.remove('selected');
        });
        selectedSeats = [];
        updateSelectionBar();
        updateSidebarContent();
    }

    // Event Listeners

    // Seat button clicks
    seatBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            selectSeat(this);
        });
    });

    // Seat type option clicks
    seatTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
            seatTypeOptions.forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            this.querySelector('input').checked = true;
            selectedSeatType = this.dataset.typeId;
            updateSidebarContent();
        });
    });

    // Initialize first option as active
    if (seatTypeOptions.length > 0) {
        seatTypeOptions[0].classList.add('active');
    }

    // Open sidebar button
    openSidebarBtn.addEventListener('click', function() {
        if (selectedSeats.length === 0) {
            alert('Please select at least one seat first.');
            return;
        }
        openSidebar();
    });

    // Close sidebar
    closeSidebarBtn.addEventListener('click', closeSidebar);
    cancelSidebarBtn.addEventListener('click', closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);

    // Apply seat type
    applySeatTypeBtn.addEventListener('click', applySeatType);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && seatSidebar.classList.contains('show')) {
            closeSidebar();
        }
    });

    // Double-click to open sidebar
    document.getElementById('adminSeatMap').addEventListener('dblclick', function(e) {
        if (selectedSeats.length > 0) {
            openSidebar();
        }
    });

    // ========== Seat Prices AJAX Form ==========
    const seatPricesForm = document.getElementById('seatPricesForm');

    // Only initialize if the form exists (it might not exist on all pages)
    if (seatPricesForm) {
        const savePricesBtn = document.getElementById('savePricesBtn');
        const pricesSaveStatus = document.getElementById('pricesSaveStatus');

        seatPricesForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Disable button and show loading
            savePricesBtn.disabled = true;
            savePricesBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
            pricesSaveStatus.innerHTML = '';

            const formData = new FormData(seatPricesForm);
            const updatePricesUrl = seatPricesForm.dataset.updateUrl; // Get URL from data attribute

            fetch(updatePricesUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    pricesSaveStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>' + data.message + '</span>';
                } else {
                    pricesSaveStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>' + data.message + '</span>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                pricesSaveStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Failed to save prices</span>';
            })
            .finally(() => {
                // Re-enable button
                savePricesBtn.disabled = false;
                savePricesBtn.innerHTML = '<i class="bi bi-save me-2"></i>Save Prices';

                // Clear status after 3 seconds
                setTimeout(() => {
                    pricesSaveStatus.innerHTML = '';
                }, 3000);
            });
        });
    }
});
