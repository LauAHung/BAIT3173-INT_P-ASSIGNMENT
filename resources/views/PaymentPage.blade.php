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
                    <input type="radio" name="method" value="Credit Card" checked required onclick="showPaymentForm('credit')">
                    Credit Card
                </label>
                <label>
                    <input type="radio" name="method" value="EWallet" onclick="showPaymentForm('ewallet')">
                    Touch 'n Go eWallet
                </label>
                <label>
                    <input type="radio" name="method" value="Wallet" onclick="showPaymentForm('wallet')">
                    Wallet Balance
                </label>
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
        <p><strong>Train:</strong> {{ $booking->Journey->Train->TrainNo ?? 'Unknown' }}</p>
        <p><strong>From:</strong> {{ $booking->Journey->FromLocation }} → {{ $booking->Journey->ToLocation }}</p>
        <p><strong>Amount:</strong> RM {{ number_format($booking->Price, 2) }}</p>
    </div>
</div>

<!-- SweetAlert for Payment -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function showPaymentForm(method) {
    document.querySelectorAll('.payment-form').forEach(div => div.classList.add('hidden'));
    document.getElementById(method).classList.remove('hidden');

    // reset errors
    document.querySelectorAll('.error').forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });
    document.querySelectorAll('.error-indicator').forEach(el => el.classList.remove('show'));
    document.querySelectorAll('.error-border').forEach(el => el.classList.remove('error-border'));

    if (method !== 'credit') {
        document.querySelectorAll('#credit input').forEach(input => {
            input.value = '';
        });
    }

    if (method === 'ewallet') {
        const display = document.getElementById('timer');
        startTimer(30, display);
    } else {
        clearInterval(timerInterval);
    }
}

// ========== Timer Function ==========
let timerInterval;
function startTimer(duration, display) {
    clearInterval(timerInterval);
    let timer = duration, minutes, seconds;
    timerInterval = setInterval(() => {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            clearInterval(timerInterval);
            location.reload(); // 🔥 Auto-refresh page when expired
        }
    }, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.payment-section');
    const creditFields = ['card_number','card_name','expiry','cvv'];
    const confirmBtn = document.getElementById('confirmBtn');
    showPaymentForm('credit');

    const cardInput = document.getElementById('card_number');
    const expiryInput = document.getElementById('expiry');

    cardInput.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\D/g, '');
        value = value.substring(0, 16);
        e.target.value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
        validateField(e.target);
    });

    expiryInput.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 4) value = value.substring(0, 4);

        if (value.length >= 3) {
            e.target.value = value.substring(0,2) + '/' + value.substring(2);
        } else {
            e.target.value = value;
        }
        validateField(e.target);
    });

    function getSelectedMethod() {
        const methodInput = document.querySelector('input[name="method"]:checked');
        return methodInput ? methodInput.value : null;
    }

    function validateField(field){
        const rawValue = field.value.trim();
        let value = rawValue;
        if(field.id === 'card_number') value = value.replace(/\s/g,'');

        let errorText = '';
        if(value === ''){
            errorText = 'This field is required';
        } else {
            if(field.id === 'card_number' && !/^\d{16}$/.test(value)) {
                errorText = 'Card number must be 16 digits';
            }
            if(field.id === 'card_name' && value.length < 2) {
                errorText = 'Card holder name required';
            }
            if(field.id === 'expiry'){
                if(!/^(0[1-9]|1[0-2])\/\d{2}$/.test(rawValue)){
                    errorText = 'Expiry must be MM/YY';
                } else {
                    const [month, year] = rawValue.split('/');
                    const expMonth = parseInt(month, 10);
                    const expYear = 2000 + parseInt(year, 10);
                    const now = new Date();
                    const currentMonth = now.getMonth() + 1;
                    const currentYear = now.getFullYear();
                    if(expYear < currentYear || (expYear === currentYear && expMonth < currentMonth)){
                        errorText = 'Card has expired';
                    }
                }
            }
            if(field.id === 'cvv' && !/^\d{3}$/.test(value)) {
                errorText = 'CVV must be 3 digits';
            }
        }

        const errorEl = document.getElementById('error-' + field.id);
        const indicator = document.getElementById('error-indicator-credit');

        if(errorText){
            errorEl.textContent = errorText;
            errorEl.classList.add('show');
            indicator.classList.add('show');
            field.classList.add('error-border');
        } else {
            errorEl.textContent = '';
            errorEl.classList.remove('show');
            indicator.classList.remove('show');
            field.classList.remove('error-border');
        }
    }

    creditFields.forEach(id => {
        const field = document.getElementById(id);
        if(field){
            field.addEventListener('blur', ()=> validateField(field));
            field.addEventListener('input', ()=> validateField(field));
        }
    });

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

        if(!isValid){
            event.preventDefault();
        } else {
            confirmBtn.disabled = true;
        }
    });

    // ✅ SweetAlert integration
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Payment Successful!',
            text: {!! json_encode(session('success')) !!},
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = "{{ route('booking') }}";
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Payment Failed',
            text: '{{ session("error") }}',
            confirmButtonColor: '#d33'
        });
    @endif
});
</script>

@endsection
