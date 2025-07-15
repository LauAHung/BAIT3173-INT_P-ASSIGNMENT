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
            <h3>Filter</h3>
            <label><input type="checkbox" name="price"> Price</label>
            <label><input type="checkbox" name="xxx"> XXX</label>
            <label><input type="checkbox" name="xxx2"> XXX</label>
        </div>
        <div class="train-select">
            <table>
                <thead>
                    <tr>
                        <th>Train No</th>
                        <th>Time</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>011</td>
                        <td>3pm - 6pm</td>
                        <td>RM 26</td>
                    </tr>
                    <tr>
                        <td>012</td>
                        <td>4pm - 7pm</td>
                        <td>RM 24</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="{{ asset('js/HomePage.js') }}" defer></script>

@endsection
