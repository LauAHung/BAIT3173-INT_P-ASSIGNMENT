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
                    errorText = 'Expiration Date must be MM/YY';
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
            const walletBalance = parseFloat(document.getElementById('wallet-balance')?.textContent.replace('RM','').trim() || 0);
            const walletAmount = parseFloat(document.getElementById('wallet-amount').textContent.trim());

            if(walletBalance < walletAmount){
                Swal.fire({
                    icon: 'warning',
                    title: 'Insufficient Balance',
                    text: 'Your wallet balance is not enough to complete this payment.',
                    confirmButtonColor: '#d33'
                });
                isValid = false;
            }
        }


        if(!isValid){
            event.preventDefault();
        } else {
            confirmBtn.disabled = true;
        }
    });

    // ✅ SweetAlert integration
    if (window.paymentSuccess) {
        Swal.fire({
            icon: 'success',
            title: 'Payment Successful!',
            text: window.paymentSuccess,
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = window.bookingRoute;
        });
    }

    if (window.paymentError) {
        Swal.fire({
            icon: 'error',
            title: 'Payment Failed',
            text: window.paymentError,
            confirmButtonColor: '#d33'
        });
    }
});
