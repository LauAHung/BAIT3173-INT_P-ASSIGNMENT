@extends('Layout.master')

@section('title', 'View Feedback')

@push('styles')
    <link href="{{ asset('css/Viewfeedback.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush

@section('content')
<section>
    <div class="feedback-main-layout">
        <!-- Sidebar -->
        <aside class="feedback-sidebar">
            <ul>
                <li class="feedback-tab active" id="my-tab">My Feedback</li>
                <li class="feedback-tab" id="other-tab">Other Feedback</li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="feedback-content">
            <!-- My Feedback -->
            <div id="my-content" class="feedback-section">
                <div class="feedback-heading">
                    <h2>My Feedback</h2>
                </div>
                <div class="feedback-item-container">
                    @if ($myFeedback && $myFeedback->count() > 0)
                        @foreach($myFeedback as $feedback)
                            <div class="feedback-item">
                                <div class="feedback-flex-row">
                                    <div class="feedback-col feedback-col-left">
                                        <i class="fas fa-user-circle feedback-icon"></i>
                                        <div><strong>{{ $feedback->last_name }} {{ $feedback->first_name }}</strong></div>
                                    </div>
                                    <div class="feedback-col feedback-col-middle">
                                        <div class="star-rating">
                                            @for ($i = 0; $i < $feedback->rating_value; $i++) ★ @endfor
                                            @for ($i = $feedback->rating_value; $i < 5; $i++) ☆ @endfor
                                        </div>
                                        <p class="feedback-text">{{ $feedback->feedback_text }}</p>
                                        <p class="feedback-date"><strong>Date:</strong> {{ $feedback->feedback_time }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="feedback-item">
                            <p>No feedback submitted yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Other Feedback -->
            <div id="other-content" class="feedback-section" style="display:none;">
                <div class="feedback-heading">
                    <h2>Other Users' Feedback</h2>
                </div>
                <div class="feedback-item-container">
                    @if (isset($otherFeedback))
                        @forelse($otherFeedback as $feedback)
                            <div class="feedback-item">
                                <div class="feedback-flex-row">
                                    <div class="feedback-col feedback-col-left">
                                        <i class="fas fa-user-circle feedback-icon"></i>
                                        <div><strong>{{ $feedback->last_name }} {{ $feedback->first_name }}</strong></div>
                                    </div>
                                    <div class="feedback-col feedback-col-middle">
                                        <div class="star-rating">
                                            @for ($i = 0; $i < $feedback->rating_value; $i++) ★ @endfor
                                            @for ($i = $feedback->rating_value; $i < 5; $i++) ☆ @endfor
                                        </div>
                                        <p class="feedback-text">{{ $feedback->feedback_text }}</p>
                                        <p class="feedback-date"><strong>Date:</strong> {{ $feedback->feedback_time }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="feedback-item">
                                <p>No feedback from other users yet.</p>
                            </div>
                        @endforelse
                    @else
                        <div class="feedback-item">
                            <p>Unable to load other users' feedback. Please try again later.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.feedback-tab');
    const contents = document.querySelectorAll('.feedback-section');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.style.display = 'none');
            this.classList.add('active');
            const contentId = this.id.replace('-tab', '-content');
            document.getElementById(contentId).style.display = 'block';
        });
    });
});
</script>
@endsection