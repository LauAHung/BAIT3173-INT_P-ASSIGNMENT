@extends('Layout.master')

@section('title', 'Feedback Page')

@push('styles')
    <link href="{{ asset('css/FeedbackPage.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="feedback-container">
    <!-- Rate Card -->
    <a href="{{ route('selectrating') }}" class="card-link">
        <div class="card full-card">
            <div class="icon-placeholder">🚂</div>
            <div class="card-content">
                <h3 class="card-title">Rate</h3>
            </div>
        </div>
    </a>

    <!-- View Feedback Card -->
    <a href="{{ route('viewfeedback') }}" class="card-link">
        <div class="card full-card">
            <div class="icon-placeholder">📋</div>
            <div class="card-content">
                <h3 class="card-title">View Feedback</h3>
            </div>
        </div>
    </a>
</div>
@endsection