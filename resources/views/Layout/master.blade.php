<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TravelFree')</title>
    <link href="css/master.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
    @stack('styles')
</head>
<body>
    <header>
        <div>
            <a href="{{ route('HomePage') }}">
                <img src="{{ asset('images/logo_black.png') }}" alt="Logo" id="logo">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="{{ route('TrainSelectionPage') }}">Ticketing</a></li>
                <li><a href="#">Discover</a></li>
                <li><a href="#">Feedback</a></li>
                <li><a href="#">Schedule</a></li>
            </ul>
        </nav>
        <nav>
            <ul>
                <li><a href="#">Booking</a></li>
                <li><a href="{{ route('signup') }}">Account</a></li>
            </ul>
        </nav>
        <div class="nav-animate"></div>
    </header>


    <main>
        @yield('content')
    </main>
    <footer>
        <div class="footer">
            <div class="footer-container">
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <ul>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                    </ul>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>About</h3>
                    <ul>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Other Services</h3>
                    <ul>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                        <li><a href="#">Can be Anything</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Payment Methods</h3>
                    <div class="payment-methods">
                        <img src="{{ asset('images/payment/mastercard.png') }}" alt="Mastercard">
                        <img src="{{ asset('images/payment/tng.png') }}" alt="TNG">
                        <img src="{{ asset('images/payment/visa.png') }}" alt="Visa">
                    </div>
                    <h3>Our Partners</h3>
                    <div class="partners">
                        <img src="{{ asset('images/partners/google.png') }}" alt="Google">
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>Copyright © 2025 TravelFree Malaysia Sdn. Bhd. All rights reserved</p>
                <p>Site Operator: TravelFree Malaysia Sdn. Bhd.</p>
            </div>
        </div>
    </footer>
    <script src="{{ asset('js/master.js') }}" defer></script>
</body>
</html>