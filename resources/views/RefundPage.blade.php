@extends('Layout.master')

@section('title', 'Refund Ticket')

@push('styles')
    <link href="{{ asset('css/RefundPage.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="refund-container">
    <div class="refund-card">
        <!-- Page Title -->
        <div class="refund-header">Refund Ticket</div>
        <hr class="divider">

        <!-- Ticket Summary -->
        <h2 class="section-title">Ticket Summary</h2>
        <div class="ticket-summary">
            <p><strong>Booking ID:</strong> {{ $booking->BookingID }}</p>
              <p><strong>Train:</strong> {{ $journey->TrainNo ?? 'Unknown' }}</p>
            <p><strong>From:</strong> {{ $journey->FromLocation }} → {{ $journey->ToLocation }}</p>

            <p><strong>Amount:</strong> RM {{ number_format($booking->Price, 2) }}</p>
        </div>

        <hr class="divider">

        <!-- Refund Summary -->
        @php
            $charge = $booking->Price * 0.20;
            $refundAmount = $booking->Price - $charge;
        @endphp
        <h2 class="section-title">Refund Summary</h2>
        <div class="refund-details">
            <p><strong>Cancellation Charge (20%):</strong> RM {{ number_format($charge, 2) }}</p>
            <p><strong>Refund Amount:</strong> RM {{ number_format($refundAmount, 2) }}</p>
        </div>

        <!-- Action Buttons -->
        <form action="{{ route('refund.process', $booking->BookingID) }}" method="POST">
            @csrf
            <button type="submit" class="confirm-button">Confirm Refund</button>
            <a href="{{ route('booking') }}" class="cancel-link">Cancel</a>
        </form>
        
        <!-- Footnote Refund Policy -->
        <p class="refund-warning">
            *The refund amount will be credited to account wallet.
        </p>
    </div>
</div>

<!-- SweetAlert & Refund.js -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Pass Laravel session messages into JS global vars
    window.refundSuccess = @json(session('success'));
    window.refundError   = @json(session('error'));
    window.refundRedirectUrl = "{{ route('booking') }}";
</script>
<script src="{{ asset('js/Refund.js') }}"></script>
@endsection


