@extends('Layout.master')

@section('title', 'Train Selection - TravelFree')

@push('styles')
    <link href="css/TrainSelectionPage.css" rel="stylesheet">
@endpush

@section('content')

<section class="first-section">
        <div class="video-wrapper">
            <video autoplay muted loop playsinline>
                <source src="{{ asset('video/HomePage_BGVideo.mp4') }}" type="video/mp4">
            </video>
        </div>

        <div class="ticket-search-container">

            <form class="search-form">
                <div class="form-group">
                    <label>Depart Location</label>
                    <input type="text" placeholder="Where from?">
                </div>

                <div class="swap-btn-container">
                    <button type="button" class="swap-btn" onclick="swapLocations()">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>

                <div class="form-group">
                    <label>To Location</label>
                    <input type="text" placeholder="Where to?">
                </div>

                <div class="form-group">
                    <label>Departure Date</label>
                    <div class="date-input-container">
                        <i class="fas fa-calendar-alt date-icon"></i>
                        <input type="text" id="depart-date" placeholder="Select date" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label>Return Date</label>
                    <div class="date-input-container">
                        <i class="fas fa-calendar-alt date-icon"></i>
                        <input type="text" id="return-date" placeholder="Select date" readonly disabled>
                    </div>
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

<section class="train-select-section">
    <div class="train-select-container">
        <div class="filter-container">
            <h2>Filters</h2>
            <br />
            <div class="filter-section">
                <h4>Train Type</h4>
                <div class="checkbox-row">
                    <input type="checkbox" id="direct" />
                    <label for="direct">ETS</label>
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" id="oneStop" />
                    <label for="oneStop">KTM Komuter</label>
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" id="twoStops" />
                    <label for="twoStops">KTM Intercity</label>
                </div>
            </div>

            <!-- Departure Time -->
            <div class="filter-section">
                <h4>Departure Time</h4>
                <div class="checkbox-row">
                    <input type="checkbox" id="early" />
                    <label for="early">Early Train (00:00 - 06:00)</label>
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" id="morning" />
                    <label for="morning">Morning Train (06:00 - 12:00)</label>
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" id="afternoon" />
                    <label for="afternoon">Afternoon Train (12:00 - 18:00)</label>
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" id="night" />
                    <label for="night">Night Train (18:00 - 00:00)</label>
                </div>
                <button class="filter-submit-button">Filter Now</button>
            </div>
        </div>
        <div class="train-select">
            <div class="train-select-header">
                <div class="header-cell">Departing Train</div>
            </div>
            <div class="train-card">
                <div class="train-col train-name">
                    <img src="{{ asset('images/logo/ets_logo.png') }}" class="train-logo" alt="ETS Logo">
                    ETS-2039
                </div>
                <div class="train-col train-type">Gold</div>
                <div class="train-col train-time">
                    <div><b>07:00 PM</b> &mdash; <b>08:05 PM</b></div>
                    <div class="train-desc">(1 hour, Direct)</div>
                </div>
                <div class="train-col train-capacity">
                    200
                    <div class="capacity-desc">(seat left)</div>
                </div>
                <div class="train-col train-action">
                    <span class="train-price">RM105</span>
                    <a href="{{ route('passengerinfo') }}"><button class="btn-select">Select</button></a>
                </div>
            </div>
            <div class="train-card">
                <div class="train-col train-name">
                    <img src="{{ asset('images/logo/ets_logo.png') }}" class="train-logo" alt="ETS Logo">
                    ETS-1192
                </div>
                <div class="train-col train-type">Silver</div>
                <div class="train-col train-time">
                    <div><b>09:00 PM</b> &mdash; <b>10:05 PM</b></div>
                    <div class="train-desc">(1 hour 05 minutes)</div>
                </div>
                <div class="train-col train-capacity">
                    29
                    <div class="capacity-desc">(seat left)</div>
                </div>
                <div class="train-col train-action">
                    <span class="train-price">RM210</span>
                    <a href="{{ route('passengerinfo') }}"><button class="btn-select">Select</button></a>
                </div>
            </div>
            <div class="train-card">
                <div class="train-col train-name">
                    <img src="{{ asset('images/logo/komuter_logo.png') }}" class="train-logo" alt="ETS Logo">
                    KTM-3923
                </div>
                <div class="train-col train-type">Komuter</div>
                <div class="train-col train-time">
                    <div><b>08:05 PM</b> &mdash; <b>11.00 PM</b></div>
                    <div class="train-desc">(3 hour 05 minutes)</div>
                </div>
                <div class="train-col train-capacity">
                    70
                    <div class="capacity-desc">(seat left)</div>
                </div>
                <div class="train-col train-action">
                    <span class="train-price">RM210</span>
                    <a href="{{ route('passengerinfo') }}"><button class="btn-select">Select</button></a>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="{{ asset('js/HomePage.js') }}" defer></script>

@endsection
