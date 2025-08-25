@extends('Layout.master')

@section('title', 'View Feedback')

@push('styles')
    <link href="{{ asset('css/Viewfeedback.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="feedback-page">
    <div class="tab-container">
        <div class="tab-item active" onclick="showFeedback('my')">My Feedback</div>
        <div class="tab-item" onclick="showFeedback('other')">Other Feedback</div>
    </div>

    <div class="feedback-content">
        <div id="my-feedback" class="feedback-section">
            <h2>My Feedback</h2>
            <p>You have submitted the following feedbacks:</p>
            <!-- Example Feedback -->
            <div class="feedback-box">
                <p><strong>Train:</strong> KTM 123</p>
                <p><strong>Date:</strong> 20 July 2025</p>
                <p><strong>Feedback:</strong> Very clean and punctual!</p>
            </div>
        </div>

        <div id="other-feedback" class="feedback-section hidden">
            <h2>Other Users' Feedback</h2>
            <p>See what others are saying:</p>
            <div class="feedback-box">
                <p><strong>User:</strong> John</p>
                <p><strong>Train:</strong> ETS 456</p>
                <p><strong>Date:</strong> 19 July 2025</p>
                <p><strong>Feedback:</strong> Comfortable seats and smooth ride.</p>
            </div>
            <div class="feedback-box">
                <p><strong>User:</strong> John</p>
                <p><strong>Train:</strong> ETS 456</p>
                <p><strong>Date:</strong> 19 July 2025</p>
                <p><strong>Feedback:</strong> Comfortable seats and smooth ride.</p>
            </div>
            <div class="feedback-box">
                <p><strong>User:</strong> John</p>
                <p><strong>Train:</strong> ETS 456</p>
                <p><strong>Date:</strong> 19 July 2025</p>
                <p><strong>Feedback:</strong> Comfortable seats and smooth ride.</p>
            </div><div class="feedback-box">
                <p><strong>User:</strong> John</p>
                <p><strong>Train:</strong> ETS 456</p>
                <p><strong>Date:</strong> 19 July 2025</p>
                <p><strong>Feedback:</strong> Comfortable seats and smooth ride.</p>
            </div><div class="feedback-box">
                <p><strong>User:</strong> John</p>
                <p><strong>Train:</strong> ETS 456</p>
                <p><strong>Date:</strong> 19 July 2025</p>
                <p><strong>Feedback:</strong> Comfortable seats and smooth ride.</p>
            </div><div class="feedback-box">
                <p><strong>User:</strong> John</p>
                <p><strong>Train:</strong> ETS 456</p>
                <p><strong>Date:</strong> 19 July 2025</p>
                <p><strong>Feedback:</strong> Comfortable seats and smooth ride.</p>
            </div><div class="feedback-box">
                <p><strong>User:</strong> John</p>
                <p><strong>Train:</strong> ETS 456</p>
                <p><strong>Date:</strong> 19 July 2025</p>
                <p><strong>Feedback:</strong> Comfortable seats and smooth ride.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function showFeedback(type) {
        document.querySelectorAll('.tab-item').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.feedback-section').forEach(section => section.classList.add('hidden'));

        if (type === 'my') {
            document.querySelector('.tab-item:nth-child(1)').classList.add('active');
            document.getElementById('my-feedback').classList.remove('hidden');
        } else {
            document.querySelector('.tab-item:nth-child(2)').classList.add('active');
            document.getElementById('other-feedback').classList.remove('hidden');
        }
    }
</script>
@endsection
