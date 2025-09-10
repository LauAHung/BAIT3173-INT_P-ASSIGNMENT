@extends('Layout.master')

@section('title', 'RatingSection')

@push('styles')
    <link href="{{ asset('css/Rating.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="rating-container-box">

    <!-- 🎟 Ticket Info Card -->
    <div class="rating-ticket-card">
        <h2>Your Trip</h2>
        <div class="rating-ticket-flex">
            
            <!-- Left side: logo + train info -->
            <div class="rating-ticket-left">
                <img src="{{ asset('images/logo/' . ($booking->Journey->Train->TrainService ?? 'default_logo') . '_logo.png') }}" 
                     alt="Train Service Logo" 
                     class="rating-ticket-logo">
                <div class="rating-train-no">Train No: {{ $booking->Journey->Train->TrainNo ?? 'Unknown' }}</div>
            </div>

            <!-- Right side: journey details -->
            <div class="rating-ticket-details">
                <div class="rating-booking-id">Booking ID: {{ $booking->BookingID }}</div>
                <div class="rating-route-row">
                    <span class="station">{{ $booking->Journey->FromLocation ?? 'Unknown' }}</span>
                    <span class="train-icon center-icon"><i class="fas fa-train"></i></span>
                    <span class="station">{{ $booking->Journey->ToLocation ?? 'Unknown' }}</span>
                </div>

                <div class="rating-time-row">
                    <span class="time">
                        {{ $booking->Journey->DepartureTime 
                            ? date('g:i A', strtotime($booking->Journey->DepartureTime)) 
                            : 'Unknown' }}
                    </span>
                    <span class="train-icon center-icon"><i class="fas fa-train"></i></span>
                    <span class="time">
                        {{ $booking->Journey->ArrivalTime 
                            ? date('g:i A', strtotime($booking->Journey->ArrivalTime)) 
                            : 'Unknown' }}
                    </span>
                </div>

                <div class="rating-date-row">
                    <span class="date">
                        Date: 
                        {{ $booking->Journey->DepartureTime 
                            ? date('d F Y', strtotime($booking->Journey->DepartureTime)) 
                            : 'Unknown' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ⭐ Rating Form -->
    <div class="rating-feedbackform" style="max-width:100%">
        <h2>Leave a Review</h2>

        <form action="{{ route('rating.store', $booking->BookingID) }}" method="POST">
            @csrf

            <div class="rating-stars">
                <input type="radio" id="star1" name="rating" value="5">
                <label for="star1">&#9733;</label>
                <input type="radio" id="star2" name="rating" value="4">
                <label for="star2">&#9733;</label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3">&#9733;</label>
                <input type="radio" id="star4" name="rating" value="2">
                <label for="star4">&#9733;</label>
                <input type="radio" id="star5" name="rating" value="1">
                <label for="star5">&#9733;</label>
            </div>

            <textarea name="feedback" class="rating-feedback" placeholder="Write your feedback..." required></textarea>

            <button type="submit" class="rating-submit-btn">Submit Review</button>
        </form>
    </div>

    <p>Selected Star: <span id="selected-star">0</span></p>
</div>

<script>
    // Show selected star
    const stars = document.querySelectorAll('input[name="rating"]');
    const display = document.getElementById('selected-star');
    stars.forEach(star => {
        star.addEventListener('change', () => {
            display.textContent = star.value;
        });
    });
</script>
@endsection
