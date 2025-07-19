@extends('Layout.master')

@section('title', 'TrainBooking - TravelFree')

@push('styles')
<link href="css/BookingPage.css" rel="stylesheet">
@endpush

@section('content')

<body>
<section>

    <div class="on-going-booking">

        <div class="booking-heading">
        <h2>On-going Booking Ticket</h2>
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

                <div class="btn-view">
                    <a href="{{ route('bookingdetail') }}"><button type="submit">View QR Code</button></a>
                </div>

            </div>
        </div>
    </div>

    <div class="booking-history">
        <div class="booking-heading">
            <h2>Booking History</h2>
        </div>

        <div class="booking-item-container">
            <div class="booking-item">
                <div class="booking-container">
                    <div class="booking-info">
                        <img src="{{ asset('images/logo/ets_logo.png') }}" alt="service_type">
                    </div>

                    <div class="booking-info">
                    <label class="booking-label">Ticket ID: </label>
                    <span class="booking-value">11521</span>
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
                    <span class="booking-value">12 July 2025</span>
                    </div>

                    <div class="booking-info">
                    <label class="booking-label">Status: </label>
                    <span class="booking-value">Completed</span>
                    </div>
                </div>

            </div>

                    <div class="booking-item-container">
            <div class="booking-item">
                <div class="booking-container">
                    <div class="booking-info">
                        <img src="{{ asset('images/logo/ets_logo.png') }}" alt="service_type">
                    </div>

                    <div class="booking-info">
                    <label class="booking-label">Ticket ID: </label>
                    <span class="booking-value">11219</span>
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
                    <span class="booking-value">08 July 2025</span>
                    </div>

                    <div class="booking-info">
                    <label class="booking-label">Status: </label>
                    <span class="booking-value">Completed</span>
                    </div>
                </div>

            </div>

        </div>
    </div>

</section>
</body>

@endsection