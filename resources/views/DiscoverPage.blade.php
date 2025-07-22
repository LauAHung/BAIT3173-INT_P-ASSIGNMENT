@extends('Layout.master')

@section('title', 'Discover - TravelFree')

@push('styles')
    <link href="{{ asset('css/DiscoverPage.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/DiscoverPage.js') }}" defer></script>
@endpush

@section('content')
    <div class="container">
        <div class="header-section">
            <div class="header-left">
                <h1>Fast and Reliable</h1>
                <h2>Railway Ticketing Services</h2>
                <a href="{{ route('HomePage') }}" class="explore-btn">Get Started</a>
            </div>
            <div class="header-right">
                <img src="{{ asset('images/discover1.jpeg') }}" alt="Train">
            </div>
        </div>

        <div class="services-section">
            <h2 style="text-align: center;">Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <img src="{{ asset('images/discover2.jpg') }}" alt="Ticket Booking">
                    <h3>Ticket Booking</h3>
                    <p>Secure and instant railway ticket reservations</p>
                    <a href="{{ route('TrainSelectionPage') }}" class="explore-btn">Book Now</a>
                </div>
                <div class="service-card">
                    <img src="{{ asset('images/discover3.jpg') }}" alt="Schedule Planning">
                    <h3>Schedule Planning</h3>
                    <p>Plan your journey with our detailed schedules</p>
                    <a href="#" class="explore-btn">View Schedule</a>
                </div>
                <div class="service-card">
                    <img src="{{ asset('images/discover4.jpg') }}" alt="Customer Support">
                    <h3>Customer Support</h3>
                    <p>24/7 assistance for all your travel needs</p>
                    <a href="#" class="explore-btn">Looks for help</a>
                </div>
            </div>
        </div>

        <div class="background-section">
            <h2 style="color: white;">Our Story</h2>
            <div class="background-grid">
                <div class="background-card">
                    <p><b>At TravelFree, our journey began with a simple passion for connecting people to the world through seamless railway travel. Founded in 2018, we set out to revolutionize the ticketing experience by blending cutting-edge technology with a deep commitment to customer satisfaction. </br></br>Over the years, we’ve grown from a small startup to a trusted name, proudly serving millions of travelers with fast, reliable, and affordable services. Today, we continue to innovate, inspired by every journey taken and every story shared, as we strive to make travel dreams a reality for all.</b></p>
                </div>
                <div class="background-card">
                    <p><b>TravelFree has reached remarkable milestones since its inception, reflecting our dedication to excellence in railway travel. We take pride in having facilitated over 2 million ticket bookings, achieving a consistent 96% customer satisfaction rate, and establishing partnerships with more than 600 travel agencies worldwide. <br/><br/>Our innovative approach earned us the "Best Travel Service Provider" award in 2023, and we continue to lead the industry with sustainable practices, reducing carbon emissions by 15% through optimized scheduling. These achievements fuel our commitment to enhancing every traveler's experience.</b></p>
                </div>
            </div>
        </div>

        <div class="tips-section">
            <div class="tips-container">
                <div class="tips-image">
                    <img src="{{ asset('images/discover6.jpg') }}" alt="Travel Tips Image">
                </div>
                <div class="tips-content">
                    <h2>Travel Tips</h2>
                    <p>Make the most of your railway journey with these tips:</p>
                    <ul>
                        <li>&nbsp;Book tickets in advance for the best rates</li>
                        <li>&nbsp;Arrive early to avoid rush hours</li>
                        <li>&nbsp;Pack light for a comfortable trip</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="contact-section">
            <div class="contact-image">
                <img src="{{ asset('images/discover7.png') }}" alt="Contact Image">
            </div>
            <div class="contact-content">
                <h2>Contact Us</h2>
                <div class="contact-details">
                    <div class="contact-qr">
                        <img src="{{ asset('images/discover8.png') }}" alt="QR Code">
                        <p>Scan QR to download app</p>
                    </div>
                    <ul class="contact-text">
                        <li>&nbsp;Have questions? Reach out to us!</li>
                        <li>&nbsp;Email: <a href="mailto:gunyux@gmail.com">travelfree@gmail.com</a></li>
                        <li>&nbsp;Phone: <a href="tel:+1-800-872-835">+60-3-1234-5678</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection