@extends('Layout.master')

@section('title', 'TrainBookingDetails - TravelFree')

@push('styles')
<link href="{{ asset('css/BookingDetailPage.css') }}" rel="stylesheet">
@endpush

<body data-booking-id="{{ $bookingId }}" data-user-id="{{ Auth::id() }}"></body>

@section('content') 
<section>
    <div class="booking-detail">
        <div class="booking-detail-container">
            <div class="booking-heading">
                <h2>Booking Details</h2>
            </div>
            {{-- Placeholder for booking info --}}
            <div id="booking-container"></div>
        </div>
        {{-- Placeholder for tickets info --}}
        <div class="qr-ticket-container" id="tickets-container"></div>
    </div>
</section>

<!-- Modal QR -->
<div id="qrModal" class="modal-qr">
    <div class="modal-content-qr">
        <span class="close-qr">×</span>
        <img src="" alt="Zoomed QR Code" id="modalQrImage">
    </div>
</div>

<script src="{{ asset('js/BookingDetail.js') }}"></script>
@endsection
