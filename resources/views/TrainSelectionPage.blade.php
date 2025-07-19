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

        <div class="search-bar">
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
        </div>    
</section>

<section class="train-select-section">
    <div class="train-select-container">
        <div class="filter-container">
            <h3>Filters</h3>
            <a>Service Type</a>
            <label><input type="checkbox" name="ets"> ETS</label>
            <label><input type="checkbox" name="ktm"> KTM</label>
            <label><input type="checkbox" name="komuter"> KOMUTER</label>

            <a>Depart Time</a>
            <label><input type="checkbox" name="time1"> 8:00am - 12:00pm</label>
            <label><input type="checkbox" name="time2"> 12:00pm - 4:00pm</label>
            <label><input type="checkbox" name="time3"> 4:00pm - 6:00pm</label>
            <label><input type="checkbox" name="time4"> 6:00pm - 12:00pm</label>
        </div>
        <div class="train-select">
            <div class="train-select-header">
                <div class="header-cell">Departing Train</div>
            </div>
            <div class="train-select-row">
                <div class="row-cell"><img src="{{ asset('images/logo/ets_logo.png') }}"></div>
                <div class="row-cell">3pm - 6pm</div>
                <div class="row-cell">999</div>
                <div class="row-cell">RM 26</div>
                <div class="row-cell"><a href="{{ route('passengerinfo') }}"><button class="btn-select">Select</button></a></div>
            </div>
            <div class="train-select-row">
                <div class="row-cell">T-012</div>
                <div class="row-cell">4pm - 7pm</div>
                <div class="row-cell">778</div>
                <div class="row-cell">RM 24</div>
                <div class="row-cell"><a href="{{ route('passengerinfo') }}"><button class="btn-select">Select</button></a></div>
            </div>
        </div>
    </div>
</section>

<script src="{{ asset('js/HomePage.js') }}" defer></script>

@endsection
