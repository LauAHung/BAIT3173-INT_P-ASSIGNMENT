@extends('Layout.master')

@section('title', 'Home Page')

@push('styles')
<link href="css/HomePage.css" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
@endpush

@section('content')
<main>
    <section class="first-section">
        <div class="video-wrapper">
            <video autoplay muted loop playsinline>
                <source src="{{ asset('video/HomePage_BGVideo.mp4') }}" type="video/mp4">
            </video>
        </div>

        <div class="ticket-search-header">
            <h1>Search & Book Trains</h1>
            <p>Book & Start your journey now</p>
        </div>

        <div class="train-type-toggle">
            <div class="toggle-container">
                <input type="radio" id="return-train" name="train-type" class="train-option" checked
                    onclick="handleTrainTypeChange()">
                <label for="return-train">Return</label>

                <input type="radio" id="one-way-train" name="train-type" class="train-option"
                    onclick="handleTrainTypeChange()">
                <label for="one-way-train">One Way</label>

                <span class="toggle-switch"></span>
            </div>
        </div>

        <div class="ticket-search-container">
            <form class="search-form" action="{{ route('train.selection') }}" method="GET" id="searchForm">
                <div class="form-group">
                    <label>Depart Location</label>
                    <input type="text" name="fromlocation" placeholder="Where from?"
                        value="{{ request()->input('fromlocation') }}">
                </div>

                <div class="swap-btn-container">
                    <button type="button" class="swap-btn" onclick="swapLocations()">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>

                <div class="form-group">
                    <label>To Location</label>
                    <input type="text" name="tolocation" placeholder="Where to?"
                        value="{{ request()->input('tolocation') }}">
                </div>

                <div class="form-group">
                    <label>Departure Date</label>
                    <div class="date-input-container">
                        <i class="fas fa-calendar-alt date-icon"></i>
                        <input type="text" id="depart-date" name="journeydate" placeholder="Select date" readonly
                            value="{{ request()->input('journeydate') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Return Date</label>
                    <div class="date-input-container">
                        <i class="fas fa-calendar-alt date-icon"></i>
                        <input type="text" id="return-date" name="returndate" placeholder="Select date" readonly
                            disabled value="{{ request()->input('returndate') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Passengers</label>
                    <select name="passengers">
                        <option value="1" {{ request()->input('passengers') == '1' ? 'selected' : '' }}>1 Passenger
                        </option>
                        <option value="2" {{ request()->input('passengers') == '2' ? 'selected' : '' }}>2 Passengers
                        </option>
                        <option value="3" {{ request()->input('passengers') == '3' ? 'selected' : '' }}>3 Passengers
                        </option>
                    </select>
                </div>

                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>
    </section>
    <section>
        <div class="slider">
            <!-- list Items -->
            <div class="list">
                <div class="item active">
                    <img src="{{ asset('images/penang.jpeg') }}">

                    <div class="content">
                        <p>Penang, Malaysia</p>
                        <h2>The Enchanted Heights of Penang</h2>
                        <p>
                            Perched amidst lush rainforest and offering breathtaking vistas of the island, Penang Hill
                            is a serene retreat where nature, heritage, and tranquility converge.
                        </p>
                    </div>
                </div>
                <div class="item">
                    <img src="{{ asset('images/kl.jpeg') }}" />
                    <div class="content">
                        <p>Kuala Lumpur</p>
                        <h2>The Beating Heart of Malaysia</h2>
                        <p>
                            A dynamic fusion of soaring skyscrapers, historic landmarks, and diverse cultures, Kuala
                            Lumpur is a captivating city where modern ambition intertwines with deep-rooted heritage.
                        </p>
                    </div>
                </div>
                <div class="item">
                    <img src="{{ asset('images/johor.jpg') }}" />
                    <div class="content">
                        <p>Johor Bahru</p>
                        <h2>The Gateway to Southern Malaysia</h2>
                        <p>
                            A vibrant city that seamlessly blends Malay traditions with modern urban flair, Johor Bahru
                            is a gateway to the rich cultural tapestry of Malaysia's southern states.
                        </p>
                    </div>
                </div>
                <div class="item">
                    <img src="{{ asset('images/perak.jpeg') }}" />
                    <div class="content">
                        <p>Perak</p>
                        <h2>The Heart of Malaysia's Heritage</h2>
                        <p>
                            A land of ancient temples, vibrant markets, and serene landscapes, Perak is a treasure trove
                            of Malaysia's cultural and natural diversity.
                        </p>
                    </div>
                </div>
                <div class="item">
                    <img src="{{ asset('images/penang2.jpg') }}" />
                    <div class="content">
                        <p>Penang</p>
                        <h2>The Enchanted Heights of Penang</h2>
                        <p>
                            A vibrant city that seamlessly blends Malay traditions with modern urban flair, Johor Bahru
                            is a gateway to the rich cultural tapestry of Malaysia's southern states.
                        </p>
                    </div>
                </div>
            </div>
            <!-- button arrows -->
            <div class="arrows">
                <button id="prev">
                    << /button>
                        <button id="next">></button>
            </div>
            <!-- thumbnail -->
            <div class="thumbnail">
                <div class="item active">
                    <img src="{{ asset('images/penang.jpeg') }}" />
                    <div class="content">
                    </div>
                </div>
                <div class="item">
                    <img src="{{ asset('images/kl.jpeg') }}" />
                    <div class="content">
                    </div>
                </div>
                <div class="item">
                    <img src="{{ asset('images/johor.jpg') }}" />
                    <div class="content">
                    </div>
                </div>
                <div class="item">
                    <img src="{{ asset('images/perak.jpeg') }}" />
                    <div class="content">
                    </div>
                </div>
                <div class="item">
                    <img src="{{ asset('images/penang2.jpg') }}" />
                    <div class="content">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="newsletter-section">
        <div class="newsletter-container">

            <!-- Left Side Image -->
            <div class="newsletter-image">
                <img src="{{ asset('images/penang.jpeg') }}" alt="Newsletter Illustration">
            </div>

            <!-- Right Side Form -->
            <form action="#" class="newsletter-form">
                <div class="form-header">
                    <div class="form-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h1 class="form-title">Subscribe</h1>
                    <p class="form-description">Subscribe to our newsletter and stay updated.</p>
                </div>
                <div class="form-body">
                    <input type="email" placeholder="Your Email" class="email-input" required>
                    <button type="submit" class="subscribe-button">Subscribe</button>
                </div>
            </form>

        </div>
    </section>

    <section class="multi-banner-section">

        <div class="banner-block ets-banner">
            <div class="banner-content">
                <img src="{{ asset('images/logo/ets_logo.png') }}" alt="ETS Logo" class="banner-logo">
                <h2>Look inside ETS</h2>
                <p>If you need to travel a long distance quickly, you should think of us</p>
                <button>Read More</button>
            </div>
        </div>

        <div class="banner-block intercity-banner">
            <div class="banner-content">
                <img src="{{ asset('images/logo/intercity_logo.png') }}" alt="Intercity Logo" class="banner-logo">
                <h2>Groundbreaking with KTM INTERCITY</h2>
                <p>The chance to enjoy travelling along the iconic line</p>
                <button>Read More</button>
            </div>
        </div>

    </section>
    <section>
        <div class="banner-block komuter-banner">
            <div class="banner-content">
                <img src="{{ asset('images/logo/komuter_logo.png') }}" alt="KTM Logo" class="banner-logo">
                <h2>Hassle-free with KTM KOMUTER</h2>
                <p>Country's pioneer rail provider</p>
                <button>Read More</button>
            </div>
        </div>

    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{ asset('js/HomePage.js') }}" defer></script>
@endsection