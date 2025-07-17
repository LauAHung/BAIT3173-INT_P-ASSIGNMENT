@extends('Layout.master')

@section('title', 'SelectSeat - TravelFree')

@push('styles')
<link href="css/SelectSeatPage.css" rel="stylesheet">
@endpush

@section('content')

<section class="ticket-info-section">
    <div class="passenger-info-box">
        <div class="passenger-head-info">
            <h2>Passenger Details</h2>
            <a href="{{ route('TrainSelectionPage') }}" class="change-journey">Change Journey</a>
        </div>
        <div class="passenger-container">
            <div class="passenger-info-details">
                <div class="passenger-info">
                    <div class="passenger-item">
                        <span class="passenger-label">Passenger 1</span>
                        <span class="passenger-subtext">KL SENTRAL KUALA LUMPUR > IPOH, A-1AC (MYR 24.00)</span>
                    </div>
                    <div class="details-item">
                        <span class="details-label">Ticket type</span>
                        <span class="details-value">DEWASA/ADULT</span>
                        <span class="details-label">MyKad no. / passport</span>
                        <span class="details-value">************</span>
                        <span class="details-label">Contact no.</span>
                        <span class="details-value">************</span>
                    </div>
                </div>
                <div class="passenger-info">
                    <div class="passenger-item">
                        <span class="passenger-label">Passenger 2</span>
                        <span class="passenger-subtext">KL SENTRAL KUALA LUMPUR > IPOH, B-2BD (MYR 24.00)</span>
                    </div>
                    <div class="details-item">
                        <span class="details-label">Ticket type</span>
                        <span class="details-value">DEWASA/CHILD</span>
                        <span class="details-label">MyKad no. / passport</span>
                        <span class="details-value">************</span>
                        <span class="details-label">Contact no.</span>
                        <span class="details-value">************</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="ticket-details-box">
        <h2>Price Details</h2>
        <div class="trip-details">
            <span class="label">DEPART</span>
            <div>T-011</div>
            <div>Sun, Jan 05 (07:00 PM - 08:05 PM)</div>
            <div class="trip-price-info">
                <div class="price-info">
                    <div>Total ticket (2)</div>
                    <div>RM 48.00</div>
                </div>
                <div class="total-price-info">
                    <a>Trip Total</a>
                    <a>RM 48.00</a>
                </div>
            </div>
        </div>
        <button class="proceed-payment">Proceed to Payment</button>
    </div>
</section>

<section class="select-seat-section">
    <div class="seat-info-box">
        <h2>Select Seats</h2>

    </div>
</section>

@endsection