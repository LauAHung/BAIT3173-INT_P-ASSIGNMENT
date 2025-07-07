@extends('Layout.master')

@section('title', 'Home Page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/HomePage.css') }}">
@endpush

@section('content')
    <div class="ticket-search-container">
    <div class="trip-toggle">
        <button class="toggle-btn inactive">Return</button>
        <button class="toggle-btn active">One Way</button>
    </div>

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
            <input type="date" disabled> <!-- 默认禁用，One Way 时隐藏或禁用 -->
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