// QR Check-in System
(function() {
    'use strict';

    const qrInput = document.getElementById('qrInput');
    const previewBtn = document.getElementById('previewBtn');
    const checkInBtn = document.getElementById('checkInBtn');
    const statusMessage = document.getElementById('statusMessage');
    const resultSection = document.getElementById('resultSection');
    const recentCheckIns = document.getElementById('recentCheckIns');

    let recentList = [];

    // Auto focus on input when page loads
    if (qrInput) {
        qrInput.focus();
    }

    // Preview QR Code
    if (previewBtn) {
        previewBtn.addEventListener('click', async () => {
            const qrCode = qrInput.value.trim();
            if (!qrCode) {
                showMessage('Vui lòng nhập mã QR', 'error');
                return;
            }

            try {
                const response = await fetch(window.qrRoutes.preview, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ qr_code: qrCode })
                });

                const data = await response.json();

                if (data.success) {
                    displayBookingInfo(data.data, 'preview');
                    showMessage('', 'clear');
                } else {
                    showMessage(data.message, 'error');
                    clearResult();
                }
            } catch (error) {
                showMessage('Lỗi kết nối: ' + error.message, 'error');
            }
        });
    }

    // Check-in
    if (checkInBtn) {
        checkInBtn.addEventListener('click', async () => {
            const qrCode = qrInput.value.trim();
            if (!qrCode) {
                showMessage('Vui lòng nhập mã QR', 'error');
                return;
            }

            if (!confirm('Xác nhận check-in?')) {
                return;
            }

            try {
                const response = await fetch(window.qrRoutes.checkIn, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ qr_code: qrCode })
                });

                const data = await response.json();

                if (data.success) {
                    displayBookingInfo(data.data, 'checked');
                    showMessage(data.message, 'success');
                    addToRecentList(data.data);
                    qrInput.value = '';
                    qrInput.focus();
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                showMessage('Lỗi kết nối: ' + error.message, 'error');
            }
        });
    }

    // Enter key to check-in
    if (qrInput) {
        qrInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                checkInBtn.click();
            }
        });
    }

    function displayBookingInfo(data, status) {
        const statusBadge = status === 'checked' 
            ? '<span class="badge bg-success">✓ Đã check-in</span>'
            : status === 'preview' && data.qr_status === 'checked'
            ? '<span class="badge bg-secondary">Đã check-in trước đó</span>'
            : '<span class="badge bg-warning text-dark">Chưa check-in</span>';

        resultSection.innerHTML = `
            <div class="booking-info">
                <p><strong>Booking ID:</strong> #${data.booking_id} ${statusBadge}</p>
                <p><strong>Khách hàng:</strong> ${data.customer_name}</p>
                <p><strong>Phim:</strong> ${data.movie_title}</p>
                <p><strong>Suất chiếu:</strong> ${data.show_date} - ${data.show_time}</p>
                <p><strong>Ghế:</strong><br>
                    ${data.seats.map(seat => `<span class="seat-badge">${seat}</span>`).join('')}
                </p>
                ${data.checked_at ? `<p><strong>Thời gian check-in:</strong> ${data.checked_at}</p>` : ''}
            </div>
        `;
    }

    function showMessage(message, type) {
        if (type === 'clear') {
            statusMessage.innerHTML = '';
            return;
        }
        const className = type === 'success' ? 'status-success' : type === 'error' ? 'status-error' : 'status-info';
        statusMessage.innerHTML = `<div class="${className}">${message}</div>`;
    }

    function clearResult() {
        resultSection.innerHTML = '<p class="text-muted text-center">Quét mã QR để xem thông tin</p>';
    }

    function addToRecentList(data) {
        recentList.unshift(data);
        if (recentList.length > 10) recentList.pop();

        recentCheckIns.innerHTML = recentList.map(item => `
            <div class="recent-checkin-item px-3">
                <small>
                    <strong>#${item.booking_id}</strong> - ${item.customer_name} - 
                    ${item.movie_title} - 
                    Ghế: ${item.seats.join(', ')} -
                    <span class="text-success">${item.checked_at}</span>
                </small>
            </div>
        `).join('');
    }

    // Initialize QR Code Scanner
    if (document.getElementById('reader')) {
        function onScanSuccess(decodedText, decodedResult) {
            // console.log(`Code matched = ${decodedText}`, decodedResult);
            
            if(qrInput && qrInput.value !== decodedText) {
                qrInput.value = decodedText;
                
                // Play a beep sound if desired, or just trigger preview
                if(previewBtn) {
                    previewBtn.click();
                }
            }
        }

        function onScanFailure(error) {
            // console.warn(`Code scan error = ${error}`);
        }

        // Check if library is loaded
        if (typeof Html5QrcodeScanner !== 'undefined') {
            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: {width: 250, height: 250} },
                /* verbose= */ false);
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        } else {
            console.error('Html5QrcodeScanner library not found');
        }
    }
})();
