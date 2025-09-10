document.addEventListener('DOMContentLoaded', function () {
    // Sidebar tabs
    const ongoingTab = document.getElementById('ongoing-tab');
    const pastTab = document.getElementById('past-tab');
    const refundTab = document.getElementById('refunded-tab');
    const ongoingContent = document.getElementById('ongoing-content');
    const pastContent = document.getElementById('past-content');
    const refundContent = document.getElementById('refunded-content');

    // Verify elements exist
    if (!ongoingTab || !pastTab || !refundTab || !ongoingContent || !pastContent || !refundContent) {
        console.error('One or more tab or content elements not found.');
        return;
    }

    // Tab switching
    ongoingTab.addEventListener('click', function () {
        console.log('Ongoing tab clicked');
        ongoingTab.classList.add('active');
        pastTab.classList.remove('active');
        refundTab.classList.remove('active');
        ongoingContent.style.display = '';
        pastContent.style.display = 'none';
        refundContent.style.display = 'none';
    });

    pastTab.addEventListener('click', function () {
        console.log('Past tab clicked');
        pastTab.classList.add('active');
        ongoingTab.classList.remove('active');
        refundTab.classList.remove('active');
        pastContent.style.display = '';
        ongoingContent.style.display = 'none';
        refundContent.style.display = 'none';
    });

    refundTab.addEventListener('click', function () {
        console.log('Refunded tab clicked');
        refundTab.classList.add('active');
        ongoingTab.classList.remove('active');
        pastTab.classList.remove('active');
        refundContent.style.display = '';
        ongoingContent.style.display = 'none';
        pastContent.style.display = 'none';
    });

    // ===== API Fetch for bookings =====
    const userId = document.body.getAttribute('data-user-id');
    const apiUrl = `/api/bookings/${userId}`;

    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);
            renderBookings('ongoing-list', data.ongoing, 'ongoing');
            renderBookings('past-list', data.past, 'past');
            renderBookings('refunded-list', data.refunded, 'refunded');
        })
        .catch(err => {
            console.error('Error fetching bookings:', err);
            document.getElementById('ongoing-list').innerHTML = '<p>Failed to load bookings.</p>';
            document.getElementById('past-list').innerHTML = '<p>Failed to load bookings.</p>';
            document.getElementById('refunded-list').innerHTML = '<p>Failed to load bookings.</p>';
        });

    // ===== Render Bookings =====
    function renderBookings(containerId, bookings, type) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container ${containerId} not found.`);
            return;
        }

        if (!bookings || !Array.isArray(bookings) || bookings.length === 0) {
            container.innerHTML = `<p>No ${type} bookings found.</p>`;
            return;
        }

        container.innerHTML = bookings.map(booking => {
            let buttons = '';

            if (type === 'ongoing') {
                if (booking.Status === 'Booked') {
                    buttons += `<a href="/booking_detail/${booking.BookingID}">
                                    <button type="button" class="btn-view">View QR Code</button>
                                </a>`;
                    buttons += `<a href="/refund/${booking.BookingID}">
                                    <button type="button" class="btn-refund">Refund</button>
                                </a>`;
                }
                if (booking.Status === 'Pending') {
                    buttons += `<a href="/proceedPayment/${booking.BookingID}">
                                    <button type="button" class="btn-payment">Proceed Payment</button>
                                </a>`;
                    buttons += `<button type="button" class="btn-cancel" onclick="confirmCancel('${booking.BookingID}')">Cancel</button>`;
                }
            }

            if (type === 'past' && booking.Status === 'Completed') {
                buttons += `<a href="/bookingdetail/${booking.BookingID}">
                                <button type="button" class="btn-view">View QR Code</button>
                            </a>`;
                buttons += `<a href="/rateTrip/${booking.BookingID}">
                                <button type="button" class="btn-rate">Rate Trip</button>
                            </a>`;
            }

            return `
                <div class="booking-item">
                    <div class="booking-flex-row">
                        <div class="booking-col booking-col-left">
                            <img src="/images/logo/${booking.journey?.train?.TrainService ?? 'default'}_logo.png" 
                                 alt="service_type" class="booking-logo">
                            <div class="train-number">${booking.journey?.train?.TrainNo ?? 'Unknown'}</div>
                            <div class="booking-id">Booking ID: ${booking.BookingID}</div>
                        </div>
                        <div class="booking-col booking-col-middle">
                            <div class="route-row dashed-line">
                                <span class="station">${booking.journey?.FromLocation ?? 'Unknown'}</span>
                                <span class="train-icon center-icon"><i class="fas fa-train"></i></span>
                                <span class="station">${booking.journey?.ToLocation ?? 'Unknown'}</span>
                            </div>
                            <div class="time-row dashed-line">
                                <span class="time">${formatTime(booking.journey?.DepartureTime)}</span>
                                <span class="train-icon center-icon"><i class="fas fa-train"></i></span>
                                <span class="time">${formatTime(booking.journey?.ArrivalTime)}</span>
                            </div>
                            <div class="info-row">
                                <span class="date">Date: ${formatDate(booking.journey?.DepartureTime)}</span>
                            </div>
                            <div class="status-row">
                                <span class="status ${type === 'refunded' ? 'refunded' : ''}">Status: ${booking.Status}</span>
                            </div>
                        </div>
                        <div class="booking-col booking-col-right booking-col-height">
                            ${buttons}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function formatTime(time) {
        if (!time) return 'Unknown';
        try {
            return new Date(time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } catch (e) {
            console.error('Error formatting time:', time, e);
            return 'Unknown';
        }
    }

    function formatDate(date) {
        if (!date) return 'Unknown';
        try {
            return new Date(date).toLocaleDateString([], { day: '2-digit', month: 'long', year: 'numeric' });
        } catch (e) {
            console.error('Error formatting date:', date, e);
            return 'Unknown';
        }
    }
});

// ===== Confirm Cancel (API version) =====
function confirmCancel(bookingId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to cancel this booking? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/booking/cancel/${bookingId}/${document.body.getAttribute('data-user-id')}`, {
                method: 'PATCH'
            })
            .then(res => res.json())
            .then(data => {
                if (data.message) {
                    Swal.fire('Cancelled!', data.message, 'success')
                        .then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.error || 'Cancellation failed.', 'error');
                }
            })
            .catch(err => {
                console.error('Cancel failed:', err);
                Swal.fire('Error', 'Cancellation request failed.', 'error');
            });
        }
    });
}