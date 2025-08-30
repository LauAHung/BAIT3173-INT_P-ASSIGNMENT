@extends('Layout.master')

@section('title', 'RatingSection')

@push('styles')
    <link href="{{ asset('css/Rating.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class = "feedbackform" style="max-width:100%">
<div class="container">
    <h2>Leave a Review</h2>
    <form action="#">
        <div class="star-rating">
            <input type="radio" id="star5" name="rating" value="5">
            <label for="star5">&#9733;</label>

            <input type="radio" id="star4" name="rating" value="4">
            <label for="star4">&#9733;</label>

            <input type="radio" id="star3" name="rating" value="3">
            <label for="star3">&#9733;</label>

            <input type="radio" id="star2" name="rating" value="2">
            <label for="star2">&#9733;</label>

            <input type="radio" id="star1" name="rating" value="1">
            <label for="star1">&#9733;</label>
        </div>

        <textarea name="feedback" placeholder="Write your feedback..." required></textarea>

        <button type="submit">Submit Review</button>
    </form>
</div>

</div>
@endsection
