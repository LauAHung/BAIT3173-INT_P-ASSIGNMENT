@extends('Layout.master')

@section('title', 'Train Selection - TravelFree')

@push('styles')
<link href="{{ asset('css/TrainSelectionPage.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
@endpush

@section('content')
<section class="first-section">
    <div class="video-wrapper">
        <video autoplay muted loop playsinline>
            <source src="{{ asset('video/HomePage_BGVideo.mp4') }}" type="video/mp4">
        </video>
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
                    <input type="text" id="return-date" name="returndate" placeholder="Select date" readonly disabled
                        value="{{ request()->input('returndate') }}">
                </div>
            </div>

            <div class="form-group">
                <label>Passengers</label>
                <select name="passengers">
                    <option value="1" {{ request()->input('passengers') == '1' ? 'selected' : '' }}>1 Passenger</option>
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

<section class="train-select-section">
    <div class="train-select-container">
        <div class="filter-container">
            <h2>Filters</h2>
            <br />
            <form action="{{ route('train.selection') }}" method="GET" id="filterForm">
                <!-- Hidden inputs to preserve search parameters -->
                <input type="hidden" name="fromlocation" value="{{ request()->input('fromlocation') }}">
                <input type="hidden" name="tolocation" value="{{ request()->input('tolocation') }}">
                <input type="hidden" name="journeydate" value="{{ request()->input('journeydate') }}">
                <input type="hidden" name="passengers" value="{{ request()->input('passengers') }}">
                <input type="hidden" name="returndate" value="{{ request()->input('returndate') }}">

                <div class="filter-section">
                    <h4>Train Type</h4>
                    <div class="checkbox-row">
                        <input type="checkbox" name="train_type[]" value="ETS" id="direct"
                            {{ in_array('ETS', request()->input('train_type', [])) ? 'checked' : '' }}>
                        <label for="direct">ETS</label>
                    </div>
                    <div class="checkbox-row">
                        <input type="checkbox" name="train_type[]" value="Komuter" id="oneStop"
                            {{ in_array('Komuter', request()->input('train_type', [])) ? 'checked' : '' }}>
                        <label for="oneStop">KTM Komuter</label>
                    </div>
                    <div class="checkbox-row">
                        <input type="checkbox" name="train_type[]" value="Intercity" id="twoStops"
                            {{ in_array('Intercity', request()->input('train_type', [])) ? 'checked' : '' }}>
                        <label for="twoStops">KTM Intercity</label>
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Departure Time</h4>
                    <div class="checkbox-row">
                        <input type="checkbox" name="departure_time[]" value="early" id="early"
                            {{ in_array('early', request()->input('departure_time', [])) ? 'checked' : '' }}>
                        <label for="early">Early Train (00:00 - 06:00)</label>
                    </div>
                    <div class="checkbox-row">
                        <input type="checkbox" name="departure_time[]" value="morning" id="morning"
                            {{ in_array('morning', request()->input('departure_time', [])) ? 'checked' : '' }}>
                        <label for="morning">Morning Train (06:00 - 12:00)</label>
                    </div>
                    <div class="checkbox-row">
                        <input type="checkbox" name="departure_time[]" value="afternoon" id="afternoon"
                            {{ in_array('afternoon', request()->input('departure_time', [])) ? 'checked' : '' }}>
                        <label for="afternoon">Afternoon Train (12:00 - 18:00)</label>
                    </div>
                    <div class="checkbox-row">
                        <input type="checkbox" name="departure_time[]" value="night" id="night"
                            {{ in_array('night', request()->input('departure_time', [])) ? 'checked' : '' }}>
                        <label for="night">Night Train (18:00 - 00:00)</label>
                    </div>
                </div>
                <button type="submit" class="filter-submit-button">Filter</button>
            </form>
        </div>

        <div class="train-select">
            <div class="train-select-header">
                <div class="header-cell">Departing Train</div>
            </div>
            @php
            $journeys = $journeys ?? collect(); // Default to empty collection if undefined
            @endphp
            @if ($journeys->isNotEmpty())
            @foreach ($journeys as $journey)
            <div class="train-card">
                <div class="train-col train-name">
                    @php
                    $logoMap = [
                    'ETS' => 'ets_logo.png',
                    'Komuter' => 'komuter_logo.png',
                    'Intercity' => 'intercity_logo.png'
                    ];
                    $trainService = $journey->Train->TrainService ?? 'Unknown';
                    $logoFile = $logoMap[$trainService] ?? 'default_logo.png';
                    @endphp
                    <img src="{{ asset('images/logo/' . $logoFile) }}" class="train-logo" alt="Train Logo">
                    {{ $journey->Train->TrainNo ?? 'Unknown' }}
                </div>
                <div class="train-col train-type">{{ $journey->Train->TrainService ?? 'Unknown' }}</div>
                <div class="train-col train-time">
                    <div><b>{{ date('h:i A', strtotime($journey->DepartureTime)) }}</b> &mdash;
                        <b>{{ date('h:i A', strtotime($journey->ArrivalTime)) }}</b>
                    </div>
                    <div class="train-desc">({{ $journey->FromLocation ?? 'Unknown' }} to
                        {{ $journey->ToLocation ?? 'Unknown' }})</div>
                </div>
                <div class="train-col train-capacity">
                    {{ $journey->SeatAvailable }}
                    <div class="capacity-desc">(seat left)</div>
                </div>
                <div class="train-col train-action">
                    <span class="train-price">RM{{ $journey->Price }}</span>
                    <a
                        href="{{ route('passengerinfo', ['passengers' => request()->input('passengers', 1), 'journey_id' => $journey->JourneyID]) }}">
                        <button class="btn-select">Select</button>
                    </a>
                </div>
            </div>
            @endforeach
            @else
            <div class="result">
                <p>No trains available matching your criteria.</p>
            </div>
            @endif
        </div>
    </div>
</section>

<script src="{{ asset('js/HomePage.js') }}" defer></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#depart-date", {
    minDate: "today",
    dateFormat: "Y-m-d",
    onChange: function(selectedDates, dateStr, instance) {
        if (dateStr) {
            document.getElementById('return-date').disabled = false;
            flatpickr("#return-date", {
                minDate: dateStr,
                dateFormat: "Y-m-d"
            });
        }
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Pass the session success value to JavaScript
    const successMessage = "{{ session('success') }}";

    // Normal JavaScript if statement
    if (successMessage) {
        console.log('Success message detected:', successMessage); // Debug log
        Swal.fire({
            title: 'Success!',
            text: successMessage,
            icon: 'success',
            timer: 3000, // Auto-close after 3 seconds
            showConfirmButton: false,
            toast: true,
            width: 800, // Increase width to 400px (default is auto)
            padding: '15px 20px', // Increase padding for larger content area
            customClass: {
                popup: 'custom-larger-toast' // Custom class for additional styling
            }
        }).then(() => {
            console.log('Toast displayed'); // Debug log
        });
    } else {
        console.log('No success message found'); // Debug log
    }
});
</script>

@endsection