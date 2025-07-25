@extends('Layout.master')

@section('title', 'TrainBookingDetails - TravelFree')

@push('styles')
<link href="css/BookingDetailPage.css" rel="stylesheet">
@endpush

@section('content')

<body>
    <section>
        <div class="booking-detail">
            <div class="booking-detail-container">
                <div class="booking-heading">
                    <h2>Booking Details</h2>
                </div>
                <div class="booking-item-container">
                    <div class="booking-item">
                        <div class="booking-container">
                            <div class="booking-info">
                                <img src="{{ asset('images/logo/ets_logo.png') }}" alt="service_type">
                            </div>

                            <div class="booking-info">
                                <label class="booking-label">Ticket ID: </label>
                                <span class="booking-value">11524</span>
                            </div>

                            <div class="booking-info">
                                <label class="booking-label">Route: </label>
                                <span class="booking-value">KL Sentral to Ipoh</span>
                            </div>

                            <div class="booking-info">
                                <label class="booking-label">Departure: </label>
                                <span class="booking-value">7:00 PM - 8:05 PM</span>
                            </div>

                            <div class="booking-info">
                                <label class="booking-label">Date: </label>
                                <span class="booking-value">22 July 2025</span>
                            </div>

                            <div class="booking-info">
                                <label class="booking-label">Status: </label>
                                <span class="booking-value">Booked</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="ticket-heading">
            <h2>Your Tickets: </h2>
            </div>
            <div class="qr-ticket-container">
                <div class="ticket-container">
                    <div class="ticket">
                        <div class="hqr">
                            <div class="column left-one"></div>
                            <div class="column center">
                                <div class="qrcode"><img src="{{ asset('images/testqr.png') }}" alt="QR"></div>
                            </div>
                            <div class="column right-one"></div>
                        </div>
                    </div>
                    <div class="details">
                        <div class="info">
                            Full name
                        </div>
                        <div class="data name">
                            Jimmy
                        </div>
                        <div class="info">
                            Ticket type
                        </div>
                        <div class="data">
                            Dewasa/Adult
                        </div>
                        <div class="info">
                            Journey ID
                        </div>
                        <div class="data">
                            J152
                        </div>
                        <div class="info">
                            Seat No.
                        </div>
                        <div class="data">
                            13A
                        </div>
                        <div class="masinfo">
                            <div class="left">
                                <div class="info">
                                    date
                                </div>
                                <div class="data nesp">
                                    MON. APR 09 2025
                                </div>
                            </div>
                            <div class="right">
                                <div class="info">
                                    time
                                </div>
                                <div class="data nesp">
                                    7:00 PM - 8:05 PM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>             

                


                <div class="ticket-container">
                    <div class="ticket">
                        <div class="hqr">
                            <div class="column left-one"></div>
                            <div class="column center">
                                <div class="qrcode"><img src="{{ asset('images/testqr.png') }}" alt="QR"></div>
                            </div>
                            <div class="column right-one"></div>
                        </div>
                    </div>
                    <div class="details">
                        <div class="info">
                            Full name
                        </div>
                        <div class="data name">
                            Jimmy
                        </div>
                        <div class="info">
                            Ticket type
                        </div>
                        <div class="data">
                            Dewasa/Adult
                        </div>
                        <div class="info">
                            Journey ID
                        </div>
                        <div class="data">
                            J152
                        </div>
                        <div class="info">
                            Seat No.
                        </div>
                        <div class="data">
                            13A
                        </div>
                        <div class="masinfo">
                            <div class="left">
                                <div class="info">
                                    date
                                </div>
                                <div class="data nesp">
                                    MON. APR 09 2025
                                </div>
                            </div>
                            <div class="right">
                                <div class="info">
                                    time
                                </div>
                                <div class="data nesp">
                                    7:00 PM - 8:05 PM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ticket-container">
                    <div class="ticket">
                        <div class="hqr">
                            <div class="column left-one"></div>
                            <div class="column center">
                                <div class="qrcode"><img src="{{ asset('images/testqr.png') }}" alt="QR"></div>
                            </div>
                            <div class="column right-one"></div>
                        </div>
                    </div>
                    <div class="details">
                        <div class="info">
                            Full name
                        </div>
                        <div class="data name">
                            Jimmy
                        </div>
                        <div class="info">
                            Ticket type
                        </div>
                        <div class="data">
                            Dewasa/Adult
                        </div>
                        <div class="info">
                            Journey ID
                        </div>
                        <div class="data">
                            J152
                        </div>
                        <div class="info">
                            Seat No.
                        </div>
                        <div class="data">
                            13A
                        </div>
                        <div class="masinfo">
                            <div class="left">
                                <div class="info">
                                    date
                                </div>
                                <div class="data nesp">
                                    MON. APR 09 2025
                                </div>
                            </div>
                            <div class="right">
                                <div class="info">
                                    time
                                </div>
                                <div class="data nesp">
                                    7:00 PM - 8:05 PM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ticket-container">
                    <div class="ticket">
                        <div class="hqr">
                            <div class="column left-one"></div>
                            <div class="column center">
                                <div class="qrcode"><img src="{{ asset('images/testqr.png') }}" alt="QR"></div>
                            </div>
                            <div class="column right-one"></div>
                        </div>
                    </div>
                    <div class="details">
                        <div class="info">
                            Full name
                        </div>
                        <div class="data name">
                            Jimmy
                        </div>
                        <div class="info">
                            Ticket type
                        </div>
                        <div class="data">
                            Dewasa/Adult
                        </div>
                        <div class="info">
                            Journey ID
                        </div>
                        <div class="data">
                            J152
                        </div>
                        <div class="info">
                            Seat No.
                        </div>
                        <div class="data">
                            13A
                        </div>
                        <div class="masinfo">
                            <div class="left">
                                <div class="info">
                                    date
                                </div>
                                <div class="data nesp">
                                    MON. APR 09 2025
                                </div>
                            </div>
                            <div class="right">
                                <div class="info">
                                    time
                                </div>
                                <div class="data nesp">
                                    7:00 PM - 8:05 PM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ticket-container">
                    <div class="ticket">
                        <div class="hqr">
                            <div class="column left-one"></div>
                            <div class="column center">
                                <div class="qrcode"><img src="{{ asset('images/testqr.png') }}" alt="QR"></div>
                            </div>
                            <div class="column right-one"></div>
                        </div>
                    </div>
                    <div class="details">
                        <div class="info">
                            Full name
                        </div>
                        <div class="data name">
                            Jimmy
                        </div>
                        <div class="info">
                            Ticket type
                        </div>
                        <div class="data">
                            Dewasa/Adult
                        </div>
                        <div class="info">
                            Journey ID
                        </div>
                        <div class="data">
                            J152
                        </div>
                        <div class="info">
                            Seat No.
                        </div>
                        <div class="data">
                            13A
                        </div>
                        <div class="masinfo">
                            <div class="left">
                                <div class="info">
                                    date
                                </div>
                                <div class="data nesp">
                                    MON. APR 09 2025
                                </div>
                            </div>
                            <div class="right">
                                <div class="info">
                                    time
                                </div>
                                <div class="data nesp">
                                    7:00 PM - 8:05 PM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ticket-container">
                    <div class="ticket">
                        <div class="hqr">
                            <div class="column left-one"></div>
                            <div class="column center">
                                <div class="qrcode"><img src="{{ asset('images/testqr.png') }}" alt="QR"></div>
                            </div>
                            <div class="column right-one"></div>
                        </div>
                    </div>
                    <div class="details">
                        <div class="info">
                            Full name
                        </div>
                        <div class="data name">
                            Jimmy
                        </div>
                        <div class="info">
                            Ticket type
                        </div>
                        <div class="data">
                            Dewasa/Adult
                        </div>
                        <div class="info">
                            Journey ID
                        </div>
                        <div class="data">
                            J152
                        </div>
                        <div class="info">
                            Seat No.
                        </div>
                        <div class="data">
                            13A
                        </div>
                        <div class="masinfo">
                            <div class="left">
                                <div class="info">
                                    date
                                </div>
                                <div class="data nesp">
                                    MON. APR 09 2025
                                </div>
                            </div>
                            <div class="right">
                                <div class="info">
                                    time
                                </div>
                                <div class="data nesp">
                                    7:00 PM - 8:05 PM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>

    <!-- Modal -->
    <div id="qrModal" class="modal">
        <div class="modal-content">
            <span class="close">×</span>
            <img src="" alt="Zoomed QR Code" id="modalQrImage">
        </div>
    </div>

    <script>
    // Get all qrcode elements
    var qrcodes = document.getElementsByClassName("qrcode");
    var modal = document.getElementById("qrModal");
    var modalImg = document.getElementById("modalQrImage");
    var span = document.getElementsByClassName("close")[0];

    // Attach click event to each qrcode
    for (var i = 0; i < qrcodes.length; i++) {
        qrcodes[i].getElementsByTagName("img")[0].onclick = function() {
            modal.style.display = "flex";
            modalImg.src = this.src;
        }
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>

</body>

@endsection