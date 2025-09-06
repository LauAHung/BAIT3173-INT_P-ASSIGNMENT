<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TravelFree')</title>
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">
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
                <li><a href="{{ route('HomePage') }}">Ticketing</a></li>
                <li><a href="{{ route('DiscoverPage') }}">Discover</a></li>
                <li><a href="{{ route('feedback') }}">Feedback</a></li>
                <li><a href="{{ route('concession_card') }}">Card Application</a></li>
            </ul>
        </nav>
        <nav>
            <ul>
                <li><a href="{{ route('booking') }}">Booking</a></li>
                @auth
                    <li><a href="{{ route('profile') }}">Account</a></li>
                @else
                    <li><a href="#" onclick="showLoginRequiredModal()">Account</a></li>
                @endauth
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
                    <h3>Address</h3>
                    <ul>
                        <li><span>No. 114, Jalan Damai 5/3,</span></li>
                        <li><span>Taman Damai Utama,</span></li>
                        <li><span>47180 Puchong, Selangor.</span></li>
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
                        <li><a href="#">About TravelFree</a></li>
                        <li><a href="#">News</a></li>
                        <li><a href="#">Carrer</a></li>
                        <li><a href="#">Terms and Condition</a></li>
                        <li><a href="#">Privacy Statement</a></li>
                        <li><a href="#">Accessibility Statement</a></li>
                        <li><a href="#">Do Not Sell My Personal Information</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Other Services</h3>
                    <ul>
                        <li><a href="#">Investor Relations</a></li>
                        <li><a href="#">TravelFree Rewards</a></li>
                        <li><a href="#">List Your Property</a></li>
                        <li><a href="#">Affiliate Program</a></li>
                        <li><a href="#">Become a Supplier</a></li>
                        <li><a href="#">Security</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Payment Methods</h3>
                    <div class="payment-methods">
                        <img src="{{ asset('images/mastercard_logo.png') }}" alt="Mastercard">
                        <img src="{{ asset('images/tng_logo.png') }}" alt="TNG">
                        <img src="{{ asset('images/visa_logo.png') }}" alt="Visa">
                    </div>
                    <h3>Our Partners</h3>
                    <div class="partners">
                        <img src="{{ asset('images/google_logo.png') }}" alt="Google">
                        <img src="{{ asset('images/facebook_logo.png') }}" alt="Google">
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
    
    <!-- Include Login Required Modal Component -->
    @include('components.login_required_modal')
</body>
</html>