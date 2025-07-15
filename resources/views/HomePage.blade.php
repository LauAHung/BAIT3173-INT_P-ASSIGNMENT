@extends('Layout.master')

@section('title', 'Home Page')

@push('styles')
    <link href="css/HomePage.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
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

                <form class="search-form">
                    <div class="form-group">
                        <label>Depart Location</label>
                        <input type="text" placeholder="Where from?">
                    </div>

                    <div class="form-group swap-group">
                        <label>To Location</label>
                        <input type="text" placeholder="Where to?">
                        <span class="swap-btn">⇄</span>
                    </div>

                    <div class="form-group">
                        <label>Departure Date</label>
                        <input type="date">
                    </div>

                    <div class="form-group">
                        <label>Return Date</label>
                        <input type="date" disabled>
                    </div>

                    <div class="form-group">
                        <label>Passengers</label>
                        <select>
                            <option>1 Passenger</option>
                            <option>2 Passengers</option>
                            <option>3 Passengers</option>
                        </select>
                    </div>
                </form>

                <button class="search-btn">Search</button>
            </div>
        </section>
            <section>
                <div class="slider">
                    <!-- list Items -->
                    <div class="list">
                        <div class="item active">
                            <img src="{{ asset('images/penang.jpeg') }}" >
                            
                            <div class="content">
                                <p>Penang, Malaysia</p>
                                <h2>The Enchanted Heights of Penang</h2>
                                <p>
                                    Perched amidst lush rainforest and offering breathtaking vistas of the island, Penang Hill is a serene retreat where nature, heritage, and tranquility converge.
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <img src="{{ asset('images/kl.jpeg') }}" />
                            <div class="content">
                                <p>Singapore</p>
                                <h2>The Radiant Jewel of Asia</h2>
                                <p>
                                    A harmonious blend of futuristic skyscrapers, verdant gardens, and rich cultural heritage, Singapore is a vibrant city-state where innovation meets timeless tradition.
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <img src="{{ asset('images/johor.jpg') }}" />
                            <div class="content">
                                <p>BANGKOK, Thailand</p>
                                <h2>The Land of Smiles and Splendor</h2>
                                <p>
                                    Thailand enchants with its golden temples, pristine beaches, and warm hospitality, offering a perfect harmony of cultural richness and natural beauty.
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <img src="{{ asset('images/perak.jpg') }}" />
                            <div class="content">
                                <p>YUNNAN, China</p>
                                <h2>The Timeless Heart of Asia</h2>
                                <p>
                                    A vast land of ancient wonders, dynamic cities, and diverse landscapes, China is a living tapestry of history, culture, and modern marvels.
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <img src="{{ asset('images/penang2.jpg') }}" />
                            <div class="content">
                                <p>Osaka, Japan</p>
                                <h2>The Eternal Land of the Rising Sun</h2>
                                <p>
                                    Japan captivates with its seamless blend of ancient traditions, cutting-edge innovation, and breathtaking natural landscapes, creating an unparalleled cultural journey.
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- button arrows -->
                    <div class="arrows">
                        <button id="prev"><</button>
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

  <div class="banner-block" style="background-image: url('{{ asset('images/ets_bg.jpeg') }}');">
    <div class="banner-content">
      <img src="{{ asset('images/ets_logo.png') }}" alt="ETS Logo" class="banner-logo">
      <h2>Look inside ETS</h2>
      <p>If you need to travel a long distance quickly, you should think of us</p>
      <button>Read More</button>
    </div>
  </div>

  <div class="banner-block" style="background-image: url('{{ asset('images/intercity_bg.jpeg') }}');">
    <div class="banner-content">
      <img src="{{ asset('images/ktm_intercity_logo.png') }}" alt="KTM Logo" class="banner-logo">
      <h2>Groundbreaking with KTM INTERCITY</h2>
      <p>The chance to enjoy travelling along the iconic line</p>
      <button>Read More</button>
    </div>
  </div>

</section>




    </main>

    <script src="{{ asset('js/HomePage.js') }}" defer></script>
@endsection