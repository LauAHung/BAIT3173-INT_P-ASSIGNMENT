@extends('Layout.master')

@section('title', 'Home Page')

@vite(['resources/js/HomePage.js'])

@section('content')
<video id="bg-video" autoplay muted loop playsinline>
    <source src="{{ asset('video/HomePage_BGVideo.mp4') }}" type="video/mp4">
</video>

    <div class="trip-toggle">
        <button class="toggle-btn inactive">Return</button>
        <button class="toggle-btn active">One Way</button>
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

@endsection