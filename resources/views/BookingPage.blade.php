@extends('Layout.master')

@section('title', 'TrainBooking - TravelFree')

@push('styles')
<link href="{{ asset('css/BookingPage.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush

@section('content')

<body>
    <section>
        <div class="booking-main-layout">
            <!-- Sidebar -->
            <aside class="booking-sidebar">
                <ul>
                    <li class="sidebar-tab active" id="ongoing-tab">Ongoing</li>
                    <li class="sidebar-tab" id="past-tab">Past Trip</li>
                </ul>
            </aside>
            <!-- Main Content -->
            <div class="booking-content">
                <!-- Ongoing Booking -->
                <div class="on-going-booking" id="ongoing-content">
                    <div class="booking-heading">
                        <h2>On-going Booking Ticket</h2>
                    </div>
                    <div class="booking-item-container">
                        @php
                        $ongoingBookings = $ongoingBookings ?? collect();
                        @endphp
                        @forelse ($ongoingBookings as $booking)
                        <div class="booking-item">
                            <div class="booking-flex-row">
                                <div class="booking-col booking-col-left">
                                    <img src="{{ asset('images/logo/' . ($booking->Journey->Train->TrainService ?? 'default_logo.png') . '_logo.png') }}"
                                        alt="service_type" class="booking-logo">
                                    <div class="train-number">{{ $booking->Journey->Train->TrainNo ?? 'Unknown' }}</div>
                                    <div class="booking-id">Booking ID: {{ $booking->BookingID }}</div>
                                </div>
                                <div class="booking-col booking-col-middle">
                                    <div class="route-row dashed-line">
                                        <span class="station">{{ $booking->Journey->FromLocation ?? 'Unknown'}}</span>
                                        <span class="train-icon center-icon">
                                            <i class="fas fa-train"></i>
                                        </span>
                                        <span class="station">{{ $booking->Journey->ToLocation ?? 'Unknown'}}</span>
                                    </div>
                                    <div class="time-row dashed-line">
                                        <span
                                            class="time">{{ date('g:i A', strtotime($booking->Journey->DepartureTime ?? 'Unknown')) }}</span>
                                        <span class="train-icon center-icon">
                                            <i class="fas fa-train"></i>
                                        </span>
                                        <span
                                            class="time">{{ date('g:i A', strtotime($booking->Journey->ArrivalTime ?? 'Unknown')) }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="date">Date:
                                            {{ date('d F Y', strtotime($booking->Journey->DepartureTime ?? 'Unknown')) }}</span>
                                    </div>
                                    <div class="status-row">
                                        <span class="status">Status: {{ $booking->Status }}</span>
                                    </div>
                                </div>
                                <div class="booking-col booking-col-right">
                                    <a href="{{ route('bookingdetail', ['bookingId' => $booking->BookingID]) }}">
                                        <button type="submit" class="btn-view">View QR Code</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="booking-item">
                            <p>No ongoing bookings found.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <!-- Past Trip -->
                <div class="booking-history" id="past-content" style="display:none;">
                    <div class="booking-heading">
                        <h2>Booking History</h2>
                    </div>
                    <div class="booking-item-container">
                        @php
                        $pastBookings = $pastBookings ?? collect();
                        @endphp
                        @forelse ($pastBookings as $booking)
                        <div class="booking-item">
                            <div class="booking-flex-row">
                                <div class="booking-col booking-col-left">
                                    <img src="{{ asset('images/logo/' . ($booking->Journey->Train->TrainService ?? 'default_logo.png') . '_logo.png') }}"
                                        alt="service_type" class="booking-logo">
                                    <div class="train-number">{{ $booking->Journey->Train->TrainNo ?? 'Unknown' }}</div>
                                    <div class="booking-id">Booking ID: {{ $booking->BookingID }}</div>
                                </div>
                                <div class="booking-col booking-col-middle">
                                    <div class="route-row dashed-line">
                                        <span class="station">{{ $booking->Journey->FromLocation ?? 'Unknown' }}</span>
                                        <span class="train-icon center-icon">
                                            <i class="fas fa-train"></i>
                                        </span>
                                        <span class="station">{{ $booking->Journey->ToLocation ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="time-row dashed-line">
                                        <span
                                            class="time">{{ date('g:i A', strtotime($booking->Journey->DepartureTime ?? 'Unknown')) }}</span>
                                        <span class="train-icon center-icon">
                                            <i class="fas fa-train"></i>
                                        </span>
                                        <span
                                            class="time">{{ date('g:i A', strtotime($booking->Journey->ArrivalTime ?? 'Unknown')) }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="date">Date:
                                            {{ date('d F Y', strtotime($booking->Journey->DepartureTime ?? 'Unknown')) }}</span>
                                    </div>
                                    <div class="status-row">
                                        <span class="status">Status: {{ $booking->Status }}</span>
                                    </div>
                                </div>
                                <div class="booking-col booking-col-right">
                                    <a href="{{ route('bookingdetail', ['bookingId' => $booking->BookingID]) }}">
                                        <button type="button" class="btn-view">View QR Code</button>
                                    </a>
                                    <a href="#">
                                        <button type="button" class="btn-rate">Rate Trip</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="booking-item">
                            <p>No past bookings found.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="{{ asset('js/BookingPage.js') }}" defer></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.sidebar-tab');
        const contents = document.querySelectorAll('.booking-content > div');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.style.display = 'none');
                this.classList.add('active');
                const contentId = this.id.replace('-tab', '-content');
                document.getElementById(contentId).style.display = 'block';
            });
        });
    });
    </script>
</body>

@endsection