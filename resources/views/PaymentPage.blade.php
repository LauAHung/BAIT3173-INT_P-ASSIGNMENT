@extends('Layout.master')

@section('title', 'Choose Payment Method')

@push('styles')
    <link href="{{ asset('css/PaymentPage.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="payment-container">
    <!-- LEFT SIDE: Payment Methods -->
    <div class="payment-card">
        <div class="payment-header">Choose Payment Method</div>

        <form action="{{ route('payment.complete', $booking->BookingID) }}" method="POST" class="payment-section">
            @csrf

           <div class="payment-methods">
            <input type="radio" id="creditRadio" name="method" value="Credit Card" checked onclick="showPaymentForm('credit')">
            <label for="creditRadio">Credit Card</label>

            <input type="radio" id="ewalletRadio" name="method" value="EWallet" onclick="showPaymentForm('ewallet')">
            <label for="ewalletRadio">Touch 'n Go eWallet</label>

            <input type="radio" id="walletRadio" name="method" value="Wallet" onclick="showPaymentForm('wallet')">
            <label for="walletRadio">Wallet Balance</label>
        </div>


            <!-- Credit Card Form -->
            <div id="credit" class="payment-form">
                <h4>Credit Card Details <span class="error-indicator" id="error-indicator-credit">*</span></h4>
                <br>
                <div class="info-item">
                    <span class="info-label">Card Number<a>*</a></span>
                    <input type="text" class="info-value" name="card_number" id="card_number" placeholder="XXXX XXXX XXXX XXXX">
                    <span class="error" id="error-card_number"></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Card Holder Name<a>*</a></span>
                    <input type="text" class="info-value" name="card_name" id="card_name" placeholder="John Doe">
                    <span class="error" id="error-card_name"></span>
                </div>

                <div class="form-row">
                    <div class="info-item">
                        <span class="info-label">Expiration Date (MM/YY)<a>*</a></span>
                        <input type="text" class="info-value" name="expiry" id="expiry" placeholder="MM/YY">
                        <span class="error" id="error-expiry"></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">CVV<a>*</a></span>
                        <input type="text" class="info-value" name="cvv" id="cvv" placeholder="123">
                        <span class="error" id="error-cvv"></span>
                    </div>
                </div>
            </div>

            <!-- eWallet Form -->
            <div id="ewallet" class="payment-form hidden">
                <h4>Touch 'n Go eWallet</h4>
                <div class="ewallet-container">
                    <!-- Left: QR + Timer -->
                    <div class="ewallet-left">
                        <img src="{{ asset('images/tng_qr.jpg') }}" alt="eWallet QR" class="ewallet-qr">
                        <p class="ewallet-timer">QR code will expire in <span id="timer">00:30</span></p>
                    </div>
                    <!-- Right: Steps -->
                    <div class="ewallet-right">
                        <h4>Pay with Touch 'n Go eWallet!</h4>
                        <p>
                            1. Open <strong>Touch 'n Go eWallet App</strong><br>
                            2. Tap on <strong>Scan</strong><br>
                            3. Scan QR Code and complete the payment
                        </p>
                        <span class="error" id="error-ewallet"></span>
                    </div>
                </div>
            </div>

            <!-- Wallet Form -->
            <div id="wallet" class="payment-form hidden" style="height: 265px;">
                <h4>Wallet Payment</h4>
                <br>
                 <br>
                @if($user)
                    <p><strong>Your balance :</strong> <span id="wallet-balance">RM {{ number_format($user->wallet_balance, 2) }}</span></p>
                @else
                    <p class="error">Please log in to view your wallet balance.</p>
                @endif
                <br> 
                <p><strong>Amount :</strong> RM <span id="wallet-amount">{{ number_format($booking->Price, 2) }}</span></p>
                <span class="error" id="error-wallet"></span>
            </div>

            <button type="submit" class="confirm-button" id="confirmBtn">Confirm Payment</button>
        </form>
    </div>

    <!-- RIGHT SIDE: Ticket Summary -->
    <div class="ticket-summary">
    <h4>Ticket Summary</h4>
    <p><strong>Booking ID:</strong> {{ $booking->BookingID }}</p>
    <p><strong>Train:</strong> {{ $journey->TrainNo ?? 'Unknown' }}</p>
    <p><strong>From:</strong> {{ $journey->FromLocation }} → {{ $journey->ToLocation }}</p>
    <p><strong>Amount:</strong> RM {{ number_format($booking->Price, 2) }}</p>
</div>

</div>

<!-- SweetAlert for Payment -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Pass PHP session messages to JS
    @if(session('success'))
        window.paymentSuccess = {!! json_encode(session('success')) !!};
        window.bookingRoute = "{{ route('booking') }}";
    @endif
    @if(session('error'))
        window.paymentError = "{{ session('error') }}";
    @endif
</script>
<script src="{{ asset('js/Payment.js') }}"></script>


@endsection
