@extends('Layout.master')

@section('title', 'Choose Payment Method')

@push('styles')
<link href="{{ asset('css/PaymentPage.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="payment-container">
    <div class="payment-card">
        <div class="payment-header">Payment for Booking #{{ $booking->BookingID }}</div>
        <div class="payment-details">
            <p><strong>Train:</strong> {{ $booking->Journey->Train->TrainNo ?? 'Unknown' }}</p>
            <p><strong>From:</strong> {{ $booking->Journey->FromLocation }} → {{ $booking->Journey->ToLocation }}</p>
            <p><strong>Amount:</strong> RM {{ number_format($booking->Amount, 2) }}</p>
        </div>

        <form action="{{ route('payment.complete', $booking->BookingID) }}" method="POST" class="payment-methods">
            @csrf
            <label><input type="radio" name="method" value="Credit Card" required onclick="showPaymentForm('credit')"> Credit Card</label>
            <label><input type="radio" name="method" value="FPX" onclick="showPaymentForm('fpx')"> FPX Online Banking</label>
            <label><input type="radio" name="method" value="Wallet" onclick="showPaymentForm('wallet')"> Wallet Balance</label>

            <!-- Dynamic Payment Forms -->
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
                <p><strong>Amount:</strong> RM {{ number_format($booking->Price, 2) }}</p>
                @if($user->wallet_balance < $booking->Amount)
                    <p style="color:red;">Insufficient balance. Please top up.</p>
                @endif
            </div>

            <button type="submit" class="confirm-button">Confirm Payment</button>
        </form>
    </div>
</div>

<script>
    function showPaymentForm(method) {
        // Hide all
        document.querySelectorAll('.payment-form').forEach(div => div.classList.add('hidden'));
        // Show selected
        document.getElementById(method).classList.remove('hidden');
    }
</script>

<style>
    .hidden { display: none; }
    .payment-form { margin-top: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
</style>
@endsection
