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

            <!-- Payment Option Boxes -->
            <div class="payment-methods">
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
            </div>

            <!-- Credit Card Form -->
            <div id="credit" class="payment-form hidden">
                <h4>Credit Card Details <span class="error-indicator" id="error-indicator-credit">*</span></h4>

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
                        <span class="info-label">Expiry (MM/YY)<a>*</a></span>
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

            <!-- FPX Form -->
            <div id="fpx" class="payment-form hidden">
                <h4>FPX Online Banking</h4>
                <select name="bank" class="info-value">
                    <option value="">Select Bank</option>
                    <option value="Maybank">Maybank</option>
                    <option value="CIMB">CIMB</option>
                    <option value="Public Bank">Public Bank</option>
                </select>
            </div>

            <!-- Wallet Form -->
            <div id="wallet" class="payment-form hidden">
                <h4>Wallet Payment</h4>
                @if($user)
                    <p>Your balance: <strong id="wallet-balance">RM {{ number_format($user->wallet_balance, 2) }}</strong></p>
                @else
                    <p class="error">Please log in to view your wallet balance.</p>
                @endif
                <p><strong>Amount:</strong> RM <span id="wallet-amount">{{ number_format($booking->Price, 2) }}</span></p>
                <span class="error" id="error-wallet"></span>
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
        <p><strong>Amount:</strong> RM {{ number_format($booking->Price, 2) }}</p>
    </div>
</div>

<script>
function showPaymentForm(method) {
    document.querySelectorAll('.payment-form').forEach(div => div.classList.add('hidden'));
    document.getElementById(method).classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.payment-section');
    const creditFields = ['card_number','card_name','expiry','cvv'];

    function getSelectedMethod() {
        const methodInput = document.querySelector('input[name="method"]:checked');
        return methodInput ? methodInput.value : null;
    }

    function validateField(field){
        const value = field.value.trim();
        let errorText = '';
        if(field.id === 'card_number' && !/^\d{16}$/.test(value)) errorText = 'Card number must be 16 digits';
        if(field.id === 'card_name' && value.length < 2) errorText = 'Card holder name required';
        if(field.id === 'expiry' && !/^(0[1-9]|1[0-2])\/\d{2}$/.test(value)) errorText = 'Expiry must be MM/YY';
        if(field.id === 'cvv' && !/^\d{3}$/.test(value)) errorText = 'CVV must be 3 digits';

        const errorEl = document.getElementById('error-' + field.id);
        const indicator = document.getElementById('error-indicator-credit');
        if(errorText){
            errorEl.textContent = errorText;
            errorEl.classList.add('show');
            indicator.classList.add('show');
        } else {
            errorEl.textContent = '';
            errorEl.classList.remove('show');
            indicator.classList.remove('show');
        }
    }

    // Live validation on blur
    creditFields.forEach(id => {
        const field = document.getElementById(id);
        if(field){
            field.addEventListener('blur', ()=> validateField(field));
        }
    });

    // Submit-time validation
    form.addEventListener('submit', (event)=>{
        let isValid = true;
        const selectedMethod = getSelectedMethod();

        if(selectedMethod === 'Credit Card'){
            creditFields.forEach(id=>{
                const field = document.getElementById(id);
                validateField(field);
                if(document.getElementById('error-' + id).textContent) isValid = false;
            });
        }
        if(selectedMethod === 'Wallet'){
            const walletError = document.getElementById('error-wallet');
            const walletBalance = parseFloat(document.getElementById('wallet-balance')?.textContent.replace('RM','').trim() || 0);
            const walletAmount = parseFloat(document.getElementById('wallet-amount').textContent.trim());
            
            if(walletBalance < walletAmount){
                walletError.textContent = 'Insufficient wallet balance!';
                walletError.classList.add('show');
                isValid = false;
            } else {
                walletError.textContent = '';
                walletError.classList.remove('show');
            }
        }
        // Prevent form submission if validation fails
        if(!isValid) event.preventDefault();
    });
});
</script>

@endsection