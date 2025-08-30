@extends('Layout.master')

@section('title', 'Choose Payment Method')

@push('styles')
    <link href="{{ asset('css/PaymentPage.css') }}" rel="stylesheet">
@endpush

@section('content')
    @if(session('insufficient_balance'))
        <script>
            alert("{{ session('insufficient_balance') }}");
        </script>
    @endif

    <div class="payment-container">
    
    <!-- LEFT SIDE: Payment Methods -->
    <div class="payment-card">
        <div class="payment-header">
            Choose Payment Method
        </div>

        <form action="{{ route('payment.complete', $booking->BookingID) }}" method="POST" class="payment-methods">
            @csrf

            <!-- Payment Option Boxes -->
            <label>
                <input type="radio" name="method" value="Credit Card" required onclick="showPaymentForm('credit')">
                Credit Card
            </label>
            <label>
                <input type="radio" name="method" value="FPX" onclick="showPaymentForm('fpx')">
                FPX Online Banking
            </label>
            <label>
                <input type="radio" name="method" value="Wallet" onclick="showPaymentForm('wallet')">
                Wallet Balance
            </label>

            <!-- Dynamic Forms -->
            <div id="credit" class="payment-form hidden">
                <h4>Credit Card Details</h4>
                <input type="text" name="card_number" placeholder="Card Number">
                <input type="text" name="card_name" placeholder="Card Holder Name">
                <input type="text" name="expiry" placeholder="MM/YY">
                <input type="text" name="cvv" placeholder="CVV">
            </div>

            <div id="fpx" class="payment-form hidden">
                <h4>FPX Online Banking</h4>
                <select name="bank">
                    <option value="">Select Bank</option>
                    <option value="Maybank">Maybank</option>
                    <option value="CIMB">CIMB</option>
                    <option value="Public Bank">Public Bank</option>
                </select>
            </div>

            <div id="wallet" class="payment-form hidden">
                <h4>Wallet Payment</h4>
                <p>Your balance: <strong>RM {{ number_format($user->wallet_balance, 2) }}</strong></p>
                <p><strong>Amount:</strong> RM {{ number_format($booking->Price, 2) }}</strong></p>
            </div>

            <button type="submit" class="confirm-button">Confirm Payment</button>
        </form>
    </div>

    <!-- RIGHT SIDE: Ticket Summary -->
    <div class="ticket-summary">
        <h4>Ticket Summary</h4>
        <p><strong>Booking ID:</strong> {{ $booking->BookingID }}</p>
        <p><strong>Train:</strong> {{ $booking->Journey->Train->TrainNo ?? 'Unknown' }}</p>
        <p><strong>From:</strong> {{ $booking->Journey->FromLocation }} → {{ $booking->Journey->ToLocation }}</p>
        <p><strong>Amount:</strong> RM {{ number_format($booking->Amount, 2) }}</p>
    </div>

</div>

    <script>
        function showPaymentForm(method) {
            document.querySelectorAll('.payment-form').forEach(div => div.classList.add('hidden'));
            document.getElementById(method).classList.remove('hidden');
        }
    </script>
@endsection
