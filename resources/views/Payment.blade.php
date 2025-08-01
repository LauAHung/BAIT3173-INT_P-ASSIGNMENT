<!DOCTYPE html>
<html>
<head>
    <title>Stripe Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif
@if(session('error'))
    <p style="color:red">{{ session('error') }}</p>
@endif

<form action="/payment" method="POST" id="payment-form">
    @csrf
    <input type="number" name="amount" placeholder="Amount" required>
    <div id="card-element"></div>
    <button type="submit">Pay</button>
</form>

<script>
    var STRIPE_KEY = "{{ config('services.stripe.key') }}";
    var stripe = Stripe(STRIPE_KEY);
    var elements = stripe.elements();
    var card = elements.create('card');
    card.mount('#card-element');

    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
    event.preventDefault();

    // ✅ 确保 CSRF token 还在
    if (!form.querySelector('input[name="_token"]')) {
        alert('CSRF token missing!');
        return;
    }

    stripe.createToken(card).then(function(result) {
        if (result.error) {
            alert(result.error.message);
        } else {
            // ✅ 添加 Stripe Token 到表单
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', result.token.id);
            form.appendChild(hiddenInput);

            // ✅ 提交表单
            form.submit();
        }
    });
});

</script>
</body>
</html>