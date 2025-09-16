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
                <img src="{{ asset('images/logo/' . ($journey->train_service ?? 'default_logo') . '_logo.png') }}" 
                     alt="Train Service Logo" 
                     class="rating-ticket-logo">
                <div class="rating-train-no">Train No: {{ $journey->train_no ?? 'Unknown' }}</div>
            </div>

            <!-- Right side: journey details -->
            <div class="rating-ticket-details">
                <div class="rating-booking-id">Booking ID: {{ $booking->booking_id ?? 'Unknown' }}</div>

                <div class="rating-route-row">
                    <span class="station">{{ $journey->from_location ?? 'Unknown' }}</span>
                    <span class="train-icon center-icon"><i class="fas fa-train"></i></span>
                    <span class="station">{{ $journey->to_location ?? 'Unknown' }}</span>
                </div>

                <div class="rating-time-row">
                    <span class="time">
                        {{ !empty($journey->departure_time) ? date('g:i A', strtotime($journey->departure_time)) : 'Unknown' }}
                    </span>
                    <span class="train-icon center-icon"><i class="fas fa-train"></i></span>
                    <span class="time">
                        {{ !empty($journey->arrival_time) ? date('g:i A', strtotime($journey->arrival_time)) : 'Unknown' }}
                    </span>
                </div>

                <div class="rating-date-row">
                    <span class="date">
                        Date: {{ !empty($journey->departure_time) ? date('d F Y', strtotime($journey->departure_time)) : 'Unknown' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ⭐ Rating Form -->
    <div class="rating-feedbackform" style="max-width:100%">
        <h2>Leave a Review</h2>

        <form id="ratingForm" action="{{ route('rating.store', $booking->booking_id ) }}" method="POST">
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

            <!-- Removed required -->
            <textarea name="feedback" class="rating-feedback" placeholder="Write your feedback..."></textarea>

            <button type="submit" class="rating-submit-btn">Submit Review</button>
        </form>
    </div>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Form validation with SweetAlert
    document.getElementById('ratingForm').addEventListener('submit', function(e) {
        e.preventDefault(); // stop immediate submit
        const rating = document.querySelector('input[name="rating"]:checked');
        const feedback = document.querySelector('.rating-feedback').value.trim();

        if (!rating || feedback === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Review',
                text: 'Please rate and provide feedback before submitting.',
                confirmButtonColor: '#d33'
            });
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Thank you!',
                text: 'Your feedback has been submitted successfully.',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                // only submit when user closes swal
                e.target.submit();
            });
        }
    });
</script>
@endsection
