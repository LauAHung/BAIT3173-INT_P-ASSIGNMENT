@extends('Layout.master')

@section('title', 'TrainBooking - TravelFree')

@push('styles')
<link href="{{ asset('css/BookingPage.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush

<body data-user-id="{{ Auth::id() }}"></body>

@section('content')

<section>
    <div class="booking-main-layout">
        <!-- Sidebar -->
        <aside class="booking-sidebar">
            <ul>
                <li class="sidebar-tab active" id="ongoing-tab">Ongoing</li>
                <li class="sidebar-tab" id="past-tab">Past Trip</li>
                <li class="sidebar-tab" id="refunded-tab">Refunded</li>
            </ul>
        </aside>
        <!-- Main Content -->
        <div class="booking-content">
            <!-- Ongoing Booking -->
            <div class="on-going-booking" id="ongoing-content">
                <div class="booking-heading">
                    <h2>On-going Booking Ticket</h2>
                </div>
                <div class="booking-item-container" id="ongoing-list">
                    <p>Loading ongoing bookings...</p>
                </div>
            </div>
            <!-- Past Trip -->
            <div class="booking-history" id="past-content" style="display:none;">
                <div class="booking-heading">
                    <h2>Booking History</h2>
                </div>
                <div class="booking-item-container" id="past-list">
                    <p>Loading past bookings...</p>
                </div>
            </div>
            <!-- Refunded Section -->
            <div class="refunded-booking" id="refunded-content" style="display:none;">
                <div class="booking-heading">
                    <h2>Refunded/Cancelled Bookings</h2>
                </div>
                <div class="booking-item-container" id="refunded-list">
                    <p>Loading refunded bookings...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/BookingPage.js') }}" defer></script>

@endsection