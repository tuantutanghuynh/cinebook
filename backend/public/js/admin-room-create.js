/**
 * Admin Room Create - Seat Map Configuration
 *
 * Handles interactive seat map configuration including:
 * - Preview generation with custom dimensions
 * - Seat type assignment (Standard, VIP, Couple)
 * - Template application (Cinema-style, VIP Center, Couple Back)
 * - Interactive seat selection and editing
 * - Sidebar for seat type changes
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const totalRowsInput = document.getElementById('total_rows');
    const seatsPerRowInput = document.getElementById('seats_per_row');
    const generatePreviewBtn = document.getElementById('generatePreviewBtn');
    const previewSeatMap = document.getElementById('previewSeatMap');
    const seatConfigInputs = document.getElementById('seatConfigInputs');
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
    const templateBtns = document.querySelectorAll('.template-btn');

    // State
    let seatData = []; // Array of {row, number, type}
    let selectedSeats = []; // Array of {row, number, index, isCouple, number2}
    let selectedSeatType = 1; // Default to Standard
    let previewGenerated = false;

    const rowLabels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');

    // Functions
    function generatePreview() {
        const totalRows = parseInt(totalRowsInput.value) || 0;
        const seatsPerRow = parseInt(seatsPerRowInput.value) || 0;

        if (totalRows < 1 || seatsPerRow < 1) {
            alert('Please enter valid row and seat numbers.');
            return;
        }

        // Initialize seat data with default type (Standard = 1)
        seatData = [];
        for (let r = 0; r < totalRows; r++) {
            for (let s = 1; s <= seatsPerRow; s++) {
                seatData.push({
                    row: rowLabels[r],
                    number: s,
                    type: 1 // Standard
                });
            }
        }

        renderSeatMap();
        previewGenerated = true;
        updateHiddenInputs();
    }

    function renderSeatMap() {
        const totalRows = parseInt(totalRowsInput.value) || 0;
        const seatsPerRow = parseInt(seatsPerRowInput.value) || 0;

        if (seatData.length === 0) {
            previewSeatMap.innerHTML = `
                <div class="empty-preview">
                    <i class="bi bi-grid-1x2"></i>
                    <p>Enter room dimensions and click "Generate Preview" to see the seat layout</p>
                </div>
            `;
            return;
        }

        let html = '';

        for (let r = 0; r < totalRows; r++) {
            const rowLabel = rowLabels[r];
            html += `<div class="seat-row"><div class="seat-row-label">Row ${rowLabel}:</div><div class="seats-container">`;

            let s = 0;
            while (s < seatsPerRow) {
                const seatIndex = r * seatsPerRow + s;
                const seat = seatData[seatIndex];

                if (!seat) {
                    s++;
                    continue;
                }

                // Check if this and next seat are both couple type
                const nextSeat = seatData[seatIndex + 1];
                if (seat.type === 3 && nextSeat && nextSeat.type === 3 && nextSeat.row === seat.row) {
                    // Render as couple (wide button)
                    html += `
                        <button type="button" class="seat-btn couple seat-type-3"
                            data-row="${seat.row}" data-number="${seat.number}" data-number2="${nextSeat.number}"
                            data-type="3" data-index="${seatIndex}" data-is-couple="true">
                            ${seat.number}-${nextSeat.number}
                        </button>
                    `;
                    s += 2;
                } else {
                    // Render as single seat
                    html += `
                        <button type="button" class="seat-btn seat-type-${seat.type}"
                            data-row="${seat.row}" data-number="${seat.number}" data-type="${seat.type}" data-index="${seatIndex}">
                            ${seat.number}
                        </button>
                    `;
                    s++;
                }
            }

            html += '</div></div>';
        }

        previewSeatMap.innerHTML = html;

        // Re-bind click events
        bindSeatClicks();
    }

    function bindSeatClicks() {
        const seatBtns = previewSeatMap.querySelectorAll('.seat-btn');

        seatBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                selectSeat(this);
            });
        });
    }

    function updateHiddenInputs() {
        let html = '';
        seatData.forEach((seat, index) => {
            html += `<input type="hidden" name="seat_configs[${index}][row]" value="${seat.row}">`;
            html += `<input type="hidden" name="seat_configs[${index}][number]" value="${seat.number}">`;
            html += `<input type="hidden" name="seat_configs[${index}][type]" value="${seat.type}">`;
        });
        seatConfigInputs.innerHTML = html;
    }

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
            openSidebarBtn.disabled = false;
        } else {
            selectionModeBar.classList.remove('show');
            openSidebarBtn.disabled = true;
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
        const row = btn.dataset.row;
        const number = parseInt(btn.dataset.number);
        const number2 = btn.dataset.number2 ? parseInt(btn.dataset.number2) : null;
        const index = parseInt(btn.dataset.index);
        const isCouple = btn.dataset.isCouple === 'true';

        const existing = selectedSeats.find(s => s.row === row && s.number === number);

        if (existing) {
            selectedSeats = selectedSeats.filter(s => !(s.row === row && s.number === number));
            btn.classList.remove('selected');
        } else {
            selectedSeats.push({
                row: row,
                number: number,
                number2: number2,
                index: index,
                element: btn,
                currentType: parseInt(btn.dataset.type),
                isCouple: isCouple
            });
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

        if (newType === 3 && !checkCanCouple()) {
            alert('Please select exactly 2 adjacent seats in the same row for couple seats.');
            return;
        }

        // Update seat data
        selectedSeats.forEach(seat => {
            if (seat.isCouple) {
                // Update both seats in couple
                seatData[seat.index].type = newType;
                seatData[seat.index + 1].type = newType;
            } else {
                seatData[seat.index].type = newType;
            }
        });

        // If setting to couple with 2 single seats, need to update both
        if (newType === 3 && selectedSeats.length === 2 && !selectedSeats[0].isCouple && !selectedSeats[1].isCouple) {
            // Both are already updated above
        }

        // Clear selection and re-render
        clearSelection();
        renderSeatMap();
        updateHiddenInputs();
        closeSidebar();
    }

    function clearSelection() {
        selectedSeats.forEach(seat => {
            if (seat.element) {
                seat.element.classList.remove('selected');
            }
        });
        selectedSeats = [];
        updateSelectionBar();
        updateSidebarContent();
    }

    // Track active templates
    let activeTemplates = new Set();

    // Templates - VIP Center and Couple Back can be combined
    function applyTemplate(template) {
        if (!previewGenerated || seatData.length === 0) {
            alert('Please generate preview first.');
            return;
        }

        const totalRows = parseInt(totalRowsInput.value);
        const seatsPerRow = parseInt(seatsPerRowInput.value);

        // Handle template toggle logic
        if (template === 'all-standard' || template === 'cinema-style') {
            // These reset everything
            activeTemplates.clear();
            if (template === 'cinema-style') {
                activeTemplates.add('cinema-style');
            }
        } else if (template === 'vip-center' || template === 'couple-back') {
            // These can be combined - toggle on/off
            if (activeTemplates.has(template)) {
                activeTemplates.delete(template);
            } else {
                // Remove cinema-style if adding individual templates
                activeTemplates.delete('cinema-style');
                activeTemplates.add(template);
            }
        }

        // Reset all to standard first
        seatData.forEach(seat => seat.type = 1);

        // Apply active templates
        if (activeTemplates.has('cinema-style')) {
            // Cinema style: Standard front, VIP middle, Couple back
            const frontRows = Math.floor(totalRows / 3);
            const middleRows = Math.ceil(totalRows * 2 / 3);

            seatData.forEach((seat, index) => {
                const rowIndex = rowLabels.indexOf(seat.row);

                if (rowIndex >= frontRows && rowIndex < middleRows) {
                    // Middle rows - VIP for center seats
                    const vipStart = Math.floor(seatsPerRow / 4);
                    const vipEnd = Math.ceil(seatsPerRow * 3 / 4);
                    if (seat.number > vipStart && seat.number <= vipEnd) {
                        seat.type = 2;
                    }
                } else if (rowIndex >= middleRows) {
                    // Back rows - Couple
                    if (seat.number % 2 === 1 && seat.number < seatsPerRow) {
                        seat.type = 3;
                        if (seatData[index + 1] && seatData[index + 1].row === seat.row) {
                            seatData[index + 1].type = 3;
                        }
                    }
                }
            });
        } else {
            // Apply VIP Center if active
            if (activeTemplates.has('vip-center')) {
                const vipStartRow = Math.floor(totalRows / 3);
                const vipEndRow = Math.ceil(totalRows * 2 / 3) - (activeTemplates.has('couple-back') ? 1 : 0);
                const vipStartSeat = Math.floor(seatsPerRow / 4);
                const vipEndSeat = Math.ceil(seatsPerRow * 3 / 4);

                seatData.forEach(seat => {
                    const rowIndex = rowLabels.indexOf(seat.row);
                    if (rowIndex >= vipStartRow && rowIndex < vipEndRow &&
                        seat.number > vipStartSeat && seat.number <= vipEndSeat) {
                        seat.type = 2; // VIP
                    }
                });
            }

            // Apply Couple Back if active (last row only)
            if (activeTemplates.has('couple-back')) {
                const lastRowIndex = totalRows - 1;
                seatData.forEach((seat, index) => {
                    const rowIndex = rowLabels.indexOf(seat.row);
                    if (rowIndex === lastRowIndex) {
                        // Make pairs (1-2, 3-4, etc.)
                        if (seat.number % 2 === 1 && seat.number < seatsPerRow) {
                            seat.type = 3; // Couple
                            if (seatData[index + 1] && seatData[index + 1].row === seat.row) {
                                seatData[index + 1].type = 3;
                            }
                        } else if (seat.number % 2 === 0 && seat.number === seatsPerRow) {
                            // Last seat if odd number - keep as couple pair
                            seat.type = 3;
                        }
                    }
                });
            }
        }

        // Update button states
        updateTemplateButtons();
        renderSeatMap();
        updateHiddenInputs();
        clearSelection();
    }

    function updateTemplateButtons() {
        templateBtns.forEach(btn => {
            const template = btn.dataset.template;
            if (activeTemplates.has(template)) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    // Event Listeners
    generatePreviewBtn.addEventListener('click', generatePreview);

    // Auto-generate on input change
    [totalRowsInput, seatsPerRowInput].forEach(input => {
        input.addEventListener('change', function() {
            if (previewGenerated) {
                generatePreview();
            }
        });
    });

    // Template buttons
    templateBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            applyTemplate(this.dataset.template);
        });
    });

    // Seat type options
    seatTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
            seatTypeOptions.forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            this.querySelector('input').checked = true;
            selectedSeatType = this.dataset.typeId;
            updateSidebarContent();
        });
    });

    if (seatTypeOptions.length > 0) {
        seatTypeOptions[0].classList.add('active');
    }

    // Sidebar controls
    openSidebarBtn.addEventListener('click', function() {
        if (selectedSeats.length === 0) {
            alert('Please select at least one seat first.');
            return;
        }
        openSidebar();
    });

    closeSidebarBtn.addEventListener('click', closeSidebar);
    cancelSidebarBtn.addEventListener('click', closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);
    applySeatTypeBtn.addEventListener('click', applySeatType);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && seatSidebar.classList.contains('show')) {
            closeSidebar();
        }
    });

    // Double-click to open sidebar
    previewSeatMap.addEventListener('dblclick', function(e) {
        if (selectedSeats.length > 0) {
            openSidebar();
        }
    });
});
