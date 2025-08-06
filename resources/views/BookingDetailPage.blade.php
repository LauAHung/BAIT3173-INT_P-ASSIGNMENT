@extends('Layout.master')

@section('title', 'TrainBookingDetails - TravelFree')

@push('styles')
<link href="{{ asset('css/BookingDetailPage.css') }}" rel="stylesheet">
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
                                <img src="{{ asset('images/logo/' . ($booking->Journey->Train->TrainService ?? 'default_logo.png') . '_logo.png') }}"
                                    alt="service_type">
                            </div>

                            <div class="booking-info">
                                <label class="booking-label">Ticket ID: </label>
                                <span class="booking-value">{{ $booking->BookingID ?? 'Unknown' }}</span>
                            </div>

                            <div class="booking-info">
                                <label class="booking-label">Route: </label>
                                <span class="booking-value">{{ $booking->Journey->FromLocation ?? 'Unknown' }} to
                                    {{ $booking->Journey->ToLocation ?? 'Unknown' }}</span>
                            </div>

                            <div class="booking-info">
                                <label class="booking-label">Departure: </label>
                                <span
                                    class="booking-value">{{ date('g:i A', strtotime($booking->Journey->DepartureTime ?? 'Unknown')) }}
                                    - {{ date('g:i A', strtotime($booking->Journey->ArrivalTime ?? 'Unknown')) }}</span>
                            </div>

                            <div class="booking-info">
                                <label class="booking-label">Date: </label>
                                <span
                                    class="booking-value">{{ \Carbon\Carbon::parse($booking->Journey->DepartureTime ?? '')->format('d F Y') }}</span>
                            </div>

                            <div class="booking-info">
                                <label class="booking-label">Status: </label>
                                <span class="booking-value">{{ $booking->Status ?? 'Unknown' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ticket-heading">
                <h2>Your Tickets: </h2>
            </div>
            <div class="qr-ticket-container">
                @php
                    $tickets = $tickets ?? collect();
                @endphp
                @forelse ($tickets as $ticket)
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
                        <div class="info">Full name</div>
                        <div class="data name">{{ $ticket->Passenger->Name ?? 'Unknown' }}</div>
                        <div class="info">Ticket type</div>
                        <div class="data">{{ $ticket->Passenger->TicketType ?? 'Unknown' }}</div>
                        <div class="info">Ticket ID</div>
                        <div class="data">{{ $ticket->TicketID ?? 'Unknown'}}</div>
                        <div class="info">Journey ID</div>
                        <div class="data">{{ $ticket->JourneyID ?? 'Unknown'}}</div>
                        <div class="info">Seat No.</div>
                        <div class="data">{{ $ticket->Seat->SeatNo ?? 'Not assigned' }}</div>
                        <div class="masinfo">
                            <div class="left">
                                <div class="info">date</div>
                                <div class="data nesp">
                                    {{ \Carbon\Carbon::parse($ticket->Journey->DepartureTime)->format('D. M d Y') }}
                                </div>
                            </div>
                            <div class="right">
                                <div class="info">time</div>
                                <div class="data nesp">{{ date('g:i A', strtotime($ticket->Journey->DepartureTime)) }} -
                                    {{ date('g:i A', strtotime($ticket->Journey->ArrivalTime)) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="ticket-container">
                    <p>No tickets found for this booking.</p>
                </div>
                @endforelse
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