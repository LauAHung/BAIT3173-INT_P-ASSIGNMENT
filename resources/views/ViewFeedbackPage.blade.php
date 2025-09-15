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
                <!-- Filter Buttons -->
                <div class="feedback-filter" style="text-align: right; margin-bottom: 20px; margin-right: 20px;">
                    <span>Filter by Train:</span>

                    <label class="circle-filter">
                        <span class="circle-btn train-filter-btn active" data-train="All"></span>
                        <span>All</span>
                    </label>

                    <label class="circle-filter">
                        <span class="circle-btn train-filter-btn" data-train="ETS"></span>
                        <span>ETS</span>
                    </label>

                    <label class="circle-filter">
                        <span class="circle-btn train-filter-btn" data-train="Komuter"></span>
                        <span>KTM</span>
                    </label>
                </div>
                <div class="feedback-item-container">
                    @if ($myFeedback && $myFeedback->count() > 0)
                        @foreach($myFeedback as $feedback)
                            <div class="feedback-item" data-train="{{ $feedback['TrainService'] ?? 'Unknown' }}">
                                <div class="feedback-flex-row">
                                    <!-- User Info -->
                                    <div class="feedback-col feedback-col-left">
                                        <i class="fas fa-user-circle feedback-icon"></i>
                                        <strong>{{ $feedback['last_name'] ?? '' }} {{ $feedback['first_name'] ?? '' }}</strong>
                                    </div>

                                    <!-- Feedback Info -->
                                    <div class="feedback-col feedback-col-middle">
                                        <div class="star-rating">
                                            @for ($i = 0; $i < ($feedback['rating_value'] ?? 0); $i++) ★ @endfor
                                            @for ($i = ($feedback['rating_value'] ?? 0); $i < 5; $i++) ☆ @endfor
                                        </div>
                                        <p class="feedback-text">{{ $feedback['feedback_text'] ?? '' }}</p>
                                        <p class="feedback-date"><strong>Date:</strong> {{ $feedback['feedback_time'] ?? '' }}</p>
                                    </div>

                                    <!-- Journey Details -->
                                    <div class="feedback-journey">
                                        <p><strong>Journey ID:</strong> {{ $feedback['JourneyID'] ?? 'Unknown' }}</p>
                                        <p><strong>Train:</strong> {{ $feedback['TrainService'] ?? 'Unknown' }} ({{ $feedback['TrainNo'] ?? 'Unknown' }})</p>
                                        <p><strong>Route:</strong> {{ $feedback['FromLocation'] ?? 'Unknown' }} → {{ $feedback['ToLocation'] ?? 'Unknown' }}</p>
                                        <p><strong>Time:</strong> 
                                            {{ !empty($feedback['DepartureTime']) ? date('g:i A', strtotime($feedback['DepartureTime'])) : 'Unknown' }} 
                                            - 
                                            {{ !empty($feedback['ArrivalTime']) ? date('g:i A', strtotime($feedback['ArrivalTime'])) : 'Unknown' }}
                                        </p>
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
                <!-- Filter Buttons -->
                <div class="feedback-filter" style="text-align: right; margin-bottom: 20px; margin-right: 20px;">
                    <span>Filter by Train:</span>

                    <label class="circle-filter">
                        <span class="circle-btn train-filter-btn active" data-train="All"></span>
                        <span>All</span>
                    </label>

                    <label class="circle-filter">
                        <span class="circle-btn train-filter-btn" data-train="ETS"></span>
                        <span>ETS</span>
                    </label>

                    <label class="circle-filter">
                        <span class="circle-btn train-filter-btn" data-train="Komuter"></span>
                        <span>KTM</span>
                    </label>
                </div>

                <div class="feedback-item-container">
                    @if ($otherFeedback && $otherFeedback->count() > 0)
                        @foreach($otherFeedback as $feedback)
                            <div class="feedback-item" data-train="{{ $feedback['TrainService'] ?? 'Unknown' }}">
                                <div class="feedback-flex-row">
                                    <div class="feedback-col feedback-col-left">
                                        <i class="fas fa-user-circle feedback-icon"></i>
                                        <strong>{{ $feedback['last_name'] ?? '' }} {{ $feedback['first_name'] ?? '' }}</strong>
                                    </div>
                                    <div class="feedback-col feedback-col-middle">
                                        <div class="star-rating">
                                            @for ($i = 0; $i < ($feedback['rating_value'] ?? 0); $i++) ★ @endfor
                                            @for ($i = ($feedback['rating_value'] ?? 0); $i < 5; $i++) ☆ @endfor
                                        </div>
                                        <p class="feedback-text">{{ $feedback['feedback_text'] ?? '' }}</p>
                                        <p class="feedback-date"><strong>Date:</strong> {{ $feedback['feedback_time'] ?? '' }}</p>
                                    </div>
                                    <div class="feedback-journey">
                                        <p><strong>Journey ID:</strong> {{ $feedback['JourneyID'] ?? 'Unknown' }}</p>
                                        <p><strong>Train:</strong> {{ $feedback['TrainService'] ?? 'Unknown' }} ({{ $feedback['TrainNo'] ?? 'Unknown' }})</p>
                                        <p><strong>Route:</strong> {{ $feedback['FromLocation'] ?? 'Unknown' }} → {{ $feedback['ToLocation'] ?? 'Unknown' }}</p>
                                        <p><strong>Time:</strong> 
                                            {{ !empty($feedback['DepartureTime']) ? date('g:i A', strtotime($feedback['DepartureTime'])) : 'Unknown' }} 
                                            - 
                                            {{ !empty($feedback['ArrivalTime']) ? date('g:i A', strtotime($feedback['ArrivalTime'])) : 'Unknown' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="feedback-item">
                            <p>No feedback from other users yet.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tabs switching (My Feedback / Other Feedback)
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

    // Train filter buttons
    const buttons = document.querySelectorAll('.train-filter-btn');
    const feedbackItems = document.querySelectorAll('.feedback-item');

    let activeFilters = ['All']; // Default is All

    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            const train = this.getAttribute('data-train');

            if(train === 'All') {
                // Reset to show all
                activeFilters = ['All'];
                buttons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            } else {
                // Toggle specific filter
                if(activeFilters.includes(train)){
                    activeFilters = activeFilters.filter(f => f !== train);
                    this.classList.remove('active');
                } else {
                    activeFilters.push(train);
                    this.classList.add('active');
                }

                // If any specific selected, remove "All"
                activeFilters = activeFilters.filter(f => f !== 'All');
                buttons.forEach(b => {
                    if(b.getAttribute('data-train') === 'All'){
                        b.classList.remove('active');
                    }
                });

                // If nothing selected, default back to "All"
                if(activeFilters.length === 0){
                    activeFilters = ['All'];
                    buttons.forEach(b => b.classList.remove('active'));
                    document.querySelector('.train-filter-btn[data-train="All"]').classList.add('active');
                }
            }

            // Filter feedback items
            feedbackItems.forEach(item => {
                const itemTrain = item.getAttribute('data-train');
                if(activeFilters.includes('All') || activeFilters.includes(itemTrain)){
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endsection
