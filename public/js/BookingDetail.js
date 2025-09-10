document.addEventListener("DOMContentLoaded", async function () {
    const body = document.querySelector("body");
    const bookingId = body.dataset.bookingId;
    const userId = body.dataset.userId;

    const bookingContainer = document.getElementById("booking-container");
    const ticketsContainer = document.getElementById("tickets-container");

    // Show loading state
    ticketsContainer.innerHTML = `<div class="loading">Loading Info... <span class="spinner"></span></div>`;

    // Fetch data from API
    try {
        const response = await fetch(`/api/booking/${bookingId}/${userId}`);
        const data = await response.json();

        const booking = data.booking || {};
        const tickets = data.tickets || [];

        // Render booking details
        bookingContainer.innerHTML = `
            <div class="booking-item">
                <div class="booking-container">
                    <div class="booking-info">
                        <img src="/images/logo/${booking.journey?.train?.TrainService ?? 'default'}_logo.png" alt="service_type">
                    </div>
                    <div class="booking-info"><label class="booking-label">Booking ID:</label> <span>${booking.BookingID ?? 'Unknown'}</span></div>
                    <div class="booking-info"><label class="booking-label">Route:</label> <span>${booking.journey?.FromLocation ?? 'Unknown'} to ${booking.journey?.ToLocation ?? 'Unknown'}</span></div>
                    ${booking.BookingType === 'Return' ? `
                        <div class="booking-info"><label class="booking-label">Return:</label> <span>${booking.journey2?.FromLocation ?? 'Unknown'} to ${booking.journey2?.ToLocation ?? 'Unknown'}</span></div>
                    ` : ""}
                    <div class="booking-info"><label class="booking-label">DepartDate:</label> 
                        <span>${new Date(booking.journey?.DepartureTime ?? "").toLocaleDateString("en-US", {month: "short",day: "numeric",year: "numeric"}).replace(",", "")}</span> |
                        <span>${formatTime(booking.journey?.DepartureTime)} - ${formatTime(booking.journey?.ArrivalTime)}</span>
                    </div>
                    ${booking.BookingType === 'Return' ? `
                        <div class="booking-info"><label class="booking-label">ReturnDate:</label>
                            <span>${new Date(booking.journey2?.DepartureTime ?? "").toLocaleDateString("en-US", {month: "short",day: "numeric",year: "numeric"}).replace(",", "")}</span> |
                            <span>${formatTime(booking.journey2?.DepartureTime)} - ${formatTime(booking.journey2?.ArrivalTime)}</span>
                        </div>
                    ` : ""}
                    <div class="booking-info "><label class="booking-label">Status:</label> <span>${booking.Status ?? 'Unknown'}</span></div>
                    <div class="booking-info"><label class="booking-label">Total Price:</label> <span>RM ${booking.Price ? Number(booking.Price).toFixed(2) : 'Unknown'}</span></div>
                </div>
            </div>
            <div class="ticket-heading">
                <h2>Your Tickets: </h2>
            </div>
        `;

        // Render tickets
        if (tickets.length > 0) {
            ticketsContainer.innerHTML = tickets.map(ticket => `
                <div class="ticket-container">
                    <div class="ticket">
                        <div class="hqr">
                            <div class="column left-one"></div>
                            <div class="column center">
                                <div class="qrcode">
                                    <img src="https://quickchart.io/chart?cht=qr&chs=300x300&chl=${ticket.TicketID}" alt="QR Code for Ticket ${ticket.TicketID}">
                                </div>
                            </div>
                            <div class="column right-one"></div>
                        </div>
                    </div>
                    <div class="details">
                        <div class="info">Full name</div>
                        <div class="data name">${ticket.passenger?.Name ?? 'Unknown'}</div>
                        <div class="info">Ticket type</div>
                        <div class="data">${ticket.passenger?.TicketType ?? 'Unknown'}</div>
                        <div class="info">Journey ID</div>
                        <div class="data">${ticket.JourneyID ?? 'Unknown'}</div>
                        <div class="info">Seat No.</div>
                        <div class="data">${ticket.seat?.SeatNo ?? '-'}</div>
                        <div class="masinfo">
                            <div class="left">
                                <div class="info">date</div>
                                <div class="data nesp">${new Date(ticket.journey?.DepartureTime).toDateString()}</div>
                            </div>
                            <div class="right">
                                <div class="info">time</div>
                                <div class="data nesp">${formatTime(ticket.journey?.DepartureTime)} - ${formatTime(ticket.journey?.ArrivalTime)}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join("");
        } else {
            ticketsContainer.innerHTML = `<p>No tickets found for this booking.</p>`;
        }

        // QR Modal
        initQrModal();

    } catch (error) {
        console.error("Error fetching booking data:", error);
        bookingContainer.innerHTML = "<p>Error loading booking details.</p>";
        ticketsContainer.innerHTML = "<p>Error loading tickets.</p>";
    }
});

// Helper: format time
function formatTime(datetime) {
    if (!datetime) return "Unknown";
    const date = new Date(datetime);
    return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
}

// Modal QR logic
function initQrModal() {
    const qrcodes = document.getElementsByClassName("qrcode");
    const modalQR = document.getElementById("qrModal");
    const modalImg = document.getElementById("modalQrImage");
    const span = document.getElementsByClassName("close-qr")[0];

    for (let i = 0; i < qrcodes.length; i++) {
        qrcodes[i].getElementsByTagName("img")[0].onclick = function () {
            modalQR.style.display = "flex";
            modalImg.src = this.src;
        }
    }
    span.onclick = function () { modalQR.style.display = "none"; }
    modalQR.addEventListener('click', function (event) {
        if (event.target === modalQR) {
            modalQR.style.display = "none";
        }
    });
}
