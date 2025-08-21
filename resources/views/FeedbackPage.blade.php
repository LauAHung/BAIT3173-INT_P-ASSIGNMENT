@extends('Layout.master')

@section('title', 'Feedback Page')

@push('styles')
    <link href="{{ asset('css/FeedbackPage.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="page-container">
    <div class="card-container">

        <!-- Rate Card -->
        <div class="card">
            <!-- Link wraps the icon -->
            <a href="{{ route('selectrating') }}">
                <div class="icon-placeholder">🚂</div>
            </a>
            <div class="card-content">
                <h3 class="card-title">Rate</h3>
            </div>
        </div>

        <!-- View Feedback Card -->
        <div class="card">
            <!-- Link wraps the icon -->
            <a href="{{ route('viewfeedback') }}">
                <div class="icon-placeholder">📋</div>
            </a>
            <div class="card-content">
                <h3 class="card-title">View Feedback</h3>
            </div>
        </div>

    </div>
</div>


@endsection