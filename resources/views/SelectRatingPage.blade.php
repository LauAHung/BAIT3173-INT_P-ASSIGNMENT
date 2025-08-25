@extends('Layout.master')

@section('title', 'Rate Your Completed Rides')

@push('styles')
    <link href="{{ asset('css/selectrating.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="rate-container">
    <h1>Completed Journeys Available for Rating</h1>
    <br>
    <p>These journeys were completed at least 1 day ago. Please rate your experience.</p>
<br>
<br>
    <!-- Ride 1 -->
    <div class="ride-card">
        <div class="ride-content">
            <div class="ride-details">
                <div class="ride-header">Train Number: KTM123</div>
                Date: 2025-07-10<br>
                Time: 09:30 AM<br>
                Train Type: KTM
            </div>
        </div>
        <button class="rate-button">Rate This Ride</button>
    </div>

    <!-- Ride 2 -->
    <div class="ride-card">
        <div class="ride-content">
            <div class="ride-details">
                <div class="ride-header">Train Number: ETS456</div>
                Date: 2025-07-08<br>
                Time: 03:15 PM<br>
                Train Type: ETS
            </div>
        </div>
        <button class="rate-button">Rate This Ride</button>
    </div>
</div>
@endsection
